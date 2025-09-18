@extends('layouts.main')

@section('title', 'Create Asset')

@section('title_page')
    Create Asset
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('assets.index') }}">Assets</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus mr-1"></i>
                                New Asset
                            </h3>
                            <a href="{{ route('assets.index') }}" class="btn btn-sm btn-secondary float-right">
                                <i class="fas fa-arrow-left"></i> Back to Assets
                            </a>
                        </div>
                        <form method="post" action="{{ route('assets.store') }}">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="code">Asset Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-sm" id="code"
                                                name="code" value="{{ old('code') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Asset Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-sm" id="name"
                                                name="name" value="{{ old('name') }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="serial_number">Serial Number</label>
                                            <input type="text" class="form-control form-control-sm" id="serial_number"
                                                name="serial_number" value="{{ old('serial_number') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category_id">Category <span class="text-danger">*</span></label>
                                            <select class="form-control form-control-sm select2bs4" id="category_id"
                                                name="category_id" required>
                                                <option value="">Select Category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control form-control-sm" id="description" name="description" rows="2">{{ old('description') }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="acquisition_cost">Acquisition Cost <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="number" class="form-control" id="acquisition_cost"
                                                    name="acquisition_cost" step="0.01" min="0"
                                                    value="{{ old('acquisition_cost') }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="salvage_value">Salvage Value</label>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="number" class="form-control" id="salvage_value"
                                                    name="salvage_value" step="0.01" min="0"
                                                    value="{{ old('salvage_value', 0) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="method">Depreciation Method <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control form-control-sm select2bs4" id="method"
                                                name="method" required>
                                                <option value="straight_line"
                                                    {{ old('method', 'straight_line') == 'straight_line' ? 'selected' : '' }}>
                                                    Straight Line</option>
                                                <option value="declining_balance"
                                                    {{ old('method') == 'declining_balance' ? 'selected' : '' }}>Declining
                                                    Balance</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="life_months">Life (Months) <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" class="form-control form-control-sm" id="life_months"
                                                name="life_months" min="1" value="{{ old('life_months') }}"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="placed_in_service_date">Placed in Service Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control form-control-sm"
                                                id="placed_in_service_date" name="placed_in_service_date"
                                                value="{{ old('placed_in_service_date', date('Y-m-d')) }}" required>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="text-primary">Dimensions</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="fund_id">Fund</label>
                                            <select class="form-control form-control-sm select2bs4" id="fund_id"
                                                name="fund_id">
                                                <option value="">Select Fund</option>
                                                @foreach ($funds as $fund)
                                                    <option value="{{ $fund->id }}"
                                                        {{ old('fund_id') == $fund->id ? 'selected' : '' }}>
                                                        {{ $fund->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="project_id">Project</label>
                                            <select class="form-control form-control-sm select2bs4" id="project_id"
                                                name="project_id">
                                                <option value="">Select Project</option>
                                                @foreach ($projects as $project)
                                                    <option value="{{ $project->id }}"
                                                        {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                                        {{ $project->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="department_id">Department</label>
                                            <select class="form-control form-control-sm select2bs4" id="department_id"
                                                name="department_id">
                                                <option value="">Select Department</option>
                                                @foreach ($departments as $department)
                                                    <option value="{{ $department->id }}"
                                                        {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                        {{ $department->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="vendor_id">Vendor</label>
                                            <select class="form-control form-control-sm select2bs4" id="vendor_id"
                                                name="vendor_id">
                                                <option value="">Select Vendor</option>
                                                @foreach ($vendors as $vendor)
                                                    <option value="{{ $vendor->id }}"
                                                        {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                        {{ $vendor->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Asset
                                </button>
                                <a href="{{ route('assets.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2BS4
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });
        });
    </script>
@endsection
