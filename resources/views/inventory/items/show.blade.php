@extends('layouts.main')

@section('title_page', 'Item Details')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Item Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
                        <li class="breadcrumb-item active">{{ $item->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <!-- Item Information -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-box"></i> Item Information
                            </h3>
                            <div class="card-tools">
                                @if ($item->is_active)
                                    <span class="badge badge-success badge-lg">Active</span>
                                @else
                                    <span class="badge badge-danger badge-lg">Inactive</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Code:</th>
                                    <td><strong>{{ $item->code }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $item->name }}</td>
                                </tr>
                                <tr>
                                    <th>Category:</th>
                                    <td>{{ $item->category->name }}</td>
                                </tr>
                                <tr>
                                    <th>Barcode:</th>
                                    <td>{{ $item->barcode ?: 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <th>Unit:</th>
                                    <td>{{ $item->unit_of_measure }}</td>
                                </tr>
                                <tr>
                                    <th>Cost Method:</th>
                                    <td>{{ strtoupper($item->cost_method) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Stock Information -->
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-warehouse"></i> Stock Information
                            </h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Current Stock:</th>
                                    <td><strong>{{ number_format($item->current_stock_quantity, 4) }}
                                            {{ $item->unit_of_measure }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Stock Value:</th>
                                    <td><strong>Rp {{ number_format($item->current_stock_value, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Min Stock:</th>
                                    <td>{{ number_format($item->min_stock_level, 4) }}</td>
                                </tr>
                                <tr>
                                    <th>Max Stock:</th>
                                    <td>{{ $item->max_stock_level ? number_format($item->max_stock_level, 4) : 'Not set' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Last Cost:</th>
                                    <td>{{ $item->last_cost_price ? 'Rp ' . number_format($item->last_cost_price, 0, ',', '.') : 'Not set' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Average Cost:</th>
                                    <td>{{ $item->average_cost_price ? 'Rp ' . number_format($item->average_cost_price, 0, ',', '.') : 'Not set' }}
                                    </td>
                                </tr>
                            </table>

                            @if ($item->isLowStock())
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Low Stock Alert!</strong> Current stock is below minimum level.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cogs"></i> Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('items.edit', $item->id) }}" class="btn btn-primary btn-block mb-2">
                                <i class="fas fa-edit"></i> Edit Item
                            </a>
                            <a href="{{ route('stock-adjustments.create') }}" class="btn btn-info btn-block mb-2">
                                <i class="fas fa-adjust"></i> Stock Adjustment
                            </a>
                            <a href="{{ route('items.index') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-arrow-left"></i> Back to Items
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- Description -->
                    @if ($item->description)
                        <div class="card card-default">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-align-left"></i> Description
                                </h3>
                            </div>
                            <div class="card-body">
                                <p>{{ $item->description }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Stock Layers (FIFO) -->
                    <div class="card card-secondary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-layer-group"></i> Stock Layers (FIFO)
                            </h3>
                        </div>
                        <div class="card-body">
                            @if ($item->stockLayers->where('remaining_quantity', '>', 0)->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Purchase Date</th>
                                                <th class="text-right">Original Qty</th>
                                                <th class="text-right">Remaining Qty</th>
                                                <th class="text-right">Unit Cost</th>
                                                <th class="text-right">Remaining Value</th>
                                                <th>Reference</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($item->stockLayers->where('remaining_quantity', '>', 0)->sortBy('purchase_date') as $layer)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($layer->purchase_date)->format('d/m/Y') }}
                                                    </td>
                                                    <td class="text-right">{{ number_format($layer->quantity, 4) }}</td>
                                                    <td class="text-right">
                                                        <strong>{{ number_format($layer->remaining_quantity, 4) }}</strong>
                                                    </td>
                                                    <td class="text-right">Rp
                                                        {{ number_format($layer->unit_cost, 0, ',', '.') }}</td>
                                                    <td class="text-right">Rp
                                                        {{ number_format($layer->remaining_quantity * $layer->unit_cost, 0, ',', '.') }}
                                                    </td>
                                                    <td>
                                                        @if ($layer->reference_type && $layer->reference_id)
                                                            {{ ucfirst(str_replace('_', ' ', $layer->reference_type)) }}
                                                            #{{ $layer->reference_id }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No stock layers available. Stock will be created when items are received.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Stock Movements -->
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history"></i> Recent Stock Movements
                            </h3>
                        </div>
                        <div class="card-body">
                            @if ($item->stockMovements->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th class="text-right">Quantity</th>
                                                <th class="text-right">Unit Cost</th>
                                                <th class="text-right">Total Cost</th>
                                                <th>Reference</th>
                                                <th>Created By</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($item->stockMovements->sortByDesc('movement_date')->take(10) as $movement)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($movement->movement_date)->format('d/m/Y') }}
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
                                                        class="text-right {{ $movement->quantity >= 0 ? 'text-success' : 'text-danger' }}">
                                                        {{ $movement->quantity >= 0 ? '+' : '' }}{{ number_format($movement->quantity, 4) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ $movement->unit_cost ? 'Rp ' . number_format($movement->unit_cost, 0, ',', '.') : '-' }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ $movement->total_cost ? 'Rp ' . number_format($movement->total_cost, 0, ',', '.') : '-' }}
                                                    </td>
                                                    <td>
                                                        @if ($movement->reference_type && $movement->reference_number)
                                                            {{ ucfirst(str_replace('_', ' ', $movement->reference_type)) }}
                                                            #{{ $movement->reference_number }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>{{ $movement->creator ? $movement->creator->name : 'Unknown' }}
                                                    </td>
                                                    <td>{{ $movement->notes ?: '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-history fa-3x mb-3"></i>
                                    <p>No stock movements recorded yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
