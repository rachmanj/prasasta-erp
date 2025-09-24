@extends('layouts.main')

@section('title', 'Create Sales Receipt')

@section('title_page')
    Create Sales Receipt
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('sales-receipts.index') }}">Sales Receipts</a></li>
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
                                <i class="fas fa-hand-holding-usd mr-1"></i>
                                New Sales Receipt
                            </h3>
                            <a href="{{ route('sales-receipts.index') }}" class="btn btn-sm btn-secondary float-right">
                                <i class="fas fa-arrow-left"></i> Back to Sales Receipts
                            </a>
                        </div>
                        <form method="post" action="{{ route('sales-receipts.store') }}">
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
                                                        placeholder="Receipt description">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-secondary card-outline mt-3 mb-2">
                                    <div class="card-header py-2">
                                        <h3 class="card-title">
                                            <i class="fas fa-list-ul mr-1"></i>
                                            Receipt Lines
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
                                                        <th style="width: 30%">Bank/Cash Account <span
                                                                class="text-danger">*</span></th>
                                                        <th style="width: 30%">Description</th>
                                                        <th style="width: 15%">Amount <span class="text-danger">*</span>
                                                        </th>
                                                        <th style="width: 15%">Project</th>
                                                        <th style="width: 15%">Fund</th>
                                                        <th style="width: 15%">Department</th>
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
                                                                class="form-control form-control-sm"
                                                                placeholder="Description">
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" min="0.01"
                                                                name="lines[0][amount]"
                                                                class="form-control form-control-sm text-right amount-input"
                                                                value="0">
                                                        </td>
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
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-xs btn-default"
                                                                disabled>
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="2" class="text-right">Total:</th>
                                                        <th class="text-right" id="total-amount">0.00</th>
                                                        <th colspan="4"></th>
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
                                            <i class="fas fa-save mr-1"></i> Save Receipt
                                        </button>
                                        <a href="{{ route('sales-receipts.index') }}" class="btn btn-default">
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
                    <input type="text" name="lines[${idx}][description]" class="form-control form-control-sm" placeholder="Description">
                </td>
                <td>
                    <input type="number" step="0.01" min="0.01" name="lines[${idx}][amount]" 
                        class="form-control form-control-sm text-right amount-input" value="0">
                </td>
                <td>
                    <select name="lines[${idx}][project_id]" class="form-control form-control-sm select2bs4">
                        <option value="">-- none --</option>
                        ${@json($projects).map(p => `<option value="${p.id}">${p.code} - ${p.name}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <select name="lines[${idx}][fund_id]" class="form-control form-control-sm select2bs4">
                        <option value="">-- none --</option>
                        ${@json($funds).map(f => `<option value="${f.id}">${f.code} - ${f.name}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <select name="lines[${idx}][dept_id]" class="form-control form-control-sm select2bs4">
                        <option value="">-- none --</option>
                        ${@json($departments).map(d => `<option value="${d.id}">${d.code} - ${d.name}</option>`).join('')}
                    </select>
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
            const customerId = document.querySelector('select[name="customer_id"]').value;

            if (!customerId || amount <= 0) {
                toastr.warning('Please select a customer and enter a receipt amount');
                return;
            }

            const params = new URLSearchParams({
                customer_id: customerId,
                amount: amount
            });

            try {
                const res = await fetch(`{{ route('sales-receipts.previewAllocation') }}?${params.toString()}`);
                const data = await res.json();
                const tbody = document.querySelector('#alloc-table tbody');
                tbody.innerHTML = '';

                if (data.rows.length === 0) {
                    const tr = document.createElement('tr');
                    tr.innerHTML =
                        '<td colspan="3" class="text-center">No outstanding invoices found for this customer</td>';
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
