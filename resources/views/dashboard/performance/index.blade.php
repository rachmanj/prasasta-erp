@extends('layouts.main')

@section('title_page', 'Performance Dashboard')

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Performance Dashboard</li>
@endsection

@section('content')
    <!-- Performance Overview Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $dashboardData['trainer_performance']->count() }}</h3>
                    <p>Active Trainers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <a href="{{ route('trainers.index') }}" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $dashboardData['course_completion']->count() }}</h3>
                    <p>Total Courses</p>
                </div>
                <div class="icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <a href="{{ route('courses.index') }}" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $dashboardData['course_completion']->avg('completion_rate') ? round($dashboardData['course_completion']->avg('completion_rate'), 1) : 0 }}%
                    </h3>
                    <p>Avg Completion Rate</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <a href="#" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>Rp {{ number_format($dashboardData['collection_performance']['total_due'], 0, ',', '.') }}</h3>
                    <p>Pending Collections</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <a href="{{ route('reports.payment.index') }}" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Trainer Performance -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chalkboard-teacher"></i> Top Performing Trainers
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if ($dashboardData['trainer_performance']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Trainer Name</th>
                                        <th>Batches</th>
                                        <th>Enrollments</th>
                                        <th>Revenue</th>
                                        <th>Performance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dashboardData['trainer_performance'] as $trainer)
                                        <tr>
                                            <td>{{ $trainer['trainer_name'] }}</td>
                                            <td>{{ $trainer['batch_count'] }}</td>
                                            <td>{{ $trainer['enrollments'] }}</td>
                                            <td>Rp {{ number_format($trainer['revenue'], 0, ',', '.') }}</td>
                                            <td>
                                                @if ($trainer['revenue'] > 0)
                                                    <span class="badge badge-success">Excellent</span>
                                                @else
                                                    <span class="badge badge-secondary">No Data</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No trainer performance data available</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie"></i> Collection Performance
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Pending Collections</span>
                                    <span class="info-box-number">Rp
                                        {{ number_format($dashboardData['collection_performance']['total_due'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Collected</span>
                                    <span class="info-box-number">Rp
                                        {{ number_format($dashboardData['collection_performance']['total_collected'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Overdue</span>
                                    <span class="info-box-number">Rp
                                        {{ number_format($dashboardData['collection_performance']['total_overdue'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Completion Analysis -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-graduation-cap"></i> Course Completion Analysis
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-book"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Courses</span>
                                    <span
                                        class="info-box-number">{{ $dashboardData['course_completion']->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Enrollments</span>
                                    <span
                                        class="info-box-number">{{ $dashboardData['course_completion']->sum('total_enrollments') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Completed</span>
                                    <span
                                        class="info-box-number">{{ $dashboardData['course_completion']->sum('completed_enrollments') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-percentage"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Avg Completion Rate</span>
                                    <span
                                        class="info-box-number">{{ $dashboardData['course_completion']->avg('completion_rate') ? round($dashboardData['course_completion']->avg('completion_rate'), 1) : 0 }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
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
                            <a href="{{ route('trainers.index') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-chalkboard-teacher"></i> Manage Trainers
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#" class="btn btn-info btn-block">
                                <i class="fas fa-chart-line"></i> Trainer Reports
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#" class="btn btn-success btn-block">
                                <i class="fas fa-graduation-cap"></i> Course Reports
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('reports.payment.index') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-money-bill-wave"></i> Payment Reports
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
