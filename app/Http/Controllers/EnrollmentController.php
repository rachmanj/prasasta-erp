<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\CourseBatch;
use App\Models\Master\Customer;
use App\Events\EnrollmentCreated;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:enrollments.view'])->only(['index']);
        $this->middleware(['auth', 'permission:enrollments.create'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:enrollments.update'])->only(['edit', 'update']);
        $this->middleware(['auth', 'permission:enrollments.delete'])->only(['destroy']);
    }

    public function index()
    {
        return view('enrollments.index');
    }

    public function create()
    {
        $batches = CourseBatch::with('course')->where('status', 'planned')->get();
        $students = Customer::whereNotNull('student_id')->get();
        return view('enrollments.create', compact('batches', 'students'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:customers,id'],
            'batch_id' => ['required', 'exists:course_batches,id'],
            'enrollment_date' => ['required', 'date'],
            'status' => ['required', 'in:enrolled,completed,dropped,suspended'],
            'payment_plan_id' => ['nullable', 'integer'],
            'total_amount' => ['required', 'numeric', 'min:0'],
        ]);

        // Check if student is already enrolled in this batch
        $existingEnrollment = Enrollment::where('student_id', $data['student_id'])
            ->where('batch_id', $data['batch_id'])
            ->first();

        if ($existingEnrollment) {
            return response()->json(['error' => 'Student is already enrolled in this batch'], 400);
        }

        // Check batch capacity
        $batch = CourseBatch::find($data['batch_id']);
        if ($batch->enrollment_count >= $batch->capacity) {
            return response()->json(['error' => 'Batch is at full capacity'], 400);
        }

        $enrollment = Enrollment::create($data);

        // Trigger enrollment created event for accounting integration
        EnrollmentCreated::dispatch($enrollment);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'id' => $enrollment->id]);
        }

        return redirect()->route('enrollments.index')->with('success', 'Enrollment created successfully');
    }

    public function edit(Enrollment $enrollment)
    {
        $batches = CourseBatch::with('course')->get();
        $students = Customer::whereNotNull('student_id')->get();
        return view('enrollments.edit', compact('enrollment', 'batches', 'students'));
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:customers,id'],
            'batch_id' => ['required', 'exists:course_batches,id'],
            'enrollment_date' => ['required', 'date'],
            'status' => ['required', 'in:enrolled,completed,dropped,suspended'],
            'payment_plan_id' => ['nullable', 'integer'],
            'total_amount' => ['required', 'numeric', 'min:0'],
        ]);

        // Check if student is already enrolled in this batch (excluding current enrollment)
        $existingEnrollment = Enrollment::where('student_id', $data['student_id'])
            ->where('batch_id', $data['batch_id'])
            ->where('id', '!=', $enrollment->id)
            ->first();

        if ($existingEnrollment) {
            return response()->json(['error' => 'Student is already enrolled in this batch'], 400);
        }

        $enrollment->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('enrollments.index')->with('success', 'Enrollment updated successfully');
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('enrollments.index')->with('success', 'Enrollment deleted successfully');
    }

    public function data(Request $request)
    {
        $q = Enrollment::query()->with(['student', 'batch.course'])->select(['id', 'student_id', 'batch_id', 'enrollment_date', 'status', 'payment_plan_id', 'total_amount']);

        return DataTables::of($q)
            ->addColumn('student_name', function ($row) {
                return $row->student ? $row->student->name : '-';
            })
            ->addColumn('student_id', function ($row) {
                return $row->student ? $row->student->student_id : '-';
            })
            ->addColumn('course_name', function ($row) {
                return $row->batch && $row->batch->course ? $row->batch->course->name : '-';
            })
            ->addColumn('batch_code', function ($row) {
                return $row->batch ? $row->batch->batch_code : '-';
            })
            ->addColumn('amount_display', function ($row) {
                return 'Rp ' . number_format($row->total_amount, 0, ',', '.');
            })
            ->addColumn('status', function ($row) {
                $statusClass = match ($row->status) {
                    'enrolled' => 'badge-success',
                    'completed' => 'badge-info',
                    'dropped' => 'badge-warning',
                    'suspended' => 'badge-danger',
                    default => 'badge-secondary'
                };
                return '<span class="badge ' . $statusClass . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('actions', function ($row) {
                $editUrl = route('enrollments.update', $row->id);
                $deleteUrl = route('enrollments.destroy', $row->id);

                $actions = '';

                if (auth()->user()->can('enrollments.update')) {
                    $actions .= '<button type="button" class="btn btn-xs btn-info btn-edit" data-id="' . $row->id . '" data-student-id="' . $row->student_id . '" data-batch-id="' . $row->batch_id . '" data-enrollment-date="' . $row->enrollment_date->format('Y-m-d') . '" data-status="' . $row->status . '" data-payment-plan-id="' . $row->payment_plan_id . '" data-total-amount="' . $row->total_amount . '" data-url="' . $editUrl . '">Edit</button>';
                }

                if (auth()->user()->can('enrollments.delete')) {
                    $actions .= ' <button type="button" class="btn btn-xs btn-danger btn-delete" data-id="' . $row->id . '" data-url="' . $deleteUrl . '">Delete</button>';
                }

                return $actions;
            })
            ->rawColumns(['status', 'actions'])
            ->toJson();
    }
}
