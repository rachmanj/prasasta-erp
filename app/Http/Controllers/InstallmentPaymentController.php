<?php

namespace App\Http\Controllers;

use App\Models\InstallmentPayment;
use App\Models\Enrollment;
use App\Services\PaymentProcessingService;
use App\Events\PaymentReceived;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class InstallmentPaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentProcessingService $paymentService)
    {
        $this->middleware(['auth', 'permission:installment_payments.view'])->only(['index']);
        $this->middleware(['auth', 'permission:installment_payments.create'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:installment_payments.update'])->only(['edit', 'update', 'processPayment']);
        $this->middleware(['auth', 'permission:installment_payments.delete'])->only(['destroy']);

        $this->paymentService = $paymentService;
    }

    public function index()
    {
        return view('installment-payments.index');
    }

    public function create()
    {
        $enrollments = Enrollment::with(['student', 'batch.course'])->get();
        return view('installment-payments.create', compact('enrollments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'enrollment_id' => ['required', 'exists:enrollments,id'],
            'installment_number' => ['required', 'integer', 'min:0'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        // Check if installment already exists
        $existing = InstallmentPayment::where('enrollment_id', $data['enrollment_id'])
            ->where('installment_number', $data['installment_number'])
            ->first();

        if ($existing) {
            return response()->json(['error' => 'Installment already exists for this enrollment'], 400);
        }

        $installment = InstallmentPayment::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'id' => $installment->id]);
        }

        return redirect()->route('installment-payments.index')->with('success', 'Installment payment created successfully');
    }

    public function edit(InstallmentPayment $installmentPayment)
    {
        $enrollments = Enrollment::with(['student', 'batch.course'])->get();
        return view('installment-payments.edit', compact('installmentPayment', 'enrollments'));
    }

    public function update(Request $request, InstallmentPayment $installmentPayment)
    {
        $data = $request->validate([
            'enrollment_id' => ['required', 'exists:enrollments,id'],
            'installment_number' => ['required', 'integer', 'min:0'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        // Check if installment already exists (excluding current)
        $existing = InstallmentPayment::where('enrollment_id', $data['enrollment_id'])
            ->where('installment_number', $data['installment_number'])
            ->where('id', '!=', $installmentPayment->id)
            ->first();

        if ($existing) {
            return response()->json(['error' => 'Installment already exists for this enrollment'], 400);
        }

        $installmentPayment->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('installment-payments.index')->with('success', 'Installment payment updated successfully');
    }

    public function destroy(InstallmentPayment $installmentPayment)
    {
        if ($installmentPayment->isPaid()) {
            return response()->json(['error' => 'Cannot delete paid installment'], 400);
        }

        $installmentPayment->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('installment-payments.index')->with('success', 'Installment payment deleted successfully');
    }

    public function processPayment(Request $request, InstallmentPayment $installmentPayment)
    {
        $data = $request->validate([
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'reference_number' => ['nullable', 'string', 'max:100'],
        ]);

        $payment = $this->paymentService->processPayment(
            $installmentPayment,
            $data['paid_amount'],
            $data['payment_method'] ?? null,
            $data['reference_number'] ?? null
        );

        // Trigger payment received event for accounting integration
        PaymentReceived::dispatch($payment);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('installment-payments.index')->with('success', 'Payment processed successfully');
    }

    public function generateInstallments(Request $request, Enrollment $enrollment)
    {
        try {
            $installments = $this->paymentService->generateInstallmentPayments($enrollment);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Installments generated successfully',
                    'count' => count($installments)
                ]);
            }

            return redirect()->route('installment-payments.index')->with('success', 'Installments generated successfully');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function updateOverdueInstallments()
    {
        $updatedCount = $this->paymentService->updateOverdueInstallments();

        return response()->json([
            'success' => true,
            'message' => "Updated {$updatedCount} overdue installments",
            'count' => $updatedCount
        ]);
    }

    public function data(Request $request)
    {
        $q = InstallmentPayment::query()->with(['enrollment.student', 'enrollment.batch.course'])
            ->select(['id', 'enrollment_id', 'installment_number', 'amount', 'due_date', 'paid_date', 'paid_amount', 'late_fee', 'status']);

        return DataTables::of($q)
            ->addColumn('student_name', function ($row) {
                return $row->enrollment->student ? $row->enrollment->student->name : '-';
            })
            ->addColumn('course_name', function ($row) {
                return $row->enrollment->batch && $row->enrollment->batch->course
                    ? $row->enrollment->batch->course->name : '-';
            })
            ->addColumn('batch_code', function ($row) {
                return $row->enrollment->batch ? $row->enrollment->batch->batch_code : '-';
            })
            ->addColumn('amount_display', function ($row) {
                return $row->formatted_amount;
            })
            ->addColumn('paid_amount_display', function ($row) {
                return $row->formatted_paid_amount;
            })
            ->addColumn('late_fee_display', function ($row) {
                return $row->formatted_late_fee;
            })
            ->addColumn('total_amount_display', function ($row) {
                return $row->formatted_total_amount;
            })
            ->addColumn('days_overdue', function ($row) {
                return $row->days_overdue;
            })
            ->addColumn('status', function ($row) {
                $statusClass = match ($row->status) {
                    'pending' => 'badge-warning',
                    'paid' => 'badge-success',
                    'overdue' => 'badge-danger',
                    'cancelled' => 'badge-secondary',
                    default => 'badge-secondary'
                };
                return '<span class="badge ' . $statusClass . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('actions', function ($row) {
                $editUrl = route('installment-payments.update', $row->id);
                $deleteUrl = route('installment-payments.destroy', $row->id);
                $processPaymentUrl = route('installment-payments.process-payment', $row->id);

                $actions = '';

                if (auth()->user()->can('installment_payments.update')) {
                    $actions .= '<button type="button" class="btn btn-xs btn-info btn-edit" data-id="' . $row->id . '" data-enrollment-id="' . $row->enrollment_id . '" data-installment-number="' . $row->installment_number . '" data-amount="' . $row->amount . '" data-due-date="' . $row->due_date->format('Y-m-d') . '" data-notes="' . e($row->notes) . '" data-url="' . $editUrl . '">Edit</button>';
                }

                if (auth()->user()->can('installment_payments.update') && $row->isPending()) {
                    $actions .= ' <button type="button" class="btn btn-xs btn-success btn-process-payment" data-id="' . $row->id . '" data-url="' . $processPaymentUrl . '">Process Payment</button>';
                }

                if (auth()->user()->can('installment_payments.delete') && !$row->isPaid()) {
                    $actions .= ' <button type="button" class="btn btn-xs btn-danger btn-delete" data-id="' . $row->id . '" data-url="' . $deleteUrl . '">Delete</button>';
                }

                return $actions;
            })
            ->rawColumns(['status', 'actions'])
            ->toJson();
    }
}
