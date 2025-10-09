@extends('layouts.main')

@section('title_page')
    Create Purchase Invoice
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('purchase-invoices.index') }}">Purchase Invoices</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
    <!-- Include Item Selection Modal -->
    @include('components.item-selection-modal')

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Create Purchase Invoice</h3>
                            <div class="card-tools">
                                <a href="{{ route('purchase-invoices.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back to Purchase Invoices
                                </a>
                            </div>
                        </div>
                        <form method="post" action="{{ route('purchase-invoices.store') }}">
                            @csrf
                            <div class="card-body">
                                @isset($purchase_order_id)
                                    <input type="hidden" name="purchase_order_id" value="{{ $purchase_order_id }}" />
                                @endisset
                                @isset($goods_receipt_id)
                                    <input type="hidden" name="goods_receipt_id" value="{{ $goods_receipt_id }}" />
                                @endisset

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Invoice Number (Auto-generated)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                </div>
                                                <input type="text" class="form-control bg-light"
                                                    value="{{ $invoiceNumberPreview ?? 'Auto' }}" readonly
                                                    title="Invoice number will be generated automatically">
                                            </div>
                                            <small class="text-muted">Will be generated when saved</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Date <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="far fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="date" name="date" id="invoice-date"
                                                    value="{{ old('date', now()->toDateString()) }}" class="form-control"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Reference / PO Number</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="fas fa-file-contract"></i></span>
                                                </div>
                                                <input type="text" name="reference_number" class="form-control"
                                                    value="{{ old('reference_number') }}" placeholder="e.g., PO-2025-001">
                                            </div>
                                            <small class="text-muted">External reference for tracking</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Vendor <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select name="vendor_id" id="vendor-select" class="form-control select2bs4"
                                                    required>
                                                    <option value="">-- select vendor --</option>
                                                    @foreach ($vendors as $v)
                                                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-info" id="copy-from-po-btn"
                                                        disabled title="Select a vendor first">
                                                        <i class="fas fa-copy"></i> Copy from PO
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Terms (days)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="fas fa-calendar-day"></i></span>
                                                </div>
                                                <input type="number" min="0" name="terms_days" id="terms-days"
                                                    value="{{ old('terms_days', 30) }}" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Due Date</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="far fa-calendar-check"></i></span>
                                                </div>
                                                <input type="date" name="due_date" id="due-date"
                                                    value="{{ old('due_date') }}" class="form-control bg-light" readonly
                                                    title="Auto-calculated from invoice date and terms">
                                            </div>
                                            <small class="text-muted">Auto-calculated</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control" rows="2" placeholder="Invoice description">{{ old('description') }}</textarea>
                                </div>

                                <div class="card card-info card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-list"></i> Invoice Lines</h3>
                                        <button type="button" class="btn btn-primary btn-sm float-right" id="add-line">
                                            <i class="fas fa-plus"></i> Add Line
                                        </button>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped mb-0" id="lines">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th style="width: 7%">Type</th>
                                                        <th style="width: 18%">Item/Account <span
                                                                class="text-danger">*</span></th>
                                                        <th style="width: 15%">Description</th>
                                                        <th style="width: 7%">Qty <span class="text-danger">*</span></th>
                                                        <th style="width: 10%">Unit Price <span
                                                                class="text-danger">*</span></th>
                                                        <th style="width: 8%">Disc %</th>
                                                        <th style="width: 8%">VAT</th>
                                                        <th style="width: 8%">WTax</th>
                                                        <th style="width: 12%">Amount</th>
                                                        <th style="width: 7%">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot class="bg-light">
                                                    <tr>
                                                        <th colspan="8" class="text-right">Subtotal:</th>
                                                        <th class="text-right" id="subtotal-amount">Rp 0,00</th>
                                                        <th></th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="8" class="text-right">Total Discount:</th>
                                                        <th class="text-right text-success" id="total-discount">Rp 0,00
                                                        </th>
                                                        <th></th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="8" class="text-right">Amount after Discount:</th>
                                                        <th class="text-right" id="original-amount">Rp 0,00</th>
                                                        <th></th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="8" class="text-right">VAT (+):</th>
                                                        <th class="text-right text-danger" id="total-vat">Rp 0,00</th>
                                                        <th></th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="8" class="text-right">WTax (-):</th>
                                                        <th class="text-right text-warning" id="total-wtax">Rp 0,00</th>
                                                        <th></th>
                                                    </tr>
                                                    <tr class="table-primary">
                                                        <th colspan="8" class="text-right"><strong>Amount Due:</strong>
                                                        </th>
                                                        <th class="text-right" id="amount-due"><strong>Rp 0,00</strong>
                                                        </th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-warning card-outline mt-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-sticky-note"></i> Notes & Terms (Optional)
                                        </h3>
                                        <button type="button" class="btn btn-tool float-right"
                                            data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Internal Notes</label>
                                                    <textarea name="notes" class="form-control" rows="4"
                                                        placeholder="Internal notes (not visible on printed invoice)">{{ old('notes') }}</textarea>
                                                    <small class="text-muted">For internal tracking only</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Terms & Conditions</label>
                                                    <textarea name="terms" class="form-control" rows="4" placeholder="Payment terms, delivery conditions, etc.">{{ old('terms') }}</textarea>
                                                    <small class="text-muted">Will be printed on invoice</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-secondary card-outline mt-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> Dimensions (Optional)</h3>
                                        <button type="button" class="btn btn-tool float-right"
                                            data-card-widget="collapse">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="card-body p-0" style="display: none;">
                                        <div class="table-responsive">
                                            <table class="table table-sm mb-0">
                                                <thead class="bg-light">
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
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-save"></i> Save Invoice
                                </button>
                                <a href="{{ route('purchase-invoices.index') }}" class="btn btn-default">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <div class="text-muted float-right">
                                    <small>* Required fields</small>
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
        window.prefill = @json($prefill ?? null);
        window.accounts = @json($accounts ?? []);
        window.taxCodes = @json($taxCodes ?? []);
        window.projects = @json($projects ?? []);
        window.funds = @json($funds ?? []);
        window.departments = @json($departments ?? []);

        $(document).ready(function() {
            // Initialize Select2BS4 for all select elements
            $('.select2bs4').select2({
                theme: 'bootstrap4',
                placeholder: 'Select an option',
                allowClear: true
            });

            // Initialize card widget functionality
            $('.card-widget').CardWidget();

            // Auto-calculate due date when terms or date changes
            function calculateDueDate() {
                const invoiceDate = $('#invoice-date').val();
                const termsDays = parseInt($('#terms-days').val() || 0);

                if (invoiceDate && termsDays > 0) {
                    const date = new Date(invoiceDate);
                    date.setDate(date.getDate() + termsDays);
                    const dueDate = date.toISOString().split('T')[0];
                    $('#due-date').val(dueDate);
                } else if (invoiceDate && termsDays === 0) {
                    $('#due-date').val(invoiceDate);
                } else {
                    $('#due-date').val('');
                }
            }

            $('#invoice-date, #terms-days').on('change', calculateDueDate);
            calculateDueDate(); // Initial calculation

            // Enable/disable Copy from PO button based on vendor selection
            $('#vendor-select').on('change', function() {
                const vendorId = $(this).val();
                if (vendorId) {
                    $('#copy-from-po-btn').prop('disabled', false).attr('title',
                    'Copy from Purchase Order');
                } else {
                    $('#copy-from-po-btn').prop('disabled', true).attr('title', 'Select a vendor first');
                }
            });

            let i = 0;
            const $tb = $('#lines tbody');

            // Add first line
            $('#add-line').on('click', function() {
                addLineRow();
            }).trigger('click');

            // Remove line
            $tb.on('click', '.rm', function() {
                const lineIdx = $(this).closest('tr').data('line-idx');
                $(this).closest('tr').remove();
                $(`#dim-${lineIdx}`).remove();
                updateTotals();
            });

            // Handle line type change
            $(document).on('change', '.line-type-select', function() {
                const row = $(this).closest('tr');
                const lineType = $(this).val();
                const itemDisplay = row.find('.item-display');
                const selectBtn = row.find('.select-item-btn');

                if (lineType === 'item') {
                    itemDisplay.attr('placeholder', 'Click to select item');
                    selectBtn.attr('title', 'Select Item');
                } else if (lineType === 'service') {
                    itemDisplay.attr('placeholder', 'Click to select account');
                    selectBtn.attr('title', 'Select Account');
                }
            });

            // Handle item selection
            $(document).on('click', '.select-item-btn', function() {
                const row = $(this).closest('tr');
                const lineType = row.find('.line-type-select').val();

                if (lineType === 'item') {
                    window.itemSelector.open(function(item) {
                        row.find('.item-account-id').val(item.id);
                        row.find('.item-display').val(item.name);
                        row.find('.item-code-name-display').text(`${item.code} - ${item.name}`);
                        row.find('.item-id-input').val(item.id);
                        row.find('.account-id-input').val(''); // Clear account if switching to item
                        row.find('.description-input').val(item.description || item.name);
                        row.find('.price-input').val(item.last_cost_price || 0);
                        updateLineAmount(row);
                        updateTotals();
                    });
                } else {
                    // Handle account selection for services
                    showAccountSelector(function(account) {
                        row.find('.item-account-id').val(account.id);
                        row.find('.item-display').val(account.name);
                        row.find('.item-code-name-display').text(
                            `${account.code} - ${account.name}`);
                        row.find('.account-id-input').val(account.id);
                        row.find('.item-id-input').val(''); // Clear item if switching to service
                        row.find('.description-input').val(account.name);
                        row.find('.price-input').val(0);
                        updateLineAmount(row);
                        updateTotals();
                    });
                }
            });

            // Update totals when values change
            $(document).on('input', '.qty-input, .price-input, .discount-input', function() {
                updateLineAmount($(this).closest('tr'));
                updateTotals();
            });

            // Update totals when tax rates change
            $(document).on('change', '.vat-rate-select, .wtax-rate-select', function() {
                updateLineAmount($(this).closest('tr'));
                updateTotals();
            });

            // Handle prefill data if available
            if (window.prefill) {
                $tb.empty();
                i = 0;
                $('[name=date]').val(window.prefill.date);
                $('[name=vendor_id]').val(window.prefill.vendor_id);

                if (window.prefill.lines && window.prefill.lines.length > 0) {
                    window.prefill.lines.forEach(function(l) {
                        addLineRow(l);
                    });
                } else {
                    addLineRow();
                }

                // Initialize Select2 for prefilled data
                $('.select2bs4').select2({
                    theme: 'bootstrap4',
                    placeholder: 'Select an option',
                    allowClear: true
                });

                updateTotals();
            }

            function addLineRow(data = {}) {
                const lineIdx = i++;
                const tr = document.createElement('tr');
                tr.setAttribute('data-line-idx', lineIdx);

                tr.innerHTML = `
            <td>
                <select name="lines[${lineIdx}][line_type]" class="form-control form-control-sm line-type-select" required>
                    <option value="item" ${data.line_type === 'item' ? 'selected' : ''}>Item</option>
                    <option value="service" ${data.line_type === 'service' ? 'selected' : ''}>Service</option>
                </select>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="hidden" name="lines[${lineIdx}][item_account_id]" class="item-account-id" value="${data?.item_account_id || ''}">
                        <input type="text" class="form-control item-display text-small" placeholder="Click to select item" readonly>
                        <small class="text-muted item-code-name-display"></small>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-primary btn-sm select-item-btn" title="Select Item">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                </div>
                <input type="hidden" name="lines[${lineIdx}][item_id]" class="item-id-input" value="${data.item_id || ''}">
                <input type="hidden" name="lines[${lineIdx}][account_id]" class="account-id-input" value="${data.account_id || ''}">
            </td>
            <td>
                <input type="text" name="lines[${lineIdx}][description]" class="form-control form-control-sm description-input"
                    value="${data.description || ''}" placeholder="Description">
            </td>
            <td>
                <input type="number" step="0.01" min="0.01" name="lines[${lineIdx}][qty]"
                    class="form-control form-control-sm text-right qty-input" value="${data.qty || 1}" required>
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="lines[${lineIdx}][unit_price]"
                    class="form-control form-control-sm text-right price-input" value="${data.unit_price || 0}" required>
            </td>
            <td>
                <input type="number" step="0.01" min="0" max="100" name="lines[${lineIdx}][discount_percent]"
                    class="form-control form-control-sm text-right discount-input" value="${data.discount_percent || 0}"
                    placeholder="0" title="Discount percentage">
                <input type="hidden" name="lines[${lineIdx}][discount_amount]" class="discount-amount-input" value="${data.discount_amount || 0}">
            </td>
            <td>
                <select name="lines[${lineIdx}][vat_rate]" class="form-control form-control-sm vat-rate-select">
                    <option value="0" ${data.vat_rate == 0 ? 'selected' : ''}>No</option>
                    <option value="11" ${data.vat_rate == 11 ? 'selected' : ''}>11%</option>
                </select>
                <input type="hidden" name="lines[${lineIdx}][vat_amount]" class="vat-amount-input" value="${data.vat_amount || 0}">
            </td>
            <td>
                <select name="lines[${lineIdx}][wtax_rate]" class="form-control form-control-sm wtax-rate-select">
                    <option value="0" ${data.wtax_rate == 0 ? 'selected' : ''}>No</option>
                    <option value="2" ${data.wtax_rate == 2 ? 'selected' : ''}>2%</option>
                </select>
                <input type="hidden" name="lines[${lineIdx}][wtax_amount]" class="wtax-amount-input" value="${data.wtax_amount || 0}">
            </td>
            <td>
                <div class="amount-display text-right font-weight-bold">Rp 0,00</div>
                <input type="hidden" name="lines[${lineIdx}][amount]" class="amount-input" value="${data.amount || 0}">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-xs btn-danger rm">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;

                $tb.append(tr);

                // Initialize Select2BS4 for the newly added select elements
                $(tr).find('.select2bs4').select2({
                    theme: 'bootstrap4',
                    placeholder: 'Select an option',
                    allowClear: true
                });

                // Trigger line type change to populate item/account dropdown
                $(tr).find('.line-type-select').trigger('change');

                // Set prefill data if available
                if (data.line_type === 'item' && data.item_id) {
                    $(tr).find('.item-account-select').val(data.item_id);
                    $(tr).find('.item-id-input').val(data.item_id);
                } else if (data.line_type === 'service' && data.account_id) {
                    $(tr).find('.item-account-select').val(data.account_id);
                    $(tr).find('.account-id-input').val(data.account_id);
                }

                // Add dimensions row
                addDimensionRow(lineIdx);

                updateLineAmount(tr);
                updateTotals();
            }

            function addDimensionRow(lineIdx) {
                const dimensionsTable = document.getElementById('dimensions-tbody');
                const tr = document.createElement('tr');
                tr.id = 'dim-' + lineIdx;

                tr.innerHTML = `
            <td>
                <select name="lines[${lineIdx}][project_id]" class="form-control form-control-sm select2bs4">
                    <option value="">-- none --</option>
                    ${window.projects.map(p => `<option value="${p.id}">${p.code} - ${p.name}</option>`).join('')}
                </select>
            </td>
            <td>
                <select name="lines[${lineIdx}][fund_id]" class="form-control form-control-sm select2bs4">
                    <option value="">-- none --</option>
                    ${window.funds.map(f => `<option value="${f.id}">${f.code} - ${f.name}</option>`).join('')}
                </select>
            </td>
            <td>
                <select name="lines[${lineIdx}][dept_id]" class="form-control form-control-sm select2bs4">
                    <option value="">-- none --</option>
                    ${window.departments.map(d => `<option value="${d.id}">${d.code} - ${d.name}</option>`).join('')}
                </select>
            </td>
        `;

                dimensionsTable.appendChild(tr);

                // Initialize Select2BS4 for the newly added select elements
                $(tr).find('.select2bs4').select2({
                    theme: 'bootstrap4',
                    placeholder: 'Select an option',
                    allowClear: true
                });
            }

            function updateLineAmount(row) {
                const $row = $(row);
                const qty = parseFloat($row.find('.qty-input').val() || 0);
                const price = parseFloat($row.find('.price-input').val() || 0);
                const discountPercent = parseFloat($row.find('.discount-input').val() || 0);
                const vatRate = parseFloat($row.find('.vat-rate-select').val() || 0);
                const wtaxRate = parseFloat($row.find('.wtax-rate-select').val() || 0);

                const subtotal = qty * price;
                const discountAmount = subtotal * (discountPercent / 100);
                const amountAfterDiscount = subtotal - discountAmount;
                const vatAmount = amountAfterDiscount * (vatRate / 100);
                const wtaxAmount = amountAfterDiscount * (wtaxRate / 100);
                const amount = amountAfterDiscount + vatAmount - wtaxAmount;

                // Update hidden inputs with calculated amounts
                $row.find('.discount-amount-input').val(discountAmount.toFixed(2));
                $row.find('.vat-amount-input').val(vatAmount.toFixed(2));
                $row.find('.wtax-amount-input').val(wtaxAmount.toFixed(2));
                $row.find('.amount-input').val(amount.toFixed(2));

                // Update display with Indonesia currency formatting
                $row.find('.amount-display').text(`Rp ${amount.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}`);
            }

            function updateTotals() {
                let subtotal = 0;
                let totalDiscount = 0;
                let originalAmount = 0;
                let totalVat = 0;
                let totalWtax = 0;
                let amountDue = 0;

                // Calculate totals from all line items
                $('#lines tbody tr').each(function() {
                    const qty = parseFloat($(this).find('.qty-input').val() || 0);
                    const price = parseFloat($(this).find('.price-input').val() || 0);
                    const discountAmount = parseFloat($(this).find('.discount-amount-input').val() || 0);
                    const vatAmount = parseFloat($(this).find('.vat-amount-input').val() || 0);
                    const wtaxAmount = parseFloat($(this).find('.wtax-amount-input').val() || 0);

                    subtotal += qty * price;
                    totalDiscount += discountAmount;
                    totalVat += vatAmount;
                    totalWtax += wtaxAmount;
                });

                originalAmount = subtotal - totalDiscount;
                amountDue = originalAmount + totalVat - totalWtax;

                // Update display with Indonesian number formatting
                $('#subtotal-amount').text(`Rp ${subtotal.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}`);

                $('#total-discount').text(`Rp ${totalDiscount.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}`);

                $('#original-amount').text(`Rp ${originalAmount.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}`);

                $('#total-vat').text(`Rp ${totalVat.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}`);

                $('#total-wtax').text(`Rp ${totalWtax.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}`);

                $('#amount-due').text(`Rp ${amountDue.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}`);
            }

            // Account selector for services
            function showAccountSelector(callback) {
                const modal = `
                    <div class="modal fade" id="accountModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Select Expense Account</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped" id="accountsTable">
                                            <thead>
                                                <tr>
                                                    <th>Code</th>
                                                    <th>Account Name</th>
                                                    <th>Type</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${window.accounts.map(account => `
                                                            <tr>
                                                                <td>${account.code}</td>
                                                                <td>${account.name}</td>
                                                                <td><span class="badge badge-info">${account.type}</span></td>
                                                                <td>
                                                                    <button type="button" class="btn btn-sm btn-primary select-account-btn" 
                                                                        data-code="${account.code}" data-name="${account.name}" data-id="${account.id}">
                                                                        Select
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        `).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                if ($('#accountModal').length === 0) {
                    $('body').append(modal);
                }

                $('#accountModal').modal('show');

                $('#accountModal').on('click', '.select-account-btn', function() {
                    const account = {
                        id: $(this).data('id'),
                        code: $(this).data('code'),
                        name: $(this).data('name')
                    };
                    $('#accountModal').modal('hide');
                    callback(account);
                });
            }

            // Copy from PO modal functionality
            $('#copy-from-po-btn').on('click', function() {
                const vendorId = $('#vendor-select').val();
                if (!vendorId) {
                    alert('Please select a vendor first');
                    return;
                }

                // Show loading state
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');

                // Fetch purchase orders for selected vendor
                $.ajax({
                    url: '{{ route('purchase-invoices.getPurchaseOrders') }}',
                    method: 'GET',
                    data: {
                        vendor_id: vendorId
                    },
                    success: function(pos) {
                        showPOModal(pos);
                        $('#copy-from-po-btn').prop('disabled', false).html(
                            '<i class="fas fa-copy"></i> Copy from PO');
                    },
                    error: function(xhr) {
                        alert('Error loading purchase orders: ' + (xhr.responseJSON?.error ||
                            'Unknown error'));
                        $('#copy-from-po-btn').prop('disabled', false).html(
                            '<i class="fas fa-copy"></i> Copy from PO');
                    }
                });
            });

            function showPOModal(pos) {
                // Remove existing modal if any
                $('#poModal').remove();

                const poRows = pos.map(po => `
                    <tr>
                        <td>${po.order_no || '-'}</td>
                        <td>${po.date}</td>
                        <td>${po.description || '-'}</td>
                        <td class="text-right">Rp ${parseFloat(po.total_amount || 0).toLocaleString('id-ID', {minimumFractionDigits: 2})}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary select-po-btn" 
                                data-po='${JSON.stringify(po)}'>
                                <i class="fas fa-check"></i> Select
                            </button>
                        </td>
                    </tr>
                `).join('');

                const modal = `
                    <div class="modal fade" id="poModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-info">
                                    <h5 class="modal-title"><i class="fas fa-file-invoice"></i> Select Purchase Order</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    ${pos.length > 0 ? `
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped table-hover">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>PO Number</th>
                                                            <th>Date</th>
                                                            <th>Description</th>
                                                            <th class="text-right">Total Amount</th>
                                                            <th style="width: 100px">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        ${poRows}
                                                    </tbody>
                                                </table>
                                            </div>
                                        ` : `
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i> No purchase orders found for this vendor.
                                            </div>
                                        `}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        <i class="fas fa-times"></i> Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                $('body').append(modal);
                $('#poModal').modal('show');

                // Handle PO selection
                $('#poModal').on('click', '.select-po-btn', function() {
                    const po = JSON.parse($(this).attr('data-po'));
                    copyFromPO(po);
                    $('#poModal').modal('hide');
                });
            }

            function copyFromPO(po) {
                // Fill header fields
                $('[name="reference_number"]').val(po.order_no);
                if (po.description) {
                    $('[name="description"]').val(po.description);
                }

                // Clear existing lines
                $tb.empty();
                $('#dimensions-tbody').empty();
                i = 0;

                // Add lines from PO
                if (po.lines && po.lines.length > 0) {
                    po.lines.forEach(function(line) {
                        const lineData = {
                            line_type: line.line_type || 'item',
                            item_id: line.item_id,
                            account_id: line.account_id,
                            description: line.description,
                            qty: line.qty,
                            unit_price: line.unit_price,
                            discount_percent: line.discount_percent || 0,
                            vat_rate: 0, // Reset tax rates, user can set them
                            wtax_rate: 0
                        };
                        addLineRow(lineData);
                    });
                } else {
                    // Add one empty line if no lines in PO
                    addLineRow();
                }

                updateTotals();

                // Show success message
                if (typeof toastr !== 'undefined') {
                    toastr.success('Purchase Order data copied successfully!', 'Success');
                } else {
                    alert('Purchase Order data copied successfully!');
                }
            }
        });
    </script>
@endpush
