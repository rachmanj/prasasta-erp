@extends('layouts.main')

@section('title_page')
    AP Party Balances
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">AP Party Balances</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">AP Party Balances</h4>
                    <div class="btn-group float-right">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('reports.ap-balances', ['export' => 'csv']) }}">
                                <i class="fas fa-file-csv"></i> Export CSV
                            </a>
                            <a class="dropdown-item" href="{{ route('reports.ap-balances', ['export' => 'pdf']) }}">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm mb-0" id="ap-balances-table">
                            <thead>
                                <tr>
                                    <th>Vendor</th>
                                    <th class="text-right">Invoices</th>
                                    <th class="text-right">Payments</th>
                                    <th class="text-right">Balance</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows ?? [] as $r)
                                    <tr>
                                        <td>{{ $r['vendor_name'] ?? '#' . $r['vendor_id'] }}</td>
                                        <td class="text-right">{{ number_format($r['invoices'], 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($r['payments'], 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($r['balance'], 2, ',', '.') }}</td>
                                        <td><a href="{{ route('reports.ap-aging', ['vendor_id' => $r['vendor_id']]) }}"
                                                class="btn btn-xs btn-info">Reconcile</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            @if (!empty($totals))
                                <tfoot>
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-right">
                                            {{ number_format($totals['invoices'] ?? 0, 2, ',', '.') }}</th>
                                        <th class="text-right">
                                            {{ number_format($totals['payments'] ?? 0, 2, ',', '.') }}</th>
                                        <th class="text-right">
                                            {{ number_format($totals['balance'] ?? 0, 2, ',', '.') }}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
