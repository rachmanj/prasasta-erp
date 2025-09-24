<?php

namespace App\Http\Controllers;

use App\Models\InventoryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryCategoryController extends Controller
{
    public function index()
    {
        $this->authorize('inventory.categories.view');
        return view('inventory.categories.index');
    }

    public function data()
    {
        $this->authorize('inventory.categories.view');
        $categories = InventoryCategory::select(['id', 'code', 'name', 'description', 'is_active', 'created_at'])
            ->withCount('items');

        return datatables()->of($categories)
            ->addColumn('items_count', function ($category) {
                return $category->items_count;
            })
            ->addColumn('status', function ($category) {
                return $category->is_active
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-secondary">Inactive</span>';
            })
            ->addColumn('actions', function ($category) {
                return '
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-primary" onclick="editCategory(' . $category->id . ')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteCategory(' . $category->id . ')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['status', 'actions'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $this->authorize('inventory.categories.create');
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:inventory_categories,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data['type'] = 'item';
        $data['is_active'] = $data['is_active'] ?? true;

        InventoryCategory::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully'
        ]);
    }

    public function edit($id)
    {
        $this->authorize('inventory.categories.update');
        $category = InventoryCategory::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('inventory.categories.update');
        $category = InventoryCategory::findOrFail($id);

        $data = $request->validate([
            'code' => 'required|string|max:50|unique:inventory_categories,code,' . $id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $data['is_active'] ?? true;

        $category->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('inventory.categories.delete');
        $category = InventoryCategory::findOrFail($id);

        // Check if category has items
        if ($category->items()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with existing items'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }
}
