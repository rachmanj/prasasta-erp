@extends('layouts.main')

@section('title_page', 'Operational Dashboard')

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Operational Dashboard</li>
@endsection

@section('content')
    <!-- Operational Overview Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ count($dashboardData['capacity_utilization']) }}</h3>
                    <p>Total Batches</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('course-batches.index') }}" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $dashboardData['capacity_utilization']->sum('enrolled') }}</h3>
                    <p>Total Enrollments</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <a href="{{ route('enrollments.index') }}" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $dashboardData['capacity_utilization']->avg('utilization') ? round($dashboardData['capacity_utilization']->avg('utilization'), 1) : 0 }}%
                    </h3>
                    <p>Avg Utilization</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <a href="#" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $dashboardData['capacity_utilization']->sum('capacity') - $dashboardData['capacity_utilization']->sum('enrolled') }}
                    </h3>
                    <p>Available Slots</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <a href="{{ route('course-batches.create') }}" class="small-box-footer">
                    Create Batch <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Upcoming Batches -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt"></i> Upcoming Batches
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if (count($dashboardData['upcoming_batches']) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Batch Code</th>
                                        <th>Course</th>
                                        <th>Start Date</th>
                                        <th>Capacity</th>
                                        <th>Enrolled</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dashboardData['upcoming_batches'] as $batch)
                                        <tr>
                                            <td>{{ $batch->batch_code }}</td>
                                            <td>{{ $batch->course->name ?? 'N/A' }}</td>
                                            <td>{{ $batch->start_date->format('M d, Y') }}</td>
                                            <td>{{ $batch->capacity }}</td>
                                            <td>{{ $batch->enrollments->where('status', 'enrolled')->count() }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $batch->status === 'planned' ? 'warning' : ($batch->status === 'ongoing' ? 'success' : 'secondary') }}">
                                                    {{ ucfirst($batch->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No upcoming batches scheduled</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> Daily Enrollment Trends
                    </h3>
                </div>
                <div class="card-body">
                    @if (count($dashboardData['enrollment_trends']) > 0)
                        @foreach ($dashboardData['enrollment_trends'] as $day)
                            <div class="progress-group">
                                <span class="float-left">{{ $day['date'] }}</span>
                                <span class="float-right">{{ $day['count'] }}</span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-primary"
                                        style="width: {{ $day['count'] > 0 ? ($day['count'] / max(array_column($dashboardData['enrollment_trends'], 'count'))) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No enrollment data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Course Performance Overview -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-graduation-cap"></i> Course Performance Overview
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if ($dashboardData['top_courses']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Course Name</th>
                                        <th>Total Enrollments</th>
                                        <th>Total Revenue</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dashboardData['top_courses'] as $course)
                                        <tr>
                                            <td>{{ $course['course_name'] }}</td>
                                            <td>{{ $course['enrollments'] }}</td>
                                            <td>Rp {{ number_format($course['revenue'], 0, ',', '.') }}</td>
                                            <td>
                                                <span class="badge badge-success">Active</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No course data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('course-batches.create') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i> Create New Batch
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('enrollments.create') }}" class="btn btn-success btn-block">
                                <i class="fas fa-user-plus"></i> New Enrollment
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('courses.index') }}" class="btn btn-info btn-block">
                                <i class="fas fa-graduation-cap"></i> Manage Courses
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#" class="btn btn-warning btn-block">
                                <i class="fas fa-chart-pie"></i> Capacity Reports
                            </a>
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
            // Auto-refresh dashboard data every 5 minutes
            setInterval(function() {
                location.reload();
            }, 300000);
        });
    </script>
@endpush
