@extends('layouts.main')

@section('title_page')
    Trial Balance
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Trial Balance</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Trial Balance</h4>
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
                    <form id="form" class="mb-3 form-inline">
                        <div class="form-group mr-2">
                            <label class="mr-1">As of:</label>
                            <input type="date" name="date" class="form-control form-control-sm"
                                value="{{ now()->toDateString() }}" />
                        </div>
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fas fa-search"></i> Load
                        </button>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm mb-0" id="trial-balance-table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th class="text-right">Debit</th>
                                    <th class="text-right">Credit</th>
                                    <th class="text-right">Balance</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2">Totals</th>
                                    <th class="text-right" id="tdebit">0</th>
                                    <th class="text-right" id="tcredit">0</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const form = document.getElementById('form');
        const tbody = document.querySelector('#trial-balance-table tbody');
        async function load() {
            const date = form.date.value;
            const res = await fetch(`/reports/trial-balance?date=${date}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            const data = await res.json();
            tbody.innerHTML = '';
            let tdebit = 0,
                tcredit = 0;
            data.rows.forEach(r => {
                tdebit += r.debit;
                tcredit += r.credit;
                const tr = document.createElement('tr');
                tr.innerHTML =
                    `<td>${r.code}</td><td>${r.name}</td><td class="text-right">${formatNumber(r.debit)}</td><td class="text-right">${formatNumber(r.credit)}</td><td class="text-right">${formatNumber(r.balance)}</td>`;
                tbody.appendChild(tr);
            });
            document.getElementById('tdebit').innerText = formatNumber(tdebit);
            document.getElementById('tcredit').innerText = formatNumber(tcredit);
        }
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            load();
        });
        load();

        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num);
        }

        function exportToCSV() {
            const form = document.getElementById('form');
            const params = new URLSearchParams({
                date: form.date.value,
                export: 'csv'
            });
            window.open(`/reports/trial-balance?${params.toString()}`, '_blank');
        }

        function exportToPDF() {
            const form = document.getElementById('form');
            const params = new URLSearchParams({
                date: form.date.value,
                export: 'pdf'
            });
            window.open(`/reports/trial-balance?${params.toString()}`, '_blank');
        }
    </script>
@endsection
