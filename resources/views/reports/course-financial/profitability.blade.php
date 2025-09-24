@extends('layouts.app')

@section('title', 'Course Profitability Report')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Course Profitability Report</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('reports.course-financial.index') }}">Course
                                    Financial</a></li>
                            <li class="breadcrumb-item active">Profitability</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
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
                                        <label>Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date">
                                    </div>
                                </div>
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
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                                            <button type="button" class="btn btn-secondary"
                                                id="reset-filters">Reset</button>
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
                        <h3 class="card-title">Course Profitability Analysis</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-success btn-sm" id="export-excel">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="profitability-table">
                                <thead>
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>Category</th>
                                        <th>Base Price</th>
                                        <th>Total Batches</th>
                                        <th>Total Enrollments</th>
                                        <th>Total Revenue</th>
                                        <th>Recognized Revenue</th>
                                        <th>Deferred Revenue</th>
                                        <th>Revenue per Enrollment</th>
                                        <th>Utilization Rate</th>
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
                                        <h4 class="mb-0" id="total-courses">0</h4>
                                        <p class="mb-0">Total Courses</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-graduation-cap fa-2x"></i>
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
                                        <h4 class="mb-0" id="total-revenue">Rp 0</h4>
                                        <p class="mb-0">Total Revenue</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-dollar-sign fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0" id="total-enrollments">0</h4>
                                        <p class="mb-0">Total Enrollments</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-users fa-2x"></i>
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
                                        <h4 class="mb-0" id="avg-utilization">0%</h4>
                                        <p class="mb-0">Avg Utilization</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-chart-pie fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#profitability-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('reports.course-financial.profitability.data') }}',
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.category_id = $('#category_id').val();
                    }
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
                        data: 'category_name',
                        name: 'category_name'
                    },
                    {
                        data: 'base_price_formatted',
                        name: 'base_price'
                    },
                    {
                        data: 'total_batches',
                        name: 'total_batches'
                    },
                    {
                        data: 'total_enrollments',
                        name: 'total_enrollments'
                    },
                    {
                        data: 'total_revenue_formatted',
                        name: 'total_revenue'
                    },
                    {
                        data: 'recognized_revenue_formatted',
                        name: 'recognized_revenue'
                    },
                    {
                        data: 'deferred_revenue',
                        name: 'deferred_revenue'
                    },
                    {
                        data: 'revenue_per_enrollment',
                        name: 'revenue_per_enrollment'
                    },
                    {
                        data: 'utilization_rate',
                        name: 'utilization_rate'
                    }
                ],
                order: [
                    [6, 'desc']
                ], // Sort by total revenue descending
                pageLength: 25,
                responsive: true
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
                // Implementation for Excel export
                alert('Excel export functionality would be implemented here');
            });

            // Update summary cards when data loads
            table.on('draw', function() {
                updateSummaryCards();
            });

            function updateSummaryCards() {
                var data = table.rows({
                    filter: 'applied'
                }).data();
                var totalCourses = data.length;
                var totalRevenue = 0;
                var totalEnrollments = 0;
                var totalUtilization = 0;

                for (var i = 0; i < data.length; i++) {
                    totalRevenue += parseFloat(data[i].total_revenue) || 0;
                    totalEnrollments += parseInt(data[i].total_enrollments) || 0;
                    totalUtilization += parseFloat(data[i].utilization_rate.replace('%', '')) || 0;
                }

                $('#total-courses').text(totalCourses);
                $('#total-revenue').text('Rp ' + totalRevenue.toLocaleString('id-ID'));
                $('#total-enrollments').text(totalEnrollments);
                $('#avg-utilization').text((totalUtilization / totalCourses).toFixed(1) + '%');
            }
        });
    </script>
@endpush
