@extends('layouts.main')

@section('title_page', 'Create Item')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create Item</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus"></i> Create New Item
                            </h3>
                        </div>
                        <form action="{{ route('items.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="code">Item Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                                id="code" name="code" value="{{ old('code') }}" required>
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="barcode">Barcode</label>
                                            <input type="text"
                                                class="form-control @error('barcode') is-invalid @enderror" id="barcode"
                                                name="barcode" value="{{ old('barcode') }}">
                                            @error('barcode')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="name">Item Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                                rows="3">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category_id">Category <span class="text-danger">*</span></label>
                                            <select class="form-control @error('category_id') is-invalid @enderror"
                                                id="category_id" name="category_id" required>
                                                <option value="">Select Category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="unit_of_measure">Unit of Measure <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('unit_of_measure') is-invalid @enderror"
                                                id="unit_of_measure" name="unit_of_measure"
                                                value="{{ old('unit_of_measure', 'pcs') }}" required>
                                            @error('unit_of_measure')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="min_stock_level">Minimum Stock Level</label>
                                            <input type="number"
                                                class="form-control @error('min_stock_level') is-invalid @enderror"
                                                id="min_stock_level" name="min_stock_level"
                                                value="{{ old('min_stock_level', 0) }}" step="0.0001" min="0">
                                            @error('min_stock_level')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="max_stock_level">Maximum Stock Level</label>
                                            <input type="number"
                                                class="form-control @error('max_stock_level') is-invalid @enderror"
                                                id="max_stock_level" name="max_stock_level"
                                                value="{{ old('max_stock_level') }}" step="0.0001" min="0">
                                            @error('max_stock_level')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="last_cost_price">Last Cost Price</label>
                                            <input type="number"
                                                class="form-control @error('last_cost_price') is-invalid @enderror"
                                                id="last_cost_price" name="last_cost_price"
                                                value="{{ old('last_cost_price') }}" step="0.01" min="0">
                                            @error('last_cost_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="inventory_account_id">Inventory Account</label>
                                            <select
                                                class="form-control @error('inventory_account_id') is-invalid @enderror"
                                                id="inventory_account_id" name="inventory_account_id">
                                                <option value="">Auto-assign based on category</option>
                                                @foreach ($inventoryAccounts as $account)
                                                    <option value="{{ $account->id }}"
                                                        {{ old('inventory_account_id') == $account->id ? 'selected' : '' }}>
                                                        {{ $account->code }} - {{ $account->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('inventory_account_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                If not selected, will be auto-assigned based on item category
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cost_of_goods_sold_account_id">Cost of Goods Sold Account</label>
                                            <select
                                                class="form-control @error('cost_of_goods_sold_account_id') is-invalid @enderror"
                                                id="cost_of_goods_sold_account_id" name="cost_of_goods_sold_account_id">
                                                <option value="">Auto-assign default COGS account</option>
                                                @foreach ($cogsAccounts as $account)
                                                    <option value="{{ $account->id }}"
                                                        {{ old('cost_of_goods_sold_account_id') == $account->id ? 'selected' : '' }}>
                                                        {{ $account->code }} - {{ $account->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('cost_of_goods_sold_account_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                If not selected, will use default Cost of Goods Sold account
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="is_active"
                                                    name="is_active" value="1"
                                                    {{ old('is_active', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    Active
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Item
                                </button>
                                <a href="{{ route('items.index') }}" class="btn btn-secondary">
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
