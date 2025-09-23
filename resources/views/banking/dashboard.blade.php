@extends('layouts.main')

@section('title_page')
    Banking Dashboard
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Banking</li>
@endsection

@section('content')
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="daily-cash-in">Rp 0</h3>
                    <p>Daily Cash In</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-down"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="daily-cash-out">Rp 0</h3>
                    <p>Daily Cash Out</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="daily-net-flow">Rp 0</h3>
                    <p>Daily Net Flow</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3 id="monthly-net-flow">Rp 0</h3>
                    <p>Monthly Net Flow</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Account Balances -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cash/Bank Account Balances</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th class="text-right">Balance</th>
                                </tr>
                            </thead>
                            <tbody id="account-balances">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Transactions</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="recent-transactions">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Expenses -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top Expenses (This Month)</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="top-expenses">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Revenues -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top Revenues (This Month)</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="top-revenues">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            loadDashboardData();

            // Refresh data every 5 minutes
            setInterval(loadDashboardData, 300000);
        });

        function loadDashboardData() {
            $.ajax({
                url: '{{ route('banking.dashboard.data') }}',
                method: 'GET',
                success: function(data) {
                    // Update summary cards
                    $('#daily-cash-in').text(formatCurrency(data.daily.cash_in));
                    $('#daily-cash-out').text(formatCurrency(data.daily.cash_out));
                    $('#daily-net-flow').text(formatCurrency(data.daily.net_flow));
                    $('#monthly-net-flow').text(formatCurrency(data.monthly.net_flow));

                    // Update account balances
                    let accountBalancesHtml = '';
                    data.account_balances.forEach(function(account) {
                        const balanceClass = account.balance >= 0 ? 'text-success' : 'text-danger';
                        accountBalancesHtml += `
                    <tr>
                        <td>${account.account.code} - ${account.account.name}</td>
                        <td class="text-right ${balanceClass}">${formatCurrency(account.balance)}</td>
                    </tr>
                `;
                    });
                    $('#account-balances').html(accountBalancesHtml);

                    // Update recent transactions
                    let recentTransactionsHtml = '';
                    data.recent_transactions.forEach(function(transaction) {
                        const typeClass = transaction.type === 'cash_in' ? 'badge-success' :
                            'badge-warning';
                        const typeText = transaction.type === 'cash_in' ? 'Cash In' : 'Cash Out';
                        recentTransactionsHtml += `
                    <tr>
                        <td>${formatDate(transaction.date)}</td>
                        <td><span class="badge ${typeClass}">${typeText}</span></td>
                        <td>${transaction.description || transaction.voucher_number}</td>
                        <td class="text-right">${formatCurrency(transaction.total_amount)}</td>
                    </tr>
                `;
                    });
                    $('#recent-transactions').html(recentTransactionsHtml);

                    // Update top expenses
                    let topExpensesHtml = '';
                    data.top_expenses.forEach(function(expense) {
                        topExpensesHtml += `
                    <tr>
                        <td>${expense.account_name}</td>
                        <td class="text-right">${formatCurrency(expense.total_amount)}</td>
                    </tr>
                `;
                    });
                    $('#top-expenses').html(topExpensesHtml);

                    // Update top revenues
                    let topRevenuesHtml = '';
                    data.top_revenues.forEach(function(revenue) {
                        topRevenuesHtml += `
                    <tr>
                        <td>${revenue.account_name}</td>
                        <td class="text-right">${formatCurrency(revenue.total_amount)}</td>
                    </tr>
                `;
                    });
                    $('#top-revenues').html(topRevenuesHtml);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading dashboard data:', error);
                }
            });
        }

        function formatCurrency(amount) {
            return 'Rp ' + parseFloat(amount).toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }
    </script>
@endpush
