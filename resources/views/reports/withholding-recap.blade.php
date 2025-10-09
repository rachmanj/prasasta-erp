@extends('layouts.main')

@section('title_page')
    Withholding Recap
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Withholding Recap</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Withholding Recap</h4>
                    <div class="btn-group float-right">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item"
                                href="{{ route('reports.withholding-recap', array_filter(['from' => request('from'), 'to' => request('to'), 'vendor_id' => request('vendor_id'), 'export' => 'csv'])) }}">
                                <i class="fas fa-file-csv"></i> Export CSV
                            </a>
                            <a class="dropdown-item"
                                href="{{ route('reports.withholding-recap', array_filter(['from' => request('from'), 'to' => request('to'), 'vendor_id' => request('vendor_id'), 'export' => 'pdf'])) }}">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-inline mb-3" method="get">
                        <div class="form-group mr-2">
                            <label class="mr-1">From:</label>
                            <input type="date" name="from" class="form-control form-control-sm"
                                value="{{ request('from') }}" />
                        </div>
                        <div class="form-group mr-2">
                            <label class="mr-1">To:</label>
                            <input type="date" name="to" class="form-control form-control-sm"
                                value="{{ request('to') }}" />
                        </div>
                        <div class="form-group mr-2">
                            <label class="mr-1">Vendor ID:</label>
                            <input type="number" name="vendor_id" class="form-control form-control-sm"
                                value="{{ request('vendor_id') }}" placeholder="Vendor ID" />
                        </div>
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fas fa-search"></i> Apply
                        </button>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm mb-0" id="withholding-recap-table">
                            <thead>
                                <tr>
                                    <th>Vendor</th>
                                    <th class="text-right">Withholding Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows ?? [] as $r)
                                    <tr>
                                        <td>{{ $r['vendor_name'] ?? '#' . $r['vendor_id'] }}</td>
                                        <td class="text-right">
                                            {{ number_format($r['withholding_total'], 2, ',', '.') }}</td>
                                        <td>
                                            <a class="btn btn-xs btn-info"
                                                href="{{ route('purchase-invoices.index') }}?q=&vendor_id={{ $r['vendor_id'] }}">View
                                                PIs</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            @if (!empty($totals))
                                <tfoot>
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-right">
                                            {{ number_format($totals['withholding_total'] ?? 0, 2, ',', '.') }}
                                        </th>
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
