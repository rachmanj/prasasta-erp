@extends('layouts.main')

@section('title_page')
    New Cash In Transaction
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('banking.dashboard.index') }}">Banking</a></li>
    <li class="breadcrumb-item"><a href="{{ route('banking.cash-in.index') }}">Cash In</a></li>
    <li class="breadcrumb-item active">New Transaction</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">New Cash In Transaction</h4>
                </div>

                <form action="{{ route('banking.cash-in.store') }}" method="POST" id="cash-in-form">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date">Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cash_account_id">Cash/Bank Account <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" id="cash_account_id" name="cash_account_id" required>
                                        <option value="">Select Cash Account</option>
                                        @foreach ($cashAccounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="2" placeholder="Transaction description"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="project_id">Project</label>
                                    <select class="form-control" id="project_id" name="project_id">
                                        <option value="">Select Project</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->code }} - {{ $project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fund_id">Fund</label>
                                    <select class="form-control" id="fund_id" name="fund_id">
                                        <option value="">Select Fund</option>
                                        @foreach ($funds as $fund)
                                            <option value="{{ $fund->id }}">{{ $fund->code }} - {{ $fund->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dept_id">Department</label>
                                    <select class="form-control" id="dept_id" name="dept_id">
                                        <option value="">Select Department</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->code }} -
                                                {{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h5>Revenue Lines</h5>
                        <div id="revenue-lines">
                            <div class="row revenue-line">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Account <span class="text-danger">*</span></label>
                                        <select class="form-control account-select" name="lines[0][account_id]" required>
                                            <option value="">Select Account</option>
                                            @foreach ($creditAccounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->code }} -
                                                    {{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Amount <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control amount-input" name="lines[0][amount]"
                                            step="0.01" min="0.01" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Memo</label>
                                        <input type="text" class="form-control" name="lines[0][memo]" placeholder="Memo">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-danger btn-sm btn-block remove-line"
                                            style="display: none;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-success btn-sm" id="add-line">
                                    <i class="fas fa-plus"></i> Add Line
                                </button>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Total Amount</label>
                                    <input type="text" class="form-control" id="total-amount" readonly
                                        style="font-weight: bold; font-size: 16px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Post Transaction</button>
                        <a href="{{ route('banking.cash-in.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            let lineIndex = 1;

            // Add new line
            $('#add-line').click(function() {
                const newLine = `
            <div class="row revenue-line">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Account <span class="text-danger">*</span></label>
                        <select class="form-control account-select" name="lines[${lineIndex}][account_id]" required>
                            <option value="">Select Account</option>
                            @foreach ($creditAccounts as $account)
                                <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control amount-input" name="lines[${lineIndex}][amount]" step="0.01" min="0.01" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Memo</label>
                        <input type="text" class="form-control" name="lines[${lineIndex}][memo]" placeholder="Memo">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm btn-block remove-line">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

                $('#revenue-lines').append(newLine);
                lineIndex++;
                updateRemoveButtons();
                updateTotalAmount();
            });

            // Remove line
            $(document).on('click', '.remove-line', function() {
                $(this).closest('.revenue-line').remove();
                updateRemoveButtons();
                updateTotalAmount();
            });

            // Update total amount when amount inputs change
            $(document).on('input', '.amount-input', function() {
                updateTotalAmount();
            });

            // Update remove buttons visibility
            function updateRemoveButtons() {
                const lines = $('.revenue-line');
                if (lines.length === 1) {
                    $('.remove-line').hide();
                } else {
                    $('.remove-line').show();
                }
            }

            // Calculate total amount
            function updateTotalAmount() {
                let total = 0;
                $('.amount-input').each(function() {
                    const amount = parseFloat($(this).val()) || 0;
                    total += amount;
                });
                $('#total-amount').val('Rp ' + total.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }

            // Form validation
            $('#cash-in-form').submit(function(e) {
                const totalAmount = $('.amount-input').toArray().reduce((sum, input) => {
                    return sum + (parseFloat($(input).val()) || 0);
                }, 0);

                if (totalAmount <= 0) {
                    e.preventDefault();
                    toastr.error('Total amount must be greater than 0');
                    return false;
                }

                // Validate all required fields
                let isValid = true;
                $('.account-select').each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                $('.amount-input').each(function() {
                    if (!$(this).val() || parseFloat($(this).val()) <= 0) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    toastr.error('Please fill in all required fields');
                    return false;
                }
            });

            // Initialize
            updateTotalAmount();
        });
    </script>
@endpush
