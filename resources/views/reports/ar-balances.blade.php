@extends('layouts.main')

@section('title', 'AR Party Balances')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">AR Party Balances</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">AR Party Balances</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">AR Party Balances</h3>
                            <div class="card-tools">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm dropdown-toggle"
                                        data-toggle="dropdown">
                                        <i class="fas fa-download"></i> Export
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                            href="{{ route('reports.ar-balances', ['export' => 'csv']) }}">
                                            <i class="fas fa-file-csv"></i> Export CSV
                                        </a>
                                        <a class="dropdown-item"
                                            href="{{ route('reports.ar-balances', ['export' => 'pdf']) }}">
                                            <i class="fas fa-file-pdf"></i> Export PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Customer</th>
                                            <th class="text-right">Invoices</th>
                                            <th class="text-right">Receipts</th>
                                            <th class="text-right">Balance</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rows ?? [] as $r)
                                            <tr>
                                                <td>{{ $r['customer_name'] ?? '#' . $r['customer_id'] }}</td>
                                                <td class="text-right">{{ number_format($r['invoices'], 2, ',', '.') }}</td>
                                                <td class="text-right">{{ number_format($r['receipts'], 2, ',', '.') }}</td>
                                                <td class="text-right">{{ number_format($r['balance'], 2, ',', '.') }}</td>
                                                <td><a href="{{ route('reports.ar-aging', ['customer_id' => $r['customer_id']]) }}"
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
                                                    {{ number_format($totals['receipts'] ?? 0, 2, ',', '.') }}</th>
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
        </div>
    </section>
@endsection
