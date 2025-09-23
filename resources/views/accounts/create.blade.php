@extends('layouts.main')

@section('title_page')
    Account
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('accounts.index') }}">Accounts</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Create Account</h3>
                </div>
                <form method="POST" action="{{ route('accounts.store') }}">
                    @csrf
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
                                <input type="text" name="code" class="form-control" required />
                            </div>
                            <div class="form-group col-md-8">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" required />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Type</label>
                                <select name="type" class="form-control" required>
                                    <option value="asset">Asset</option>
                                    <option value="liability">Liability</option>
                                    <option value="net_assets">Net Assets</option>
                                    <option value="income">Income</option>
                                    <option value="expense">Expense</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Postable</label>
                                <select name="is_postable" class="form-control" required>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Parent</label>
                                <select name="parent_id" class="form-control">
                                    <option value="">(none)</option>
                                    @foreach ($parents as $p)
                                        <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}
                                        </option>
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
                                                        class="form-check-input" value="1">
                                                    <label class="form-check-label" for="is_control_account">
                                                        <strong>Mark as Control Account</strong>
                                                    </label>
                                                </div>
                                                <small class="form-text text-muted">Enable this to create a control account
                                                    for reconciliation purposes</small>
                                            </div>
                                        </div>

                                        <div id="control_account_fields" style="display: none;">
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label>Control Type</label>
                                                    <select name="control_type" class="form-control">
                                                        <option value="">Select Control Type</option>
                                                        <option value="ap">Accounts Payable</option>
                                                        <option value="ar">Accounts Receivable</option>
                                                        <option value="cash">Cash & Bank</option>
                                                        <option value="inventory">Inventory</option>
                                                        <option value="fixed_assets">Fixed Assets</option>
                                                        <option value="other">Other</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Reconciliation Frequency</label>
                                                    <select name="reconciliation_frequency" class="form-control">
                                                        <option value="daily">Daily</option>
                                                        <option value="weekly">Weekly</option>
                                                        <option value="monthly" selected>Monthly</option>
                                                        <option value="quarterly">Quarterly</option>
                                                        <option value="yearly">Yearly</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label>Tolerance Amount</label>
                                                    <input type="number" name="tolerance_amount" class="form-control"
                                                        step="0.01" min="0" value="0.00">
                                                    <small class="form-text text-muted">Maximum variance amount for
                                                        reconciliation</small>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Description</label>
                                                    <textarea name="description" class="form-control" rows="2"
                                                        placeholder="Optional description for this control account"></textarea>
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
                    </div>
                </form>
            </div>
        </div>
    </section>
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
        });
    </script>
@endsection
