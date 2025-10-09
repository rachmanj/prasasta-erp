@extends('layouts.main')

@section('title_page')
    AR Aging
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">AR Aging</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">AR Aging Report</h4>
                    <div class="btn-group float-right">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick="exportToCSV()">
                                <i class="fas fa-file-csv"></i> Export CSV
                            </a>
                            <a class="dropdown-item" href="#" onclick="exportToPDF()">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="get" class="mb-3 form-inline">
                        <div class="form-group mr-2">
                            <label class="mr-1">As of:</label>
                            <input type="date" name="as_of" value="{{ request('as_of', now()->toDateString()) }}"
                                class="form-control form-control-sm">
                        </div>
                        <div class="form-group mr-2">
                            <div class="form-check">
                                <input type="checkbox" name="overdue" value="1" class="form-check-input"
                                    {{ request('overdue') ? 'checked' : '' }} id="overdue">
                                <label class="form-check-label" for="overdue">
                                    Overdue only
                                </label>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fas fa-search"></i> Apply
                        </button>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm mb-0" id="ar-aging-table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th class="text-right">Current</th>
                                    <th class="text-right">31-60</th>
                                    <th class="text-right">61-90</th>
                                    <th class="text-right">91+</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody id="rows"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(async function() {
            const params = new URLSearchParams({
                as_of: '{{ request('as_of', now()->toDateString()) }}',
                overdue: '{{ request('overdue') ? 1 : 0 }}'
            });
            const res = await fetch(`{{ route('reports.ar-aging') }}?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                }
            });
            const data = await res.json();
            const tbody = document.getElementById('rows');
            tbody.innerHTML = '';
            data.rows.forEach(r => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
      <td>${(r.customer_name||('#'+r.customer_id))}</td>
      <td class="text-right">${formatNumber(r.current)}</td>
      <td class="text-right">${formatNumber(r.d31_60)}</td>
      <td class="text-right">${formatNumber(r.d61_90)}</td>
      <td class="text-right">${formatNumber(r.d91_plus)}</td>
      <td class="text-right">${formatNumber(r.total)}</td>
    `;
                tbody.appendChild(tr);
            });
        });

        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num);
        }

        function exportToCSV() {
            const params = new URLSearchParams({
                as_of: '{{ request('as_of', now()->toDateString()) }}',
                overdue: '{{ request('overdue') ? 1 : 0 }}',
                export: 'csv'
            });
            window.open(`{{ route('reports.ar-aging') }}?${params.toString()}`, '_blank');
        }

        function exportToPDF() {
            const params = new URLSearchParams({
                as_of: '{{ request('as_of', now()->toDateString()) }}',
                overdue: '{{ request('overdue') ? 1 : 0 }}',
                export: 'pdf'
            });
            window.open(`{{ route('reports.ar-aging') }}?${params.toString()}`, '_blank');
        }
    </script>
@endsection
