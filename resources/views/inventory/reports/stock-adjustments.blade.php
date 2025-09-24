@extends('layouts.main')

@section('title_page', 'Stock Adjustments Report')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Stock Adjustments Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('inventory.reports.dashboard') }}">Inventory
                                Reports</a></li>
                        <li class="breadcrumb-item active">Stock Adjustments</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Summary Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalAdjustments">-</h3>
                            <p>Total Adjustments</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-adjust"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="approvedAdjustments">-</h3>
                            <p>Approved</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="pendingAdjustments">-</h3>
                            <p>Pending</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="totalAdjustmentValue">-</h3>
                            <p>Total Value</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter"></i>
                        Filters
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                        value="{{ date('Y-m-01') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status_filter">Status</label>
                                    <select class="form-control" id="status_filter" name="status">
                                        <option value="">All Status</option>
                                        <option value="draft">Draft</option>
                                        <option value="approved">Approved</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="created_by_filter">Created By</label>
                                    <select class="form-control select2" id="created_by_filter" name="created_by">
                                        <option value="">All Users</option>
                                        @foreach (\App\Models\User::whereHas('stockAdjustments')->get() as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search_filter">Search</label>
                                    <input type="text" class="form-control" id="search_filter" name="search"
                                        placeholder="Search adjustment no, reason...">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="button" class="btn btn-primary btn-block" onclick="applyFilters()">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Stock Adjustments Report Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-adjust"></i>
                        Stock Adjustments Report
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" onclick="exportReport()">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <a href="{{ route('stock-adjustments.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New Adjustment
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="stockAdjustmentsTable">
                            <thead>
                                <tr>
                                    <th>Adjustment No</th>
                                    <th>Date</th>
                                    <th>Reason</th>
                                    <th>Items Count</th>
                                    <th>Total Value</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>Approved By</th>
                                    <th>Approved At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Adjustments by Reason -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i>
                        Adjustments by Reason (Last 30 Days)
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="adjustmentsByReasonTable">
                            <thead>
                                <tr>
                                    <th>Reason</th>
                                    <th>Count</th>
                                    <th>Total Value</th>
                                    <th>Average Value</th>
                                    <th>Last Adjustment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: 'Select User'
            });

            // Initialize DataTable
            var table = $('#stockAdjustmentsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('inventory.reports.stock-adjustments.data') }}',
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#status_filter').val();
                        d.created_by = $('#created_by_filter').val();
                        d.search = $('#search_filter').val();
                    }
                },
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
                        data: 'items_count',
                        name: 'items_count',
                        orderable: false
                    },
                    {
                        data: 'total_adjustment_value',
                        name: 'total_adjustment_value'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'creator_name',
                        name: 'creator.name'
                    },
                    {
                        data: 'approver_name',
                        name: 'approver.name'
                    },
                    {
                        data: 'approved_at',
                        name: 'approved_at'
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var actions = '<a href="/inventory/stock-adjustments/' + row.id +
                                '" class="btn btn-sm btn-info" title="View Details"><i class="fas fa-eye"></i></a>';

                            if (row.status === 'draft') {
                                actions +=
                                    ' <button class="btn btn-sm btn-success" onclick="approveAdjustment(' +
                                    row.id +
                                    ')" title="Approve"><i class="fas fa-check"></i></button>';
                            }

                            return actions;
                        }
                    }
                ],
                order: [
                    [1, 'desc']
                ], // Order by date descending
                pageLength: 25,
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                footerCallback: function(row, data, start, end, display) {
                    // Update summary cards
                    var totalAdjustments = 0;
                    var approvedAdjustments = 0;
                    var pendingAdjustments = 0;
                    var totalAdjustmentValue = 0;

                    api.rows({
                        page: 'current'
                    }).every(function() {
                        var data = this.data();
                        totalAdjustments++;

                        if (data[5] === 'approved') { // status column
                            approvedAdjustments++;
                        } else if (data[5] === 'draft') {
                            pendingAdjustments++;
                        }

                        var value = parseFloat(data[4].replace(/[^\d.-]/g, '')) ||
                        0; // total_adjustment_value column
                        totalAdjustmentValue += value;
                    });

                    $('#totalAdjustments').text(totalAdjustments);
                    $('#approvedAdjustments').text(approvedAdjustments);
                    $('#pendingAdjustments').text(pendingAdjustments);
                    $('#totalAdjustmentValue').text('Rp ' + totalAdjustmentValue.toLocaleString(
                    'id-ID', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }));
                }
            });

            // Load adjustments by reason
            function loadAdjustmentsByReason() {
                $.ajax({
                    url: '{{ route('inventory.reports.stock-adjustments.data') }}',
                    data: {
                        reason_summary: true,
                        draw: 1
                    },
                    success: function(response) {
                        if (response.reason_summary) {
                            var tbody = $('#adjustmentsByReasonTable tbody');
                            tbody.empty();

                            response.reason_summary.forEach(function(reason) {
                                var row = '<tr>' +
                                    '<td><strong>' + reason.reason + '</strong></td>' +
                                    '<td>' + reason.count + '</td>' +
                                    '<td>Rp ' + parseFloat(reason.total_value).toLocaleString(
                                        'id-ID', {
                                            minimumFractionDigits: 0,
                                            maximumFractionDigits: 0
                                        }) + '</td>' +
                                    '<td>Rp ' + parseFloat(reason.average_value).toLocaleString(
                                        'id-ID', {
                                            minimumFractionDigits: 0,
                                            maximumFractionDigits: 0
                                        }) + '</td>' +
                                    '<td>' + reason.last_adjustment_date + '</td>' +
                                    '</tr>';
                                tbody.append(row);
                            });
                        }
                    }
                });
            }

            // Apply filters function
            window.applyFilters = function() {
                table.ajax.reload();
                loadAdjustmentsByReason();
            };

            // Export function
            window.exportReport = function() {
                var params = new URLSearchParams();
                params.append('start_date', $('#start_date').val());
                params.append('end_date', $('#end_date').val());
                params.append('status', $('#status_filter').val());
                params.append('created_by', $('#created_by_filter').val());
                params.append('search', $('#search_filter').val());

                window.open('{{ route('inventory.reports.export.stock-adjustments') }}?' + params.toString(),
                    '_blank');
            };

            // Approve adjustment
            window.approveAdjustment = function(adjustmentId) {
                if (confirm('Are you sure you want to approve this stock adjustment?')) {
                    $.ajax({
                        url: '/inventory/stock-adjustments/' + adjustmentId + '/approve',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                table.ajax.reload();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr) {
                            var response = JSON.parse(xhr.responseText);
                            toastr.error(response.message || 'An error occurred');
                        }
                    });
                }
            };

            // Auto-apply filters when Enter is pressed in search field
            $('#search_filter').on('keypress', function(e) {
                if (e.which === 13) {
                    applyFilters();
                }
            });

            // Load initial data
            loadAdjustmentsByReason();
        });
    </script>
@endpush
