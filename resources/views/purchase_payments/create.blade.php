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
                                            <label class="col-sm-3 col-form-label">Vendor <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <select name="vendor_id" class="form-control form-control-sm select2bs4"
                                                    required>
                                                    <option value="">-- select vendor --</option>
                                                    @foreach ($vendors as $v)
                                                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
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
                                                        placeholder="Payment description">
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
                                                <thead>
                                                    <tr>
                                                        <th style="width: 50%">Bank/Cash Account <span
                                                                class="text-danger">*</span></th>
                                                        <th style="width: 30%">Notes</th>
                                                        <th style="width: 15%">Amount <span class="text-danger">*</span>
                                                        </th>
                                                        <th style="width: 5%">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="lines">
                                                    <tr class="line-item" id="line-0">
                                                        <td>
                                                            <select name="lines[0][account_id]"
                                                                class="form-control form-control-sm select2bs4" required>
                                                                @foreach ($accounts as $a)
                                                                    <option value="{{ $a->id }}">{{ $a->code }}
                                                                        - {{ $a->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="lines[0][description]"
                                                                class="form-control form-control-sm" placeholder="Notes">
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" min="0.01"
                                                                name="lines[0][amount]"
                                                                class="form-control form-control-sm text-right amount-input"
                                                                value="0">
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-xs btn-default" disabled>
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="2" class="text-right">Total:</th>
                                                        <th class="text-right" id="total-amount">0.00</th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
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

            // Update total amount when any amount input changes
            $(document).on('input', '.amount-input', function() {
                updateTotalAmount();
            });

            // Initial total calculation
            updateTotalAmount();
        });

        function updateTotalAmount() {
            const total = Array.from(document.querySelectorAll('.amount-input'))
                .reduce((sum, el) => sum + parseFloat(el.value || 0), 0);
            document.getElementById('total-amount').textContent = total.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        let idx = 1;

        function addLine() {
            const tbody = document.getElementById('lines');
            const tr = document.createElement('tr');
            tr.className = 'line-item';
            tr.id = 'line-' + idx;

            tr.innerHTML = `
                <td>
                    <select name="lines[${idx}][account_id]" class="form-control form-control-sm select2bs4" required>
                        ${@json($accounts).map(a => `<option value="${a.id}">${a.code} - ${a.name}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <input type="text" name="lines[${idx}][description]" class="form-control form-control-sm" placeholder="Notes">
                </td>
                <td>
                    <input type="number" step="0.01" min="0.01" name="lines[${idx}][amount]" 
                        class="form-control form-control-sm text-right amount-input" value="0">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-danger" onclick="removeLine(${idx})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);

            // Initialize Select2BS4 for the newly added select elements
            $(tr).find('.select2bs4').select2({
                theme: 'bootstrap4',
                placeholder: 'Select an option',
                allowClear: true
            });

            idx++;
            updateTotalAmount();
        }

        function removeLine(lineIdx) {
            document.getElementById('line-' + lineIdx).remove();
            updateTotalAmount();
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
                        <td class="text-right">${Number(r.remaining_before).toLocaleString('id-ID', {minimumFractionDigits: 2})}</td>
                        <td class="text-right">${Number(r.allocate).toLocaleString('id-ID', {minimumFractionDigits: 2})}</td>
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
                    <td class="text-right">${totalAllocated.toLocaleString('id-ID', {minimumFractionDigits: 2})}</td>
                `;
                tbody.appendChild(totalRow);

            } catch (error) {
                console.error('Error fetching allocation preview:', error);
                toastr.error('Failed to load allocation preview');
            }
        }
    </script>
@endpush
