<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorUsers;
use Exception;

class IntegrationController extends Controller
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('BACKEND_API_URL');
        $this->middleware('auth');
    }

    /**
     * Sync product to external API
     */
    public function syncProduct(Request $request)
    {
        try {
            $user = Auth::user();
            $vendorUser = VendorUsers::where('user_id', $user->id)->first();

            if (!$vendorUser) {
                return response()->json(['success' => false, 'message' => 'Vendor not found'], 404);
            }

            // Firestore vendor ID (vandorId from JS)
            $vendorFirestoreId = $request->vendorID ?? $vendorUser->firestore_vendor_id ?? '';

            $fields = [
                ['name' => 'vendor',        'contents' => $vendorFirestoreId],
                ['name' => 'vendor_id',     'contents' => $vendorUser->uuid],
                ['name' => 'firestore_id',  'contents' => $request->id ?? ''],
                ['name' => 'name',          'contents' => $request->name ?? ''],
                ['name' => 'price',         'contents' => (string)($request->price ?? 0)],
                ['name' => 'discount_price','contents' => (string)($request->disPrice ?? 0)],
                ['name' => 'quantity',      'contents' => (string)($request->quantity ?? -1)],
                ['name' => 'description',   'contents' => $request->description ?? ''],
                ['name' => 'category',      'contents' => $request->categoryID ?? ''],
                ['name' => 'section',       'contents' => $request->section_id ?? ''],
                ['name' => 'is_publish',    'contents' => ($request->publish == 'true' || $request->publish == true || $request->publish == 1) ? 'true' : 'false'],
            ];

            if ($request->filled('item_attribute')) {
                $itemAttribute = $request->input('item_attribute');
                if (is_array($itemAttribute)) {
                    $itemAttribute = json_encode($itemAttribute, JSON_UNESCAPED_UNICODE);
                }
                $fields[] = ['name' => 'item_attribute', 'contents' => (string) $itemAttribute];
            }

            if ($request->filled('attributes')) {
                $fields[] = ['name' => 'attributes', 'contents' => (string) $request->input('attributes')];
            }

            if ($request->filled('variants')) {
                $fields[] = ['name' => 'variants', 'contents' => (string) $request->input('variants')];
            }

            // Attach uploaded image file directly
            \Log::info('syncProduct image check', [
                'hasFile'    => $request->hasFile('image'),
                'allFiles'   => array_keys($request->allFiles()),
                'allInput'   => array_keys($request->all()),
                'contentType'=> $request->header('Content-Type'),
            ]);
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file     = $request->file('image');
                $tmpPath  = $file->getRealPath();
                $filename = $file->getClientOriginalName() ?: ('product_' . uniqid() . '.' . $file->getClientOriginalExtension());
                $mime     = $file->getMimeType() ?: 'image/jpeg';

                $fields[] = [
                    'name'     => 'image',
                    'contents' => fopen($tmpPath, 'r'),
                    'filename' => $filename,
                    'headers'  => ['Content-Type' => $mime],
                ];
            }

            $client = new \GuzzleHttp\Client(['verify' => false]);

            // If backend_id provided use PATCH (update), otherwise POST (create)
            $backendId = $request->backend_id ?? null;

            // If no backend_id, try to find existing product by firestore_id
            if (empty($backendId) && !empty($request->id)) {
                try {
                    $lookup = $client->get($this->apiUrl . '/products/', [
                        'query' => ['firestore_id' => $request->id, 'vendor' => $vendorFirestoreId],
                    ]);
                    $lookupData = json_decode($lookup->getBody()->getContents(), true);
                    $existing = $lookupData['data']['results'] ?? $lookupData['results'] ?? [];
                    if (!empty($existing)) {
                        $backendId = $existing[0]['id'] ?? null;
                    }
                } catch (\Exception $e) {
                    \Log::warning('syncProduct lookup failed', ['error' => $e->getMessage()]);
                }
            }

            if (!empty($backendId)) {
                $guzzleResponse = $client->patch($this->apiUrl . '/products/' . $backendId . '/', [
                    'multipart' => $fields,
                ]);
            } else {
                $guzzleResponse = $client->post($this->apiUrl . '/products/', [
                    'multipart' => $fields,
                ]);
            }

            $rawBody = $guzzleResponse->getBody()->getContents();
            \Log::info('syncProduct backend response', ['status' => $guzzleResponse->getStatusCode(), 'body' => $rawBody]);
            $body = json_decode($rawBody, true);

            return response()->json(['success' => true, 'data' => $body]);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $body = $e->getResponse()->getBody()->getContents();
            return response()->json(['success' => false, 'message' => 'API Error: ' . $body], $e->getResponse()->getStatusCode());
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch products for current vendor from external API (with pagination support)
     */
    public function getProducts(Request $request)
    {
        session_write_close();
        set_time_limit(300); // allow up to 5 min for large catalogs
        try {
            $user = Auth::user();
            $vendorUser = VendorUsers::where('user_id', $user->id)->first();

            if (!$vendorUser) {
                return response()->json(['success' => false, 'message' => 'Vendor not found'], 404);
            }

            $firestoreVendorId = $request->vendor_id ?: ($vendorUser->firestore_vendor_id ?? '');

            if (empty($firestoreVendorId)) {
                return response()->json([
                    'results' => [],
                    'count'   => 0,
                    'total'   => 0,
                    'error'   => 'vendor_not_synced',
                ]);
            }

            // cursor-based single page fetch
            $cursor = $request->cursor ?? null;

            if ($cursor) {
                $url = $this->apiUrl . '/products/';
                if (str_starts_with($cursor, 'offset:')) {
                    $params = [
                        'vendor' => $firestoreVendorId,
                        'limit'  => 20,
                        'offset' => (int) substr($cursor, strlen('offset:')),
                    ];
                } elseif (str_starts_with($cursor, 'page:')) {
                    $params = [
                        'vendor' => $firestoreVendorId,
                        'limit'  => 20,
                        'page'   => (int) substr($cursor, strlen('page:')),
                    ];
                } else {
                    $params = ['vendor' => $firestoreVendorId, 'cursor' => $cursor];
                }
            } else {
                $url    = $this->apiUrl . '/products/';
                $params = ['vendor' => $firestoreVendorId, 'limit' => 20];
            }
            if ($request->filled('search')) {
                $search = trim((string) $request->search);
                $params['search'] = $search;
                $params['name'] = $search;
                $params['q'] = $search;
            }

            \Log::info('getProducts fetch', ['vendor' => $firestoreVendorId, 'cursor' => $cursor]);

            $resp = Http::withoutVerifying()->timeout(30)->get($url, $params);

            if (!$resp->successful()) {
                return response()->json(['success' => false, 'message' => 'API Error'], 502);
            }

            $json    = $resp->json();
            $rawList = $json['data']['results'] ?? $json['results'] ?? [];
            $rawNext = $json['data']['next'] ?? $json['next'] ?? null;

            // Extract cursor from next URL
            $nextCursor = null;
            if ($rawNext) {
                $query = parse_url($rawNext, PHP_URL_QUERY);
                parse_str($query ?? '', $qp);
                $nextCursor = $qp['cursor']
                    ?? (isset($qp['offset']) ? 'offset:' . $qp['offset'] : null)
                    ?? (isset($qp['page']) ? 'page:' . $qp['page'] : null);
            }

            \Log::info('getProducts done', ['count' => count($rawList), 'next_cursor' => $nextCursor]);

            $normalized = array_map(function ($item) {
                return [
                    'id'          => ($item['firestore_id'] ?: null) ?? ($item['id'] ?? ''),
                    'backend_id'  => $item['id'] ?? null,
                    'name'        => $item['name'] ?? '',
                    'price'       => $item['price'] ?? 0,
                    'disPrice'    => $item['discount_price'] ?? '0',
                    'photo'       => !empty($item['image']) ? str_replace('http://', 'https://', $item['image']) : '',
                    'photos'      => $item['images'] ?? [],
                    'vendorID'    => $item['vendor'] ?? '',
                    'categoryID'  => $item['category'] ?? '',
                    'section_id'  => $item['section'] ?? '',
                    'description' => $item['description'] ?? '',
                    'publish'     => ($item['is_publish'] ?? false) ? 'Yes' : 'No',
                    'quantity'    => $item['quantity'] ?? 0,
                    'createdAt'   => $item['created_at'] ?? null,
                ];
            }, $rawList);

            return response()->json([
                'results'     => $normalized,
                'count'       => count($normalized),
                'next_cursor' => $nextCursor,
                'has_next'    => !empty($nextCursor),
            ]);

        } catch (Exception $e) {
            \Log::error('getProducts error', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch a single product from external API.
     */
    public function getProduct(Request $request, string $id)
    {
        try {
            $resp = Http::withoutVerifying()->timeout(30)->get($this->apiUrl . '/products/' . $id . '/');

            if (!$resp->successful()) {
                return response()->json(['success' => false, 'message' => 'API Error'], 502);
            }

            $json = $resp->json();
            $item = $json['data'] ?? $json;

            if (!is_array($item) || empty($item)) {
                return response()->json(['success' => false, 'message' => 'Product not found'], 404);
            }

            $itemAttribute = $item['item_attribute'] ?? null;
            if (is_string($itemAttribute) && $itemAttribute !== '') {
                $decoded = json_decode($itemAttribute, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $itemAttribute = $decoded;
                }
            }

            $normalized = [
                'id' => $item['firestore_id'] ?? '',
                'backend_id' => $item['id'] ?? $id,
                'name' => $item['name'] ?? '',
                'price' => $item['price'] ?? 0,
                'disPrice' => $item['discount_price'] ?? '0',
                'photo' => !empty($item['image']) ? str_replace('http://', 'https://', $item['image']) : '',
                'photos' => $item['images'] ?? [],
                'vendorID' => $item['vendor'] ?? '',
                'categoryID' => $item['category'] ?? '',
                'brandID' => $item['brand'] ?? '',
                'section_id' => $item['section'] ?? '',
                'description' => $item['description'] ?? '',
                'publish' => (bool) ($item['is_publish'] ?? false),
                'quantity' => $item['quantity'] ?? 0,
                'item_attribute' => is_array($itemAttribute) ? $itemAttribute : null,
            ];

            return response()->json(['success' => true, 'data' => $normalized]);
        } catch (Exception $e) {
            \Log::error('getProduct error', ['message' => $e->getMessage(), 'product_id' => $id]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete product from external API
     */
    public function deleteProduct(Request $request)
    {
        try {
            $productId = $request->product_id;
            if (empty($productId)) {
                return response()->json(['success' => false, 'message' => 'product_id required'], 422);
            }

            $resp = Http::withoutVerifying()->timeout(15)->delete($this->apiUrl . '/products/' . $productId . '/');

            if ($resp->status() === 404) {
                return response()->json(['success' => true, 'message' => 'Already deleted']);
            }

            if (!$resp->successful()) {
                return response()->json(['success' => false, 'message' => 'API error: ' . $resp->status()], 500);
            }

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            \Log::error('deleteProduct error', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Sync category to external API
     */
    public function syncCategory(Request $request)
    {
        try {
            $data = $request->all();
            $response = Http::post($this->apiUrl . '/categories', $data);

            if ($response->successful()) {
                return response()->json(['success' => true, 'data' => $response->json()]);
            }

            return response()->json(['success' => false, 'message' => 'API Error'], $response->status());
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch categories from external API
     */
    public function getCategories(Request $request)
    {
        session_write_close();
        try {
            $response = Http::get($this->apiUrl . '/categories');

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json(['success' => false, 'message' => 'API Error'], $response->status());
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
