@extends('layouts.main')

@section('title_page')
    Create Stock Adjustment
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('stock-adjustments.index') }}">Stock Adjustments</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Create Stock Adjustment</h3>
                        </div>
                        <form id="adjustmentForm">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="date">Adjustment Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="date" name="date"
                                                value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="reason">Reason <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="reason" name="reason"
                                                placeholder="e.g., Physical count discrepancy" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="project_id">Project</label>
                                            <select class="form-control select2bs4" id="project_id" name="project_id">
                                                <option value="">Select Project</option>
                                                @foreach ($projects as $project)
                                                    <option value="{{ $project->id }}">{{ $project->code }} -
                                                        {{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fund_id">Fund</label>
                                            <select class="form-control select2bs4" id="fund_id" name="fund_id">
                                                <option value="">Select Fund</option>
                                                @foreach ($funds as $fund)
                                                    <option value="{{ $fund->id }}">{{ $fund->code }} -
                                                        {{ $fund->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="dept_id">Department</label>
                                            <select class="form-control select2bs4" id="dept_id" name="dept_id">
                                                <option value="">Select Department</option>
                                                @foreach ($departments as $department)
                                                    <option value="{{ $department->id }}">{{ $department->code }} -
                                                        {{ $department->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Item Search Section -->
                                <div class="card card-secondary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-search"></i> Add Items
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="category_filter">Filter by Category</label>
                                                    <select class="form-control select2bs4" id="category_filter">
                                                        <option value="">All Categories</option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="item_search">Search Items</label>
                                                    <select class="form-control select2bs4" id="item_search"
                                                        style="width: 100%">
                                                        <option value="">Search by code, name, or barcode...</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    <button type="button" class="btn btn-primary btn-block"
                                                        onclick="addItem()">
                                                        <i class="fas fa-plus"></i> Add
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Adjustment Lines -->
                                <div class="card card-secondary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-list"></i> Adjustment Lines
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="adjustmentLinesTable">
                                                <thead>
                                                    <tr>
                                                        <th>Item Code</th>
                                                        <th>Item Name</th>
                                                        <th>Current Stock</th>
                                                        <th>Adjusted Stock</th>
                                                        <th>Variance</th>
                                                        <th>Unit Cost</th>
                                                        <th>Variance Value</th>
                                                        <th>Notes</th>
                                                        <th width="60">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="adjustmentLinesBody">
                                                    <!-- Lines will be added dynamically -->
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="6" class="text-right">Total Adjustment Value:</th>
                                                        <th id="totalAdjustmentValue" class="text-right">Rp 0</th>
                                                        <th colspan="2"></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <div id="noItemsMessage" class="text-center text-muted py-4"
                                            style="display: none;">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>No items added yet. Use the search above to add items for adjustment.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Adjustment
                                </button>
                                <a href="{{ route('stock-adjustments.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back
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
        let selectedItems = [];
        let adjustmentLines = [];

        $(document).ready(function() {
            // Initialize Select2
            $('.select2bs4').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Category filter change
            $('#category_filter').on('change', function() {
                loadItems();
            });

            // Item search change
            $('#item_search').on('change', function() {
                // Item selection handled in addItem function
            });

            // Load initial items
            loadItems();

            // Form submission
            $('#adjustmentForm').on('submit', function(e) {
                e.preventDefault();

                if (adjustmentLines.length === 0) {
                    Swal.fire('Error!', 'Please add at least one item for adjustment.', 'error');
                    return;
                }

                // Prepare form data
                const formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('date', $('#date').val());
                formData.append('reason', $('#reason').val());
                formData.append('project_id', $('#project_id').val());
                formData.append('fund_id', $('#fund_id').val());
                formData.append('dept_id', $('#dept_id').val());
                formData.append('lines', JSON.stringify(adjustmentLines));

                // Submit form
                $.ajax({
                    url: '{{ route('stock-adjustments.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success').then(() => {
                                window.location.href =
                                    '{{ route('stock-adjustments.index') }}';
                            });
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = 'Please fix the following errors:\n';
                        $.each(errors, function(key, value) {
                            errorMessage += '• ' + value[0] + '\n';
                        });
                        Swal.fire('Error!', errorMessage, 'error');
                    }
                });
            });
        });

        function loadItems() {
            const categoryId = $('#category_filter').val();
            const searchTerm = $('#item_search').data('select2') ? $('#item_search').data('select2').search.val() : '';

            $.get('{{ route('stock-adjustments.search-items') }}', {
                q: searchTerm,
                category_id: categoryId
            }).done(function(data) {
                $('#item_search').empty().append('<option value="">Search by code, name, or barcode...</option>');

                data.forEach(function(item) {
                    $('#item_search').append(new Option(
                        `${item.code} - ${item.name} (Stock: ${item.current_stock} ${item.unit_of_measure})`,
                        item.id,
                        false,
                        false
                    ));
                });
            });
        }

        function addItem() {
            const itemId = $('#item_search').val();
            if (!itemId) {
                Swal.fire('Error!', 'Please select an item first.', 'error');
                return;
            }

            // Check if item already added
            if (adjustmentLines.find(line => line.item_id == itemId)) {
                Swal.fire('Error!', 'This item has already been added.', 'error');
                return;
            }

            // Get item details
            const itemText = $('#item_search option:selected').text();
            const itemCode = itemText.split(' - ')[0];
            const itemName = itemText.split(' - ')[1].split(' (Stock: ')[0];
            const stockInfo = itemText.split(' (Stock: ')[1].split(')')[0];
            const currentStock = parseFloat(stockInfo.split(' ')[0]);

            // Add to adjustment lines
            const lineId = Date.now();
            const line = {
                id: lineId,
                item_id: itemId,
                item_code: itemCode,
                item_name: itemName,
                current_stock: currentStock,
                adjusted_stock: currentStock,
                variance: 0,
                unit_cost: 0,
                variance_value: 0,
                notes: ''
            };

            adjustmentLines.push(line);
            selectedItems.push(itemId);
            renderAdjustmentLines();
            updateTotalValue();

            // Clear selection
            $('#item_search').val('').trigger('change');
        }

        function removeItem(lineId) {
            adjustmentLines = adjustmentLines.filter(line => line.id !== lineId);
            selectedItems = adjustmentLines.map(line => line.item_id);
            renderAdjustmentLines();
            updateTotalValue();
        }

        function updateLine(lineId, field, value) {
            const line = adjustmentLines.find(l => l.id == lineId);
            if (!line) return;

            line[field] = value;

            // Calculate variance
            if (field === 'adjusted_stock') {
                line.variance = line.adjusted_stock - line.current_stock;
                line.variance_value = line.variance * line.unit_cost;
            } else if (field === 'unit_cost') {
                line.variance_value = line.variance * line.unit_cost;
            }

            renderAdjustmentLines();
            updateTotalValue();
        }

        function renderAdjustmentLines() {
            const tbody = $('#adjustmentLinesBody');
            tbody.empty();

            if (adjustmentLines.length === 0) {
                $('#noItemsMessage').show();
                $('#adjustmentLinesTable').hide();
                return;
            }

            $('#noItemsMessage').hide();
            $('#adjustmentLinesTable').show();

            adjustmentLines.forEach(function(line) {
                const row = `
                    <tr>
                        <td>${line.item_code}</td>
                        <td>${line.item_name}</td>
                        <td class="text-right">${line.current_stock}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm" 
                                   value="${line.adjusted_stock}" step="0.0001" min="0"
                                   onchange="updateLine(${line.id}, 'adjusted_stock', parseFloat(this.value))">
                        </td>
                        <td class="text-right ${line.variance >= 0 ? 'text-success' : 'text-danger'}">
                            ${line.variance >= 0 ? '+' : ''}${line.variance.toFixed(4)}
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm" 
                                   value="${line.unit_cost}" step="0.01" min="0"
                                   onchange="updateLine(${line.id}, 'unit_cost', parseFloat(this.value))">
                        </td>
                        <td class="text-right ${line.variance_value >= 0 ? 'text-success' : 'text-danger'}">
                            Rp ${Math.abs(line.variance_value).toLocaleString('id-ID')}
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" 
                                   value="${line.notes}" placeholder="Notes"
                                   onchange="updateLine(${line.id}, 'notes', this.value)">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger" 
                                    onclick="removeItem(${line.id})" title="Remove">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }

        function updateTotalValue() {
            const total = adjustmentLines.reduce((sum, line) => sum + Math.abs(line.variance_value), 0);
            $('#totalAdjustmentValue').text('Rp ' + total.toLocaleString('id-ID'));
        }
    </script>
@endpush
