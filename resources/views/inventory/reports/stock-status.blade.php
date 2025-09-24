@extends('layouts.main')

@section('title_page', 'Stock Status Report')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Stock Status Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('inventory.reports.dashboard') }}">Inventory
                                Reports</a></li>
                        <li class="breadcrumb-item active">Stock Status</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
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
                                        <option value="">All Status</option>
                                        <option value="low_stock">Low Stock</option>
                                        <option value="out_of_stock">Out of Stock</option>
                                        <option value="in_stock">In Stock</option>
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
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="button" class="btn btn-primary btn-block" onclick="applyFilters()">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Report Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i>
                        Stock Status Report
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" onclick="exportReport()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="stockStatusTable">
                            <thead>
                                <tr>
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>Unit</th>
                                    <th>Current Stock</th>
                                    <th>Stock Value</th>
                                    <th>Last Cost</th>
                                    <th>Avg Cost</th>
                                    <th>Status</th>
                                    <th>Reorder Level</th>
                                    <th>Max Level</th>
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

            // Initialize DataTable
            var table = $('#stockStatusTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('inventory.reports.stock-status.data') }}',
                    data: function(d) {
                        d.category_id = $('#category_filter').val();
                        d.stock_status = $('#stock_status_filter').val();
                        d.search = $('#search_filter').val();
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
                        name: 'current_stock_quantity',
                        orderable: false
                    },
                    {
                        data: 'current_stock_value',
                        name: 'current_stock_value',
                        orderable: false
                    },
                    {
                        data: 'last_cost_price',
                        name: 'last_cost_price',
                        orderable: false
                    },
                    {
                        data: 'average_cost_price',
                        name: 'average_cost_price',
                        orderable: false
                    },
                    {
                        data: 'stock_status',
                        name: 'stock_status',
                        orderable: false
                    },
                    {
                        data: 'reorder_level',
                        name: 'min_stock_level',
                        orderable: false
                    },
                    {
                        data: 'max_stock_level',
                        name: 'max_stock_level',
                        orderable: false
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                pageLength: 25,
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
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
                params.append('search', $('#search_filter').val());

                window.open('{{ route('inventory.reports.export.stock-status') }}?' + params.toString(),
                    '_blank');
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
