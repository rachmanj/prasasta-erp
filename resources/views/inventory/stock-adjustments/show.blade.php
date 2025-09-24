@extends('layouts.main')

@section('title_page', 'Stock Adjustment Details')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Stock Adjustment Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('stock-adjustments.index') }}">Stock Adjustments</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $adjustment->adjustment_no }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <!-- Adjustment Details -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-adjust"></i> Adjustment Information
                            </h3>
                            <div class="card-tools">
                                @if ($adjustment->status === 'draft')
                                    <span class="badge badge-warning badge-lg">Draft</span>
                                @elseif($adjustment->status === 'approved')
                                    <span class="badge badge-success badge-lg">Approved</span>
                                @else
                                    <span class="badge badge-danger badge-lg">Cancelled</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Adjustment No:</th>
                                            <td><strong>{{ $adjustment->adjustment_no }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Date:</th>
                                            <td>{{ \Carbon\Carbon::parse($adjustment->date)->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Reason:</th>
                                            <td>{{ $adjustment->reason }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Total Value:</th>
                                            <td><strong>Rp
                                                    {{ number_format($adjustment->total_adjustment_value, 0, ',', '.') }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Created By:</th>
                                            <td>{{ $adjustment->creator ? $adjustment->creator->name : 'Unknown' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created At:</th>
                                            <td>{{ \Carbon\Carbon::parse($adjustment->created_at)->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                        @if ($adjustment->approved_by)
                                            <tr>
                                                <th>Approved By:</th>
                                                <td>{{ $adjustment->approver ? $adjustment->approver->name : 'Unknown' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Approved At:</th>
                                                <td>{{ \Carbon\Carbon::parse($adjustment->approved_at)->format('d/m/Y H:i') }}
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Adjustment Lines -->
                    <div class="card card-secondary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list"></i> Adjustment Lines ({{ $adjustment->lines->count() }} items)
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Category</th>
                                            <th class="text-right">Current Stock</th>
                                            <th class="text-right">Adjusted Stock</th>
                                            <th class="text-right">Variance</th>
                                            <th class="text-right">Unit Cost</th>
                                            <th class="text-right">Variance Value</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($adjustment->lines as $line)
                                            <tr>
                                                <td><strong>{{ $line->item->code }}</strong></td>
                                                <td>{{ $line->item->name }}</td>
                                                <td>{{ $line->item->category->name }}</td>
                                                <td class="text-right">{{ number_format($line->current_quantity, 4) }}</td>
                                                <td class="text-right">{{ number_format($line->adjusted_quantity, 4) }}
                                                </td>
                                                <td
                                                    class="text-right {{ $line->variance_quantity >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $line->variance_quantity >= 0 ? '+' : '' }}{{ number_format($line->variance_quantity, 4) }}
                                                </td>
                                                <td class="text-right">Rp
                                                    {{ number_format($line->unit_cost, 0, ',', '.') }}</td>
                                                <td
                                                    class="text-right {{ $line->variance_value >= 0 ? 'text-success' : 'text-danger' }}">
                                                    Rp {{ number_format(abs($line->variance_value), 0, ',', '.') }}
                                                </td>
                                                <td>{{ $line->notes ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-light">
                                            <th colspan="7" class="text-right">Total Adjustment Value:</th>
                                            <th class="text-right">Rp
                                                {{ number_format($adjustment->total_adjustment_value, 0, ',', '.') }}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Summary Cards -->
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie"></i> Summary
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="description-block">
                                        <span class="description-percentage text-success">
                                            <i class="fas fa-plus"></i>
                                            {{ $adjustment->lines->where('variance_quantity', '>', 0)->count() }}
                                        </span>
                                        <h5 class="description-header">Items Increased</h5>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="description-block">
                                        <span class="description-percentage text-danger">
                                            <i class="fas fa-minus"></i>
                                            {{ $adjustment->lines->where('variance_quantity', '<', 0)->count() }}
                                        </span>
                                        <h5 class="description-header">Items Decreased</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="description-block">
                                        <span class="description-percentage text-info">
                                            <i class="fas fa-equals"></i>
                                            {{ $adjustment->lines->where('variance_quantity', '=', 0)->count() }}
                                        </span>
                                        <h5 class="description-header">Items Unchanged</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cogs"></i> Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            @if ($adjustment->status === 'draft' && auth()->user()->can('inventory.adjustments.approve'))
                                <button type="button" class="btn btn-success btn-block mb-2"
                                    onclick="approveAdjustment({{ $adjustment->id }})">
                                    <i class="fas fa-check"></i> Approve Adjustment
                                </button>
                            @endif

                            <a href="{{ route('stock-adjustments.index') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <!-- Status Information -->
                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i> Status Information
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="time-label">
                                    <span
                                        class="bg-blue">{{ \Carbon\Carbon::parse($adjustment->created_at)->format('d M Y') }}</span>
                                </div>
                                <div>
                                    <i class="fas fa-user bg-blue"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i>
                                            {{ \Carbon\Carbon::parse($adjustment->created_at)->format('H:i') }}</span>
                                        <h3 class="timeline-header">Adjustment Created</h3>
                                        <div class="timeline-body">
                                            Created by
                                            <strong>{{ $adjustment->creator ? $adjustment->creator->name : 'Unknown' }}</strong>
                                        </div>
                                    </div>
                                </div>

                                @if ($adjustment->approved_at)
                                    <div>
                                        <i class="fas fa-check bg-green"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i>
                                                {{ \Carbon\Carbon::parse($adjustment->approved_at)->format('H:i') }}</span>
                                            <h3 class="timeline-header">Adjustment Approved</h3>
                                            <div class="timeline-body">
                                                Approved by
                                                <strong>{{ $adjustment->approver ? $adjustment->approver->name : 'Unknown' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div>
                                    <i class="fas fa-clock bg-gray"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function approveAdjustment(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will approve the stock adjustment and update inventory quantities. This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('stock-adjustments.approve', ':id') }}'.replace(':id', id),
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Approved!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            var response = xhr.responseJSON;
                            Swal.fire(
                                'Error!',
                                response.message || 'Something went wrong!',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
@endpush
