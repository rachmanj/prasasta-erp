@extends('layouts.main')

@section('title_page')
    Control Account Details
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('control-accounts.index') }}">Control Accounts</a></li>
    <li class="breadcrumb-item active">{{ $controlAccount->name }}</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            @if (session('success'))
                <script>
                    toastr.success(@json(session('success')));
                </script>
            @endif

            @if (session('error'))
                <script>
                    toastr.error(@json(session('error')));
                </script>
            @endif

            @if (session('warning'))
                <script>
                    toastr.warning(@json(session('warning')));
                </script>
            @endif

            @if (session('info'))
                <script>
                    toastr.info(@json(session('info')));
                </script>
            @endif
                <!-- Control Account Overview -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Account Information</h3>
                                <div class="card-tools">
                                    @can('control_accounts.edit')
                                        <a href="{{ route('control-accounts.edit', $controlAccount) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endcan
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Code:</strong></td>
                                                <td>{{ $controlAccount->code }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Name:</strong></td>
                                                <td>{{ $controlAccount->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Type:</strong></td>
                                                <td>{{ ucfirst($controlAccount->type) }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Control Type:</strong></td>
                                                <td>{{ ucfirst(str_replace('_', ' ', $controlAccount->control_type)) }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td>
                                                    @if ($controlAccount->is_active)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Reconciliation Frequency:</strong></td>
                                                <td>{{ ucfirst($controlAccount->reconciliation_frequency) }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tolerance Amount:</strong></td>
                                                <td>{{ number_format($controlAccount->tolerance_amount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Last Reconciliation:</strong></td>
                                                <td>{{ $controlAccount->getLastReconciliationDate() ?? 'Never' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                @if ($controlAccount->description)
                                    <div class="row">
                                        <div class="col-12">
                                            <strong>Description:</strong><br>
                                            {{ $controlAccount->description }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Current Status</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="fas fa-calculator"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Current Balance</span>
                                                <span
                                                    class="info-box-number">{{ number_format($controlAccount->getCurrentBalance(), 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-list"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Subsidiary Total</span>
                                                <span
                                                    class="info-box-number">{{ number_format($controlAccount->getSubsidiaryTotal(), 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="info-box">
                                            <span
                                                class="info-box-icon {{ abs($controlAccount->calculateVariance()) <= $controlAccount->tolerance_amount ? 'bg-success' : 'bg-danger' }}">
                                                <i class="fas fa-balance-scale"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Variance</span>
                                                <span
                                                    class="info-box-number">{{ number_format($controlAccount->calculateVariance(), 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="info-box">
                                            <span
                                                class="info-box-icon {{ $controlAccount->getReconciliationStatus() === 'reconciled' ? 'bg-success' : ($controlAccount->getReconciliationStatus() === 'variance' ? 'bg-danger' : 'bg-warning') }}">
                                                <i class="fas fa-check-circle"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Reconciliation Status</span>
                                                <span
                                                    class="info-box-number">{{ ucfirst($controlAccount->getReconciliationStatus()) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('control-accounts.reconciliation', $controlAccount) }}"
                                        class="btn btn-primary">
                                        <i class="fas fa-balance-scale"></i> Reconcile
                                    </a>
                                    <a href="{{ route('control-accounts.subsidiary-accounts', $controlAccount) }}"
                                        class="btn btn-info">
                                        <i class="fas fa-list"></i> Manage Subsidiaries
                                    </a>
                                    <a href="{{ route('control-accounts.balances', $controlAccount) }}"
                                        class="btn btn-success">
                                        <i class="fas fa-chart-line"></i> Balance History
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subsidiary Accounts Summary -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Subsidiary Accounts</h3>
                                <div class="card-tools">
                                    <a href="{{ route('control-accounts.subsidiary-accounts', $controlAccount) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-list"></i> View All
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="fas fa-list"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Subsidiaries</span>
                                                <span
                                                    class="info-box-number">{{ $controlAccount->subsidiaryAccounts()->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Active</span>
                                                <span
                                                    class="info-box-number">{{ $controlAccount->subsidiaryAccounts()->where('is_active', true)->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">With Transactions</span>
                                                <span
                                                    class="info-box-number">{{ $controlAccount->subsidiaryAccounts()->whereNotNull('last_transaction_date')->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-danger"><i
                                                    class="fas fa-exclamation-triangle"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Inactive</span>
                                                <span
                                                    class="info-box-number">{{ $controlAccount->subsidiaryAccounts()->where('is_active', false)->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Recent Transactions</h3>
                            </div>
                            <div class="card-body">
                                @if ($recentTransactions->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Description</th>
                                                    <th>Debit</th>
                                                    <th>Credit</th>
                                                    <th>Memo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($recentTransactions as $transaction)
                                                    <tr>
                                                        <td>{{ $transaction->date }}</td>
                                                        <td>{{ $transaction->description }}</td>
                                                        <td class="text-right">
                                                            {{ $transaction->debit > 0 ? number_format($transaction->debit, 2) : '-' }}
                                                        </td>
                                                        <td class="text-right">
                                                            {{ $transaction->credit > 0 ? number_format($transaction->credit, 2) : '-' }}
                                                        </td>
                                                        <td>{{ $transaction->memo }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        No recent transactions found for this control account.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </section>
@endsection
