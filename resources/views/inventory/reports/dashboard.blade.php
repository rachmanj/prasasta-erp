@extends('layouts.main')

@section('title_page', 'Inventory Dashboard')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Inventory Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Inventory Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Key Metrics Row -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($metrics['total_items']) }}</h3>
                            <p>Total Items</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <a href="{{ route('items.index') }}" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>Rp {{ number_format($metrics['total_value'], 0, ',', '.') }}</h3>
                            <p>Total Inventory Value</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <a href="{{ route('inventory.reports.inventory-valuation') }}" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($metrics['low_stock_count']) }}</h3>
                            <p>Low Stock Items</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <a href="{{ route('inventory.reports.low-stock') }}" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ number_format($metrics['out_of_stock_count']) }}</h3>
                            <p>Out of Stock Items</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <a href="{{ route('inventory.reports.stock-status') }}?stock_status=out_of_stock"
                            class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Activity Metrics Row -->
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ number_format($metrics['recent_movements_count']) }}</h3>
                            <p>Stock Movements (Last 30 Days)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <a href="{{ route('inventory.reports.stock-movement') }}" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-6 col-12">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ number_format($metrics['recent_adjustments_count']) }}</h3>
                            <p>Stock Adjustments (Last 30 Days)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-adjust"></i>
                        </div>
                        <a href="{{ route('inventory.reports.stock-adjustments') }}" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Low Stock Items -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                Low Stock Items
                            </h3>
                        </div>
                        <div class="card-body">
                            @if ($lowStockItems->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Current Stock</th>
                                                <th>Min Level</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lowStockItems->take(5) as $item)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $item->code }}</strong><br>
                                                        <small class="text-muted">{{ $item->name }}</small>
                                                    </td>
                                                    <td>{{ number_format($item->current_stock_quantity, 4) }}
                                                        {{ $item->unit_of_measure }}</td>
                                                    <td>{{ number_format($item->min_stock_level, 4) }}</td>
                                                    <td>
                                                        @if ($item->current_stock_quantity == 0)
                                                            <span class="badge badge-danger">Out of Stock</span>
                                                        @else
                                                            <span class="badge badge-warning">Low Stock</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if ($lowStockItems->count() > 5)
                                    <div class="text-center">
                                        <a href="{{ route('inventory.reports.low-stock') }}"
                                            class="btn btn-sm btn-warning">
                                            View All ({{ $lowStockItems->count() }} items)
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <p>All items are adequately stocked!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Recent Stock Movements -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history"></i>
                                Recent Stock Movements
                            </h3>
                        </div>
                        <div class="card-body">
                            @if ($recentMovements->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Item</th>
                                                <th>Type</th>
                                                <th>Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recentMovements->take(5) as $movement)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($movement->movement_date)->format('d/m') }}
                                                    </td>
                                                    <td>
                                                        <strong>{{ $movement->item->code }}</strong><br>
                                                        <small class="text-muted">{{ $movement->item->name }}</small>
                                                    </td>
                                                    <td>
                                                        @if ($movement->movement_type === 'in')
                                                            <span class="badge badge-success">Stock In</span>
                                                        @elseif($movement->movement_type === 'out')
                                                            <span class="badge badge-danger">Stock Out</span>
                                                        @elseif($movement->movement_type === 'adjustment')
                                                            <span class="badge badge-warning">Adjustment</span>
                                                        @else
                                                            <span
                                                                class="badge badge-secondary">{{ ucfirst($movement->movement_type) }}</span>
                                                        @endif
                                                    </td>
                                                    <td
                                                        class="{{ $movement->quantity >= 0 ? 'text-success' : 'text-danger' }}">
                                                        {{ $movement->quantity >= 0 ? '+' : '' }}{{ number_format($movement->quantity, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if ($recentMovements->count() > 5)
                                    <div class="text-center">
                                        <a href="{{ route('inventory.reports.stock-movement') }}"
                                            class="btn btn-sm btn-primary">
                                            View All Movements
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p>No recent stock movements</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Moving Items -->
            @if ($topMovingItems->count() > 0)
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-bar"></i>
                                    Top Moving Items (Last 30 Days)
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Item Code</th>
                                                <th>Item Name</th>
                                                <th>Category</th>
                                                <th>Total Movement</th>
                                                <th>Current Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($topMovingItems as $index => $item)
                                                <tr>
                                                    <td>
                                                        @if ($index === 0)
                                                            <i class="fas fa-trophy text-warning"></i> 1st
                                                        @elseif($index === 1)
                                                            <i class="fas fa-medal text-secondary"></i> 2nd
                                                        @elseif($index === 2)
                                                            <i class="fas fa-award text-warning"></i> 3rd
                                                        @else
                                                            {{ $index + 1 }}
                                                        @endif
                                                    </td>
                                                    <td><strong>{{ $item->item->code }}</strong></td>
                                                    <td>{{ $item->item->name }}</td>
                                                    <td>{{ $item->item->category->name }}</td>
                                                    <td>{{ number_format($item->total_movement, 2) }}
                                                        {{ $item->item->unit_of_measure }}</td>
                                                    <td>{{ number_format($item->item->current_stock_quantity, 2) }}
                                                        {{ $item->item->unit_of_measure }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
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
                                    <a href="{{ route('stock-adjustments.create') }}"
                                        class="btn btn-warning btn-block mb-2">
                                        <i class="fas fa-adjust"></i> Stock Adjustment
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('items.create') }}" class="btn btn-success btn-block mb-2">
                                        <i class="fas fa-plus"></i> Add New Item
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('inventory.reports.stock-status') }}"
                                        class="btn btn-info btn-block mb-2">
                                        <i class="fas fa-list"></i> Stock Status Report
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('inventory.reports.inventory-valuation') }}"
                                        class="btn btn-primary btn-block mb-2">
                                        <i class="fas fa-calculator"></i> Inventory Valuation
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
