@extends('layouts.main')

@section('title', 'GL Detail')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">GL Detail</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">GL Detail</li>
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
                            <h3 class="card-title">GL Detail</h3>
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
                                    <label class="mr-1">From:</label>
                                    <input type="date" name="from" class="form-control form-control-sm"
                                        value="{{ now()->startOfMonth()->toDateString() }}" />
                                </div>
                                <div class="form-group mr-2">
                                    <label class="mr-1">To:</label>
                                    <input type="date" name="to" class="form-control form-control-sm"
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
                                            <th>Date</th>
                                            <th>Journal</th>
                                            <th>Account</th>
                                            <th class="text-right">Debit</th>
                                            <th class="text-right">Credit</th>
                                            <th>Memo</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
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
            const params = new URLSearchParams({
                from: form.from.value,
                to: form.to.value
            });
            const res = await fetch(`/reports/gl-detail?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            const data = await res.json();
            tbody.innerHTML = '';
            data.rows.forEach(r => {
                const tr = document.createElement('tr');
                tr.innerHTML =
                    `<td>${formatDate(r.date)}</td><td>${r.journal_desc ?? ''}</td><td>${r.account_code} - ${r.account_name}</td><td class="text-right">${formatNumber(r.debit)}</td><td class="text-right">${formatNumber(r.credit)}</td><td>${r.memo ?? ''}</td>`;
                tbody.appendChild(tr);
            });
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

        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = date.getDate().toString().padStart(2, '0');
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const month = months[date.getMonth()];
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }

        function exportToCSV() {
            const form = document.getElementById('form');
            const params = new URLSearchParams({
                from: form.from.value,
                to: form.to.value,
                export: 'csv'
            });
            window.open(`/reports/gl-detail?${params.toString()}`, '_blank');
        }

        function exportToPDF() {
            const form = document.getElementById('form');
            const params = new URLSearchParams({
                from: form.from.value,
                to: form.to.value,
                export: 'pdf'
            });
            window.open(`/reports/gl-detail?${params.toString()}`, '_blank');
        }
    </script>
@endsection
