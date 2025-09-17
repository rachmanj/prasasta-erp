<?php

namespace App\Http\Controllers;

use App\Models\RevenueRecognition;
use App\Models\CourseBatch;
use App\Models\Enrollment;
use App\Services\PaymentProcessingService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RevenueRecognitionController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentProcessingService $paymentService)
    {
        $this->middleware(['auth', 'permission:revenue_recognition.view'])->only(['index']);
        $this->middleware(['auth', 'permission:revenue_recognition.create'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:revenue_recognition.update'])->only(['edit', 'update', 'recognize', 'reverse']);
        $this->middleware(['auth', 'permission:revenue_recognition.delete'])->only(['destroy']);

        $this->paymentService = $paymentService;
    }

    public function index()
    {
        return view('revenue-recognition.index');
    }

    public function create()
    {
        $enrollments = Enrollment::with(['student', 'batch.course'])->get();
        $batches = CourseBatch::with('course')->get();
        return view('revenue-recognition.create', compact('enrollments', 'batches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'enrollment_id' => ['nullable', 'exists:enrollments,id'],
            'batch_id' => ['required', 'exists:course_batches,id'],
            'recognition_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'in:deferred,recognized,reversed'],
            'description' => ['nullable', 'string'],
        ]);

        $revenueRecognition = RevenueRecognition::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'id' => $revenueRecognition->id]);
        }

        return redirect()->route('revenue-recognition.index')->with('success', 'Revenue recognition created successfully');
    }

    public function edit(RevenueRecognition $revenueRecognition)
    {
        $enrollments = Enrollment::with(['student', 'batch.course'])->get();
        $batches = CourseBatch::with('course')->get();
        return view('revenue-recognition.edit', compact('revenueRecognition', 'enrollments', 'batches'));
    }

    public function update(Request $request, RevenueRecognition $revenueRecognition)
    {
        $data = $request->validate([
            'enrollment_id' => ['nullable', 'exists:enrollments,id'],
            'batch_id' => ['required', 'exists:course_batches,id'],
            'recognition_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'in:deferred,recognized,reversed'],
            'description' => ['nullable', 'string'],
        ]);

        $revenueRecognition->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('revenue-recognition.index')->with('success', 'Revenue recognition updated successfully');
    }

    public function destroy(RevenueRecognition $revenueRecognition)
    {
        if ($revenueRecognition->isPosted()) {
            return response()->json(['error' => 'Cannot delete posted revenue recognition'], 400);
        }

        $revenueRecognition->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('revenue-recognition.index')->with('success', 'Revenue recognition deleted successfully');
    }

    public function recognize(Request $request, RevenueRecognition $revenueRecognition)
    {
        $data = $request->validate([
            'description' => ['nullable', 'string'],
        ]);

        $revenueRecognition->recognize($data['description'] ?? null);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('revenue-recognition.index')->with('success', 'Revenue recognized successfully');
    }

    public function reverse(Request $request, RevenueRecognition $revenueRecognition)
    {
        $data = $request->validate([
            'description' => ['nullable', 'string'],
        ]);

        $revenueRecognition->reverse($data['description'] ?? null);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('revenue-recognition.index')->with('success', 'Revenue recognition reversed successfully');
    }

    public function recognizeBatchRevenue(Request $request, CourseBatch $batch)
    {
        try {
            $recognitions = $this->paymentService->recognizeRevenueForBatch($batch);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Batch revenue recognized successfully',
                    'count' => count($recognitions)
                ]);
            }

            return redirect()->route('revenue-recognition.index')->with('success', 'Batch revenue recognized successfully');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function generateDeferredRevenue(Request $request, Enrollment $enrollment)
    {
        try {
            $revenueRecognition = $this->paymentService->generateRevenueRecognition($enrollment);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Deferred revenue generated successfully',
                    'id' => $revenueRecognition->id
                ]);
            }

            return redirect()->route('revenue-recognition.index')->with('success', 'Deferred revenue generated successfully');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function data(Request $request)
    {
        $q = RevenueRecognition::query()->with(['enrollment.student', 'batch.course'])
            ->select(['id', 'enrollment_id', 'batch_id', 'recognition_date', 'amount', 'type', 'description', 'journal_entry_id', 'is_posted']);

        return DataTables::of($q)
            ->addColumn('student_name', function ($row) {
                return $row->enrollment && $row->enrollment->student
                    ? $row->enrollment->student->name : '-';
            })
            ->addColumn('course_name', function ($row) {
                return $row->batch && $row->batch->course
                    ? $row->batch->course->name : '-';
            })
            ->addColumn('batch_code', function ($row) {
                return $row->batch ? $row->batch->batch_code : '-';
            })
            ->addColumn('amount_display', function ($row) {
                return $row->formatted_amount;
            })
            ->addColumn('type', function ($row) {
                $typeClass = match ($row->type) {
                    'deferred' => 'badge-warning',
                    'recognized' => 'badge-success',
                    'reversed' => 'badge-danger',
                    default => 'badge-secondary'
                };
                return '<span class="badge ' . $typeClass . '">' . ucfirst($row->type) . '</span>';
            })
            ->addColumn('posted_status', function ($row) {
                return $row->is_posted
                    ? '<span class="badge badge-success">Posted</span>'
                    : '<span class="badge badge-secondary">Not Posted</span>';
            })
            ->addColumn('actions', function ($row) {
                $editUrl = route('revenue-recognition.update', $row->id);
                $deleteUrl = route('revenue-recognition.destroy', $row->id);
                $recognizeUrl = route('revenue-recognition.recognize', $row->id);
                $reverseUrl = route('revenue-recognition.reverse', $row->id);

                $actions = '';

                if (auth()->user()->can('revenue_recognition.update')) {
                    $actions .= '<button type="button" class="btn btn-xs btn-info btn-edit" data-id="' . $row->id . '" data-enrollment-id="' . $row->enrollment_id . '" data-batch-id="' . $row->batch_id . '" data-recognition-date="' . $row->recognition_date->format('Y-m-d') . '" data-amount="' . $row->amount . '" data-type="' . $row->type . '" data-description="' . e($row->description) . '" data-url="' . $editUrl . '">Edit</button>';
                }

                if (auth()->user()->can('revenue_recognition.update') && $row->isDeferred()) {
                    $actions .= ' <button type="button" class="btn btn-xs btn-success btn-recognize" data-id="' . $row->id . '" data-url="' . $recognizeUrl . '">Recognize</button>';
                }

                if (auth()->user()->can('revenue_recognition.update') && $row->isRecognized()) {
                    $actions .= ' <button type="button" class="btn btn-xs btn-warning btn-reverse" data-id="' . $row->id . '" data-url="' . $reverseUrl . '">Reverse</button>';
                }

                if (auth()->user()->can('revenue_recognition.delete') && !$row->isPosted()) {
                    $actions .= ' <button type="button" class="btn btn-xs btn-danger btn-delete" data-id="' . $row->id . '" data-url="' . $deleteUrl . '">Delete</button>';
                }

                return $actions;
            })
            ->rawColumns(['type', 'posted_status', 'actions'])
            ->toJson();
    }
}
