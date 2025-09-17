<?php

namespace App\Http\Controllers;

use App\Models\CourseCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CourseCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:course_categories.view'])->only(['index']);
        $this->middleware(['auth', 'permission:course_categories.manage'])->only(['create', 'store', 'edit', 'update']);
    }

    public function index()
    {
        return view('course-categories.index');
    }

    public function create()
    {
        $categories = CourseCategory::where('is_active', true)->get();
        return view('course-categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:course_categories,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:course_categories,id'],
            'is_active' => ['boolean'],
        ]);

        $category = CourseCategory::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'id' => $category->id]);
        }

        return redirect()->route('course-categories.index')->with('success', 'Course category created');
    }

    public function edit(CourseCategory $courseCategory)
    {
        $categories = CourseCategory::where('is_active', true)->where('id', '!=', $courseCategory->id)->get();
        return view('course-categories.edit', compact('courseCategory', 'categories'));
    }

    public function update(Request $request, CourseCategory $courseCategory)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:course_categories,code,' . $courseCategory->id],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:course_categories,id'],
            'is_active' => ['boolean'],
        ]);

        $courseCategory->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('course-categories.index')->with('success', 'Course category updated');
    }

    public function data(Request $request)
    {
        $q = CourseCategory::query()->with('parent')->select(['id', 'code', 'name', 'description', 'parent_id', 'is_active']);

        return DataTables::of($q)
            ->addColumn('parent_name', function ($row) {
                return $row->parent ? $row->parent->name : '-';
            })
            ->addColumn('status', function ($row) {
                return $row->is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>';
            })
            ->addColumn('actions', function ($row) {
                $editUrl = route('course-categories.update', $row->id);
                return '<button type="button" class="btn btn-xs btn-info btn-edit" data-id="' . $row->id . '" data-code="' . e($row->code) . '" data-name="' . e($row->name) . '" data-description="' . e($row->description) . '" data-parent-id="' . $row->parent_id . '" data-is-active="' . ($row->is_active ? '1' : '0') . '" data-url="' . $editUrl . '">Edit</button>';
            })
            ->rawColumns(['status', 'actions'])
            ->toJson();
    }
}
