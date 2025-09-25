@extends('layouts.main')

@section('title_page')
    Course Financial Reports
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Course Financial Reports</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Available Reports</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">Course Profitability</h4>
                                            <p class="mb-0">Revenue & Cost Analysis</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-chart-line fa-2x"></i>
                                        </div>
                                    </div>
                                    <a href="{{ route('reports.course-financial.profitability') }}"
                                        class="btn btn-light btn-sm mt-2">
                                        View Report
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">Outstanding Receivables</h4>
                                            <p class="mb-0">Payment Tracking</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-money-bill-wave fa-2x"></i>
                                        </div>
                                    </div>
                                    <a href="{{ route('reports.course-financial.outstanding-receivables') }}"
                                        class="btn btn-light btn-sm mt-2">
                                        View Report
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">Revenue Recognition</h4>
                                            <p class="mb-0">Deferred vs Recognized</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-calendar-check fa-2x"></i>
                                        </div>
                                    </div>
                                    <a href="{{ route('reports.course-financial.revenue-recognition') }}"
                                        class="btn btn-light btn-sm mt-2">
                                        View Report
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">Payment Collection</h4>
                                            <p class="mb-0">Collection Performance</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-credit-card fa-2x"></i>
                                        </div>
                                    </div>
                                    <a href="{{ route('reports.course-financial.payment-collection') }}"
                                        class="btn btn-light btn-sm mt-2">
                                        View Report
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="total-revenue">-</h4>
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
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="outstanding-ar">-</h4>
                            <p class="mb-0">Outstanding AR</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-receipt fa-2x"></i>
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
                            <h4 class="mb-0" id="deferred-revenue">-</h4>
                            <p class="mb-0">Deferred Revenue</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
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
                            <h4 class="mb-0" id="overdue-amount">-</h4>
                            <p class="mb-0">Overdue Amount</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
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
            // Load quick stats (placeholder - would need actual API endpoints)
            $('#total-revenue').text('Rp 0');
            $('#outstanding-ar').text('Rp 0');
            $('#deferred-revenue').text('Rp 0');
            $('#overdue-amount').text('Rp 0');
        });
    </script>
@endpush
