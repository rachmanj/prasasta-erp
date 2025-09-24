@extends('layouts.main')

@section('title_page', 'Inventory Valuation Report')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Inventory Valuation Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('inventory.reports.dashboard') }}">Inventory
                                Reports</a></li>
                        <li class="breadcrumb-item active">Inventory Valuation</li>
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
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalItems">-</h3>
                            <p>Total Items</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="totalValue">-</h3>
                            <p>Total Value</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="avgValue">-</h3>
                            <p>Average Value per Item</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="zeroValueItems">-</h3>
                            <p>Zero Value Items</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-circle"></i>
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
                            <div class="col-md-4">
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="value_range_filter">Value Range</label>
                                    <select class="form-control" id="value_range_filter" name="value_range">
                                        <option value="">All Values</option>
                                        <option value="zero">Zero Value</option>
                                        <option value="low">Low Value (< Rp 100,000)</option>
                                        <option value="medium">Medium Value (Rp 100,000 - Rp 1,000,000)</option>
                                        <option value="high">High Value (> Rp 1,000,000)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="search_filter">Search</label>
                                    <input type="text" class="form-control" id="search_filter" name="search"
                                        placeholder="Search by code, name, or barcode...">
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
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Valuation Report Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calculator"></i>
                        Inventory Valuation Report
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="inventoryValuationTable">
                            <thead>
                                <tr>
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>Unit</th>
                                    <th>Quantity</th>
                                    <th>Unit Cost</th>
                                    <th>Total Value</th>
                                    <th>Last Movement</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Category Summary Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie"></i>
                        Valuation by Category
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="categorySummaryTable">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Items Count</th>
                                    <th>Total Quantity</th>
                                    <th>Total Value</th>
                                    <th>Average Value</th>
                                    <th>% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
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

            // Initialize main DataTable
            var table = $('#inventoryValuationTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('inventory.reports.stock-status.data') }}', // Reuse stock status data
                    data: function(d) {
                        d.category_id = $('#category_filter').val();
                        d.search = $('#search_filter').val();
                        d.valuation_only = true;
                    }
                },
                columns: [{
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
                        data: 'unit_of_measure',
                        name: 'unit_of_measure'
                    },
                    {
                        data: 'current_stock_quantity',
                        name: 'current_stock_quantity'
                    },
                    {
                        data: 'average_cost_price',
                        name: 'average_cost_price'
                    },
                    {
                        data: 'current_stock_value',
                        name: 'current_stock_value'
                    },
                    {
                        data: 'last_movement_date',
                        name: 'last_movement_date'
                    },
                    {
                        data: 'stock_status',
                        name: 'stock_status'
                    }
                ],
                order: [
                    [6, 'desc']
                ], // Order by total value descending
                pageLength: 25,
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();

                    // Calculate totals
                    var totalValue = 0;
                    var totalItems = 0;

                    api.rows({
                        page: 'current'
                    }).every(function() {
                        var data = this.data();
                        var value = parseFloat(data[6].replace(/[^\d.-]/g, '')) || 0;
                        totalValue += value;
                        totalItems += 1;
                    });

                    // Update summary cards
                    $('#totalItems').text(totalItems.toLocaleString());
                    $('#totalValue').text('Rp ' + totalValue.toLocaleString('id-ID', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }));
                    $('#avgValue').text('Rp ' + (totalItems > 0 ? (totalValue / totalItems)
                        .toLocaleString('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }) : '0'));
                }
            });

            // Load category summary
            function loadCategorySummary() {
                $.ajax({
                    url: '{{ route('inventory.reports.stock-status.data') }}',
                    data: {
                        category_summary: true,
                        draw: 1
                    },
                    success: function(response) {
                        if (response.category_summary) {
                            var tbody = $('#categorySummaryTable tbody');
                            tbody.empty();

                            var totalValue = 0;
                            response.category_summary.forEach(function(category) {
                                totalValue += parseFloat(category.total_value);
                            });

                            response.category_summary.forEach(function(category) {
                                var percentage = totalValue > 0 ? ((category.total_value /
                                    totalValue) * 100).toFixed(1) : 0;
                                var row = '<tr>' +
                                    '<td><strong>' + category.category_name + '</strong></td>' +
                                    '<td>' + category.items_count + '</td>' +
                                    '<td>' + parseFloat(category.total_quantity).toLocaleString(
                                        'id-ID', {
                                            minimumFractionDigits: 2
                                        }) + '</td>' +
                                    '<td>Rp ' + parseFloat(category.total_value).toLocaleString(
                                        'id-ID', {
                                            minimumFractionDigits: 0,
                                            maximumFractionDigits: 0
                                        }) + '</td>' +
                                    '<td>Rp ' + parseFloat(category.average_value)
                                    .toLocaleString('id-ID', {
                                        minimumFractionDigits: 0,
                                        maximumFractionDigits: 0
                                    }) + '</td>' +
                                    '<td>' + percentage + '%</td>' +
                                    '</tr>';
                                tbody.append(row);
                            });

                            // Update zero value items count
                            var zeroValueItems = response.category_summary.reduce(function(total,
                                category) {
                                return total + (category.zero_value_items || 0);
                            }, 0);
                            $('#zeroValueItems').text(zeroValueItems);
                        }
                    }
                });
            }

            // Apply filters function
            window.applyFilters = function() {
                table.ajax.reload();
                loadCategorySummary();
            };

            // Export function
            window.exportReport = function() {
                var params = new URLSearchParams();
                params.append('category_id', $('#category_filter').val());
                params.append('value_range', $('#value_range_filter').val());
                params.append('search', $('#search_filter').val());
                params.append('export_type', 'valuation');

                window.open('{{ route('inventory.reports.export.stock-status') }}?' + params.toString(),
                    '_blank');
            };

            // Auto-apply filters when Enter is pressed in search field
            $('#search_filter').on('keypress', function(e) {
                if (e.which === 13) {
                    applyFilters();
                }
            });

            // Load initial data
            loadCategorySummary();
        });
    </script>
@endpush
