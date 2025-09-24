<?php

namespace App\Http\Controllers;

use App\Models\CourseBatch;
use App\Models\Course;
use App\Events\BatchStarted;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CourseBatchController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:course_batches.view'])->only(['index']);
        $this->middleware(['auth', 'permission:course_batches.create'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:course_batches.update'])->only(['edit', 'update']);
        $this->middleware(['auth', 'permission:course_batches.delete'])->only(['destroy']);
    }

    public function index()
    {
        return view('course-batches.index');
    }

    public function create()
    {
        $courses = Course::where('status', 'active')->get();
        return view('course-batches.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'batch_code' => ['required', 'string', 'max:50'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'schedule' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'trainer_id' => ['nullable', 'integer'],
            'capacity' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:planned,ongoing,completed,cancelled'],
        ]);

        // Check for unique batch code per course
        $existingBatch = CourseBatch::where('course_id', $data['course_id'])
            ->where('batch_code', $data['batch_code'])
            ->first();

        if ($existingBatch) {
            return response()->json(['error' => 'Batch code already exists for this course'], 400);
        }

        $batch = CourseBatch::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'id' => $batch->id]);
        }

        return redirect()->route('course-batches.index')->with('success', 'Course batch created successfully');
    }

    public function edit(CourseBatch $courseBatch)
    {
        $courses = Course::where('status', 'active')->get();
        return view('course-batches.edit', compact('courseBatch', 'courses'));
    }

    public function update(Request $request, CourseBatch $courseBatch)
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'batch_code' => ['required', 'string', 'max:50'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'schedule' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'trainer_id' => ['nullable', 'integer'],
            'capacity' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:planned,ongoing,completed,cancelled'],
        ]);

        // Check for unique batch code per course (excluding current batch)
        $existingBatch = CourseBatch::where('course_id', $data['course_id'])
            ->where('batch_code', $data['batch_code'])
            ->where('id', '!=', $courseBatch->id)
            ->first();

        if ($existingBatch) {
            return response()->json(['error' => 'Batch code already exists for this course'], 400);
        }

        $oldStatus = $courseBatch->status;
        $courseBatch->update($data);

        // Trigger batch started event if status changed to 'ongoing'
        if ($oldStatus !== 'ongoing' && $data['status'] === 'ongoing') {
            BatchStarted::dispatch($courseBatch);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('course-batches.index')->with('success', 'Course batch updated successfully');
    }

    public function destroy(CourseBatch $courseBatch)
    {
        // Check if batch has enrollments
        if ($courseBatch->enrollments()->count() > 0) {
            return response()->json(['error' => 'Cannot delete batch with existing enrollments'], 400);
        }

        $courseBatch->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('course-batches.index')->with('success', 'Course batch deleted successfully');
    }

    public function data(Request $request)
    {
        $q = CourseBatch::query()->with('course')->select(['id', 'course_id', 'batch_code', 'start_date', 'end_date', 'location', 'trainer_id', 'capacity', 'status']);

        return DataTables::of($q)
            ->addColumn('course_name', function ($row) {
                return $row->course ? $row->course->name : '-';
            })
            ->addColumn('course_code', function ($row) {
                return $row->course ? $row->course->code : '-';
            })
            ->addColumn('duration_days', function ($row) {
                return $row->start_date && $row->end_date ?
                    $row->start_date->diffInDays($row->end_date) + 1 . ' days' : '-';
            })
            ->addColumn('enrollment_count', function ($row) {
                return $row->enrollment_count;
            })
            ->addColumn('available_slots', function ($row) {
                return $row->available_slots;
            })
            ->addColumn('status', function ($row) {
                $statusClass = match ($row->status) {
                    'planned' => 'badge-info',
                    'ongoing' => 'badge-success',
                    'completed' => 'badge-secondary',
                    'cancelled' => 'badge-danger',
                    default => 'badge-secondary'
                };
                return '<span class="badge ' . $statusClass . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('actions', function ($row) {
                $editUrl = route('course-batches.update', $row->id);
                $deleteUrl = route('course-batches.destroy', $row->id);

                $actions = '';

                if (auth()->user()->can('course_batches.update')) {
                    $actions .= '<button type="button" class="btn btn-xs btn-info btn-edit" data-id="' . $row->id . '" data-course-id="' . $row->course_id . '" data-batch-code="' . e($row->batch_code) . '" data-start-date="' . $row->start_date->format('Y-m-d') . '" data-end-date="' . $row->end_date->format('Y-m-d') . '" data-location="' . e($row->location) . '" data-trainer-id="' . $row->trainer_id . '" data-capacity="' . $row->capacity . '" data-status="' . $row->status . '" data-url="' . $editUrl . '">Edit</button>';
                }

                if (auth()->user()->can('course_batches.delete')) {
                    $actions .= ' <button type="button" class="btn btn-xs btn-danger btn-delete" data-id="' . $row->id . '" data-url="' . $deleteUrl . '">Delete</button>';
                }

                return $actions;
            })
            ->rawColumns(['status', 'actions'])
            ->toJson();
    }
}
