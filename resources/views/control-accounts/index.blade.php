@extends('layouts.main')

@section('title_page')
    Control Accounts
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Control Accounts</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Control Accounts</h3>
                            <div class="card-tools">
                                @can('control_accounts.create')
                                    <a href="{{ route('control-accounts.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> New Control Account
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Statistics Cards -->
                            <div class="row mb-3">
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-info">
                                        <div class="inner">
                                            <h3 id="total-control-accounts">-</h3>
                                            <p>Total Control Accounts</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-university"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-success">
                                        <div class="inner">
                                            <h3 id="total-subsidiaries">-</h3>
                                            <p>Total Subsidiaries</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-list"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-warning">
                                        <div class="inner">
                                            <h3 id="pending-reconciliations">-</h3>
                                            <p>Pending Reconciliations</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-danger">
                                        <div class="inner">
                                            <h3 id="accounts-with-variance">-</h3>
                                            <p>Accounts with Variance</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if (session('success'))
                                <script>
                                    toastr.success(@json(session('success')));
                                </script>
                            @endif

                            <table class="table table-bordered table-striped table-sm" id="control-accounts-table">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Control Type</th>
                                        <th class="text-right">Current Balance</th>
                                        <th class="text-right">Subsidiary Total</th>
                                        <th class="text-right">Variance</th>
                                        <th>Status</th>
                                        <th>Last Reconciliation</th>
                                        <th style="width:200px;">Actions</th>
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

@push('scripts')
    <script>
        $(function() {
            var table = $('#control-accounts-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('control-accounts.data') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'control_type',
                        name: 'control_type'
                    },
                    {
                        data: 'current_balance',
                        name: 'current_balance',
                        className: 'text-right',
                        orderable: false
                    },
                    {
                        data: 'subsidiary_total',
                        name: 'subsidiary_total',
                        className: 'text-right',
                        orderable: false
                    },
                    {
                        data: 'variance',
                        name: 'variance',
                        className: 'text-right',
                        orderable: false
                    },
                    {
                        data: 'reconciliation_status',
                        name: 'reconciliation_status',
                        orderable: false
                    },
                    {
                        data: 'last_reconciliation',
                        name: 'last_reconciliation',
                        orderable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [0, 'asc']
                ],
                pageLength: 25,
                responsive: true,
                language: {
                    processing: "Loading control accounts..."
                }
            });

            // Load dashboard statistics
            loadDashboardData();

            // Delete confirmation with SweetAlert2
            $('#control-accounts-table').on('click', '.delete-btn', function(e) {
                e.preventDefault();
                var deleteUrl = $(this).data('delete-url');
                var controlAccountName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to delete control account "${controlAccountName}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    table.ajax.reload(null, false);
                                    toastr.success('Control account deleted successfully.');
                                    loadDashboardData(); // Refresh statistics
                                } else {
                                    toastr.error(response.message || 'Failed to delete control account.');
                                }
                            },
                            error: function() {
                                toastr.error('An error occurred while deleting the control account.');
                            }
                        });
                    }
                });
            });
        });

        function loadDashboardData() {
            $.ajax({
                url: "{{ route('control-accounts.dashboard.data') }}",
                type: 'GET',
                success: function(response) {
                    // Update statistics cards
                    $('#total-control-accounts').text(response.statistics.total_control_accounts);
                    $('#total-subsidiaries').text(response.statistics.total_subsidiary_accounts);
                    $('#pending-reconciliations').text(response.statistics.pending_reconciliations);
                    $('#accounts-with-variance').text(response.statistics.accounts_with_variance);
                },
                error: function(xhr) {
                    console.error('Failed to load dashboard data:', xhr);
                }
            });
        }
    </script>
@endpush
