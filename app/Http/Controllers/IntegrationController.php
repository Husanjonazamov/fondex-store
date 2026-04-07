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

            $data = $request->all();
            $data['vendor_id'] = $vendorUser->uuid; // Use Firebase ID (uuid)

            $response = Http::post($this->apiUrl . '/products', $data);

            if ($response->successful()) {
                return response()->json(['success' => true, 'data' => $response->json()]);
            }

            return response()->json(['success' => false, 'message' => 'API Error: ' . $response->body()], $response->status());

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch products from external API
     */
    public function getProducts(Request $request)
    {
        try {
            $user = Auth::user();
            $vendorUser = VendorUsers::where('user_id', $user->id)->first();

            if (!$vendorUser) {
                return response()->json(['success' => false, 'message' => 'Vendor not found'], 404);
            }

            $response = Http::get($this->apiUrl . '/products', [
                'vendor_id' => $vendorUser->uuid
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json(['success' => false, 'message' => 'API Error'], $response->status());

        } catch (Exception $e) {
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
