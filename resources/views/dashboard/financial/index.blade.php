@extends('layouts.main')

@section('title_page', 'Financial Dashboard')

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Financial Dashboard</li>
@endsection

@section('content')
    <!-- Financial Overview Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>Rp {{ number_format($dashboardData['revenue_vs_payments']['total_revenue'], 0, ',', '.') }}</h3>
                    <p>Total Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <a href="{{ route('reports.revenue.index') }}" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>Rp {{ number_format($dashboardData['revenue_vs_payments']['total_payments'], 0, ',', '.') }}</h3>
                    <p>Total Payments</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <a href="{{ route('reports.payment.index') }}" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>Rp {{ number_format($dashboardData['revenue_vs_payments']['deferred_revenue'], 0, ',', '.') }}</h3>
                    <p>Deferred Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('reports.revenue.deferred') }}" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ count($dashboardData['monthly_trends']) }}</h3>
                    <p>Months Tracked</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Last 12 Months <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Monthly Trends Chart -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-area"></i> Monthly Financial Trends
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-right">Revenue</th>
                                    <th class="text-right">Payments</th>
                                    <th class="text-right">Enrollments</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dashboardData['monthly_trends'] as $month)
                                    <tr>
                                        <td>{{ $month['month_name'] }}</td>
                                        <td class="text-right">Rp {{ number_format($month['revenue'], 0, ',', '.') }}</td>
                                        <td class="text-right">Rp {{ number_format($month['payments'], 0, ',', '.') }}</td>
                                        <td class="text-right">Rp {{ number_format($month['enrollments'], 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            @if ($month['revenue'] > 0)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">No Data</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods Distribution -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-credit-card"></i> Payment Methods Distribution
                    </h3>
                </div>
                <div class="card-body">
                    @if (count($dashboardData['payment_methods']) > 0)
                        @foreach ($dashboardData['payment_methods'] as $method)
                            <div class="progress-group">
                                <span class="float-left">{{ $method->payment_method ?: 'Unknown' }}</span>
                                <span class="float-right">Rp {{ number_format($method->total_amount, 0, ',', '.') }}</span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-primary"
                                        style="width: {{ $method->total_amount > 0 ? ($method->total_amount / collect($dashboardData['payment_methods'])->sum('total_amount')) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No payment data available</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie"></i> Revenue vs Payments Summary
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-chart-line"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Revenue</span>
                                    <span class="info-box-number">Rp
                                        {{ number_format($dashboardData['revenue_vs_payments']['total_revenue'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-money-bill-wave"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Payments</span>
                                    <span class="info-box-number">Rp
                                        {{ number_format($dashboardData['revenue_vs_payments']['total_payments'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Deferred Revenue</span>
                                    <span class="info-box-number">Rp
                                        {{ number_format($dashboardData['revenue_vs_payments']['deferred_revenue'], 0, ',', '.') }}</span>
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
                            <a href="{{ route('reports.revenue.index') }}" class="btn btn-success btn-block">
                                <i class="fas fa-chart-line"></i> Revenue Reports
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('reports.payment.index') }}" class="btn btn-info btn-block">
                                <i class="fas fa-money-bill-wave"></i> Payment Reports
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('journals.index') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-book"></i> Journal Entries
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('reports.trial-balance') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-balance-scale"></i> Trial Balance
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
