@extends('layouts.main')

@section('title', 'Trial Balance')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Trial Balance</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Trial Balance</li>
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
                            <h3 class="card-title">Trial Balance</h3>
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
                                <table class="table table-bordered table-striped table-sm" id="tb">
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
        </div>
    </section>
    <script>
        const form = document.getElementById('form');
        const tbody = document.querySelector('#tb tbody');
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
