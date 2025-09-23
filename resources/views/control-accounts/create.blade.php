@extends('layouts.main')

@section('title_page')
    Create Control Account
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('control-accounts.index') }}">Control Accounts</a></li>
    <li class="breadcrumb-item active">Create</li>
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
                            <form action="{{ route('control-accounts.store') }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="code">Account Code <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('code') is-invalid @enderror" id="code"
                                                    name="code" value="{{ old('code') }}" placeholder="e.g., 1.1.4"
                                                    required>
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
                                                    name="name" value="{{ old('name') }}"
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
                                                    <option value="asset" {{ old('type') == 'asset' ? 'selected' : '' }}>
                                                        Asset</option>
                                                    <option value="liability"
                                                        {{ old('type') == 'liability' ? 'selected' : '' }}>Liability
                                                    </option>
                                                    <option value="equity" {{ old('type') == 'equity' ? 'selected' : '' }}>
                                                        Equity</option>
                                                    <option value="revenue"
                                                        {{ old('type') == 'revenue' ? 'selected' : '' }}>Revenue</option>
                                                    <option value="expense"
                                                        {{ old('type') == 'expense' ? 'selected' : '' }}>Expense</option>
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
                                                        {{ old('control_type') == 'ar' ? 'selected' : '' }}>Accounts
                                                        Receivable</option>
                                                    <option value="ap"
                                                        {{ old('control_type') == 'ap' ? 'selected' : '' }}>Accounts
                                                        Payable</option>
                                                    <option value="inventory"
                                                        {{ old('control_type') == 'inventory' ? 'selected' : '' }}>
                                                        Inventory</option>
                                                    <option value="fixed_assets"
                                                        {{ old('control_type') == 'fixed_assets' ? 'selected' : '' }}>Fixed
                                                        Assets</option>
                                                    <option value="cash"
                                                        {{ old('control_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                                                    <option value="other"
                                                        {{ old('control_type') == 'other' ? 'selected' : '' }}>Other
                                                    </option>
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
                                                        {{ old('reconciliation_frequency') == 'daily' ? 'selected' : '' }}>
                                                        Daily</option>
                                                    <option value="weekly"
                                                        {{ old('reconciliation_frequency') == 'weekly' ? 'selected' : '' }}>
                                                        Weekly</option>
                                                    <option value="monthly"
                                                        {{ old('reconciliation_frequency') == 'monthly' ? 'selected' : '' }}>
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
                                                    value="{{ old('tolerance_amount', 0) }}" step="0.01" min="0"
                                                    required>
                                                @error('tolerance_amount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                            rows="3" placeholder="Optional description">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Create Control Account
                                    </button>
                                    <a href="{{ route('control-accounts.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Help</h3>
                            </div>
                            <div class="card-body">
                                <h5>Control Account Types:</h5>
                                <ul>
                                    <li><strong>AR:</strong> Accounts Receivable - Customer balances</li>
                                    <li><strong>AP:</strong> Accounts Payable - Vendor balances</li>
                                    <li><strong>Inventory:</strong> Stock and inventory items</li>
                                    <li><strong>Fixed Assets:</strong> Equipment and property</li>
                                    <li><strong>Cash:</strong> Bank and cash accounts</li>
                                    <li><strong>Other:</strong> Custom control accounts</li>
                                </ul>

                                <h5>Reconciliation Frequency:</h5>
                                <ul>
                                    <li><strong>Daily:</strong> High-volume accounts</li>
                                    <li><strong>Weekly:</strong> Medium-volume accounts</li>
                                    <li><strong>Monthly:</strong> Standard reconciliation</li>
                                </ul>

                                <h5>Tolerance Amount:</h5>
                                <p>Maximum acceptable variance between control balance and subsidiary total.</p>
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
