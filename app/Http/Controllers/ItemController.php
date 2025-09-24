<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\InventoryCategory;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index()
    {
        $this->authorize('inventory.items.view');
        return view('inventory.items.index');
    }

    public function data()
    {
        $this->authorize('inventory.items.view');
        $items = Item::select(['id', 'code', 'name', 'description', 'barcode', 'category_id', 'unit_of_measure', 'current_stock_quantity', 'current_stock_value', 'is_active', 'created_at'])
            ->with('category:id,name');

        return datatables()->of($items)
            ->addColumn('category_name', function ($item) {
                return $item->category ? $item->category->name : '-';
            })
            ->addColumn('stock_status', function ($item) {
                if ($item->isLowStock()) {
                    return '<span class="badge badge-warning">Low Stock</span>';
                }
                return '<span class="badge badge-success">In Stock</span>';
            })
            ->addColumn('stock_value', function ($item) {
                return 'Rp ' . number_format($item->current_stock_value, 0, ',', '.');
            })
            ->addColumn('status', function ($item) {
                return $item->is_active
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-secondary">Inactive</span>';
            })
            ->addColumn('actions', function ($item) {
                return '
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-info" onclick="viewItem(' . $item->id . ')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" onclick="editItem(' . $item->id . ')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteItem(' . $item->id . ')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['stock_status', 'stock_value', 'status', 'actions'])
            ->toJson();
    }

    public function create()
    {
        $this->authorize('inventory.items.create');
        $categories = InventoryCategory::active()->orderBy('name')->get();
        $inventoryService = new InventoryService();
        $inventoryAccounts = $inventoryService->getInventoryAccountsForDropdown();
        $cogsAccounts = $inventoryService->getCostOfGoodsSoldAccountsForDropdown();
        $projects = DB::table('projects')->orderBy('code')->get(['id', 'code', 'name']);
        $funds = DB::table('funds')->orderBy('code')->get(['id', 'code', 'name']);
        $departments = DB::table('departments')->orderBy('code')->get(['id', 'code', 'name']);

        return view('inventory.items.create', compact('categories', 'inventoryAccounts', 'cogsAccounts', 'projects', 'funds', 'departments'));
    }

    public function store(Request $request)
    {
        $this->authorize('inventory.items.create');
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:items,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|max:255|unique:items,barcode',
            'category_id' => 'required|exists:inventory_categories,id',
            'inventory_account_id' => 'nullable|exists:accounts,id',
            'cost_of_goods_sold_account_id' => 'nullable|exists:accounts,id',
            'type' => 'required|in:item,service',
            'unit_of_measure' => 'required|string|max:50',
            'min_stock_level' => 'required|numeric|min:0',
            'max_stock_level' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);
        $data['cost_method'] = 'fifo';
        $data['is_active'] = $data['is_active'] ?? true;

        $item = Item::create($data);

        // Auto-assign accounts if not provided
        $inventoryService = new InventoryService();
        if (!$item->inventory_account_id) {
            $inventoryAccountId = $inventoryService->getInventoryAccountForItem($item);
            $item->update(['inventory_account_id' => $inventoryAccountId]);
        }

        if (!$item->cost_of_goods_sold_account_id) {
            $cogsAccountId = $inventoryService->getCostOfGoodsSoldAccount();
            $item->update(['cost_of_goods_sold_account_id' => $cogsAccountId]);
        }

        return redirect()->route('items.index')->with('success', 'Item created successfully');
    }

    public function show($id)
    {
        $this->authorize('inventory.items.view');
        $item = Item::with(['category', 'stockMovements' => function ($query) {
            $query->orderBy('movement_date', 'desc')->limit(10);
        }])->findOrFail($id);

        return view('inventory.items.show', compact('item'));
    }

    public function edit($id)
    {
        $this->authorize('inventory.items.update');
        $item = Item::findOrFail($id);
        $categories = InventoryCategory::active()->orderBy('name')->get();

        return view('inventory.items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('inventory.items.update');
        $item = Item::findOrFail($id);

        $data = $request->validate([
            'code' => 'required|string|max:50|unique:items,code,' . $id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|max:255|unique:items,barcode,' . $id,
            'category_id' => 'required|exists:inventory_categories,id',
            'type' => 'required|in:item,service',
            'unit_of_measure' => 'required|string|max:50',
            'min_stock_level' => 'required|numeric|min:0',
            'max_stock_level' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $data['is_active'] ?? true;

        $item->update($data);

        return redirect()->route('items.index')->with('success', 'Item updated successfully');
    }

    public function destroy($id)
    {
        $this->authorize('inventory.items.delete');
        $item = Item::findOrFail($id);

        // Check if item has stock movements
        if ($item->stockMovements()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete item with existing stock movements');
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item deleted successfully');
    }

    public function search(Request $request)
    {
        $this->authorize('inventory.items.view');
        $query = $request->get('q');

        $items = Item::active()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('code', 'like', "%{$query}%")
                    ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->with('category:id,name')
            ->limit(20)
            ->get();

        return response()->json($items);
    }
}
