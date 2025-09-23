@extends('layouts.main')

@section('title_page')
    Reconcile Control Account
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('control-accounts.index') }}">Control Accounts</a></li>
    <li class="breadcrumb-item"><a href="{{ route('control-accounts.show', $controlAccount) }}">{{ $controlAccount->name }}</a></li>
    <li class="breadcrumb-item active">Reconcile</li>
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
                <!-- Control Account Info -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ $controlAccount->name }} ({{ $controlAccount->code }})</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Type:</strong> {{ ucfirst($controlAccount->type) }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Control Type:</strong>
                                        {{ ucfirst(str_replace('_', ' ', $controlAccount->control_type)) }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Frequency:</strong> {{ ucfirst($controlAccount->reconciliation_frequency) }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Tolerance:</strong>
                                        {{ number_format($controlAccount->tolerance_amount, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reconciliation Details -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Control Account Balance</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="fas fa-calculator"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Calculated Balance</span>
                                                <span
                                                    class="info-box-number">{{ number_format($balance->calculated_balance, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-list"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Subsidiary Total</span>
                                                <span
                                                    class="info-box-number">{{ number_format($balance->subsidiary_total, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="info-box">
                                            <span
                                                class="info-box-icon {{ abs($balance->variance_amount) <= $controlAccount->tolerance_amount ? 'bg-success' : 'bg-danger' }}">
                                                <i class="fas fa-balance-scale"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Variance Amount</span>
                                                <span
                                                    class="info-box-number">{{ number_format($balance->variance_amount, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Reconciliation Status</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="info-box">
                                            <span
                                                class="info-box-icon {{ $balance->reconciliation_status === 'reconciled' ? 'bg-success' : ($balance->reconciliation_status === 'variance' ? 'bg-danger' : 'bg-warning') }}">
                                                <i class="fas fa-check-circle"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Status</span>
                                                <span
                                                    class="info-box-number">{{ ucfirst($balance->reconciliation_status) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if ($balance->reconciled_at)
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <strong>Reconciled Date:</strong><br>
                                            {{ $balance->getReconciledDateFormatted() }}
                                        </div>
                                        <div class="col-6">
                                            <strong>Reconciled By:</strong><br>
                                            {{ $balance->getReconciledByFormatted() }}
                                        </div>
                                    </div>
                                @endif

                                @if ($balance->notes)
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <strong>Notes:</strong><br>
                                            {{ $balance->notes }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reconciliation Actions -->
                @if ($balance->reconciliation_status !== 'reconciled')
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Reconciliation Actions</h3>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('control-accounts.reconcile', $controlAccount) }}"
                                        method="POST">
                                        @csrf
                                        <input type="hidden" name="period" value="{{ $balance->period }}">

                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="notes">Notes</label>
                                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add reconciliation notes...">{{ old('notes') }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Action</label>
                                                    <div class="btn-group-vertical w-100" role="group">
                                                        <button type="submit" name="action" value="approve"
                                                            class="btn btn-success btn-lg"
                                                            {{ abs($balance->variance_amount) > $controlAccount->tolerance_amount ? 'disabled' : '' }}>
                                                            <i class="fas fa-check"></i> Approve Reconciliation
                                                        </button>
                                                        <button type="submit" name="action" value="reject"
                                                            class="btn btn-danger btn-lg">
                                                            <i class="fas fa-times"></i> Mark as Variance
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    @if (abs($balance->variance_amount) > $controlAccount->tolerance_amount)
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Warning:</strong> Variance amount
                                            ({{ number_format($balance->variance_amount, 2) }})
                                            exceeds tolerance limit
                                            ({{ number_format($controlAccount->tolerance_amount, 2) }}).
                                            Please investigate before approving.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <strong>Reconciled:</strong> This control account has been successfully reconciled for
                                {{ $balance->getPeriodFormatted() }}.
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Subsidiary Accounts Summary -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Subsidiary Accounts Summary</h3>
                                <div class="card-tools">
                                    <a href="{{ route('control-accounts.subsidiary-accounts', $controlAccount) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-list"></i> Manage Subsidiaries
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="fas fa-list"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Subsidiaries</span>
                                                <span
                                                    class="info-box-number">{{ $controlAccount->getActiveSubsidiaryCount() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Active Subsidiaries</span>
                                                <span
                                                    class="info-box-number">{{ $controlAccount->subsidiaryAccounts()->where('is_active', true)->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">With Transactions</span>
                                                <span
                                                    class="info-box-number">{{ $controlAccount->subsidiaryAccounts()->whereNotNull('last_transaction_date')->count() }}</span>
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Auto-refresh reconciliation data every 30 seconds
            setInterval(function() {
                location.reload();
            }, 30000);

            // Reconciliation action confirmation
            $('form').on('submit', function(e) {
                var action = $('button[type="submit"]:focus').val();
                var actionText = action === 'approve' ? 'approve this reconciliation' : 'mark this as variance';
                
                if (action === 'approve' || action === 'reject') {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: `Do you want to ${actionText}?`,
                        icon: action === 'approve' ? 'question' : 'warning',
                        showCancelButton: true,
                        confirmButtonColor: action === 'approve' ? '#28a745' : '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: `Yes, ${actionText}!`,
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Submit the form
                            this.submit();
                        }
                    });
                }
            });
        });
    </script>
@endpush
