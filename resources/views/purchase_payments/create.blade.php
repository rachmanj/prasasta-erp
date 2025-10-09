@extends('layouts.main')

@section('title', 'Create Purchase Payment')

@section('title_page')
    Create Purchase Payment
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('purchase-payments.index') }}">Purchase Payments</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-money-check-alt mr-1"></i>
                                New Purchase Payment
                            </h3>
                            <a href="{{ route('purchase-payments.index') }}" class="btn btn-sm btn-secondary float-right">
                                <i class="fas fa-arrow-left"></i> Back to Purchase Payments
                            </a>
                        </div>
                        <form method="post" action="{{ route('purchase-payments.store') }}">
                            @csrf
                            <div class="card-body pb-1">
                                <!-- Enhanced 3-Column Header Layout -->
                                <div class="row">
                                    <!-- Column 1: Payment Number & Date -->
                                    <div class="col-md-4">
                                        <div class="form-group row mb-2">
                                            <label class="col-sm-4 col-form-label">Payment No</label>
                                            <div class="col-sm-8">
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                    </div>
                                                    <input type="text" id="payment-number-preview" class="form-control"
                                                        value="Auto-generated" readonly style="background-color: #f8f9fa;">
                                                </div>
                                                <small class="text-muted">Auto-generated on save</small>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-sm-4 col-form-label">Date <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-sm-8">
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i
                                                                class="far fa-calendar-alt"></i></span>
                                                    </div>
                                                    <input type="date" name="date"
                                                        value="{{ old('date', now()->toDateString()) }}"
                                                        class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Column 2: Payment Method & Bank Account -->
                                    <div class="col-md-4">
                                        <div class="form-group row mb-2">
                                            <label class="col-sm-4 col-form-label">Payment Method <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-sm-8">
                                                <select name="payment_method"
                                                    class="form-control form-control-sm select2bs4" required>
                                                    <option value="">-- select method --</option>
                                                    <option value="bank_transfer"
                                                        {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>
                                                        🏦 Bank Transfer
                                                    </option>
                                                    <option value="check"
                                                        {{ old('payment_method') == 'check' ? 'selected' : '' }}>
                                                        📝 Check
                                                    </option>
                                                    <option value="cash"
                                                        {{ old('payment_method') == 'cash' ? 'selected' : '' }}>
                                                        💵 Cash
                                                    </option>
                                                    <option value="other"
                                                        {{ old('payment_method') == 'other' ? 'selected' : '' }}>
                                                        📋 Other
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2" id="bank-account-field" style="display: none;">
                                            <label class="col-sm-4 col-form-label">Bank Account</label>
                                            <div class="col-sm-8">
                                                <select name="bank_account_id"
                                                    class="form-control form-control-sm select2bs4">
                                                    <option value="">-- select account --</option>
                                                    @foreach ($accounts->where('code', 'like', '1.1.2%') as $account)
                                                        <option value="{{ $account->id }}"
                                                            {{ old('bank_account_id') == $account->id ? 'selected' : '' }}>
                                                            {{ $account->code }} - {{ $account->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Column 3: Vendor & Reference -->
                                    <div class="col-md-4">
                                        <div class="form-group row mb-2">
                                            <label class="col-sm-4 col-form-label">Vendor <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-sm-8">
                                                <div class="input-group input-group-sm">
                                                    <select name="vendor_id" class="form-control form-control-sm select2bs4"
                                                        required>
                                                        <option value="">-- select vendor --</option>
                                                        @foreach ($vendors as $v)
                                                            <option value="{{ $v->id }}"
                                                                {{ old('vendor_id') == $v->id ? 'selected' : '' }}>
                                                                {{ $v->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                                            id="load-invoices-btn" disabled>
                                                            <i class="fas fa-list"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2" id="reference-field" style="display: none;">
                                            <label class="col-sm-4 col-form-label">Reference</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="reference_number"
                                                    value="{{ old('reference_number') }}"
                                                    class="form-control form-control-sm"
                                                    placeholder="Transfer/Check reference">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2" id="check-number-field" style="display: none;">
                                            <label class="col-sm-4 col-form-label">Check No</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="check_number"
                                                    value="{{ old('check_number') }}"
                                                    class="form-control form-control-sm" placeholder="Check number">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description Row -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row mb-2">
                                            <label class="col-sm-2 col-form-label">Description</label>
                                            <div class="col-sm-10">
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-align-left"></i></span>
                                                    </div>
                                                    <input type="text" name="description"
                                                        value="{{ old('description') }}" class="form-control"
                                                        placeholder="Payment description">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Outstanding Invoices Modal -->
                                <div class="modal fade" id="outstandingInvoicesModal" tabindex="-1" role="dialog"
                                    aria-labelledby="outstandingInvoicesModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="outstandingInvoicesModalLabel">
                                                    <i class="fas fa-file-invoice mr-2"></i>
                                                    Outstanding Invoices
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                                id="select-all-invoices">
                                                                <i class="fas fa-check-square"></i> Select All
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-secondary"
                                                                id="clear-all-invoices">
                                                                <i class="fas fa-square"></i> Clear All
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <span class="badge badge-info" id="selected-count">0 invoices
                                                            selected</span>
                                                    </div>
                                                </div>

                                                <div class="table-responsive">
                                                    <table class="table table-sm table-striped"
                                                        id="outstanding-invoices-table">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th width="5%">
                                                                    <input type="checkbox" id="select-all-checkbox">
                                                                </th>
                                                                <th width="12%">PO No</th>
                                                                <th width="15%">Invoice No</th>
                                                                <th width="10%">Invoice Date</th>
                                                                <th width="10%">Due Date</th>
                                                                <th width="12%">Original Amount</th>
                                                                <th width="12%">Outstanding</th>
                                                                <th width="8%">Days Past Due</th>
                                                                <th width="16%">Amount to Pay</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="outstanding-invoices-tbody">
                                                            <!-- Dynamic content will be loaded here -->
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <div class="alert alert-info mb-0">
                                                            <i class="fas fa-info-circle mr-1"></i>
                                                            <strong>Total Selected:</strong> <span
                                                                id="total-selected-amount">Rp 0,00</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <button type="button" class="btn btn-success"
                                                            id="add-selected-invoices">
                                                            <i class="fas fa-plus mr-1"></i> Add Selected to Payment
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-secondary card-outline mt-3 mb-2">
                                    <div class="card-header py-2">
                                        <h3 class="card-title">
                                            <i class="fas fa-list-ul mr-1"></i>
                                            Payment Lines
                                        </h3>
                                        <button type="button" class="btn btn-xs btn-primary float-right"
                                            onclick="addLine()">
                                            <i class="fas fa-plus"></i> Add Line
                                        </button>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 8%">PO No</th>
                                                        <th style="width: 12%">Invoice No</th>
                                                        <th style="width: 8%">Invoice Date</th>
                                                        <th style="width: 8%">Due Date</th>
                                                        <th style="width: 10%">Original Amount</th>
                                                        <th style="width: 10%">Outstanding</th>
                                                        <th style="width: 6%">Days Past Due</th>
                                                        <th style="width: 12%">Amount to Pay <span
                                                                class="text-danger">*</span></th>
                                                        <th style="width: 20%">Notes</th>
                                                        <th style="width: 6%">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="lines">
                                                    <tr class="line-item" id="line-0">
                                                        <td>
                                                            <input type="text" name="lines[0][po_no]"
                                                                class="form-control form-control-sm" readonly
                                                                style="background-color: #f8f9fa;">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="lines[0][invoice_no]"
                                                                class="form-control form-control-sm" readonly
                                                                style="background-color: #f8f9fa;">
                                                            <input type="hidden" name="lines[0][invoice_id]">
                                                        </td>
                                                        <td>
                                                            <input type="date" name="lines[0][invoice_date]"
                                                                class="form-control form-control-sm" readonly
                                                                style="background-color: #f8f9fa;">
                                                        </td>
                                                        <td>
                                                            <input type="date" name="lines[0][due_date]"
                                                                class="form-control form-control-sm" readonly
                                                                style="background-color: #f8f9fa;">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="lines[0][original_amount]"
                                                                class="form-control form-control-sm text-right" readonly
                                                                style="background-color: #f8f9fa;">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="lines[0][outstanding_amount]"
                                                                class="form-control form-control-sm text-right" readonly
                                                                style="background-color: #f8f9fa;">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="lines[0][days_past_due]"
                                                                class="form-control form-control-sm text-center" readonly
                                                                style="background-color: #f8f9fa;">
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" min="0.01"
                                                                name="lines[0][amount]"
                                                                class="form-control form-control-sm text-right amount-input"
                                                                value="0" required>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="lines[0][description]"
                                                                class="form-control form-control-sm"
                                                                placeholder="Payment notes">
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-xs btn-default"
                                                                disabled>
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <th colspan="7" class="text-right">Total Payment:</th>
                                                        <th class="text-right" id="total-amount">Rp 0,00</th>
                                                        <th colspan="2"></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-light card-outline mt-3 mb-2">
                                    <div class="card-header py-2">
                                        <h3 class="card-title">
                                            <i class="fas fa-tags mr-1"></i>
                                            Dimensions (Optional)
                                        </h3>
                                        <button type="button" class="btn btn-xs btn-tool float-right"
                                            data-card-widget="collapse">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="card-body p-0" style="display: none;">
                                        <div class="table-responsive">
                                            <table class="table table-sm mb-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 33%">Project</th>
                                                        <th style="width: 33%">Fund</th>
                                                        <th style="width: 34%">Department</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="dimensions-tbody">
                                                    <tr>
                                                        <td>
                                                            <select name="lines[0][project_id]"
                                                                class="form-control form-control-sm select2bs4">
                                                                <option value="">-- none --</option>
                                                                @foreach ($projects as $p)
                                                                    <option value="{{ $p->id }}">
                                                                        {{ $p->code }} - {{ $p->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="lines[0][fund_id]"
                                                                class="form-control form-control-sm select2bs4">
                                                                <option value="">-- none --</option>
                                                                @foreach ($funds as $f)
                                                                    <option value="{{ $f->id }}">
                                                                        {{ $f->code }} - {{ $f->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="lines[0][dept_id]"
                                                                class="form-control form-control-sm select2bs4">
                                                                <option value="">-- none --</option>
                                                                @foreach ($departments as $d)
                                                                    <option value="{{ $d->id }}">
                                                                        {{ $d->code }} - {{ $d->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-secondary card-outline mt-3 mb-2">
                                    <div class="card-header py-2">
                                        <h3 class="card-title">
                                            <i class="fas fa-file-invoice-dollar mr-1"></i>
                                            Allocation Preview
                                        </h3>
                                        <button type="button" class="btn btn-xs btn-info float-right"
                                            onclick="previewAlloc()">
                                            <i class="fas fa-sync-alt"></i> Preview Allocation
                                        </button>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped mb-0" id="alloc-table">
                                                <thead>
                                                    <tr>
                                                        <th>Invoice</th>
                                                        <th class="text-right">Remaining</th>
                                                        <th class="text-right">Allocate</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-save mr-1"></i> Save Payment
                                        </button>
                                        <a href="{{ route('purchase-payments.index') }}" class="btn btn-default">
                                            <i class="fas fa-times mr-1"></i> Cancel
                                        </a>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <div class="text-muted">
                                            <small>* Required fields</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2BS4 for all select elements
            $('.select2bs4').select2({
                theme: 'bootstrap4',
                placeholder: 'Select an option',
                allowClear: true
            });

            // Payment method change handler
            $('select[name="payment_method"]').on('change', function() {
                togglePaymentMethodFields();
            });

            // Vendor change handler
            $('select[name="vendor_id"]').on('change', function() {
                const vendorId = $(this).val();
                $('#load-invoices-btn').prop('disabled', !vendorId);
            });

            // Load invoices button click
            $('#load-invoices-btn').on('click', function() {
                loadOutstandingInvoices();
            });

            // Modal event handlers
            $('#select-all-invoices').on('click', function() {
                $('.invoice-checkbox').prop('checked', true);
                updateSelectedCount();
            });

            $('#clear-all-invoices').on('click', function() {
                $('.invoice-checkbox').prop('checked', false);
                updateSelectedCount();
            });

            $('#select-all-checkbox').on('change', function() {
                $('.invoice-checkbox').prop('checked', $(this).is(':checked'));
                updateSelectedCount();
            });

            $(document).on('change', '.invoice-checkbox', function() {
                updateSelectedCount();
            });

            $('#add-selected-invoices').on('click', function() {
                addSelectedInvoicesToPayment();
            });

            // Update total amount when any amount input changes
            $(document).on('input', '.amount-input', function() {
                updateTotalAmount();
            });

            // Initial setup
            togglePaymentMethodFields();
            updateTotalAmount();
        });

        function togglePaymentMethodFields() {
            const paymentMethod = $('select[name="payment_method"]').val();

            // Hide all conditional fields
            $('#bank-account-field, #reference-field, #check-number-field').hide();

            // Show relevant fields based on payment method
            switch (paymentMethod) {
                case 'bank_transfer':
                    $('#bank-account-field, #reference-field').show();
                    break;
                case 'check':
                    $('#bank-account-field, #check-number-field').show();
                    break;
                case 'cash':
                    $('#bank-account-field').show();
                    break;
                case 'other':
                    $('#reference-field').show();
                    break;
            }
        }

        async function loadOutstandingInvoices() {
            const vendorId = $('select[name="vendor_id"]').val();

            if (!vendorId) {
                toastr.warning('Please select a vendor first');
                return;
            }

            try {
                const response = await fetch(
                    `{{ route('purchase-payments.outstandingInvoices') }}?vendor_id=${vendorId}`);
                const result = await response.json();

                if (result.success) {
                    populateOutstandingInvoicesTable(result.data);
                    $('#outstandingInvoicesModal').modal('show');
                } else {
                    toastr.error('Failed to load outstanding invoices');
                }
            } catch (error) {
                console.error('Error loading outstanding invoices:', error);
                toastr.error('Failed to load outstanding invoices');
            }
        }

        function populateOutstandingInvoicesTable(invoices) {
            const tbody = $('#outstanding-invoices-tbody');
            tbody.empty();

            if (invoices.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            No outstanding invoices found for this vendor
                        </td>
                    </tr>
                `);
                return;
            }

            invoices.forEach(invoice => {
                const daysPastDue = invoice.days_past_due;
                const isOverdue = invoice.is_overdue;
                const daysClass = isOverdue ? 'text-danger' : (daysPastDue > 0 ? 'text-warning' : 'text-success');
                const daysText = daysPastDue > 0 ? `${daysPastDue} days` : 'Current';

                tbody.append(`
                    <tr>
                        <td>
                            <input type="checkbox" class="invoice-checkbox" data-invoice='${JSON.stringify(invoice)}'>
                        </td>
                        <td>${invoice.po_no || '-'}</td>
                        <td>${invoice.invoice_no}</td>
                        <td>${formatDate(invoice.invoice_date)}</td>
                        <td>${formatDate(invoice.due_date)}</td>
                        <td class="text-right">${formatCurrency(invoice.total_amount)}</td>
                        <td class="text-right">${formatCurrency(invoice.outstanding_amount)}</td>
                        <td class="text-center ${daysClass}">${daysText}</td>
                        <td>
                            <input type="number" step="0.01" min="0.01" max="${invoice.outstanding_amount}" 
                                   class="form-control form-control-sm text-right payment-amount-input" 
                                   value="${invoice.outstanding_amount}" 
                                   data-outstanding="${invoice.outstanding_amount}">
                        </td>
                    </tr>
                `);
            });

            // Add event listeners for payment amount inputs
            $('.payment-amount-input').on('input', function() {
                const maxAmount = parseFloat($(this).data('outstanding'));
                const currentAmount = parseFloat($(this).val()) || 0;

                if (currentAmount > maxAmount) {
                    $(this).val(maxAmount);
                    toastr.warning('Payment amount cannot exceed outstanding amount');
                }

                updateSelectedCount();
            });
        }

        function updateSelectedCount() {
            const selectedCheckboxes = $('.invoice-checkbox:checked');
            const count = selectedCheckboxes.length;

            $('#selected-count').text(`${count} invoice${count !== 1 ? 's' : ''} selected`);

            // Calculate total selected amount
            let totalAmount = 0;
            selectedCheckboxes.each(function() {
                const row = $(this).closest('tr');
                const amountInput = row.find('.payment-amount-input');
                totalAmount += parseFloat(amountInput.val()) || 0;
            });

            $('#total-selected-amount').text(formatCurrency(totalAmount));
        }

        function addSelectedInvoicesToPayment() {
            const selectedInvoices = [];

            $('.invoice-checkbox:checked').each(function() {
                const invoice = JSON.parse($(this).data('invoice'));
                const row = $(this).closest('tr');
                const paymentAmount = parseFloat(row.find('.payment-amount-input').val()) || 0;

                if (paymentAmount > 0) {
                    selectedInvoices.push({
                        ...invoice,
                        payment_amount: paymentAmount
                    });
                }
            });

            if (selectedInvoices.length === 0) {
                toastr.warning('Please select at least one invoice with payment amount > 0');
                return;
            }

            // Clear existing lines except the first one
            $('#lines tr:not(:first)').remove();

            // Populate payment lines
            selectedInvoices.forEach((invoice, index) => {
                if (index === 0) {
                    // Update first row
                    updatePaymentLine(0, invoice);
                } else {
                    // Add new rows
                    addInvoiceLine(index, invoice);
                }
            });

            // Close modal
            $('#outstandingInvoicesModal').modal('hide');

            // Update total
            updateTotalAmount();

            toastr.success(
            `Added ${selectedInvoices.length} invoice${selectedInvoices.length !== 1 ? 's' : ''} to payment`);
        }

        function updatePaymentLine(index, invoice) {
            const row = $(`#line-${index}`);

            row.find('input[name="lines[0][po_no]"]').val(invoice.po_no || '');
            row.find('input[name="lines[0][invoice_no]"]').val(invoice.invoice_no);
            row.find('input[name="lines[0][invoice_id]"]').val(invoice.invoice_id);
            row.find('input[name="lines[0][invoice_date]"]').val(invoice.invoice_date);
            row.find('input[name="lines[0][due_date]"]').val(invoice.due_date);
            row.find('input[name="lines[0][original_amount]"]').val(formatCurrency(invoice.total_amount));
            row.find('input[name="lines[0][outstanding_amount]"]').val(formatCurrency(invoice.outstanding_amount));
            row.find('input[name="lines[0][days_past_due]"]').val(invoice.days_past_due > 0 ?
                `${invoice.days_past_due} days` : 'Current');
            row.find('input[name="lines[0][amount]"]').val(invoice.payment_amount);

            // Enable delete button
            row.find('button').removeClass('btn-default').addClass('btn-danger').prop('disabled', false);
        }

        function addInvoiceLine(index, invoice) {
            const tbody = $('#lines');
            const tr = document.createElement('tr');
            tr.className = 'line-item';
            tr.id = 'line-' + index;

            tr.innerHTML = `
                <td>
                    <input type="text" name="lines[${index}][po_no]" class="form-control form-control-sm" readonly style="background-color: #f8f9fa;" value="${invoice.po_no || ''}">
                </td>
                <td>
                    <input type="text" name="lines[${index}][invoice_no]" class="form-control form-control-sm" readonly style="background-color: #f8f9fa;" value="${invoice.invoice_no}">
                    <input type="hidden" name="lines[${index}][invoice_id]" value="${invoice.invoice_id}">
                </td>
                <td>
                    <input type="date" name="lines[${index}][invoice_date]" class="form-control form-control-sm" readonly style="background-color: #f8f9fa;" value="${invoice.invoice_date}">
                </td>
                <td>
                    <input type="date" name="lines[${index}][due_date]" class="form-control form-control-sm" readonly style="background-color: #f8f9fa;" value="${invoice.due_date}">
                </td>
                <td>
                    <input type="text" name="lines[${index}][original_amount]" class="form-control form-control-sm text-right" readonly style="background-color: #f8f9fa;" value="${formatCurrency(invoice.total_amount)}">
                </td>
                <td>
                    <input type="text" name="lines[${index}][outstanding_amount]" class="form-control form-control-sm text-right" readonly style="background-color: #f8f9fa;" value="${formatCurrency(invoice.outstanding_amount)}">
                </td>
                <td>
                    <input type="text" name="lines[${index}][days_past_due]" class="form-control form-control-sm text-center" readonly style="background-color: #f8f9fa;" value="${invoice.days_past_due > 0 ? invoice.days_past_due + ' days' : 'Current'}">
                </td>
                <td>
                    <input type="number" step="0.01" min="0.01" name="lines[${index}][amount]" 
                           class="form-control form-control-sm text-right amount-input" value="${invoice.payment_amount}" required>
                </td>
                <td>
                    <input type="text" name="lines[${index}][description]" class="form-control form-control-sm" placeholder="Payment notes">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-danger" onclick="removeLine(${index})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;

            tbody.append(tr);
        }

        function updateTotalAmount() {
            const total = Array.from(document.querySelectorAll('.amount-input'))
                .reduce((sum, el) => sum + parseFloat(el.value || 0), 0);
            $('#total-amount').text(formatCurrency(total));
        }

        function removeLine(lineIdx) {
            $(`#line-${lineIdx}`).remove();
            updateTotalAmount();
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount).replace('IDR', 'Rp');
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID');
        }

        // Legacy function for compatibility
        function addLine() {
            toastr.info('Please use "Load Outstanding Invoices" to add payment lines from vendor invoices');
        }

        async function previewAlloc() {
            const amount = Array.from(document.querySelectorAll('.amount-input'))
                .reduce((s, el) => s + parseFloat(el.value || 0), 0);
            const vendorId = document.querySelector('select[name="vendor_id"]').value;

            if (!vendorId || amount <= 0) {
                toastr.warning('Please select a vendor and enter a payment amount');
                return;
            }

            const params = new URLSearchParams({
                vendor_id: vendorId,
                amount: amount
            });

            try {
                const res = await fetch(`{{ route('purchase-payments.previewAllocation') }}?${params.toString()}`);
                const data = await res.json();
                const tbody = document.querySelector('#alloc-table tbody');
                tbody.innerHTML = '';

                if (data.rows.length === 0) {
                    const tr = document.createElement('tr');
                    tr.innerHTML =
                        '<td colspan="3" class="text-center">No outstanding invoices found for this vendor</td>';
                    tbody.appendChild(tr);
                    return;
                }

                data.rows.forEach(r => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${r.invoice_no}</td>
                        <td class="text-right">${formatCurrency(r.remaining_before)}</td>
                        <td class="text-right">${formatCurrency(r.allocate)}</td>
                    `;
                    tbody.appendChild(tr);
                });

                // Add total row
                const totalRow = document.createElement('tr');
                totalRow.className = 'font-weight-bold';
                const totalAllocated = data.rows.reduce((sum, row) => sum + Number(row.allocate), 0);
                totalRow.innerHTML = `
                    <td>Total</td>
                    <td></td>
                    <td class="text-right">${formatCurrency(totalAllocated)}</td>
                `;
                tbody.appendChild(totalRow);

            } catch (error) {
                console.error('Error fetching allocation preview:', error);
                toastr.error('Failed to load allocation preview');
            }
        }
    </script>
@endpush
