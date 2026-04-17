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

            $page   = max(1, (int)($request->page ?? 1));
            $limit  = max(1, min(100, (int)($request->limit ?? 20)));
            $offset = ($page - 1) * $limit;

            $params = [
                'vendor' => $firestoreVendorId,
                'limit'  => $limit,
                'offset' => $offset,
            ];
            if ($request->search) {
                $params['search'] = $request->search;
            }

            \Log::info('getProducts', ['vendor' => $firestoreVendorId, 'page' => $page, 'limit' => $limit]);

            $response = Http::withoutVerifying()->timeout(30)->get($this->apiUrl . '/products/', $params);

            // If not found, try to map firestore vendor ID to backend vendor ID
            if (!$response->successful() || empty($response->json()['results'] ?? $response->json()['data']['results'] ?? [])) {
                $vendorSearch = Http::withoutVerifying()->timeout(10)->get($this->apiUrl . '/vendors/', [
                    'firestore_id' => $firestoreVendorId,
                ]);
                if ($vendorSearch->successful()) {
                    $vResults = $vendorSearch->json()['results'] ?? $vendorSearch->json()['data']['results'] ?? [];
                    if (!empty($vResults)) {
                        $params['vendor'] = $vResults[0]['id'];
                        $response = Http::withoutVerifying()->timeout(30)->get($this->apiUrl . '/products/', $params);
                    }
                }
            }

            if (!$response->successful()) {
                return response()->json(['success' => false, 'message' => 'API Error', 'status' => $response->status()], 502);
            }

            $json    = $response->json();
            $rawList = $json['data']['results'] ?? $json['results'] ?? [];
            $hasNext = !empty($json['data']['next'] ?? $json['next'] ?? null);
            $hasPrev = !empty($json['data']['previous'] ?? $json['previous'] ?? null);

            $normalized = array_map(function ($item) {
                return [
                    'id'          => $item['firestore_id'] ?? ($item['id'] ?? ''),
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

            \Log::info('getProducts done', ['page' => $page, 'count' => count($normalized), 'has_next' => $hasNext]);

            return response()->json([
                'results'  => $normalized,
                'count'    => count($normalized),
                'page'     => $page,
                'limit'    => $limit,
                'has_next' => $hasNext,
                'has_prev' => $hasPrev,
            ]);

        } catch (Exception $e) {
            \Log::error('getProducts error', ['message' => $e->getMessage()]);
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
