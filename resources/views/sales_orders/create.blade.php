@extends('layouts.main')

@section('title_page')
    Create Sales Order
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('sales-orders.index') }}">Sales Orders</a></li>
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
                            <h3 class="card-title">Create Sales Order</h3>
                        </div>
                        <form method="post" action="{{ route('sales-orders.store') }}" id="so-form">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Date <span class="text-danger">*</span></label>
                                            <input type="date" name="date" class="form-control"
                                                value="{{ old('date', now()->toDateString()) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Customer <span class="text-danger">*</span></label>
                                            <select name="customer_id" class="form-control select2bs4" required>
                                                <option value="">-- select customer --</option>
                                                @foreach ($customers as $c)
                                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-secondary">
                                    <div class="card-header">
                                        <h3 class="card-title">Order Lines</h3>
                                        <div class="btn-group float-right">
                                            <button type="button" class="btn btn-xs btn-primary" id="add-item-line">
                                                <i class="fas fa-box mr-1"></i>Add Item
                                            </button>
                                            <button type="button" class="btn btn-xs btn-success" id="add-service-line">
                                                <i class="fas fa-cogs mr-1"></i>Add Service
                                            </button>
                                        </div>
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
                                                        <th style="width: 12%">Unit Price <span class="text-danger">*</span>
                                                        </th>
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
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-save mr-1"></i> Save Order
                                </button>
                                <a href="{{ route('sales-orders.index') }}" class="btn btn-default">
                                    <i class="fas fa-times mr-1"></i> Cancel
                                </a>
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

        $(document).ready(function() {
            // Initialize Select2BS4 for customer select
            $('.select2bs4').select2({
                theme: 'bootstrap4',
                placeholder: 'Select an option',
                allowClear: true
            });

            let i = 0;
            const $tb = $('#lines tbody');

            // Add item line
            $('#add-item-line').on('click', function() {
                addItemLineRow();
            });

            // Add service line
            $('#add-service-line').on('click', function() {
                addServiceLineRow();
            });

            // Remove line
            $tb.on('click', '.rm', function() {
                $(this).closest('tr').remove();
                updateTotals();
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
                        if (l.line_type === 'item') {
                            addItemLineRow(l);
                        } else {
                            addServiceLineRow(l);
                        }
                    });
                } else {
                    addItemLineRow();
                }
            } else {
                addItemLineRow();
            }

            function addItemLineRow(data = null) {
                const rowId = `line_${i++}`;
                const row = $(`
                    <tr id="${rowId}">
                        <td>
                            <select name="lines[${i-1}][line_type]" class="form-control form-control-sm line-type-select" required>
                                <option value="item" selected>Item</option>
                                <option value="service">Service</option>
                            </select>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <input type="hidden" name="lines[${i-1}][item_account_id]" class="item-account-id" value="${data?.item_account_id || ''}">
                                <input type="text" class="form-control item-display" placeholder="Click to select item" readonly>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-primary btn-sm select-item-btn" title="Select Item">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type="text" name="lines[${i-1}][description]" class="form-control form-control-sm description-input" 
                                   value="${data?.description || ''}" placeholder="Item description">
                        </td>
                        <td>
                            <input type="number" name="lines[${i-1}][qty]" class="form-control form-control-sm qty-input" 
                                   value="${data?.qty || ''}" step="0.01" min="0.01" required>
                        </td>
                        <td>
                            <input type="number" name="lines[${i-1}][unit_price]" class="form-control form-control-sm price-input" 
                                   value="${data?.unit_price || ''}" step="0.01" min="0" required>
                        </td>
                        <td>
                            <select name="lines[${i-1}][vat_rate]" class="form-control form-control-sm vat-rate-select">
                                <option value="0" ${data?.vat_rate == 0 ? 'selected' : ''}>0%</option>
                                <option value="11" ${data?.vat_rate == 11 ? 'selected' : ''}>11%</option>
                            </select>
                        </td>
                        <td>
                            <select name="lines[${i-1}][wtax_rate]" class="form-control form-control-sm wtax-rate-select">
                                <option value="0" ${data?.wtax_rate == 0 ? 'selected' : ''}>0%</option>
                                <option value="2" ${data?.wtax_rate == 2 ? 'selected' : ''}>2%</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="lines[${i-1}][amount]" class="form-control form-control-sm amount-input" 
                                   value="${data?.amount || ''}" readonly>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger rm" title="Remove line">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);

                $tb.append(row);

                // Initialize Select2 for line type
                row.find('.line-type-select').select2({
                    theme: 'bootstrap4',
                    minimumResultsForSearch: Infinity
                });

                // Handle line type change
                row.find('.line-type-select').on('change', function() {
                    const lineType = $(this).val();
                    const itemDisplay = row.find('.item-display');
                    const selectBtn = row.find('.select-item-btn');

                    if (lineType === 'service') {
                        itemDisplay.attr('placeholder', 'Click to select account');
                        selectBtn.attr('title', 'Select Account');
                        // Convert to service line
                        convertToServiceLine(row);
                    } else {
                        itemDisplay.attr('placeholder', 'Click to select item');
                        selectBtn.attr('title', 'Select Item');
                        // Convert to item line
                        convertToItemLine(row);
                    }
                });

                // Handle item selection
                row.find('.select-item-btn').on('click', function() {
                    const lineType = row.find('.line-type-select').val();

                    if (lineType === 'item') {
                        window.itemSelector.open(function(item) {
                            row.find('.item-account-id').val(item.id);
                            row.find('.item-display').val(`${item.code} - ${item.name}`);
                            row.find('.description-input').val(item.description || item.name);
                            row.find('.price-input').val(item.last_cost_price || 0);
                            updateLineAmount(row);
                            updateTotals();
                        });
                    } else {
                        // Handle account selection for services
                        openAccountSelector(row);
                    }
                });

                // Calculate initial amount
                updateLineAmount(row);
                updateTotals();
            }

            function addServiceLineRow(data = null) {
                const rowId = `line_${i++}`;
                const row = $(`
                    <tr id="${rowId}">
                        <td>
                            <select name="lines[${i-1}][line_type]" class="form-control form-control-sm line-type-select" required>
                                <option value="item">Item</option>
                                <option value="service" selected>Service</option>
                            </select>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <input type="hidden" name="lines[${i-1}][item_account_id]" class="item-account-id" value="${data?.item_account_id || ''}">
                                <input type="text" class="form-control item-display" placeholder="Click to select account" readonly>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-primary btn-sm select-item-btn" title="Select Account">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type="text" name="lines[${i-1}][description]" class="form-control form-control-sm description-input" 
                                   value="${data?.description || ''}" placeholder="Service description">
                        </td>
                        <td>
                            <input type="number" name="lines[${i-1}][qty]" class="form-control form-control-sm qty-input" 
                                   value="${data?.qty || ''}" step="0.01" min="0.01" required>
                        </td>
                        <td>
                            <input type="number" name="lines[${i-1}][unit_price]" class="form-control form-control-sm price-input" 
                                   value="${data?.unit_price || ''}" step="0.01" min="0" required>
                        </td>
                        <td>
                            <select name="lines[${i-1}][vat_rate]" class="form-control form-control-sm vat-rate-select">
                                <option value="0" ${data?.vat_rate == 0 ? 'selected' : ''}>0%</option>
                                <option value="11" ${data?.vat_rate == 11 ? 'selected' : ''}>11%</option>
                            </select>
                        </td>
                        <td>
                            <select name="lines[${i-1}][wtax_rate]" class="form-control form-control-sm wtax-rate-select">
                                <option value="0" ${data?.wtax_rate == 0 ? 'selected' : ''}>0%</option>
                                <option value="2" ${data?.wtax_rate == 2 ? 'selected' : ''}>2%</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="lines[${i-1}][amount]" class="form-control form-control-sm amount-input" 
                                   value="${data?.amount || ''}" readonly>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger rm" title="Remove line">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);

                $tb.append(row);

                // Initialize Select2 for line type
                row.find('.line-type-select').select2({
                    theme: 'bootstrap4',
                    minimumResultsForSearch: Infinity
                });

                // Handle line type change
                row.find('.line-type-select').on('change', function() {
                    const lineType = $(this).val();
                    const itemDisplay = row.find('.item-display');
                    const selectBtn = row.find('.select-item-btn');

                    if (lineType === 'item') {
                        itemDisplay.attr('placeholder', 'Click to select item');
                        selectBtn.attr('title', 'Select Item');
                        convertToItemLine(row);
                    } else {
                        itemDisplay.attr('placeholder', 'Click to select account');
                        selectBtn.attr('title', 'Select Account');
                        convertToServiceLine(row);
                    }
                });

                // Handle account selection
                row.find('.select-item-btn').on('click', function() {
                    openAccountSelector(row);
                });

                // Calculate initial amount
                updateLineAmount(row);
                updateTotals();
            }

            function convertToItemLine(row) {
                const itemDisplay = row.find('.item-display');
                const selectBtn = row.find('.select-item-btn');

                itemDisplay.attr('placeholder', 'Click to select item');
                selectBtn.attr('title', 'Select Item');

                // Clear current selection
                row.find('.item-account-id').val('');
                itemDisplay.val('');

                // Update click handler
                selectBtn.off('click').on('click', function() {
                    window.itemSelector.open(function(item) {
                        row.find('.item-account-id').val(item.id);
                        row.find('.item-display').val(`${item.code} - ${item.name}`);
                        row.find('.description-input').val(item.description || item.name);
                        row.find('.price-input').val(item.last_cost_price || 0);
                        updateLineAmount(row);
                        updateTotals();
                    });
                });
            }

            function convertToServiceLine(row) {
                const itemDisplay = row.find('.item-display');
                const selectBtn = row.find('.select-item-btn');

                itemDisplay.attr('placeholder', 'Click to select account');
                selectBtn.attr('title', 'Select Account');

                // Clear current selection
                row.find('.item-account-id').val('');
                itemDisplay.val('');

                // Update click handler
                selectBtn.off('click').on('click', function() {
                    openAccountSelector(row);
                });
            }

            function openAccountSelector(row) {
                // Simple account selection - could be enhanced with a modal similar to items
                const accountSelect = $(`
                    <select class="form-control form-control-sm">
                        <option value="">-- select account --</option>
                        ${window.accounts.map(account => 
                            `<option value="${account.id}">${account.code} - ${account.name}</option>`
                        ).join('')}
                    </select>
                `);

                // Replace the input group with select
                const inputGroup = row.find('.input-group');
                inputGroup.html(accountSelect);

                // Initialize Select2
                accountSelect.select2({
                    theme: 'bootstrap4',
                    placeholder: 'Select an account',
                    allowClear: true
                });

                // Handle selection
                accountSelect.on('change', function() {
                    const accountId = $(this).val();
                    const account = window.accounts.find(acc => acc.id == accountId);

                    if (account) {
                        row.find('.item-account-id').val(accountId);
                        row.find('.description-input').val(account.name);
                        updateLineAmount(row);
                        updateTotals();
                    }
                });
            }

            function updateLineAmount(row) {
                const qty = parseFloat(row.find('.qty-input').val()) || 0;
                const price = parseFloat(row.find('.price-input').val()) || 0;
                const vatRate = parseFloat(row.find('.vat-rate-select').val()) || 0;
                const wtaxRate = parseFloat(row.find('.wtax-rate-select').val()) || 0;

                const originalAmount = qty * price;
                const vatAmount = originalAmount * (vatRate / 100);
                const wtaxAmount = originalAmount * (wtaxRate / 100);
                const totalAmount = originalAmount + vatAmount - wtaxAmount;

                row.find('.amount-input').val(totalAmount.toFixed(2));

                // Store calculated values for form submission
                row.find('input[name*="[vat_amount]"]').val(vatAmount.toFixed(2));
                row.find('input[name*="[wtax_amount]"]').val(wtaxAmount.toFixed(2));
            }

            function updateTotals() {
                let originalTotal = 0;
                let vatTotal = 0;
                let wtaxTotal = 0;

                $tb.find('tr').each(function() {
                    const amount = parseFloat($(this).find('.amount-input').val()) || 0;
                    const qty = parseFloat($(this).find('.qty-input').val()) || 0;
                    const price = parseFloat($(this).find('.price-input').val()) || 0;
                    const vatRate = parseFloat($(this).find('.vat-rate-select').val()) || 0;
                    const wtaxRate = parseFloat($(this).find('.wtax-rate-select').val()) || 0;

                    const originalAmount = qty * price;
                    const vatAmount = originalAmount * (vatRate / 100);
                    const wtaxAmount = originalAmount * (wtaxRate / 100);

                    originalTotal += originalAmount;
                    vatTotal += vatAmount;
                    wtaxTotal += wtaxAmount;
                });

                $('#original-amount').text(originalTotal.toFixed(2));
                $('#total-vat').text(vatTotal.toFixed(2));
                $('#total-wtax').text(wtaxTotal.toFixed(2));
                $('#amount-due').text((originalTotal + vatTotal - wtaxTotal).toFixed(2));
            }
        });
    </script>
@endpush
