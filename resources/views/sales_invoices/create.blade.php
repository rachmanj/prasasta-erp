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
                                                        <th style="width: 25%">Revenue Account <span
                                                                class="text-danger">*</span></th>
                                                        <th style="width: 25%">Description</th>
                                                        <th style="width: 10%">Qty</th>
                                                        <th style="width: 15%">Unit Price</th>
                                                        <th style="width: 15%">Tax Code</th>
                                                        <th style="width: 10%">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="lines">
                                                    <tr class="line-item" id="line-0">
                                                        <td>
                                                            <select name="lines[0][account_id]"
                                                                class="form-control form-control-sm select2bs4" required>
                                                                @foreach ($accounts as $a)
                                                                    <option value="{{ $a->id }}">
                                                                        {{ $a->code }} - {{ $a->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="lines[0][description]"
                                                                class="form-control form-control-sm">
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" min="0.01"
                                                                name="lines[0][qty]" class="form-control form-control-sm"
                                                                value="1">
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" min="0"
                                                                name="lines[0][unit_price]"
                                                                class="form-control form-control-sm" value="0">
                                                        </td>
                                                        <td>
                                                            <select name="lines[0][tax_code_id]"
                                                                class="form-control form-control-sm select2bs4">
                                                                <option value="">-- none --</option>
                                                                @foreach ($taxCodes as $t)
                                                                    <option value="{{ $t->id }}">
                                                                        {{ $t->code }} ({{ $t->rate }})</option>
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
                                                <tbody>
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
        $(document).ready(function() {
            // Initialize Select2BS4 for all select elements
            $('.select2bs4').select2({
                theme: 'bootstrap4',
                placeholder: 'Select an option',
                allowClear: true
            });

            // Initialize card widget functionality
            $('.card-widget').CardWidget();
        });

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
                    <input type="number" step="0.01" min="0.01" name="lines[${idx}][qty]" class="form-control form-control-sm" value="1">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="lines[${idx}][unit_price]" class="form-control form-control-sm" value="0">
                </td>
                <td>
                    <select name="lines[${idx}][tax_code_id]" class="form-control form-control-sm select2bs4">
                        <option value="">-- none --</option>
                        ${@json($taxCodes).map(t => `<option value="${t.id}">${t.code} (${t.rate})</option>`).join('')}
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

            // Add dimensions row
            addDimensionRow(idx);

            idx++;
        }

        function addDimensionRow(lineIdx) {
            const dimensionsTable = document.querySelector('.card-light table tbody');
            const tr = document.createElement('tr');
            tr.id = 'dim-' + lineIdx;

            tr.innerHTML = `
                <td>
                    <select name="lines[${lineIdx}][project_id]" class="form-control form-control-sm select2bs4">
                        <option value="">-- none --</option>
                        ${@json($projects).map(p => `<option value="${p.id}">${p.code} - ${p.name}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <select name="lines[${lineIdx}][fund_id]" class="form-control form-control-sm select2bs4">
                        <option value="">-- none --</option>
                        ${@json($funds).map(f => `<option value="${f.id}">${f.code} - ${f.name}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <select name="lines[${lineIdx}][dept_id]" class="form-control form-control-sm select2bs4">
                        <option value="">-- none --</option>
                        ${@json($departments).map(d => `<option value="${d.id}">${d.code} - ${d.name}</option>`).join('')}
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

        function removeLine(lineIdx) {
            document.getElementById('line-' + lineIdx).remove();
            document.getElementById('dim-' + lineIdx).remove();
        }
    </script>
@endpush
