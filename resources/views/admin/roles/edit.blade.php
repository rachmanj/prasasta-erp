@extends('layouts.main')

@section('title_page')
    Edit Role
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Role Information</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                        <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Role Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $role->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Permissions</label>
                                    <div class="mb-2">
                                        <button type="button" class="btn btn-sm btn-primary" id="select-all">
                                            <i class="fas fa-check-square"></i> Select All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary" id="deselect-all">
                                            <i class="fas fa-square"></i> Deselect All
                                        </button>
                                    </div>
                                    <div id="accordion">
                                        @php
                                            $groupedPermissions = [];
                                            foreach ($permissions as $permission) {
                                                $parts = explode('.', $permission->name);
                                                $category = ucfirst(str_replace('_', ' ', $parts[0]));
                                                if (!isset($groupedPermissions[$category])) {
                                                    $groupedPermissions[$category] = [];
                                                }
                                                $groupedPermissions[$category][] = $permission;
                                            }
                                            ksort($groupedPermissions);
                                        @endphp

                                        @foreach ($groupedPermissions as $category => $categoryPermissions)
                                            <div class="card">
                                                <div class="card-header" id="heading{{ Str::slug($category) }}">
                                                    <h5 class="mb-0">
                                                        <button class="btn btn-link" type="button" data-toggle="collapse"
                                                            data-target="#collapse{{ Str::slug($category) }}"
                                                            aria-expanded="true"
                                                            aria-controls="collapse{{ Str::slug($category) }}">
                                                            <i class="fas fa-chevron-down"></i> {{ $category }}
                                                            <span
                                                                class="badge badge-info ml-2">{{ count($categoryPermissions) }}</span>
                                                        </button>
                                                    </h5>
                                                </div>

                                                <div id="collapse{{ Str::slug($category) }}" class="collapse"
                                                    aria-labelledby="heading{{ Str::slug($category) }}"
                                                    data-parent="#accordion">
                                                    <div class="card-body">
                                                        <div class="mb-3">
                                                            <button type="button"
                                                                class="btn btn-sm btn-success select-category"
                                                                data-category="{{ Str::slug($category) }}">
                                                                <i class="fas fa-check-circle"></i> Select All
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-secondary deselect-category"
                                                                data-category="{{ Str::slug($category) }}">
                                                                <i class="fas fa-times-circle"></i> Deselect All
                                                            </button>
                                                        </div>
                                                        <div class="row">
                                                            @foreach ($categoryPermissions as $permission)
                                                                <div class="col-md-6 col-lg-4">
                                                                    <div class="custom-control custom-checkbox mb-2">
                                                                        <input type="checkbox"
                                                                            class="custom-control-input permission-checkbox category-{{ Str::slug($category) }}"
                                                                            id="permission_{{ $permission->id }}"
                                                                            name="permissions[]"
                                                                            value="{{ $permission->id }}"
                                                                            {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                                        <label class="custom-control-label"
                                                                            for="permission_{{ $permission->id }}">
                                                                            {{ ucfirst(str_replace(['.', '_', '-'], ' ', $permission->name)) }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('permissions')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Role
                                </button>
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
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

@section('styles')
    <style>
        #accordion .card {
            margin-bottom: 10px;
            border: 1px solid #dee2e6;
        }

        #accordion .card-header {
            padding: 0;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        #accordion .card-header .btn-link {
            width: 100%;
            text-align: left;
            padding: 12px 15px;
            color: #495057;
            text-decoration: none;
            font-weight: 600;
        }

        #accordion .card-header .btn-link:hover {
            text-decoration: none;
            color: #007bff;
        }

        #accordion .card-header .btn-link i {
            transition: transform 0.3s ease;
        }

        #accordion .card-body {
            padding: 20px;
        }

        .custom-control-label {
            font-size: 0.9rem;
            font-weight: normal;
        }

        .badge-info {
            background-color: #17a2b8;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(function() {
            // Select All (Global)
            $('#select-all').click(function() {
                $('.permission-checkbox').prop('checked', true);
            });

            // Deselect All (Global)
            $('#deselect-all').click(function() {
                $('.permission-checkbox').prop('checked', false);
            });

            // Select All by Category
            $(document).on('click', '.select-category', function() {
                var category = $(this).data('category');
                $('.category-' + category).prop('checked', true);
            });

            // Deselect All by Category
            $(document).on('click', '.deselect-category', function() {
                var category = $(this).data('category');
                $('.category-' + category).prop('checked', false);
            });

            // Toggle chevron icon on collapse
            $('#accordion .collapse').on('show.bs.collapse', function() {
                $(this).prev('.card-header').find('.fa-chevron-down').removeClass('fa-chevron-down')
                    .addClass('fa-chevron-up');
            }).on('hide.bs.collapse', function() {
                $(this).prev('.card-header').find('.fa-chevron-up').removeClass('fa-chevron-up')
                    .addClass('fa-chevron-down');
            });
        });
    </script>
@endsection
