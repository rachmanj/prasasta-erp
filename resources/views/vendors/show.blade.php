@extends('layouts.main')

@section('title', 'Vendor Details')

@section('title_page')
    Vendor Details: {{ $vendor->name }}
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('vendors.index') }}">Suppliers</a></li>
    <li class="breadcrumb-item active">{{ $vendor->name }}</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <!-- Modern Hero Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                                            <i class="fas fa-building fa-2x"></i>
                                        </div>
                                        <div>
                                            <h2 class="mb-1 fw-bold">{{ $vendor->name }}</h2>
                                            <p class="mb-0 opacity-75">Vendor Code: {{ $vendor->code }}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-envelope me-2"></i>
                                                <span>{{ $vendor->email ?: 'No email provided' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-phone me-2"></i>
                                                <span>{{ $vendor->phone ?: 'No phone provided' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <a href="{{ route('vendors.edit', $vendor->id) }}"
                                        class="btn btn-light btn-lg shadow-sm">
                                        <i class="fas fa-edit me-2"></i>Edit Vendor
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modern Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="card-body text-center text-white">
                            <div class="mb-3">
                                <i class="fas fa-cube fa-3x opacity-75"></i>
                            </div>
                            <h3 class="fw-bold mb-1">{{ $totalAssetCount }}</h3>
                            <p class="mb-0 opacity-75">Total Assets</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100"
                        style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="card-body text-center text-white">
                            <div class="mb-3">
                                <i class="fas fa-dollar-sign fa-3x opacity-75"></i>
                            </div>
                            <h3 class="fw-bold mb-1">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</h3>
                            <p class="mb-0 opacity-75">Total Asset Value</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100"
                        style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="card-body text-center text-white">
                            <div class="mb-3">
                                <i class="fas fa-file-invoice fa-3x opacity-75"></i>
                            </div>
                            <h3 class="fw-bold mb-1">{{ $totalPurchaseCount }}</h3>
                            <p class="mb-0 opacity-75">Purchase Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100"
                        style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <div class="card-body text-center text-white">
                            <div class="mb-3">
                                <i class="fas fa-money-bill-wave fa-3x opacity-75"></i>
                            </div>
                            <h3 class="fw-bold mb-1">Rp {{ number_format($totalPurchaseValue, 0, ',', '.') }}</h3>
                            <p class="mb-0 opacity-75">Total Purchase Value</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modern Sidebar Navigation Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
                        <div class="row g-0">
                            <!-- Sidebar Navigation -->
                            <div class="col-md-3">
                                <div class="sidebar-nav h-100"
                                    style="background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);">
                                    <div class="p-4">
                                        <h5 class="text-white mb-4 fw-bold">
                                            <i class="fas fa-th-large me-2"></i>
                                            Vendor Data
                                        </h5>
                                        <nav class="nav flex-column">
                                            <a class="nav-link active mb-2 p-3 rounded-3 text-white" id="assets-tab"
                                                data-toggle="tab" href="#assets" role="tab"
                                                style="background: rgba(255,255,255,0.1); border-left: 4px solid #3498db;">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <i class="fas fa-cube fa-lg"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold">Assets</div>
                                                        <small class="opacity-75">{{ $totalAssetCount }} items</small>
                                                    </div>
                                                    <div class="badge bg-info">{{ $totalAssetCount }}</div>
                                                </div>
                                            </a>
                                            <a class="nav-link mb-2 p-3 rounded-3 text-white" id="purchase-orders-tab"
                                                data-toggle="tab" href="#purchase-orders" role="tab"
                                                style="background: rgba(255,255,255,0.05);">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <i class="fas fa-file-invoice fa-lg"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold">Purchase Orders</div>
                                                        <small class="opacity-75">{{ $totalPurchaseCount }} orders</small>
                                                    </div>
                                                    <div class="badge bg-success">{{ $totalPurchaseCount }}</div>
                                                </div>
                                            </a>
                                            <a class="nav-link mb-2 p-3 rounded-3 text-white" id="acquisition-history-tab"
                                                data-toggle="tab" href="#acquisition-history" role="tab"
                                                style="background: rgba(255,255,255,0.05);">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <i class="fas fa-history fa-lg"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold">Acquisition History</div>
                                                        <small class="opacity-75">Historical records</small>
                                                    </div>
                                                    <div class="badge bg-warning">View</div>
                                                </div>
                                            </a>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- Content Area -->
                            <div class="col-md-9">
                                <div class="tab-content h-100" id="vendorTabsContent">
                                    <!-- Assets Tab -->
                                    <div class="tab-pane fade show active" id="assets" role="tabpanel">
                                        <div class="h-100 d-flex flex-column">
                                            <!-- Header Section -->
                                            <div class="bg-white border-bottom p-4">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h3 class="mb-2 fw-bold text-dark">
                                                            <i class="fas fa-cube text-info me-3"></i>
                                                            Assets Management
                                                        </h3>
                                                        <p class="text-muted mb-0">Manage vendor assets and inventory</p>
                                                    </div>
                                                    <a href="{{ route('assets.create') }}"
                                                        class="btn btn-info btn-lg shadow-sm">
                                                        <i class="fas fa-plus me-2"></i>Create Asset
                                                    </a>
                                                </div>
                                            </div>

                                            <!-- Table Section -->
                                            <div class="flex-grow-1 p-4 bg-light">
                                                <div class="table-responsive bg-white rounded-3 shadow-sm">
                                                    <table id="assetsTable" class="table table-hover mb-0">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th class="border-0">Code</th>
                                                                <th class="border-0">Name</th>
                                                                <th class="border-0">Category</th>
                                                                <th class="border-0">Acquisition Cost</th>
                                                                <th class="border-0">Book Value</th>
                                                                <th class="border-0">Fund</th>
                                                                <th class="border-0">Project</th>
                                                                <th class="border-0">Department</th>
                                                                <th class="border-0">Placed in Service</th>
                                                                <th class="border-0">Actions</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Purchase Orders Tab -->
                                    <div class="tab-pane fade" id="purchase-orders" role="tabpanel">
                                        <div class="h-100 d-flex flex-column">
                                            <!-- Header Section -->
                                            <div class="bg-white border-bottom p-4">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h3 class="mb-2 fw-bold text-dark">
                                                            <i class="fas fa-file-invoice text-success me-3"></i>
                                                            Purchase Orders
                                                        </h3>
                                                        <p class="text-muted mb-0">Track purchase orders and transactions
                                                        </p>
                                                    </div>
                                                    <a href="{{ route('purchase-orders.create') }}"
                                                        class="btn btn-success btn-lg shadow-sm">
                                                        <i class="fas fa-plus me-2"></i>Create PO
                                                    </a>
                                                </div>
                                            </div>

                                            <!-- Table Section -->
                                            <div class="flex-grow-1 p-4 bg-light">
                                                <div class="table-responsive bg-white rounded-3 shadow-sm">
                                                    <table id="purchaseOrdersTable" class="table table-hover mb-0">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th class="border-0">Order No</th>
                                                                <th class="border-0">Date</th>
                                                                <th class="border-0">Total Amount</th>
                                                                <th class="border-0">Status</th>
                                                                <th class="border-0">Actions</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Acquisition History Tab -->
                                    <div class="tab-pane fade" id="acquisition-history" role="tabpanel">
                                        <div class="h-100 d-flex flex-column">
                                            <!-- Header Section -->
                                            <div class="bg-white border-bottom p-4">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h3 class="mb-2 fw-bold text-dark">
                                                            <i class="fas fa-history text-warning me-3"></i>
                                                            Acquisition History
                                                        </h3>
                                                        <p class="text-muted mb-0">View historical asset acquisitions</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Table Section -->
                                            <div class="flex-grow-1 p-4 bg-light">
                                                <div class="table-responsive bg-white rounded-3 shadow-sm">
                                                    <table id="acquisitionHistoryTable" class="table table-hover mb-0">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th class="border-0">Code</th>
                                                                <th class="border-0">Name</th>
                                                                <th class="border-0">Category</th>
                                                                <th class="border-0">Acquisition Cost</th>
                                                                <th class="border-0">Fund</th>
                                                                <th class="border-0">Project</th>
                                                                <th class="border-0">Department</th>
                                                                <th class="border-0">Placed in Service</th>
                                                                <th class="border-0">Actions</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <style>
        /* Custom Styles for Sidebar Navigation Design */
        .sidebar-nav .nav-link {
            transition: all 0.3s ease;
            border-radius: 12px !important;
            margin-bottom: 8px;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            transform: translateX(5px);
        }

        .sidebar-nav .nav-link.active {
            background: rgba(255, 255, 255, 0.2) !important;
            border-left: 4px solid #3498db !important;
            transform: translateX(5px);
        }

        .sidebar-nav .nav-link.active:hover {
            background: rgba(255, 255, 255, 0.25) !important;
        }

        /* Table Styling */
        .table-hover tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }

        .table-dark th {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
            color: white !important;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        /* Card Animations */
        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .btn {
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .badge {
            font-size: 0.75rem;
            border-radius: 12px;
        }

        /* Sidebar Badge Colors */
        .bg-info {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%) !important;
        }

        .bg-success {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%) !important;
        }

        .bg-warning {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%) !important;
        }

        /* DataTable Custom Styling */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            margin: 1rem 0;
            padding: 0 1rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 8px;
            margin: 0 2px;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%) !important;
            border-color: #3498db !important;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: rgba(52, 152, 219, 0.1);
            border-color: #3498db;
            color: #3498db !important;
            transform: translateY(-1px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar-nav {
                display: none;
            }

            .col-md-9 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }

        /* Content Area Styling */
        .tab-content {
            min-height: 500px;
        }

        .flex-grow-1 {
            flex-grow: 1;
        }

        /* Header Section Styling */
        .border-bottom {
            border-bottom: 2px solid #e9ecef !important;
        }

        /* Rounded corners for modern look */
        .rounded-3 {
            border-radius: 1rem !important;
        }

        /* Shadow effects */
        .shadow-sm {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }

        .shadow-lg {
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
        }
    </style>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTables with modern styling
            const assetsTable = $('#assetsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('vendors.assets', $vendor->id) }}',
                    type: 'GET'
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
                        name: 'category_name'
                    },
                    {
                        data: 'acquisition_cost',
                        name: 'acquisition_cost'
                    },
                    {
                        data: 'current_book_value',
                        name: 'current_book_value'
                    },
                    {
                        data: 'fund_name',
                        name: 'fund_name'
                    },
                    {
                        data: 'project_name',
                        name: 'project_name'
                    },
                    {
                        data: 'department_name',
                        name: 'department_name'
                    },
                    {
                        data: 'placed_in_service_date',
                        name: 'placed_in_service_date'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [8, 'desc']
                ],
                pageLength: 25,
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
                    emptyTable: "No data available in table",
                    zeroRecords: "No matching records found"
                }
            });

            const purchaseOrdersTable = $('#purchaseOrdersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('vendors.purchase-orders', $vendor->id) }}',
                    type: 'GET'
                },
                columns: [{
                        data: 'order_no',
                        name: 'order_no'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                pageLength: 25,
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
                    emptyTable: "No data available in table",
                    zeroRecords: "No matching records found"
                }
            });

            const acquisitionHistoryTable = $('#acquisitionHistoryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('vendors.asset-acquisition-history', $vendor->id) }}',
                    type: 'GET'
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
                        name: 'category_name'
                    },
                    {
                        data: 'acquisition_cost',
                        name: 'acquisition_cost'
                    },
                    {
                        data: 'fund_name',
                        name: 'fund_name'
                    },
                    {
                        data: 'project_name',
                        name: 'project_name'
                    },
                    {
                        data: 'department_name',
                        name: 'department_name'
                    },
                    {
                        data: 'placed_in_service_date',
                        name: 'placed_in_service_date'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [7, 'desc']
                ],
                pageLength: 25,
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
                    emptyTable: "No data available in table",
                    zeroRecords: "No matching records found"
                }
            });

            // Refresh tables when switching tabs
            $('#vendorTabs a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                const target = $(e.target).attr('href');

                if (target === '#assets') {
                    assetsTable.columns.adjust().draw();
                } else if (target === '#purchase-orders') {
                    purchaseOrdersTable.columns.adjust().draw();
                } else if (target === '#acquisition-history') {
                    acquisitionHistoryTable.columns.adjust().draw();
                }
            });

            // Add smooth animations
            $('.card').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
            });
        });
    </script>
@endpush
