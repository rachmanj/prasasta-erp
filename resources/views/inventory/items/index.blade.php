@extends('layouts.main')

@section('title_page')
    Inventory Items
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Inventory Items</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Inventory Items</h3>
                            <div class="card-tools">
                                @can('inventory.items.create')
                                    <a href="{{ route('items.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Create Item
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <script>
                                    toastr.success(@json(session('success')));
                                </script>
                            @endif
                            <table class="table table-bordered table-striped table-sm" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Barcode</th>
                                        <th>Unit</th>
                                        <th>Stock Qty</th>
                                        <th>Stock Value</th>
                                        <th>Status</th>
                                        <th>Stock Status</th>
                                        <th>Created</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(function() {
            $('#itemsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('items.data') }}',
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
                        name: 'category.name',
                        orderable: false
                    },
                    {
                        data: 'barcode',
                        name: 'barcode'
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
                        data: 'stock_value',
                        name: 'current_stock_value',
                        orderable: false
                    },
                    {
                        data: 'status',
                        name: 'is_active',
                        orderable: false
                    },
                    {
                        data: 'stock_status',
                        name: 'stock_status',
                        orderable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endsection
