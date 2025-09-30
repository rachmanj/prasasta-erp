@extends('layouts.main')

@section('title_page')
    Course Profitability Report
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.course-financial.index') }}">Course Financial Reports</a></li>
    <li class="breadcrumb-item active">Profitability</li>
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
            <h3 class="card-title">Course Profitability Analysis</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-success btn-sm" id="export-csv">
                    <i class="fas fa-file-csv"></i> Export CSV
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
                            <th>Recognition Status</th>
                            <th>Recognition Date</th>
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

    <!-- Revenue Recognition Summary Cards -->
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="total-recognized">Rp 0</h4>
                            <p class="mb-0">Total Recognized</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="total-deferred">Rp 0</h4>
                            <p class="mb-0">Total Deferred</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
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
                            <h4 class="mb-0" id="recognition-rate">0%</h4>
                            <p class="mb-0">Recognition Rate</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-percentage fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="courses-recognized">0</h4>
                            <p class="mb-0">Courses Recognized</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-graduation-cap fa-2x"></i>
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
                            data: 'recognition_status',
                            name: 'recognition_status'
                        },
                        {
                            data: 'recognition_date',
                            name: 'recognition_date'
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
                $('#export-csv').on('click', function() {
                    const startDate = $('#start_date').val();
                    const endDate = $('#end_date').val();
                    const categoryId = $('#category_id').val();

                    // Build export URL with current filters
                    let exportUrl = '{{ route('reports.course-financial.profitability.export') }}';
                    const params = new URLSearchParams();

                    if (startDate) params.append('start_date', startDate);
                    if (endDate) params.append('end_date', endDate);
                    if (categoryId) params.append('category_id', categoryId);

                    if (params.toString()) {
                        exportUrl += '?' + params.toString();
                    }

                    // Open export URL in new window
                    window.open(exportUrl, '_blank');
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
                    var totalRecognized = 0;
                    var totalDeferred = 0;
                    var totalEnrollments = 0;
                    var totalUtilization = 0;
                    var coursesRecognized = 0;

                    for (var i = 0; i < data.length; i++) {
                        totalRevenue += parseFloat(data[i].total_revenue) || 0;
                        totalRecognized += parseFloat(data[i].recognized_revenue) || 0;
                        totalDeferred += parseFloat(data[i].deferred_revenue.replace(/[^\d]/g, '')) || 0;
                        totalEnrollments += parseInt(data[i].total_enrollments) || 0;
                        totalUtilization += parseFloat(data[i].utilization_rate.replace('%', '')) || 0;

                        // Count courses with recognition status
                        if (data[i].recognition_status && data[i].recognition_status.includes('Fully Recognized')) {
                            coursesRecognized++;
                        }
                    }

                    var recognitionRate = totalRevenue > 0 ? (totalRecognized / totalRevenue) * 100 : 0;

                    // Update basic summary cards
                    $('#total-courses').text(totalCourses);
                    $('#total-revenue').text('Rp ' + totalRevenue.toLocaleString('id-ID'));
                    $('#total-enrollments').text(totalEnrollments);
                    $('#avg-utilization').text((totalUtilization / totalCourses).toFixed(1) + '%');

                    // Update revenue recognition summary cards
                    $('#total-recognized').text('Rp ' + totalRecognized.toLocaleString('id-ID'));
                    $('#total-deferred').text('Rp ' + totalDeferred.toLocaleString('id-ID'));
                    $('#recognition-rate').text(recognitionRate.toFixed(1) + '%');
                    $('#courses-recognized').text(coursesRecognized);
                }
            });
        </script>
    @endpush
