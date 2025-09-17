<?php

namespace App\Http\Controllers;

use App\Models\PaymentPlan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PaymentPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:payment_plans.view'])->only(['index']);
        $this->middleware(['auth', 'permission:payment_plans.create'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:payment_plans.update'])->only(['edit', 'update']);
        $this->middleware(['auth', 'permission:payment_plans.delete'])->only(['destroy']);
    }

    public function index()
    {
        return view('payment-plans.index');
    }

    public function create()
    {
        return view('payment-plans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:payment_plans,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'installment_count' => ['required', 'integer', 'min:1'],
            'installment_interval_days' => ['required', 'integer', 'min:1'],
            'down_payment_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'late_fee_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grace_period_days' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $paymentPlan = PaymentPlan::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'id' => $paymentPlan->id]);
        }

        return redirect()->route('payment-plans.index')->with('success', 'Payment plan created successfully');
    }

    public function edit(PaymentPlan $paymentPlan)
    {
        return view('payment-plans.edit', compact('paymentPlan'));
    }

    public function update(Request $request, PaymentPlan $paymentPlan)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:payment_plans,code,' . $paymentPlan->id],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'installment_count' => ['required', 'integer', 'min:1'],
            'installment_interval_days' => ['required', 'integer', 'min:1'],
            'down_payment_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'late_fee_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grace_period_days' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $paymentPlan->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('payment-plans.index')->with('success', 'Payment plan updated successfully');
    }

    public function destroy(PaymentPlan $paymentPlan)
    {
        // Check if payment plan has enrollments
        if ($paymentPlan->enrollments()->count() > 0) {
            return response()->json(['error' => 'Cannot delete payment plan with existing enrollments'], 400);
        }

        $paymentPlan->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('payment-plans.index')->with('success', 'Payment plan deleted successfully');
    }

    public function data(Request $request)
    {
        $q = PaymentPlan::query()->select(['id', 'code', 'name', 'description', 'installment_count', 'installment_interval_days', 'down_payment_percentage', 'late_fee_percentage', 'grace_period_days', 'is_active']);

        return DataTables::of($q)
            ->addColumn('installment_display', function ($row) {
                return $row->installment_count . ' x ' . $row->installment_interval_days . ' days';
            })
            ->addColumn('down_payment_display', function ($row) {
                return $row->down_payment_percentage ? $row->down_payment_percentage . '%' : 'No DP';
            })
            ->addColumn('late_fee_display', function ($row) {
                return $row->late_fee_percentage ? $row->late_fee_percentage . '%' : 'No Late Fee';
            })
            ->addColumn('status', function ($row) {
                return $row->is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>';
            })
            ->addColumn('actions', function ($row) {
                $editUrl = route('payment-plans.update', $row->id);
                $deleteUrl = route('payment-plans.destroy', $row->id);

                $actions = '';

                if (auth()->user()->can('payment_plans.update')) {
                    $actions .= '<button type="button" class="btn btn-xs btn-info btn-edit" data-id="' . $row->id . '" data-code="' . e($row->code) . '" data-name="' . e($row->name) . '" data-description="' . e($row->description) . '" data-installment-count="' . $row->installment_count . '" data-installment-interval-days="' . $row->installment_interval_days . '" data-down-payment-percentage="' . $row->down_payment_percentage . '" data-late-fee-percentage="' . $row->late_fee_percentage . '" data-grace-period-days="' . $row->grace_period_days . '" data-is-active="' . ($row->is_active ? '1' : '0') . '" data-url="' . $editUrl . '">Edit</button>';
                }

                if (auth()->user()->can('payment_plans.delete')) {
                    $actions .= ' <button type="button" class="btn btn-xs btn-danger btn-delete" data-id="' . $row->id . '" data-url="' . $deleteUrl . '">Delete</button>';
                }

                return $actions;
            })
            ->rawColumns(['status', 'actions'])
            ->toJson();
    }
}
