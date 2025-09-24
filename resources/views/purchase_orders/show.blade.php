@extends('layouts.main')

@section('title', 'Purchase Order Details')

@section('title_page')
    Purchase Order: {{ $order->order_no ?? '#' . $order->id }}
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Orders</a></li>
    <li class="breadcrumb-item active">{{ $order->order_no ?? '#' . $order->id }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-0">
                            <i class="fas fa-file-invoice mr-2"></i>
                            Purchase Order {{ $order->order_no ?? '#' . $order->id }}
                        </h3>
                    </div>
                    <div>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-sm btn-secondary mr-2"
                            aria-label="Back to Purchase Orders">
                            <i class="fas fa-arrow-left mr-1"></i>Back to List
                        </a>
                        <button type="button" class="btn btn-sm btn-primary" id="approveBtn"
                            {{ $order->status !== 'draft' ? 'disabled' : '' }}>
                            <i class="fas fa-check mr-1"></i>Approve
                        </button>
                        <button type="button" class="btn btn-sm btn-warning" id="closeBtn"
                            {{ $order->status !== 'approved' ? 'disabled' : '' }}>
                            <i class="fas fa-lock mr-1"></i>Close
                        </button>
                        <a href="{{ route('purchase-orders.create-invoice', $order->id) }}" class="btn btn-sm btn-success"
                            aria-label="Create Invoice from Purchase Order">
                            <i class="fas fa-file-invoice-dollar mr-1"></i>Create Invoice
                        </a>
                        @can('assets.create')
                            <a href="{{ route('purchase-orders.create-assets', $order->id) }}" class="btn btn-sm btn-info"
                                aria-label="Create Assets from Purchase Order">
                                <i class="fas fa-cube mr-1"></i>Create Assets
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <!-- Order Information -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-calendar"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Date</span>
                                    <span
                                        class="info-box-number">{{ $order->date ? $order->date->format('d/m/Y') : '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-building"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Vendor</span>
                                    <span class="info-box-number">#{{ $order->vendor_id }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-flag"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Status</span>
                                    <span class="info-box-number">{{ strtoupper($order->status) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Amount</span>
                                    <span class="info-box-number">Rp
                                        {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (session('success'))
                        <script>
                            toastr.success(@json(session('success')));
                        </script>
                    @endif
                    @php
                        $orderedQty = (float) DB::table('purchase_order_lines')
                            ->where('order_id', $order->id)
                            ->sum('qty');
                        $receivedQty = (float) DB::table('goods_receipt_lines as grl')
                            ->join('goods_receipts as grn', 'grn.id', '=', 'grl.grn_id')
                            ->where('grn.purchase_order_id', $order->id)
                            ->sum('grl.qty');
                    @endphp

                    <!-- Summary Information -->
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Order Summary:</strong> {{ number_format($orderedQty, 2) }} items ordered |
                        {{ number_format($receivedQty, 2) }} items received
                    </div>

                    <!-- Order Lines Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Account</th>
                                    <th>Description</th>
                                    <th class="text-right">Qty</th>
                                    <th class="text-right">Unit Price</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->lines as $l)
                                    <tr>
                                        <td>
                                            <span class="badge badge-secondary">#{{ $l->account_id }}</span>
                                        </td>
                                        <td>{{ $l->description }}</td>
                                        <td class="text-right">{{ number_format($l->qty, 2) }}</td>
                                        <td class="text-right">Rp {{ number_format($l->unit_price, 0, ',', '.') }}</td>
                                        <td class="text-right">
                                            <strong>Rp {{ number_format($l->amount, 0, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <th colspan="4" class="text-right">Total:</th>
                                    <th class="text-right">
                                        <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Approve Purchase Order Confirmation
            $('#approveBtn').on('click', function() {
                Swal.fire({
                    title: 'Approve Purchase Order?',
                    text: 'Are you sure you want to approve Purchase Order {{ $order->order_no ?? '#' . $order->id }}?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Approve!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create and submit the form
                        const form = $('<form>', {
                            'method': 'POST',
                            'action': '{{ route('purchase-orders.approve', $order->id) }}'
                        });

                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_token',
                            'value': '{{ csrf_token() }}'
                        }));

                        $('body').append(form);
                        form.submit();
                    }
                });
            });

            // Close Purchase Order Confirmation
            $('#closeBtn').on('click', function() {
                Swal.fire({
                    title: 'Close Purchase Order?',
                    text: 'Are you sure you want to close Purchase Order {{ $order->order_no ?? '#' . $order->id }}? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f39c12',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Close!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create and submit the form
                        const form = $('<form>', {
                            'method': 'POST',
                            'action': '{{ route('purchase-orders.close', $order->id) }}'
                        });

                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_token',
                            'value': '{{ csrf_token() }}'
                        }));

                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
