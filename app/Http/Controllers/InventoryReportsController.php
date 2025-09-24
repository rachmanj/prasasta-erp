<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\InventoryCategory;
use App\Models\StockMovement;
use App\Models\StockAdjustment;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryReportsController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Inventory Dashboard - Overview and key metrics
     */
    public function dashboard()
    {
        $this->authorize('inventory.reports.view');
        $metrics = $this->getInventoryMetrics();
        $lowStockItems = $this->inventoryService->getLowStockItems();
        $recentMovements = $this->getRecentStockMovements(10);
        $topMovingItems = $this->getTopMovingItems(5);

        return view('inventory.reports.dashboard', compact('metrics', 'lowStockItems', 'recentMovements', 'topMovingItems'));
    }

    /**
     * Stock Status Report - Current stock levels by item
     */
    public function stockStatus()
    {
        $this->authorize('inventory.reports.view');
        return view('inventory.reports.stock-status');
    }

    /**
     * Stock Status Report Data (DataTable)
     */
    public function stockStatusData(Request $request)
    {
        $this->authorize('inventory.reports.view');
        $query = Item::with(['category', 'stockLayers'])
            ->select(['id', 'code', 'name', 'category_id', 'unit_of_measure', 'current_stock_quantity', 'current_stock_value', 'min_stock_level', 'max_stock_level', 'last_cost_price', 'average_cost_price']);

        // Apply filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->where('current_stock_quantity', 0);
                    break;
                case 'in_stock':
                    $query->where('current_stock_quantity', '>', 0);
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('name')->get();

        return datatables()->of($items)
            ->addColumn('category_name', function ($item) {
                return $item->category->name;
            })
            ->addColumn('current_stock_quantity', function ($item) {
                return number_format($item->current_stock_quantity, 4) . ' ' . $item->unit_of_measure;
            })
            ->addColumn('current_stock_value', function ($item) {
                return 'Rp ' . number_format($item->current_stock_value, 0, ',', '.');
            })
            ->addColumn('last_cost_price', function ($item) {
                return $item->last_cost_price ? 'Rp ' . number_format($item->last_cost_price, 0, ',', '.') : '-';
            })
            ->addColumn('average_cost_price', function ($item) {
                return $item->average_cost_price ? 'Rp ' . number_format($item->average_cost_price, 0, ',', '.') : '-';
            })
            ->addColumn('stock_status', function ($item) {
                if ($item->current_stock_quantity == 0) {
                    return '<span class="badge badge-danger">Out of Stock</span>';
                } elseif ($item->isLowStock()) {
                    return '<span class="badge badge-warning">Low Stock</span>';
                } else {
                    return '<span class="badge badge-success">In Stock</span>';
                }
            })
            ->addColumn('reorder_level', function ($item) {
                return $item->min_stock_level > 0 ? number_format($item->min_stock_level, 4) : '-';
            })
            ->addColumn('max_stock_level', function ($item) {
                return $item->max_stock_level ? number_format($item->max_stock_level, 4) : '-';
            })
            ->rawColumns(['stock_status'])
            ->toJson();
    }

    /**
     * Stock Movement Report - Detailed movement history
     */
    public function stockMovement()
    {
        $this->authorize('inventory.reports.view');
        return view('inventory.reports.stock-movement');
    }

    /**
     * Stock Movement Report Data (DataTable)
     */
    public function stockMovementData(Request $request)
    {
        $this->authorize('inventory.reports.view');
        $query = StockMovement::with(['item.category', 'creator'])
            ->select(['id', 'item_id', 'movement_date', 'movement_type', 'quantity', 'unit_cost', 'total_cost', 'reference_type', 'reference_number', 'notes', 'created_by', 'created_at']);

        // Apply filters
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        if ($request->filled('start_date')) {
            $query->where('movement_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('movement_date', '<=', $request->end_date);
        }

        $movements = $query->orderBy('movement_date', 'desc')->orderBy('created_at', 'desc')->get();

        return datatables()->of($movements)
            ->addColumn('item_code', function ($movement) {
                return $movement->item->code;
            })
            ->addColumn('item_name', function ($movement) {
                return $movement->item->name;
            })
            ->addColumn('category_name', function ($movement) {
                return $movement->item->category->name;
            })
            ->addColumn('movement_date', function ($movement) {
                return Carbon::parse($movement->movement_date)->format('d/m/Y');
            })
            ->addColumn('movement_type', function ($movement) {
                $badgeClass = match ($movement->movement_type) {
                    'in' => 'badge-success',
                    'out' => 'badge-danger',
                    'adjustment' => 'badge-warning',
                    default => 'badge-secondary'
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($movement->movement_type) . '</span>';
            })
            ->addColumn('quantity', function ($movement) {
                $class = $movement->quantity >= 0 ? 'text-success' : 'text-danger';
                $sign = $movement->quantity >= 0 ? '+' : '';
                return '<span class="' . $class . '">' . $sign . number_format($movement->quantity, 4) . '</span>';
            })
            ->addColumn('unit_cost', function ($movement) {
                return $movement->unit_cost ? 'Rp ' . number_format($movement->unit_cost, 0, ',', '.') : '-';
            })
            ->addColumn('total_cost', function ($movement) {
                return $movement->total_cost ? 'Rp ' . number_format($movement->total_cost, 0, ',', '.') : '-';
            })
            ->addColumn('reference', function ($movement) {
                return $movement->reference_type && $movement->reference_number
                    ? ucfirst(str_replace('_', ' ', $movement->reference_type)) . ' #' . $movement->reference_number
                    : '-';
            })
            ->addColumn('creator_name', function ($movement) {
                return $movement->creator ? $movement->creator->name : 'Unknown';
            })
            ->rawColumns(['movement_type', 'quantity'])
            ->toJson();
    }

    /**
     * Inventory Valuation Report - Current inventory value by category
     */
    public function inventoryValuation()
    {
        $this->authorize('inventory.reports.view');
        $categories = InventoryCategory::with(['items' => function ($query) {
            $query->where('current_stock_quantity', '>', 0);
        }])->get();

        $totalValue = 0;
        foreach ($categories as $category) {
            $categoryValue = $category->items->sum('current_stock_value');
            $category->total_value = $categoryValue;
            $totalValue += $categoryValue;
        }

        return view('inventory.reports.inventory-valuation', compact('categories', 'totalValue'));
    }

    /**
     * Low Stock Report - Items below minimum stock level
     */
    public function lowStock()
    {
        $this->authorize('inventory.reports.view');
        $lowStockItems = $this->inventoryService->getLowStockItems();

        return view('inventory.reports.low-stock', compact('lowStockItems'));
    }

    /**
     * Stock Adjustment Report - All stock adjustments
     */
    public function stockAdjustments()
    {
        $this->authorize('inventory.reports.view');
        return view('inventory.reports.stock-adjustments');
    }

    /**
     * Stock Adjustment Report Data (DataTable)
     */
    public function stockAdjustmentsData(Request $request)
    {
        $this->authorize('inventory.reports.view');
        $query = StockAdjustment::with(['creator', 'approver'])
            ->withCount('lines');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        $adjustments = $query->orderBy('date', 'desc')->get();

        return datatables()->of($adjustments)
            ->addColumn('status', function ($adjustment) {
                $badgeClass = match ($adjustment->status) {
                    'draft' => 'badge-warning',
                    'approved' => 'badge-success',
                    'cancelled' => 'badge-danger',
                    default => 'badge-secondary'
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($adjustment->status) . '</span>';
            })
            ->addColumn('total_adjustment_value', function ($adjustment) {
                return 'Rp ' . number_format($adjustment->total_adjustment_value, 0, ',', '.');
            })
            ->addColumn('creator_name', function ($adjustment) {
                return $adjustment->creator ? $adjustment->creator->name : 'Unknown';
            })
            ->addColumn('approver_name', function ($adjustment) {
                return $adjustment->approver ? $adjustment->approver->name : '-';
            })
            ->rawColumns(['status'])
            ->toJson();
    }

    /**
     * Get inventory metrics for dashboard
     */
    private function getInventoryMetrics(): array
    {
        $totalItems = Item::active()->count();
        $totalValue = Item::sum('current_stock_value');
        $lowStockCount = Item::active()->lowStock()->count();
        $outOfStockCount = Item::active()->where('current_stock_quantity', 0)->count();

        // Recent movements (last 30 days)
        $recentMovementsCount = StockMovement::where('movement_date', '>=', Carbon::now()->subDays(30))->count();

        // Recent adjustments (last 30 days)
        $recentAdjustmentsCount = StockAdjustment::where('date', '>=', Carbon::now()->subDays(30))->count();

        return [
            'total_items' => $totalItems,
            'total_value' => $totalValue,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'recent_movements_count' => $recentMovementsCount,
            'recent_adjustments_count' => $recentAdjustmentsCount,
        ];
    }

    /**
     * Get recent stock movements
     */
    private function getRecentStockMovements(int $limit = 10)
    {
        return StockMovement::with(['item.category', 'creator'])
            ->orderBy('movement_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top moving items
     */
    private function getTopMovingItems(int $limit = 5)
    {
        return StockMovement::select('item_id', DB::raw('SUM(ABS(quantity)) as total_movement'))
            ->with('item.category')
            ->where('movement_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('item_id')
            ->orderBy('total_movement', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Export stock status report to Excel
     */
    public function exportStockStatus(Request $request)
    {
        $this->authorize('inventory.reports.export');
        // Implementation for Excel export
        // This would use Laravel Excel package
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    /**
     * Export stock movement report to Excel
     */
    public function exportStockMovement(Request $request)
    {
        $this->authorize('inventory.reports.export');
        // Implementation for Excel export
        // This would use Laravel Excel package
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}
