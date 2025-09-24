@extends('layouts.main')

@section('title', 'Create Sales Invoice')

@section('title_page')
    Create Sales Invoice
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('sales-invoices.index') }}">Sales Invoices</a></li>
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
                                <i class="fas fa-file-invoice mr-1"></i>
                                New Invoice
                            </h3>
                            <a href="{{ route('sales-invoices.index') }}" class="btn btn-sm btn-secondary float-right">
                                <i class="fas fa-arrow-left"></i> Back to Sales Invoices
                            </a>
                        </div>
                        <form method="post" action="{{ route('sales-invoices.store') }}">
                            @csrf
                            <div class="card-body pb-1">
                                @isset($sales_order_id)
                                    <input type="hidden" name="sales_order_id" value="{{ $sales_order_id }}" />
                                @endisset

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row mb-2">
                                            <label class="col-sm-3 col-form-label">Date <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-sm-9">
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
                                        <div class="form-group row mb-2">
                                            <label class="col-sm-3 col-form-label">Customer <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <select name="customer_id" class="form-control form-control-sm select2bs4"
                                                    required>
                                                    <option value="">-- select customer --</option>
                                                    @foreach ($customers as $c)
                                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-sm-3 col-form-label">Description</label>
                                            <div class="col-sm-9">
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-align-left"></i></span>
                                                    </div>
                                                    <input type="text" name="description"
                                                        value="{{ old('description') }}" class="form-control"
                                                        placeholder="Invoice description">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row mb-2">
                                            <label class="col-sm-3 col-form-label">Terms (days)</label>
                                            <div class="col-sm-9">
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-calendar-day"></i></span>
                                                    </div>
                                                    <input type="number" min="0" name="terms_days"
                                                        value="{{ old('terms_days', 30) }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-sm-3 col-form-label">Due Date</label>
                                            <div class="col-sm-9">
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i
                                                                class="far fa-calendar-check"></i></span>
                                                    </div>
                                                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                                                        class="form-control" placeholder="Optional">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-secondary card-outline mt-3 mb-2">
                                    <div class="card-header py-2">
                                        <h3 class="card-title">
                                            <i class="fas fa-list-ul mr-1"></i>
                                            Invoice Lines
                                        </h3>
                                        <button type="button" class="btn btn-xs btn-primary float-right" id="add-line">
                                            <i class="fas fa-plus"></i> Add Line
                                        </button>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped mb-0" id="lines">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 8%">Type</th>
                                                        <th style="width: 20%">Item/Account <span
                                                                class="text-danger">*</span></th>
                                                        <th style="width: 20%">Description</th>
                                                        <th style="width: 8%">Qty <span class="text-danger">*</span></th>
                                                        <th style="width: 12%">Unit Price <span
                                                                class="text-danger">*</span></th>
                                                        <th style="width: 10%">VAT</th>
                                                        <th style="width: 10%">WTax</th>
                                                        <th style="width: 12%">Amount</th>
                                                        <th style="width: 8%">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="7" class="text-right">Original Amount:</th>
                                                        <th class="text-right" id="original-amount">0.00</th>
                                                        <th></th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="7" class="text-right">VAT:</th>
                                                        <th class="text-right" id="total-vat">0.00</th>
                                                        <th></th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="7" class="text-right">WTax:</th>
                                                        <th class="text-right" id="total-wtax">0.00</th>
                                                        <th></th>
                                                    </tr>
                                                    <tr class="table-primary">
                                                        <th colspan="7" class="text-right"><strong>Amount Due:</strong>
                                                        </th>
                                                        <th class="text-right" id="amount-due"><strong>0.00</strong></th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-light card-outline mt-3 mb-0">
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
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-save mr-1"></i> Save Invoice
                                        </button>
                                        <a href="{{ route('sales-invoices.index') }}" class="btn btn-default">
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
        window.prefill = @json($prefill ?? null);
        window.items = @json($items ?? []);
        window.accounts = @json($accounts ?? []);
        window.taxCodes = @json($taxCodes ?? []);

        $(document).ready(function() {
            // Initialize Select2BS4 for all select elements
            $('.select2bs4').select2({
                theme: 'bootstrap4',
                placeholder: 'Select an option',
                allowClear: true
            });

            let i = 0;
            const $tb = $('#lines tbody');

            // Add first line
            $('#add-line').on('click', function() {
                addLineRow();
            }).trigger('click');

            // Remove line
            $tb.on('click', '.rm', function() {
                $(this).closest('tr').remove();
                updateTotals();
            });

            // Handle line type change
            $(document).on('change', '.line-type-select', function() {
                const row = $(this).closest('tr');
                const lineType = $(this).val();
                const itemAccountSelect = row.find('.item-account-select');

                // Clear current selection
                itemAccountSelect.empty();

                if (lineType === 'item') {
                    itemAccountSelect.append('<option value="">-- select item --</option>');
                    window.items.forEach(function(item) {
                        itemAccountSelect.append(
                            `<option value="${item.id}" data-type="item">${item.code} - ${item.name}</option>`
                            );
                    });
                } else if (lineType === 'service') {
                    itemAccountSelect.append('<option value="">-- select account --</option>');
                    window.accounts.forEach(function(account) {
                        itemAccountSelect.append(
                            `<option value="${account.id}" data-type="account">${account.code} - ${account.name}</option>`
                            );
                    });
                }

                // Reinitialize Select2
                itemAccountSelect.select2({
                    theme: 'bootstrap4',
                    placeholder: 'Select an option',
                    allowClear: true
                });
            });

            // Update totals when values change
            $(document).on('input', '.qty-input, .price-input', function() {
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
                $('[name=customer_id]').val(window.prefill.customer_id);

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
                        <select name="lines[${lineIdx}][item_account_id]" class="form-control form-control-sm item-account-select" required>
                            <option value="">-- select --</option>
                        </select>
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
                        <input type="number" step="0.01" min="0" name="lines[${lineIdx}][amount]"
                            class="form-control form-control-sm text-right amount-input" value="${data.amount || 0}" readonly>
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

                updateLineAmount(tr);
                updateTotals();
            }

            function updateLineAmount(row) {
                const $row = $(row);
                const qty = parseFloat($row.find('.qty-input').val() || 0);
                const price = parseFloat($row.find('.price-input').val() || 0);
                const vatRate = parseFloat($row.find('.vat-rate-select').val() || 0);
                const wtaxRate = parseFloat($row.find('.wtax-rate-select').val() || 0);

                const originalAmount = qty * price;
                const vatAmount = originalAmount * (vatRate / 100);
                const wtaxAmount = originalAmount * (wtaxRate / 100);
                const amount = originalAmount + vatAmount - wtaxAmount;

                // Update hidden inputs with calculated amounts
                $row.find('.vat-amount-input').val(vatAmount.toFixed(2));
                $row.find('.wtax-amount-input').val(wtaxAmount.toFixed(2));
                $row.find('.amount-input').val(amount.toFixed(2));
            }

            function updateTotals() {
                let originalAmount = 0;
                let totalVat = 0;
                let totalWtax = 0;
                let amountDue = 0;

                // Calculate totals from all line items
                $('#lines tbody tr').each(function() {
                    const qty = parseFloat($(this).find('.qty-input').val() || 0);
                    const price = parseFloat($(this).find('.price-input').val() || 0);
                    const vatAmount = parseFloat($(this).find('.vat-amount-input').val() || 0);
                    const wtaxAmount = parseFloat($(this).find('.wtax-amount-input').val() || 0);

                    originalAmount += qty * price;
                    totalVat += vatAmount;
                    totalWtax += wtaxAmount;
                });

                amountDue = originalAmount + totalVat - totalWtax;

                // Update display with Indonesian number formatting
                $('#original-amount').text(originalAmount.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));

                $('#total-vat').text(totalVat.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));

                $('#total-wtax').text(totalWtax.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));

                $('#amount-due').text(amountDue.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }
        });
    </script>
@endpush
