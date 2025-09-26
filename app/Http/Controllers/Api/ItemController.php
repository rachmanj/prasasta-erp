<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\UserItemPreferencesService;

class ItemController extends Controller
{
    public function __construct(
        private UserItemPreferencesService $preferencesService
    ) {}

    /**
     * Search items with pagination and filtering
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $categoryId = $request->get('category_id');
        $type = $request->get('type', 'item'); // item or service
        $perPage = $request->get('per_page', 20);
        $page = $request->get('page', 1);

        $itemsQuery = DB::table('items')
            ->where('is_active', 1)
            ->where('type', $type);

        // Apply search filter
        if (!empty($query)) {
            $itemsQuery->where(function ($q) use ($query) {
                $q->where('code', 'like', "%{$query}%")
                    ->orWhere('name', 'like', "%{$query}%")
                    ->orWhere('barcode', 'like', "%{$query}%");
            });
        }

        // Apply category filter
        if ($categoryId) {
            $itemsQuery->where('category_id', $categoryId);
        }

        // Get total count for pagination
        $total = $itemsQuery->count();

        // Get paginated results
        $items = $itemsQuery
            ->select(['id', 'code', 'name', 'description', 'barcode', 'unit_of_measure', 'current_stock_quantity', 'last_cost_price'])
            ->orderBy('code')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            'data' => $items,
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => (int) $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
                'has_more' => $page < ceil($total / $perPage)
            ]
        ]);
    }

    /**
     * Get item details by ID
     */
    public function show($id)
    {
        $item = DB::table('items')
            ->where('id', $id)
            ->where('is_active', 1)
            ->select(['id', 'code', 'name', 'description', 'barcode', 'unit_of_measure', 'current_stock_quantity', 'last_cost_price', 'average_cost_price'])
            ->first();

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        return response()->json($item);
    }

    /**
     * Get recent items for quick access
     */
    public function recent(Request $request)
    {
        $limit = $request->get('limit', 10);
        $userId = auth()->id();
        
        if (!$userId) {
            return response()->json([]);
        }
        
        $items = $this->preferencesService->getRecentItems($userId, $limit);
        
        return response()->json($items);
    }

    /**
     * Track item selection for user preferences
     */
    public function trackSelection(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer|exists:items,id'
        ]);
        
        $this->preferencesService->trackItemUsage($request->item_id);
        
        return response()->json(['success' => true]);
    }

    /**
     * Get user's favorite items
     */
    public function favorites(Request $request)
    {
        $limit = $request->get('limit', 10);
        $userId = auth()->id();
        
        if (!$userId) {
            return response()->json([]);
        }
        
        $items = $this->preferencesService->getFavoriteItems($userId, $limit);
        
        return response()->json($items);
    }

    /**
     * Get categories for filtering
     */
    public function categories()
    {
        $categories = DB::table('inventory_categories')
            ->where('is_active', 1)
            ->select(['id', 'name', 'code'])
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }
}
