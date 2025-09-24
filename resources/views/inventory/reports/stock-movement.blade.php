@extends('layouts.main')

@section('title_page', 'Stock Movement Report')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Stock Movement Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('inventory.reports.dashboard') }}">Inventory
                                Reports</a></li>
                        <li class="breadcrumb-item active">Stock Movement</li>
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
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                        value="{{ date('Y-m-01') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="item_filter">Item</label>
                                    <select class="form-control select2" id="item_filter" name="item_id">
                                        <option value="">All Items</option>
                                        @foreach (\App\Models\Item::active()->with('category')->get() as $item)
                                            <option value="{{ $item->id }}">{{ $item->code }} - {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="movement_type_filter">Movement Type</label>
                                    <select class="form-control" id="movement_type_filter" name="movement_type">
                                        <option value="">All Types</option>
                                        <option value="in">Stock In</option>
                                        <option value="out">Stock Out</option>
                                        <option value="adjustment">Adjustment</option>
                                        <option value="transfer">Transfer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="search_filter">Search</label>
                                    <input type="text" class="form-control" id="search_filter" name="search"
                                        placeholder="Search reference...">
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
                        <i class="fas fa-chart-line"></i>
                        Stock Movement Report
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" onclick="exportReport()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="stockMovementTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Item</th>
                                    <th>Movement Type</th>
                                    <th>Quantity</th>
                                    <th>Unit Cost</th>
                                    <th>Total Cost</th>
                                    <th>Reference</th>
                                    <th>Notes</th>
                                    <th>Created By</th>
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
                placeholder: 'Select Item'
            });

            // Initialize DataTable
            var table = $('#stockMovementTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('inventory.reports.stock-movement.data') }}',
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.item_id = $('#item_filter').val();
                        d.movement_type = $('#movement_type_filter').val();
                        d.search = $('#search_filter').val();
                    }
                },
                columns: [{
                        data: 'movement_date',
                        name: 'movement_date'
                    },
                    {
                        data: 'item_name',
                        name: 'item.name'
                    },
                    {
                        data: 'movement_type',
                        name: 'movement_type'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'unit_cost',
                        name: 'unit_cost',
                        orderable: false
                    },
                    {
                        data: 'total_cost',
                        name: 'total_cost',
                        orderable: false
                    },
                    {
                        data: 'reference',
                        name: 'reference_number'
                    },
                    {
                        data: 'notes',
                        name: 'notes'
                    },
                    {
                        data: 'creator_name',
                        name: 'creator.name'
                    }
                ],
                order: [
                    [0, 'desc']
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
                params.append('start_date', $('#start_date').val());
                params.append('end_date', $('#end_date').val());
                params.append('item_id', $('#item_filter').val());
                params.append('movement_type', $('#movement_type_filter').val());
                params.append('search', $('#search_filter').val());

                window.open('{{ route('inventory.reports.export.stock-movement') }}?' + params.toString(),
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
