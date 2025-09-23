@extends('layouts.main')

@section('title_page')
    Edit Control Account
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('control-accounts.index') }}">Control Accounts</a></li>
    <li class="breadcrumb-item"><a href="{{ route('control-accounts.show', $controlAccount) }}">{{ $controlAccount->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
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
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Control Account Information</h3>
                            </div>
                            <form action="{{ route('control-accounts.update', $controlAccount) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="code">Account Code <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('code') is-invalid @enderror" id="code"
                                                    name="code" value="{{ old('code', $controlAccount->code) }}"
                                                    placeholder="e.g., 1.1.4" required>
                                                @error('code')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Account Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror" id="name"
                                                    name="name" value="{{ old('name', $controlAccount->name) }}"
                                                    placeholder="e.g., Accounts Receivable" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="type">Account Type <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control @error('type') is-invalid @enderror"
                                                    id="type" name="type" required>
                                                    <option value="">Select Account Type</option>
                                                    <option value="asset"
                                                        {{ old('type', $controlAccount->type) == 'asset' ? 'selected' : '' }}>
                                                        Asset</option>
                                                    <option value="liability"
                                                        {{ old('type', $controlAccount->type) == 'liability' ? 'selected' : '' }}>
                                                        Liability</option>
                                                    <option value="equity"
                                                        {{ old('type', $controlAccount->type) == 'equity' ? 'selected' : '' }}>
                                                        Equity</option>
                                                    <option value="revenue"
                                                        {{ old('type', $controlAccount->type) == 'revenue' ? 'selected' : '' }}>
                                                        Revenue</option>
                                                    <option value="expense"
                                                        {{ old('type', $controlAccount->type) == 'expense' ? 'selected' : '' }}>
                                                        Expense</option>
                                                </select>
                                                @error('type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="control_type">Control Type <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control @error('control_type') is-invalid @enderror"
                                                    id="control_type" name="control_type" required>
                                                    <option value="">Select Control Type</option>
                                                    <option value="ar"
                                                        {{ old('control_type', $controlAccount->control_type) == 'ar' ? 'selected' : '' }}>
                                                        Accounts Receivable</option>
                                                    <option value="ap"
                                                        {{ old('control_type', $controlAccount->control_type) == 'ap' ? 'selected' : '' }}>
                                                        Accounts Payable</option>
                                                    <option value="inventory"
                                                        {{ old('control_type', $controlAccount->control_type) == 'inventory' ? 'selected' : '' }}>
                                                        Inventory</option>
                                                    <option value="fixed_assets"
                                                        {{ old('control_type', $controlAccount->control_type) == 'fixed_assets' ? 'selected' : '' }}>
                                                        Fixed Assets</option>
                                                    <option value="cash"
                                                        {{ old('control_type', $controlAccount->control_type) == 'cash' ? 'selected' : '' }}>
                                                        Cash</option>
                                                    <option value="other"
                                                        {{ old('control_type', $controlAccount->control_type) == 'other' ? 'selected' : '' }}>
                                                        Other</option>
                                                </select>
                                                @error('control_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reconciliation_frequency">Reconciliation Frequency <span
                                                        class="text-danger">*</span></label>
                                                <select
                                                    class="form-control @error('reconciliation_frequency') is-invalid @enderror"
                                                    id="reconciliation_frequency" name="reconciliation_frequency" required>
                                                    <option value="">Select Frequency</option>
                                                    <option value="daily"
                                                        {{ old('reconciliation_frequency', $controlAccount->reconciliation_frequency) == 'daily' ? 'selected' : '' }}>
                                                        Daily</option>
                                                    <option value="weekly"
                                                        {{ old('reconciliation_frequency', $controlAccount->reconciliation_frequency) == 'weekly' ? 'selected' : '' }}>
                                                        Weekly</option>
                                                    <option value="monthly"
                                                        {{ old('reconciliation_frequency', $controlAccount->reconciliation_frequency) == 'monthly' ? 'selected' : '' }}>
                                                        Monthly</option>
                                                </select>
                                                @error('reconciliation_frequency')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tolerance_amount">Tolerance Amount <span
                                                        class="text-danger">*</span></label>
                                                <input type="number"
                                                    class="form-control @error('tolerance_amount') is-invalid @enderror"
                                                    id="tolerance_amount" name="tolerance_amount"
                                                    value="{{ old('tolerance_amount', $controlAccount->tolerance_amount) }}"
                                                    step="0.01" min="0" required>
                                                @error('tolerance_amount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="is_active"
                                                        name="is_active" value="1"
                                                        {{ old('is_active', $controlAccount->is_active) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="is_active">Active</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                            rows="3" placeholder="Optional description">{{ old('description', $controlAccount->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Control Account
                                    </button>
                                    <a href="{{ route('control-accounts.show', $controlAccount) }}"
                                        class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </form>
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

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Warning</h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Important:</strong> Changing control account settings may affect existing
                                    reconciliations and subsidiary accounts.
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
            // Form validation
            $('form').on('submit', function(e) {
                let isValid = true;

                // Check required fields
                $('input[required], select[required]').each(function() {
                    if (!$(this).val()) {
                        $(this).addClass('is-invalid');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    toastr.error('Please fill in all required fields.');
                }
            });
        });
    </script>
@endpush
