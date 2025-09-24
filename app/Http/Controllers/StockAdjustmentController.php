<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentLine;
use App\Models\Item;
use App\Models\InventoryCategory;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockAdjustmentController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        $this->authorize('inventory.stock_adjustments.view');
        return view('inventory.stock-adjustments.index');
    }

    public function data()
    {
        $this->authorize('inventory.stock_adjustments.view');
        $adjustments = StockAdjustment::select(['id', 'adjustment_no', 'date', 'reason', 'total_adjustment_value', 'status', 'created_at'])
            ->with(['creator:id,name', 'approver:id,name'])
            ->withCount('lines');

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
                return $adjustment->creator ? $adjustment->creator->name : '-';
            })
            ->addColumn('approver_name', function ($adjustment) {
                return $adjustment->approver ? $adjustment->approver->name : '-';
            })
            ->addColumn('actions', function ($adjustment) {
                $actions = '';

                // View button
                $actions .= '<button type="button" class="btn btn-sm btn-info mr-1" onclick="viewAdjustment(' . $adjustment->id . ')">
                    <i class="fas fa-eye"></i>
                </button>';

                // Approve button (only for draft status)
                if ($adjustment->status === 'draft' && auth()->user()->can('inventory.adjustments.approve')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-success mr-1" onclick="approveAdjustment(' . $adjustment->id . ')">
                        <i class="fas fa-check"></i>
                    </button>';
                }

                return $actions;
            })
            ->rawColumns(['status', 'actions'])
            ->toJson();
    }

    public function create()
    {
        $this->authorize('inventory.stock_adjustments.create');
        $items = Item::active()->with('category')->orderBy('name')->get();
        $categories = InventoryCategory::active()->orderBy('name')->get();
        $projects = DB::table('projects')->orderBy('code')->get(['id', 'code', 'name']);
        $funds = DB::table('funds')->orderBy('code')->get(['id', 'code', 'name']);
        $departments = DB::table('departments')->orderBy('code')->get(['id', 'code', 'name']);

        return view('inventory.stock-adjustments.create', compact('items', 'categories', 'projects', 'funds', 'departments'));
    }

    public function store(Request $request)
    {
        $this->authorize('inventory.stock_adjustments.create');
        $request->validate([
            'date' => 'required|date',
            'reason' => 'required|string|max:255',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.adjusted_quantity' => 'required|numeric|min:0',
            'lines.*.unit_cost' => 'required|numeric|min:0',
            'lines.*.notes' => 'nullable|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'fund_id' => 'nullable|exists:funds,id',
            'dept_id' => 'nullable|exists:departments,id',
        ]);

        DB::transaction(function () use ($request) {
            // Generate adjustment number
            $adjustmentNo = 'SA-' . date('Y') . '-' . str_pad(StockAdjustment::count() + 1, 6, '0', STR_PAD_LEFT);

            // Create stock adjustment
            $adjustment = StockAdjustment::create([
                'adjustment_no' => $adjustmentNo,
                'date' => $request->date,
                'reason' => $request->reason,
                'total_adjustment_value' => 0,
                'status' => 'draft',
                'created_by' => auth()->id(),
                'project_id' => $request->project_id,
                'fund_id' => $request->fund_id,
                'dept_id' => $request->dept_id,
            ]);

            $totalValue = 0;

            // Create adjustment lines
            foreach ($request->lines as $lineData) {
                $item = Item::findOrFail($lineData['item_id']);
                $currentQuantity = $item->current_stock_quantity;
                $adjustedQuantity = $lineData['adjusted_quantity'];
                $varianceQuantity = $adjustedQuantity - $currentQuantity;
                $unitCost = $lineData['unit_cost'];
                $varianceValue = $varianceQuantity * $unitCost;

                StockAdjustmentLine::create([
                    'adjustment_id' => $adjustment->id,
                    'item_id' => $item->id,
                    'current_quantity' => $currentQuantity,
                    'adjusted_quantity' => $adjustedQuantity,
                    'variance_quantity' => $varianceQuantity,
                    'unit_cost' => $unitCost,
                    'variance_value' => $varianceValue,
                    'notes' => $lineData['notes'] ?? null,
                ]);

                $totalValue += abs($varianceValue);
            }

            // Update total adjustment value
            $adjustment->update(['total_adjustment_value' => $totalValue]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Stock adjustment created successfully'
        ]);
    }

    public function show($id)
    {
        $this->authorize('inventory.stock_adjustments.view');
        $adjustment = StockAdjustment::with(['lines.item.category', 'creator', 'approver'])
            ->findOrFail($id);

        return view('inventory.stock-adjustments.show', compact('adjustment'));
    }

    public function approve($id)
    {
        $this->authorize('inventory.stock_adjustments.approve');
        $adjustment = StockAdjustment::with('lines.item')->findOrFail($id);

        if ($adjustment->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft adjustments can be approved'
            ], 422);
        }

        DB::transaction(function () use ($adjustment) {
            foreach ($adjustment->lines as $line) {
                $item = $line->item;
                $variance = $line->variance_quantity;

                if ($variance != 0) {
                    $movementType = $variance > 0 ? 'in' : 'out';
                    $quantity = abs($variance);

                    $this->inventoryService->adjustStock(
                        $item,
                        $line->adjusted_quantity,
                        $line->unit_cost,
                        'stock_adjustment',
                        $adjustment->id,
                        $adjustment->adjustment_no,
                        $line->notes,
                        [
                            'project_id' => $adjustment->project_id ?? null,
                            'fund_id' => $adjustment->fund_id ?? null,
                            'dept_id' => $adjustment->dept_id ?? null,
                        ]
                    );
                }
            }

            // Update adjustment status
            $adjustment->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Stock adjustment approved successfully'
        ]);
    }

    public function searchItems(Request $request)
    {
        $this->authorize('inventory.stock_adjustments.create');
        $query = $request->get('q');
        $categoryId = $request->get('category_id');

        $items = Item::active()
            ->with('category')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($query) {
                    $query->where('code', 'like', "%{$query}%")
                        ->orWhere('name', 'like', "%{$query}%")
                        ->orWhere('barcode', 'like', "%{$query}%");
                });
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->limit(20)
            ->get();

        return response()->json($items->map(function ($item) {
            return [
                'id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'category' => $item->category->name,
                'current_stock' => $item->current_stock_quantity,
                'unit_of_measure' => $item->unit_of_measure,
                'last_cost_price' => $item->last_cost_price ?? 0,
            ];
        }));
    }
}
