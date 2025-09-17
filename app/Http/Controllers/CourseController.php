<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:courses.view'])->only(['index']);
        $this->middleware(['auth', 'permission:courses.create'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:courses.update'])->only(['edit', 'update']);
        $this->middleware(['auth', 'permission:courses.delete'])->only(['destroy']);
    }

    public function index()
    {
        return view('courses.index');
    }

    public function create()
    {
        $categories = CourseCategory::where('is_active', true)->get();
        return view('courses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:courses,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:course_categories,id'],
            'duration_hours' => ['required', 'integer', 'min:1'],
            'capacity' => ['required', 'integer', 'min:1'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive,discontinued'],
        ]);

        $course = Course::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'id' => $course->id]);
        }

        return redirect()->route('courses.index')->with('success', 'Course created successfully');
    }

    public function edit(Course $course)
    {
        $categories = CourseCategory::where('is_active', true)->get();
        return view('courses.edit', compact('course', 'categories'));
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:courses,code,' . $course->id],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:course_categories,id'],
            'duration_hours' => ['required', 'integer', 'min:1'],
            'capacity' => ['required', 'integer', 'min:1'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive,discontinued'],
        ]);

        $course->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('courses.index')->with('success', 'Course updated successfully');
    }

    public function destroy(Course $course)
    {
        // Check if course has batches or enrollments
        if ($course->batches()->count() > 0) {
            return response()->json(['error' => 'Cannot delete course with existing batches'], 400);
        }

        if ($course->enrollments()->count() > 0) {
            return response()->json(['error' => 'Cannot delete course with existing enrollments'], 400);
        }

        $course->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('courses.index')->with('success', 'Course deleted successfully');
    }

    public function data(Request $request)
    {
        $q = Course::query()->with('category')->select(['id', 'code', 'name', 'description', 'category_id', 'duration_hours', 'capacity', 'base_price', 'status']);

        return DataTables::of($q)
            ->addColumn('category_name', function ($row) {
                return $row->category ? $row->category->name : '-';
            })
            ->addColumn('duration_display', function ($row) {
                return $row->duration_hours . ' hours';
            })
            ->addColumn('price_display', function ($row) {
                return 'Rp ' . number_format($row->base_price, 0, ',', '.');
            })
            ->addColumn('status', function ($row) {
                $statusClass = match ($row->status) {
                    'active' => 'badge-success',
                    'inactive' => 'badge-warning',
                    'discontinued' => 'badge-danger',
                    default => 'badge-secondary'
                };
                return '<span class="badge ' . $statusClass . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('actions', function ($row) {
                $editUrl = route('courses.update', $row->id);
                $deleteUrl = route('courses.destroy', $row->id);

                $actions = '';

                if (auth()->user()->can('courses.update')) {
                    $actions .= '<button type="button" class="btn btn-xs btn-info btn-edit" data-id="' . $row->id . '" data-code="' . e($row->code) . '" data-name="' . e($row->name) . '" data-description="' . e($row->description) . '" data-category-id="' . $row->category_id . '" data-duration-hours="' . $row->duration_hours . '" data-capacity="' . $row->capacity . '" data-base-price="' . $row->base_price . '" data-status="' . $row->status . '" data-url="' . $editUrl . '">Edit</button>';
                }

                if (auth()->user()->can('courses.delete')) {
                    $actions .= ' <button type="button" class="btn btn-xs btn-danger btn-delete" data-id="' . $row->id . '" data-url="' . $deleteUrl . '">Delete</button>';
                }

                return $actions;
            })
            ->rawColumns(['status', 'actions'])
            ->toJson();
    }
}
