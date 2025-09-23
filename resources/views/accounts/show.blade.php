@extends('layouts.main')

@section('title_page')
    Account Details
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('accounts.index') }}">Accounts</a></li>
    <li class="breadcrumb-item active">{{ $account->name }}</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h4 class="mb-0">Account Details: {{ $account->name }}</h4>
                <div>
                    @can('accounts.manage')
                        <a href="{{ route('accounts.edit', $account->id) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    @endcan
                    <a href="{{ route('accounts.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <!-- Account Information Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Code:</strong></td>
                                    <td>{{ $account->code }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $account->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $account->type === 'asset' ? 'success' : ($account->type === 'liability' ? 'danger' : 'info') }}">
                                            {{ strtoupper($account->type) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Postable:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $account->is_postable ? 'success' : 'secondary' }}">
                                            {{ $account->is_postable ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Parent Account:</strong></td>
                                    <td>
                                        @if ($account->parent_id)
                                            {{ optional(\DB::table('accounts')->find($account->parent_id))->code }} -
                                            {{ optional(\DB::table('accounts')->find($account->parent_id))->name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Control Type:</strong></td>
                                    <td>
                                        @if ($account->control_type)
                                            <span class="badge badge-warning">{{ ucfirst($account->control_type) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Control Account:</strong></td>
                                    <td>
                                        @if ($account->is_control_account)
                                            <span class="badge badge-primary">Yes</span>
                                        @else
                                            <span class="text-muted">No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Description:</strong></td>
                                    <td>{{ $account->description ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Statistics Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Account Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-chart-line"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Current Balance</span>
                                    <span class="info-box-number" id="current-balance">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-plus"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Debits</span>
                                    <span class="info-box-number" id="total-debits">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-minus"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Credits</span>
                                    <span class="info-box-number" id="total-credits">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-list"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Transaction Count</span>
                                    <span class="info-box-number" id="transaction-count">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction History Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Transaction History</h5>
                </div>
                <div class="card-body">
                    <!-- Date Range Filter -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="date_from">From Date:</label>
                            <input type="date" class="form-control" id="date_from" name="date_from">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to">To Date:</label>
                            <input type="date" class="form-control" id="date_to" name="date_to">
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <div>
                                <button type="button" class="btn btn-primary btn-sm" id="filter-transactions">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" id="clear-filter">
                                    <i class="fas fa-times"></i> Clear
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <div>
                                <button type="button" class="btn btn-success btn-sm" id="export-excel">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <table id="transactions-table" class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Posting Date</th>
                                <th>Create Date</th>
                                <th>Journal Number</th>
                                <th>Origin Document</th>
                                <th>Description</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Balance</th>
                                <th>Created By</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Set default date range (last 2 months)
            const today = new Date();
            const twoMonthsAgo = new Date(today.getFullYear(), today.getMonth() - 2, today.getDate());

            $('#date_from').val(twoMonthsAgo.toISOString().split('T')[0]);
            $('#date_to').val(today.toISOString().split('T')[0]);

            // Initialize DataTable
            const table = $('#transactions-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('accounts.transactions.data', $account->id) }}',
                    data: function(d) {
                        d.date_from = $('#date_from').val();
                        d.date_to = $('#date_to').val();
                    }
                },
                columns: [{
                        data: 'posting_date',
                        name: 'posting_date'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'journal_number',
                        name: 'journal_number'
                    },
                    {
                        data: 'origin_document',
                        name: 'origin_document'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'debit',
                        name: 'debit',
                        className: 'text-right'
                    },
                    {
                        data: 'credit',
                        name: 'credit',
                        className: 'text-right'
                    },
                    {
                        data: 'running_balance',
                        name: 'running_balance',
                        className: 'text-right'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    }
                ],
                order: [
                    [0, 'asc']
                ], // Order by posting date ascending (oldest first)
                pageLength: 25,
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            // Filter button click
            $('#filter-transactions').click(function() {
                table.ajax.reload();
            });

            // Clear filter button click
            $('#clear-filter').click(function() {
                const today = new Date();
                const twoMonthsAgo = new Date(today.getFullYear(), today.getMonth() - 2, today.getDate());

                $('#date_from').val(twoMonthsAgo.toISOString().split('T')[0]);
                $('#date_to').val(today.toISOString().split('T')[0]);
                table.ajax.reload();
            });

            // Export Excel button click
            $('#export-excel').click(function() {
                const dateFrom = $('#date_from').val();
                const dateTo = $('#date_to').val();

                // Create export URL with current filter parameters
                let exportUrl = '{{ route('accounts.transactions.export', $account->id) }}';
                const params = new URLSearchParams();

                if (dateFrom) {
                    params.append('date_from', dateFrom);
                }
                if (dateTo) {
                    params.append('date_to', dateTo);
                }

                if (params.toString()) {
                    exportUrl += '?' + params.toString();
                }

                // Open export URL in new tab
                window.open(exportUrl, '_blank');
            });

            // Load account statistics
            loadAccountStatistics();
        });

        function loadAccountStatistics() {
            // This would typically be an AJAX call to get account statistics
            // For now, we'll show placeholder values
            $('#current-balance').text('Rp 0.00');
            $('#total-debits').text('Rp 0.00');
            $('#total-credits').text('Rp 0.00');
            $('#transaction-count').text('0');
        }
    </script>
@endsection
