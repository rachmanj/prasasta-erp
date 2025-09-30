@extends('layouts.main')

@section('title', 'Profit & Loss Report')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Profit & Loss Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('reports.trial-balance') }}">Reports</a></li>
                        <li class="breadcrumb-item active">Profit & Loss</li>
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
                            <h3 class="card-title">
                                <i class="fas fa-chart-line"></i>
                                Profit & Loss Statement
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="form" class="mb-3 form-inline">
                                <div class="form-group mr-2">
                                    <label class="mr-1">From:</label>
                                    <input type="date" name="from" class="form-control form-control-sm" id="from-date"
                                        value="{{ now()->startOfMonth()->toDateString() }}" />
                                </div>
                                <div class="form-group mr-2">
                                    <label class="mr-1">To:</label>
                                    <input type="date" name="to" class="form-control form-control-sm" id="to-date"
                                        value="{{ now()->toDateString() }}" />
                                </div>
                                <button class="btn btn-primary btn-sm" type="submit">
                                    <i class="fas fa-search"></i> Generate Report
                                </button>
                                <button type="button" class="btn btn-success btn-sm ml-2" onclick="exportToCSV()">
                                    <i class="fas fa-download"></i> Export CSV
                                </button>
                            </form>

                            <!-- Loading indicator -->
                            <div id="loading" class="text-center" style="display: none;">
                                <i class="fas fa-spinner fa-spin fa-2x"></i>
                                <p>Generating Profit & Loss report...</p>
                            </div>

                            <!-- Report content -->
                            <div id="report-content" style="display: none;">
                                <!-- Report header -->
                                <div id="report-header" class="mb-4">
                                    <h4 id="report-title">Profit & Loss Statement</h4>
                                    <p id="report-period" class="text-muted"></p>
                                </div>

                                <!-- Income Section -->
                                <div class="card mb-3">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-arrow-up"></i> Revenue & Income
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped" id="income-table">
                                                <thead>
                                                    <tr>
                                                        <th>Account Code</th>
                                                        <th>Account Name</th>
                                                        <th class="text-right">Amount (IDR)</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr class="font-weight-bold">
                                                        <th colspan="2">Total Revenue & Income</th>
                                                        <th class="text-right text-success" id="total-income">0</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Expenses Section -->
                                <div class="card mb-3">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-arrow-down"></i> Expenses
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped" id="expense-table">
                                                <thead>
                                                    <tr>
                                                        <th>Account Code</th>
                                                        <th>Account Name</th>
                                                        <th class="text-right">Amount (IDR)</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr class="font-weight-bold">
                                                        <th colspan="2">Total Expenses</th>
                                                        <th class="text-right text-danger" id="total-expense">0</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Net Profit/Loss Summary -->
                                <div class="card">
                                    <div class="card-header" id="summary-header">
                                        <h5 class="card-title mb-0" id="summary-title">
                                            <i class="fas fa-chart-line"></i> Net Profit/Loss
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-success">
                                                        <i class="fas fa-arrow-up"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Income</span>
                                                        <span class="info-box-number" id="summary-income">Rp 0</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-danger">
                                                        <i class="fas fa-arrow-down"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Expenses</span>
                                                        <span class="info-box-number" id="summary-expense">Rp 0</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-box" id="profit-box">
                                                    <span class="info-box-icon bg-success">
                                                        <i class="fas fa-chart-line"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Net Profit/Loss</span>
                                                        <span class="info-box-number" id="summary-profit">Rp 0</span>
                                                        <span class="info-box-text" id="profit-margin">0% margin</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        const form = document.getElementById('form');
        const loading = document.getElementById('loading');
        const reportContent = document.getElementById('report-content');

        // Date defaults
        const defaultFrom = '{{ now()->startOfMonth()->toDateString() }}';
        const defaultTo = '{{ now()->toDateString() }}';

        async function loadReport() {
            const from = document.getElementById('from-date').value || defaultFrom;
            const to = document.getElementById('to-date').value || defaultTo;

            // Validate dates
            if (from && to && from > to) {
                alert('Start date must be before end date');
                return;
            }

            loading.style.display = 'block';
            reportContent.style.display = 'none';

            try {
                const res = await fetch(`/reports/profit-loss?from=${from}&to=${to}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                if (!res.ok) {
                    throw new Error('HTTP error! status: ' + res.status);
                }

                const data = await res.json();
                populateReport(data);

            } catch (error) {
                console.error('Error loading report:', error);
                alert('Error loading Profit & Loss report: ' + error.message);
            } finally {
                loading.style.display = 'none';
            }
        }

        function populateReport(data) {
            // Set report header
            document.getElementById('report-period').textContent =
                `From ${data.period.from} to ${data.period.to}`;

            // Populate income table
            const incomeTbody = document.querySelector('#income-table tbody');
            incomeTbody.innerHTML = '';

            if (data.income.rows.length === 0) {
                incomeTbody.innerHTML =
                    '<tr><td colspan="3" class="text-center text-muted">No income data found for this period</td></tr>';
            } else {
                data.income.rows.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.code}</td>
                        <td>${row.name}</td>
                        <td class="text-right">${formatCurrency(row.amount)}</td>
                    `;
                    incomeTbody.appendChild(tr);
                });
            }

            // Populate expense table
            const expenseTbody = document.querySelector('#expense-table tbody');
            expenseTbody.innerHTML = '';

            if (data.expense.rows.length === 0) {
                expenseTbody.innerHTML =
                    '<tr><td colspan="3" class="text-center text-muted">No expense data found for this period</td></tr>';
            } else {
                data.expense.rows.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.code}</td>
                        <td>${row.name}</td>
                        <td class="text-right">${formatCurrency(row.amount)}</td>
                    `;
                    expenseTbody.appendChild(tr);
                });
            }

            // Update totals
            document.getElementById('total-income').textContent = formatCurrency(data.summary.total_income);
            document.getElementById('total-expense').textContent = formatCurrency(data.summary.total_expense);

            // Update summary
            document.getElementById('summary-income').textContent = formatCurrency(data.summary.total_income);
            document.getElementById('summary-expense').textContent = formatCurrency(data.summary.total_expense);
            document.getElementById('summary-profit').textContent = formatCurrency(data.summary.net_profit);
            document.getElementById('profit-margin').textContent = `${data.summary.profit_margin}% margin`;

            // Update profit/loss styling
            const profitBox = document.getElementById('profit-box');
            const profitIcon = profitBox.querySelector('.info-box-icon');
            const summaryTitle = document.getElementById('summary-title');

            if (data.summary.net_profit > 0) {
                profitBox.className = 'info-box';
                profitBox.querySelector('.info-box-icon').className = 'info-box-icon bg-success';
                profitBox.querySelector('.info-box-icon i').className = 'fas fa-trending-up';
                profitBox.querySelector('.info-box-text').innerHTML = 'Net Profit';
                summaryTitle.innerHTML = '<i class="fas fa-trending-up"></i> Net Profit';
            } else if (data.summary.net_profit < 0) {
                profitBox.className = 'info-box';
                profitBox.querySelector('.info-box-icon').className = 'info-box-icon bg-danger';
                profitBox.querySelector('.info-box-icon i').className = 'fas fa-trending-down';
                profitBox.querySelector('.info-box-text').innerHTML = 'Net Loss';
                summaryTitle.innerHTML = '<i class="fas fa-trending-down"></i> Net Loss';
            } else {
                profitBox.className = 'info-box';
                profitBox.querySelector('.info-box-icon').className = 'info-box-icon bg-warning';
                profitBox.querySelector('.info-box-icon i').className = 'fas fa-equals';
                profitBox.querySelector('.info-box-text').innerHTML = 'Break Even';
                summaryTitle.innerHTML = '<i class="fas fa-equals"></i> Break Even';
            }

            // Show report content
            reportContent.style.display = 'block';
        }

        function formatCurrency(num) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(num).replace('IDR', 'Rp');
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('id-ID');
        }

        function exportToCSV() {
            const from = document.getElementById('from-date').value || defaultFrom;
            const to = document.getElementById('to-date').value || defaultTo;

            if (!from || !to) {
                alert('Please select both start and end dates before exporting');
                return;
            }

            // Create CSV content
            let csvContent = "Profit & Loss Report\n";
            csvContent += `From: ${from}, To: ${to}\n\n`;
            csvContent += "ACCOUNT CODE,ACCOUNT NAME,AMOUNT,REGION\n";

            // Add income accounts
            const incomeRows = document.querySelectorAll('#income-table tbody tr');
            incomeRows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length === 3 && !cells[0].textContent.includes('No income data')) {
                    csvContent +=
                        `${cells[0].textContent},${cells[1].textContent},${cells[2].textContent.replace(/[^\d.-]/g, '')},Revenue\n`;
                }
            });

            // Add expense accounts
            const expenseRows = document.querySelectorAll('#expense-table tbody tr');
            expenseRows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length === 3 && !cells[0].textContent.includes('No expense data')) {
                    csvContent +=
                        `${cells[0].textContent},${cells[1].textContent},${cells[2].textContent.replace(/[^\d.-]/g, '')},Expense\n`;
                }
            });

            // Summary
            csvContent += "\n,,SUMMARY,\n";
            csvContent +=
                `Total Income,,${document.getElementById('summary-income').textContent.replace(/[^\d.-]/g, '')},\n`;
            csvContent +=
                `Total Expenses,,${document.getElementById('summary-expense').textContent.replace(/[^\d.-]/g, '')},\n`;
            csvContent +=
                `Net Profit/Loss,,${document.getElementById('summary-profit').textContent.replace(/[^\d.-]/g, '')},\n`;

            // Download CSV
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", `profit_loss_${from}_to_${to}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Event listeners
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            loadReport();
        });

        // Date validation
        document.getElementById('from-date').addEventListener('change', function() {
            const toDate = document.getElementById('to-date');
            if (toDate.value && this.value > toDate.value) {
                toDate.value = this.value;
            }
        });

        document.getElementById('to-date').addEventListener('change', function() {
            const fromDate = document.getElementById('from-date');
            if (fromDate.value && fromDate.value > this.value) {
                this.value = fromDate.value;
            }
        });

        // Load initial report
        document.addEventListener('DOMContentLoaded', function() {
            loadReport();
        });
    </script>
@endsection
