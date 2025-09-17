@extends('layouts.main')

@section('title_page', 'Executive Dashboard')

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Executive Dashboard</li>
@endsection

@section('content')
    <!-- Revenue Metrics -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>Rp {{ number_format($dashboardData['revenue']['current'], 0, ',', '.') }}</h3>
                    <p>Revenue This Month</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Growth: {{ $dashboardData['revenue']['growth'] }}% <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $dashboardData['enrollments']['current'] }}</h3>
                    <p>New Enrollments</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Growth: {{ $dashboardData['enrollments']['growth'] }}% <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>Rp {{ number_format($dashboardData['payments']['current'], 0, ',', '.') }}</h3>
                    <p>Payments Collected</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Growth: {{ $dashboardData['payments']['growth'] }}% <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $dashboardData['overdue']['count'] }}</h3>
                    <p>Overdue Payments</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Amount: Rp {{ number_format($dashboardData['overdue']['amount'], 0, ',', '.') }} <i
                        class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Revenue Trend</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-success">Rp
                                    {{ number_format($dashboardData['revenue']['current'], 0, ',', '.') }}</h4>
                                <p class="text-muted">Current Month</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-info">Rp
                                    {{ number_format($dashboardData['revenue']['previous'], 0, ',', '.') }}</h4>
                                <p class="text-muted">Previous Month</p>
                            </div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="float-left">Growth Rate</span>
                        <span class="float-right">{{ $dashboardData['revenue']['growth'] }}%</span>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success"
                                style="width: {{ abs($dashboardData['revenue']['growth']) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Enrollment Trend</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-info">{{ $dashboardData['enrollments']['current'] }}</h4>
                                <p class="text-muted">Current Month</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-secondary">{{ $dashboardData['enrollments']['previous'] }}</h4>
                                <p class="text-muted">Previous Month</p>
                            </div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="float-left">Growth Rate</span>
                        <span class="float-right">{{ $dashboardData['enrollments']['growth'] }}%</span>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-info"
                                style="width: {{ abs($dashboardData['enrollments']['growth']) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Collection Summary -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Collection Summary</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Collected This Month</span>
                                    <span class="info-box-number">Rp
                                        {{ number_format($dashboardData['payments']['current'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Previous Month</span>
                                    <span class="info-box-number">Rp
                                        {{ number_format($dashboardData['payments']['previous'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Overdue Amount</span>
                                    <span class="info-box-number">Rp
                                        {{ number_format($dashboardData['overdue']['amount'], 0, ',', '.') }}</span>
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
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('courses.index') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-book"></i> Manage Courses
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('enrollments.index') }}" class="btn btn-success btn-block">
                                <i class="fas fa-user-plus"></i> New Enrollment
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('reports.payment.index') }}" class="btn btn-info btn-block">
                                <i class="fas fa-chart-bar"></i> Payment Reports
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('reports.revenue.index') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-chart-line"></i> Revenue Reports
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
