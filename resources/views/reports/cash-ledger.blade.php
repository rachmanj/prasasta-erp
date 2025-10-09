@extends('layouts.main')

@section('title_page')
    Cash Ledger
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Cash Ledger</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Cash Ledger</h4>
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
                            <label class="mr-1">From:</label>
                            <input type="date" name="from" value="{{ request('from') }}"
                                class="form-control form-control-sm">
                        </div>
                        <div class="form-group mr-2">
                            <label class="mr-1">To:</label>
                            <input type="date" name="to" value="{{ request('to') }}"
                                class="form-control form-control-sm">
                        </div>
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fas fa-search"></i> Apply
                        </button>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm mb-0" id="cash-ledger-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th class="text-right">Debit</th>
                                    <th class="text-right">Credit</th>
                                    <th class="text-right">Balance</th>
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
                from: '{{ request('from') }}',
                to: '{{ request('to') }}'
            });
            const res = await fetch(`{{ route('reports.cash-ledger') }}?${params.toString()}`, {
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
      <td>${formatDate(r.date)}</td>
      <td>${r.description || ''}</td>
      <td class="text-right">${formatNumber(r.debit)}</td>
      <td class="text-right">${formatNumber(r.credit)}</td>
      <td class="text-right">${formatNumber(r.balance)}</td>
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

        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = date.getDate().toString().padStart(2, '0');
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const month = months[date.getMonth()];
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }

        function exportToCSV() {
            const params = new URLSearchParams({
                from: '{{ request('from') }}',
                to: '{{ request('to') }}',
                export: 'csv'
            });
            window.open(`{{ route('reports.cash-ledger') }}?${params.toString()}`, '_blank');
        }

        function exportToPDF() {
            const params = new URLSearchParams({
                from: '{{ request('from') }}',
                to: '{{ request('to') }}',
                export: 'pdf'
            });
            window.open(`{{ route('reports.cash-ledger') }}?${params.toString()}`, '_blank');
        }
    </script>
@endsection
