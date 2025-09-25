@extends('layouts.main')

@section('title_page')
    Outstanding Receivables Report
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.course-financial.index') }}">Course Financial Reports</a></li>
    <li class="breadcrumb-item active">Outstanding Receivables</li>
@endsection

@section('content')
    <!-- Filters -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filters</h3>
        </div>
        <div class="card-body">
            <form id="filter-form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Course Category</label>
                            <select class="form-control" id="category_id" name="category_id">
                                <option value="">All Categories</option>
                                <option value="1">Digital Marketing</option>
                                <option value="2">Data Analytics</option>
                                <option value="3">Project Management</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Overdue Status</label>
                            <select class="form-control" id="overdue_status" name="overdue_status">
                                <option value="">All</option>
                                <option value="overdue">Overdue Only</option>
                                <option value="current">Current Only</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Batch Status</label>
                            <select class="form-control" id="batch_status" name="batch_status">
                                <option value="">All</option>
                                <option value="planned">Planned</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                                <button type="button" class="btn btn-secondary" id="reset-filters">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Outstanding Receivables</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-success btn-sm" id="export-excel">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                <button type="button" class="btn btn-warning btn-sm" id="send-reminders">
                    <i class="fas fa-envelope"></i> Send Reminders
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="receivables-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Batch</th>
                            <th>Enrollment Date</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Outstanding</th>
                            <th>Overdue Amount</th>
                            <th>Days Overdue</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="total-students">0</h4>
                            <p class="mb-0">Total Students</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="total-outstanding">Rp 0</h4>
                            <p class="mb-0">Total Outstanding</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-receipt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="total-overdue">Rp 0</h4>
                            <p class="mb-0">Total Overdue</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="overdue-count">0</h4>
                            <p class="mb-0">Overdue Accounts</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#receivables-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('reports.course-financial.outstanding-receivables.data') }}',
                    data: function(d) {
                        d.category_id = $('#category_id').val();
                        d.overdue_status = $('#overdue_status').val();
                        d.batch_status = $('#batch_status').val();
                    }
                },
                columns: [{
                        data: 'student_name',
                        name: 'student_name'
                    },
                    {
                        data: 'course_name',
                        name: 'course_name'
                    },
                    {
                        data: 'batch_code',
                        name: 'batch_code'
                    },
                    {
                        data: 'enrollment_date',
                        name: 'enrollment_date'
                    },
                    {
                        data: 'total_amount_formatted',
                        name: 'total_amount'
                    },
                    {
                        data: 'paid_amount_formatted',
                        name: 'paid_amount'
                    },
                    {
                        data: 'outstanding_amount_formatted',
                        name: 'outstanding_amount'
                    },
                    {
                        data: 'overdue_amount_formatted',
                        name: 'overdue_amount'
                    },
                    {
                        data: 'days_overdue',
                        name: 'days_overdue'
                    },
                    {
                        data: 'overdue_badge',
                        name: 'overdue_badge',
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
                    [7, 'desc']
                ], // Sort by overdue amount descending
                pageLength: 25,
                responsive: true,
                columnDefs: [{
                    targets: -1,
                    render: function(data, type, row) {
                        return `
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary" onclick="viewDetails(${row.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-success" onclick="processPayment(${row.id})">
                                <i class="fas fa-money-bill"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-warning" onclick="sendReminder(${row.id})">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </div>
                    `;
                    }
                }]
            });

            // Filter form submission
            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                table.draw();
            });

            // Reset filters
            $('#reset-filters').on('click', function() {
                $('#filter-form')[0].reset();
                table.draw();
            });

            // Export functionality
            $('#export-excel').on('click', function() {
                alert('Excel export functionality would be implemented here');
            });

            // Send reminders
            $('#send-reminders').on('click', function() {
                alert('Send reminders functionality would be implemented here');
            });

            // Update summary cards when data loads
            table.on('draw', function() {
                updateSummaryCards();
            });

            function updateSummaryCards() {
                var data = table.rows({
                    filter: 'applied'
                }).data();
                var totalStudents = data.length;
                var totalOutstanding = 0;
                var totalOverdue = 0;
                var overdueCount = 0;

                for (var i = 0; i < data.length; i++) {
                    totalOutstanding += parseFloat(data[i].outstanding_amount) || 0;
                    totalOverdue += parseFloat(data[i].overdue_amount) || 0;
                    if (parseFloat(data[i].overdue_amount) > 0) {
                        overdueCount++;
                    }
                }

                $('#total-students').text(totalStudents);
                $('#total-outstanding').text('Rp ' + totalOutstanding.toLocaleString('id-ID'));
                $('#total-overdue').text('Rp ' + totalOverdue.toLocaleString('id-ID'));
                $('#overdue-count').text(overdueCount);
            }
        });

        // Action functions
        function viewDetails(enrollmentId) {
            // Implementation for viewing enrollment details
            alert('View details for enrollment ID: ' + enrollmentId);
        }

        function processPayment(enrollmentId) {
            // Implementation for processing payment
            alert('Process payment for enrollment ID: ' + enrollmentId);
        }

        function sendReminder(enrollmentId) {
            // Implementation for sending payment reminder
            alert('Send reminder for enrollment ID: ' + enrollmentId);
        }
    </script>
@endpush
