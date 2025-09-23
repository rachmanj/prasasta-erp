@extends('layouts.main')

@section('title_page')
    Accounts
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Accounts</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h4 class="mb-0">Chart of Accounts</h4>
                @can('accounts.manage')
                    <a href="{{ route('accounts.create') }}" class="btn btn-sm btn-primary">Create</a>
                @endcan
            </div>

            <!-- Search and Filter Section -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('accounts.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search Accounts</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Search by code or name...">
                        </div>
                        <div class="col-md-3">
                            <label for="type" class="form-label">Account Type</label>
                            <select class="form-control" id="type" name="type">
                                <option value="">All Types</option>
                                <option value="asset" {{ request('type') == 'asset' ? 'selected' : '' }}>Asset</option>
                                <option value="liability" {{ request('type') == 'liability' ? 'selected' : '' }}>Liability
                                </option>
                                <option value="net_assets" {{ request('type') == 'net_assets' ? 'selected' : '' }}>Net
                                    Assets</option>
                                <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Income</option>
                                <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="control_type" class="form-label">Control Type</label>
                            <select class="form-control" id="control_type" name="control_type">
                                <option value="">All Control Types</option>
                                <option value="ap" {{ request('control_type') == 'ap' ? 'selected' : '' }}>Accounts
                                    Payable</option>
                                <option value="ar" {{ request('control_type') == 'ar' ? 'selected' : '' }}>Accounts
                                    Receivable</option>
                                <option value="cash" {{ request('control_type') == 'cash' ? 'selected' : '' }}>Cash &
                                    Bank</option>
                                <option value="inventory" {{ request('control_type') == 'inventory' ? 'selected' : '' }}>
                                    Inventory</option>
                                <option value="fixed_assets"
                                    {{ request('control_type') == 'fixed_assets' ? 'selected' : '' }}>Fixed Assets</option>
                                <option value="other" {{ request('control_type') == 'other' ? 'selected' : '' }}>Other
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="is_control_account" class="form-label">Control Account</label>
                            <select class="form-control" id="is_control_account" name="is_control_account">
                                <option value="">All Accounts</option>
                                <option value="1" {{ request('is_control_account') == '1' ? 'selected' : '' }}>Control
                                    Accounts Only</option>
                                <option value="0" {{ request('is_control_account') == '0' ? 'selected' : '' }}>Regular
                                    Accounts Only</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('accounts.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times"></i> Clear
                            </a>
                            @if (request()->hasAny(['search', 'type', 'control_type', 'is_control_account']))
                                <span class="badge badge-info ml-2">
                                    {{ $accounts->total() }} result(s) found
                                </span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            @if (session('success'))
                <script>
                    toastr.success(@json(session('success')));
                </script>
            @endif
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Control Type</th>
                        <th>Postable</th>
                        <th>Parent</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accounts as $a)
                        <tr>
                            <td>
                                <strong>{{ $a->code }}</strong>
                                @if ($a->is_control_account)
                                    <span class="badge badge-primary badge-sm ml-1">Control</span>
                                @endif
                            </td>
                            <td>{{ $a->name }}</td>
                            <td>
                                <span
                                    class="badge badge-{{ $a->type === 'asset' ? 'success' : ($a->type === 'liability' ? 'danger' : 'info') }}">
                                    {{ strtoupper($a->type) }}
                                </span>
                            </td>
                            <td>
                                @if ($a->control_type)
                                    <span class="badge badge-warning">{{ ucfirst($a->control_type) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $a->is_postable ? 'success' : 'secondary' }}">
                                    {{ $a->is_postable ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td>
                                @if ($a->parent_id)
                                    {{ optional(\DB::table('accounts')->find($a->parent_id))->code }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @can('accounts.manage')
                                    <div class="btn-group" role="group">
                                        @can('accounts.view_transactions')
                                            <a href="{{ route('accounts.show', $a->id) }}" class="btn btn-xs btn-success">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        @endcan
                                        <a href="{{ route('accounts.edit', $a->id) }}" class="btn btn-xs btn-info">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        @if ($a->is_control_account && $a->controlAccount)
                                            <a href="{{ route('control-accounts.show', $a->controlAccount->id) }}"
                                                class="btn btn-xs btn-primary">
                                                <i class="fas fa-cogs"></i> Control
                                            </a>
                                        @endif
                                    </div>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                <i class="fas fa-search"></i> No accounts found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div>
                {{ $accounts->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </section>
@endsection
