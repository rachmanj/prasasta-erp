@extends('layouts.main')

@section('title_page')
    Stock Adjustments
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Stock Adjustments</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Stock Adjustments</h3>
                            <div class="card-tools">
                                @can('inventory.stock_adjustments.create')
                                    <a href="{{ route('stock-adjustments.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Create Adjustment
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <script>
                                    toastr.success(@json(session('success')));
                                </script>
                            @endif
                            <table class="table table-bordered table-striped table-sm" id="adjustmentsTable">
                                <thead>
                                    <tr>
                                        <th>Adjustment No</th>
                                        <th>Date</th>
                                        <th>Reason</th>
                                        <th>Total Value</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th>Approved By</th>
                                        <th>Created</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(function() {
            $('#adjustmentsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('stock-adjustments.data') }}',
                columns: [{
                        data: 'adjustment_no',
                        name: 'adjustment_no'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'reason',
                        name: 'reason'
                    },
                    {
                        data: 'total_adjustment_value',
                        name: 'total_adjustment_value',
                        orderable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false
                    },
                    {
                        data: 'creator_name',
                        name: 'creator.name',
                        orderable: false
                    },
                    {
                        data: 'approver_name',
                        name: 'approver.name',
                        orderable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });

        function viewAdjustment(id) {
            window.location.href = '/inventory/stock-adjustments/' + id;
        }

        function approveAdjustment(id) {
            Swal.fire({
                title: 'Approve Stock Adjustment?',
                text: 'This will update the inventory quantities and create journal entries.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, approve',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/inventory/stock-adjustments/' + id + '/approve',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Success!', response.message, 'success').then(() => {
                                    $('#adjustmentsTable').DataTable().ajax.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            var errorMessage = 'An error occurred while approving the adjustment.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire('Error!', errorMessage, 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
