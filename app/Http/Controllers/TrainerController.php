<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TrainerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:trainers.view'])->only(['index']);
        $this->middleware(['auth', 'permission:trainers.create'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:trainers.update'])->only(['edit', 'update']);
        $this->middleware(['auth', 'permission:trainers.delete'])->only(['destroy']);
    }

    public function index()
    {
        return view('trainers.index');
    }

    public function create()
    {
        return view('trainers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:trainers,code'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'type' => ['required', 'in:internal,external'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'qualifications' => ['nullable', 'string'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'batch_rate' => ['nullable', 'numeric', 'min:0'],
            'revenue_share_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'bank_account' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive,suspended'],
            'notes' => ['nullable', 'string'],
        ]);

        $trainer = Trainer::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'id' => $trainer->id]);
        }

        return redirect()->route('trainers.index')->with('success', 'Trainer created successfully');
    }

    public function edit(Trainer $trainer)
    {
        return view('trainers.edit', compact('trainer'));
    }

    public function update(Request $request, Trainer $trainer)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:trainers,code,' . $trainer->id],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'type' => ['required', 'in:internal,external'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'qualifications' => ['nullable', 'string'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'batch_rate' => ['nullable', 'numeric', 'min:0'],
            'revenue_share_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'bank_account' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive,suspended'],
            'notes' => ['nullable', 'string'],
        ]);

        $trainer->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('trainers.index')->with('success', 'Trainer updated successfully');
    }

    public function destroy(Trainer $trainer)
    {
        // Check if trainer has active batches
        if ($trainer->courseBatches()->count() > 0) {
            return response()->json(['error' => 'Cannot delete trainer with existing course batches'], 400);
        }

        $trainer->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('trainers.index')->with('success', 'Trainer deleted successfully');
    }

    public function data(Request $request)
    {
        $q = Trainer::query()->select(['id', 'code', 'name', 'email', 'phone', 'type', 'specialization', 'hourly_rate', 'batch_rate', 'status']);

        return DataTables::of($q)
            ->addColumn('type_display', function ($row) {
                $typeClass = $row->type === 'internal' ? 'badge-primary' : 'badge-info';
                return '<span class="badge ' . $typeClass . '">' . ucfirst($row->type) . '</span>';
            })
            ->addColumn('hourly_rate_display', function ($row) {
                return $row->hourly_rate ? 'Rp ' . number_format($row->hourly_rate, 0, ',', '.') : '-';
            })
            ->addColumn('batch_rate_display', function ($row) {
                return $row->batch_rate ? 'Rp ' . number_format($row->batch_rate, 0, ',', '.') : '-';
            })
            ->addColumn('status', function ($row) {
                $statusClass = match ($row->status) {
                    'active' => 'badge-success',
                    'inactive' => 'badge-warning',
                    'suspended' => 'badge-danger',
                    default => 'badge-secondary'
                };
                return '<span class="badge ' . $statusClass . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('actions', function ($row) {
                $editUrl = route('trainers.update', $row->id);
                $deleteUrl = route('trainers.destroy', $row->id);

                $actions = '';

                if (auth()->user()->can('trainers.update')) {
                    $actions .= '<button type="button" class="btn btn-xs btn-info btn-edit" data-id="' . $row->id . '" data-code="' . e($row->code) . '" data-name="' . e($row->name) . '" data-email="' . e($row->email) . '" data-phone="' . e($row->phone) . '" data-type="' . $row->type . '" data-specialization="' . e($row->specialization) . '" data-hourly-rate="' . $row->hourly_rate . '" data-batch-rate="' . $row->batch_rate . '" data-status="' . $row->status . '" data-url="' . $editUrl . '">Edit</button>';
                }

                if (auth()->user()->can('trainers.delete')) {
                    $actions .= ' <button type="button" class="btn btn-xs btn-danger btn-delete" data-id="' . $row->id . '" data-url="' . $deleteUrl . '">Delete</button>';
                }

                return $actions;
            })
            ->rawColumns(['type_display', 'status', 'actions'])
            ->toJson();
    }
}
