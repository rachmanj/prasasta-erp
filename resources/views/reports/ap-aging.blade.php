@extends('layouts.main')

@section('title', 'AP Aging')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">AP Aging</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">AP Aging</li>
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
                            <h3 class="card-title">AP Aging</h3>
                            <div class="card-tools">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm dropdown-toggle"
                                        data-toggle="dropdown">
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
                        </div>
                        <div class="card-body">
                            <form method="get" class="mb-3 form-inline">
                                <div class="form-group mr-2">
                                    <label class="mr-1">As of:</label>
                                    <input type="date" name="as_of"
                                        value="{{ request('as_of', now()->toDateString()) }}"
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
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Vendor</th>
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
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(async function() {
            const params = new URLSearchParams({
                as_of: '{{ request('as_of', now()->toDateString()) }}',
                overdue: '{{ request('overdue') ? 1 : 0 }}'
            });
            const res = await fetch(`{{ route('reports.ap-aging') }}?${params.toString()}`, {
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
      <td>${(r.vendor_name||('#'+r.vendor_id))}</td>
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
            window.open(`{{ route('reports.ap-aging') }}?${params.toString()}`, '_blank');
        }

        function exportToPDF() {
            const params = new URLSearchParams({
                as_of: '{{ request('as_of', now()->toDateString()) }}',
                overdue: '{{ request('overdue') ? 1 : 0 }}',
                export: 'pdf'
            });
            window.open(`{{ route('reports.ap-aging') }}?${params.toString()}`, '_blank');
        }
    </script>
@endpush
