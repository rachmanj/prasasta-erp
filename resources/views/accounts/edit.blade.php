@extends('layouts.main')

@section('title_page')
    Account
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('accounts.index') }}">Accounts</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Edit Account</h3>
            </div>
            <form method="POST" action="{{ route('accounts.update', $account->id) }}">
                @csrf
                @method('PATCH')
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Code</label>
                            <input type="text" name="code" class="form-control" value="{{ $account->code }}"
                                required />
                        </div>
                        <div class="form-group col-md-8">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $account->name }}"
                                required />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Type</label>
                            <select name="type" class="form-control" required>
                                <option value="asset" {{ $account->type === 'asset' ? 'selected' : '' }}>Asset</option>
                                <option value="liability" {{ $account->type === 'liability' ? 'selected' : '' }}>Liability
                                </option>
                                <option value="net_assets" {{ $account->type === 'net_assets' ? 'selected' : '' }}>Net
                                    Assets</option>
                                <option value="income" {{ $account->type === 'income' ? 'selected' : '' }}>Income</option>
                                <option value="expense" {{ $account->type === 'expense' ? 'selected' : '' }}>Expense
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Postable</label>
                            <select name="is_postable" class="form-control" required>
                                <option value="1" {{ $account->is_postable ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ !$account->is_postable ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Parent</label>
                            <select name="parent_id" class="form-control">
                                <option value="">(none)</option>
                                @foreach ($parents as $p)
                                    <option value="{{ $p->id }}"
                                        {{ $account->parent_id === $p->id ? 'selected' : '' }}>
                                        {{ $p->code }} - {{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Control Account Configuration -->
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Control Account Configuration</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <div class="form-check">
                                                <input type="checkbox" name="is_control_account" id="is_control_account"
                                                    class="form-check-input" value="1"
                                                    {{ $account->is_control_account ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_control_account">
                                                    <strong>Mark as Control Account</strong>
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">Enable this to create a control account for
                                                reconciliation purposes</small>
                                        </div>
                                    </div>

                                    <div id="control_account_fields"
                                        style="display: {{ $account->is_control_account ? 'block' : 'none' }};">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Control Type</label>
                                                <select name="control_type" class="form-control">
                                                    <option value="">Select Control Type</option>
                                                    <option value="ap"
                                                        {{ $account->control_type === 'ap' ? 'selected' : '' }}>Accounts
                                                        Payable</option>
                                                    <option value="ar"
                                                        {{ $account->control_type === 'ar' ? 'selected' : '' }}>Accounts
                                                        Receivable</option>
                                                    <option value="cash"
                                                        {{ $account->control_type === 'cash' ? 'selected' : '' }}>Cash &
                                                        Bank</option>
                                                    <option value="inventory"
                                                        {{ $account->control_type === 'inventory' ? 'selected' : '' }}>
                                                        Inventory</option>
                                                    <option value="fixed_assets"
                                                        {{ $account->control_type === 'fixed_assets' ? 'selected' : '' }}>
                                                        Fixed Assets</option>
                                                    <option value="other"
                                                        {{ $account->control_type === 'other' ? 'selected' : '' }}>Other
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Reconciliation Frequency</label>
                                                <select name="reconciliation_frequency" class="form-control">
                                                    <option value="daily"
                                                        {{ $account->reconciliation_frequency === 'daily' ? 'selected' : '' }}>
                                                        Daily</option>
                                                    <option value="weekly"
                                                        {{ $account->reconciliation_frequency === 'weekly' ? 'selected' : '' }}>
                                                        Weekly</option>
                                                    <option value="monthly"
                                                        {{ $account->reconciliation_frequency === 'monthly' ? 'selected' : '' }}>
                                                        Monthly</option>
                                                    <option value="quarterly"
                                                        {{ $account->reconciliation_frequency === 'quarterly' ? 'selected' : '' }}>
                                                        Quarterly</option>
                                                    <option value="yearly"
                                                        {{ $account->reconciliation_frequency === 'yearly' ? 'selected' : '' }}>
                                                        Yearly</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Tolerance Amount</label>
                                                <input type="number" name="tolerance_amount" class="form-control"
                                                    step="0.01" min="0"
                                                    value="{{ $account->tolerance_amount ?? '0.00' }}">
                                                <small class="form-text text-muted">Maximum variance amount for
                                                    reconciliation</small>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Description</label>
                                                <textarea name="description" class="form-control" rows="2"
                                                    placeholder="Optional description for this control account">{{ $account->description ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary">Save</button>
                    <a href="{{ route('accounts.index') }}" class="btn btn-secondary">Cancel</a>
                    @can('accounts.manage')
                        <button type="button" class="btn btn-danger float-right delete-btn"
                            data-delete-url="{{ route('accounts.destroy', $account->id) }}"
                            data-name="{{ $account->name }}">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    @endcan
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Toggle control account fields visibility
            $('#is_control_account').change(function() {
                if ($(this).is(':checked')) {
                    $('#control_account_fields').slideDown();
                    $('select[name="control_type"]').prop('required', true);
                } else {
                    $('#control_account_fields').slideUp();
                    $('select[name="control_type"]').prop('required', false);
                }
            });

            // Form validation
            $('form').submit(function(e) {
                if ($('#is_control_account').is(':checked')) {
                    var controlType = $('select[name="control_type"]').val();
                    if (!controlType) {
                        e.preventDefault();
                        toastr.error(
                            'Please select a control type when marking account as control account.');
                        return false;
                    }
                }
            });

            // Delete confirmation
            $('.delete-btn').click(function() {
                var deleteUrl = $(this).data('delete-url');
                var accountName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete the account "${accountName}". This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create a form to submit the DELETE request
                        var form = $('<form>', {
                            'method': 'POST',
                            'action': deleteUrl
                        });

                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_token',
                            'value': $('meta[name="csrf-token"]').attr('content')
                        }));

                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_method',
                            'value': 'DELETE'
                        }));

                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
