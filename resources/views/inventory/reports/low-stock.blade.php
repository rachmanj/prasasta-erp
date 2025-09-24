@extends('layouts.main')

@section('title_page', 'Low Stock Report')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Low Stock Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('inventory.reports.dashboard') }}">Inventory
                                Reports</a></li>
                        <li class="breadcrumb-item active">Low Stock</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Summary Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="lowStockCount">-</h3>
                            <p>Low Stock Items</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="outOfStockCount">-</h3>
                            <p>Out of Stock Items</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalValueAtRisk">-</h3>
                            <p>Value at Risk</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="itemsNeedingReorder">-</h3>
                            <p>Need Reorder</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter"></i>
                        Filters
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="category_filter">Category</label>
                                    <select class="form-control select2" id="category_filter" name="category_id">
                                        <option value="">All Categories</option>
                                        @foreach (\App\Models\InventoryCategory::active()->get() as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="stock_status_filter">Stock Status</label>
                                    <select class="form-control" id="stock_status_filter" name="stock_status">
                                        <option value="">All Low Stock</option>
                                        <option value="low_stock">Low Stock Only</option>
                                        <option value="out_of_stock">Out of Stock Only</option>
                                        <option value="critical">Critical (≤ 10% of min level)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="urgency_filter">Urgency</label>
                                    <select class="form-control" id="urgency_filter" name="urgency">
                                        <option value="">All</option>
                                        <option value="critical">Critical (0 stock)</option>
                                        <option value="urgent">Urgent (1-25% of min)</option>
                                        <option value="warning">Warning (26-50% of min)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search_filter">Search</label>
                                    <input type="text" class="form-control" id="search_filter" name="search"
                                        placeholder="Search items...">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <button type="button" class="btn btn-success" onclick="exportReport()">
                                    <i class="fas fa-download"></i> Export
                                </button>
                                <button type="button" class="btn btn-warning" onclick="generatePurchaseOrder()">
                                    <i class="fas fa-file-invoice"></i> Generate PO
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Low Stock Report Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        Low Stock Items
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-success" onclick="selectAllItems()">
                            <i class="fas fa-check-square"></i> Select All
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="clearSelection()">
                            <i class="fas fa-square"></i> Clear All
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="lowStockTable">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                                    </th>
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>Current Stock</th>
                                    <th>Min Level</th>
                                    <th>Max Level</th>
                                    <th>Shortfall</th>
                                    <th>Suggest Qty</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <button type="button" class="btn btn-warning btn-block" onclick="createStockAdjustment()">
                                <i class="fas fa-adjust"></i> Stock Adjustment
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-info btn-block" onclick="viewStockMovement()">
                                <i class="fas fa-chart-line"></i> Stock Movement
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary btn-block" onclick="viewInventoryValuation()">
                                <i class="fas fa-calculator"></i> Inventory Valuation
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-success btn-block" onclick="viewDashboard()">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: 'Select Category'
            });

            // Initialize DataTable
            var table = $('#lowStockTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('inventory.reports.stock-status.data') }}',
                    data: function(d) {
                        d.category_id = $('#category_filter').val();
                        d.stock_status = 'low_stock'; // Default to low stock
                        d.urgency = $('#urgency_filter').val();
                        d.search = $('#search_filter').val();
                        d.low_stock_report = true;
                    }
                },
                columns: [{
                        data: null,
                        name: 'select',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="item-checkbox" value="' + row.id +
                                '">';
                        }
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'category_name',
                        name: 'category.name'
                    },
                    {
                        data: 'current_stock_quantity',
                        name: 'current_stock_quantity'
                    },
                    {
                        data: 'reorder_level',
                        name: 'min_stock_level'
                    },
                    {
                        data: 'max_stock_level',
                        name: 'max_stock_level'
                    },
                    {
                        data: null,
                        name: 'shortfall',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var current = parseFloat(row.current_stock_quantity) || 0;
                            var min = parseFloat(row.min_stock_level) || 0;
                            var shortfall = Math.max(0, min - current);
                            return shortfall.toFixed(4);
                        }
                    },
                    {
                        data: null,
                        name: 'suggest_qty',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var current = parseFloat(row.current_stock_quantity) || 0;
                            var min = parseFloat(row.min_stock_level) || 0;
                            var max = parseFloat(row.max_stock_level) || min * 2;
                            var suggest = Math.max(0, max - current);
                            return suggest.toFixed(4);
                        }
                    },
                    {
                        data: 'stock_status',
                        name: 'stock_status'
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<a href="/items/' + row.id +
                                '" class="btn btn-sm btn-info" title="View Item"><i class="fas fa-eye"></i></a> ' +
                                '<a href="/inventory/stock-adjustments/create?item_id=' + row.id +
                                '" class="btn btn-sm btn-warning" title="Stock Adjustment"><i class="fas fa-adjust"></i></a>';
                        }
                    }
                ],
                order: [
                    [7, 'desc']
                ], // Order by shortfall descending
                pageLength: 25,
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                footerCallback: function(row, data, start, end, display) {
                    // Update summary cards
                    var lowStockCount = 0;
                    var outOfStockCount = 0;
                    var totalValueAtRisk = 0;

                    api.rows({
                        page: 'current'
                    }).every(function() {
                        var data = this.data();
                        lowStockCount++;
                        if (data[4] == '0.0000') { // current_stock_quantity
                            outOfStockCount++;
                        }
                        // Add value calculation if needed
                    });

                    $('#lowStockCount').text(lowStockCount);
                    $('#outOfStockCount').text(outOfStockCount);
                    $('#totalValueAtRisk').text('Rp ' + totalValueAtRisk.toLocaleString('id-ID'));
                    $('#itemsNeedingReorder').text(lowStockCount);
                }
            });

            // Apply filters function
            window.applyFilters = function() {
                table.ajax.reload();
            };

            // Export function
            window.exportReport = function() {
                var params = new URLSearchParams();
                params.append('category_id', $('#category_filter').val());
                params.append('stock_status', $('#stock_status_filter').val());
                params.append('urgency', $('#urgency_filter').val());
                params.append('search', $('#search_filter').val());
                params.append('export_type', 'low_stock');

                window.open('{{ route('inventory.reports.export.stock-status') }}?' + params.toString(),
                    '_blank');
            };

            // Select all items
            window.selectAllItems = function() {
                $('.item-checkbox').prop('checked', true);
                $('#selectAllCheckbox').prop('checked', true);
            };

            // Clear selection
            window.clearSelection = function() {
                $('.item-checkbox').prop('checked', false);
                $('#selectAllCheckbox').prop('checked', false);
            };

            // Toggle select all
            window.toggleSelectAll = function() {
                var isChecked = $('#selectAllCheckbox').prop('checked');
                $('.item-checkbox').prop('checked', isChecked);
            };

            // Generate purchase order
            window.generatePurchaseOrder = function() {
                var selectedItems = [];
                $('.item-checkbox:checked').each(function() {
                    selectedItems.push($(this).val());
                });

                if (selectedItems.length === 0) {
                    alert('Please select items to generate purchase order.');
                    return;
                }

                // Redirect to purchase order creation with selected items
                var params = new URLSearchParams();
                selectedItems.forEach(function(itemId) {
                    params.append('items[]', itemId);
                });

                window.open('/purchase-orders/create?' + params.toString(), '_blank');
            };

            // Quick action functions
            window.createStockAdjustment = function() {
                window.location.href = '{{ route('stock-adjustments.create') }}';
            };

            window.viewStockMovement = function() {
                window.location.href = '{{ route('inventory.reports.stock-movement') }}';
            };

            window.viewInventoryValuation = function() {
                window.location.href = '{{ route('inventory.reports.inventory-valuation') }}';
            };

            window.viewDashboard = function() {
                window.location.href = '{{ route('inventory.reports.dashboard') }}';
            };

            // Auto-apply filters when Enter is pressed in search field
            $('#search_filter').on('keypress', function(e) {
                if (e.which === 13) {
                    applyFilters();
                }
            });
        });
    </script>
@endpush
