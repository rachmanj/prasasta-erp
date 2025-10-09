@extends('layouts.main')

@section('title_page')
    Role Details
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
    <li class="breadcrumb-item active">{{ $role->name }}</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Role Information</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-warning btn-sm mr-1">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5><strong>Role Name:</strong></h5>
                                    <p class="text-lg">
                                        <span class="badge badge-primary" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h5><strong>Users Count:</strong></h5>
                                    <p class="text-lg">
                                        <span class="badge badge-success" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                            {{ $role->users()->count() }} {{ Str::plural('user', $role->users()->count()) }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <hr>

                            <h5 class="mb-3"><strong>Permissions ({{ $permissions->count() }})</strong></h5>

                            @if ($permissions->isEmpty())
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> This role has no permissions assigned.
                                </div>
                            @else
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
                                                        aria-expanded="false"
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
                                                    <div class="row">
                                                        @foreach ($categoryPermissions as $permission)
                                                            <div class="col-md-6 col-lg-4">
                                                                <div class="mb-2">
                                                                    <i class="fas fa-check-circle text-success"></i>
                                                                    <span class="ml-2">
                                                                        {{ ucfirst(str_replace(['.', '_', '-'], ' ', $permission->name)) }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
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

        .badge-info {
            background-color: #17a2b8;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(function() {
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
