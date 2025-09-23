@extends('layouts.main')

@section('title_page')
    Subsidiary Accounts - {{ $controlAccount->name }}
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('control-accounts.index') }}">Control Accounts</a></li>
    <li class="breadcrumb-item"><a
            href="{{ route('control-accounts.show', $controlAccount) }}">{{ $controlAccount->name }}</a></li>
    <li class="breadcrumb-item active">Subsidiary Accounts</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list"></i> Subsidiary Accounts - {{ $controlAccount->name }}
                            </h3>
                            <div class="card-tools">
                                @can('control_accounts.edit')
                                    <button class="btn btn-success btn-sm" data-toggle="modal"
                                        data-target="#addSubsidiaryModal">
                                        <i class="fas fa-plus"></i> Add Subsidiary Account
                                    </button>
                                @endcan
                                <a href="{{ route('control-accounts.show', $controlAccount) }}"
                                    class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back to Control Account
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Control Account Summary -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-calculator"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Control Balance</span>
                                            <span
                                                class="info-box-number">{{ number_format($controlAccount->getCurrentBalance(), 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-list"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Subsidiary Total</span>
                                            <span
                                                class="info-box-number">{{ number_format($controlAccount->getSubsidiaryTotal(), 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-warning"><i class="fas fa-balance-scale"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Variance</span>
                                            <span
                                                class="info-box-number">{{ number_format($controlAccount->calculateVariance(), 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Subsidiaries</span>
                                            <span
                                                class="info-box-number">{{ $controlAccount->getActiveSubsidiaryCount() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if (session('success'))
                                <script>
                                    toastr.success(@json(session('success')));
                                </script>
                            @endif

                            @if (session('error'))
                                <script>
                                    toastr.error(@json(session('error')));
                                </script>
                            @endif

                            <table class="table table-bordered table-striped table-sm" id="subsidiary-accounts-table">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Opening Balance</th>
                                        <th>Current Balance</th>
                                        <th>Last Transaction</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- DataTables will populate this -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Add Subsidiary Account Modal -->
    <div class="modal fade" id="addSubsidiaryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Subsidiary Account</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="addSubsidiaryForm">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="control_account_id" value="{{ $controlAccount->id }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subsidiary_code">Subsidiary Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="subsidiary_code" name="subsidiary_code"
                                        required>
                                    <small class="form-text text-muted">Unique code for this subsidiary account</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subsidiary_type">Subsidiary Type <span class="text-danger">*</span></label>
                                    <select class="form-control" id="subsidiary_type" name="subsidiary_type" required>
                                        <option value="">Select Type</option>
                                        @if ($controlAccount->control_type === 'ar')
                                            <option value="customer">Customer</option>
                                        @elseif($controlAccount->control_type === 'ap')
                                            <option value="vendor">Vendor</option>
                                        @elseif($controlAccount->control_type === 'cash')
                                            <option value="bank">Bank Account</option>
                                            <option value="cash">Cash Account</option>
                                        @elseif($controlAccount->control_type === 'inventory')
                                            <option value="item">Inventory Item</option>
                                            <option value="category">Item Category</option>
                                        @elseif($controlAccount->control_type === 'fixed_assets')
                                            <option value="asset">Fixed Asset</option>
                                            <option value="category">Asset Category</option>
                                        @else
                                            <option value="other">Other</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="opening_balance">Opening Balance</label>
                                    <input type="number" class="form-control" id="opening_balance"
                                        name="opening_balance" step="0.01" value="0.00">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="metadata">Additional Information (JSON)</label>
                            <textarea class="form-control" id="metadata" name="metadata" rows="3"
                                placeholder='{"email": "example@email.com", "phone": "123-456-7890"}'></textarea>
                            <small class="form-text text-muted">Optional JSON data for additional information</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Subsidiary Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Subsidiary Account Modal -->
    <div class="modal fade" id="editSubsidiaryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Subsidiary Account</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="editSubsidiaryForm">
                    <div class="modal-body">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_subsidiary_id" name="id">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_subsidiary_code">Subsidiary Code <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_subsidiary_code"
                                        name="subsidiary_code" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_subsidiary_type">Subsidiary Type <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" id="edit_subsidiary_type" name="subsidiary_type"
                                        required>
                                        <option value="">Select Type</option>
                                        @if ($controlAccount->control_type === 'ar')
                                            <option value="customer">Customer</option>
                                        @elseif($controlAccount->control_type === 'ap')
                                            <option value="vendor">Vendor</option>
                                        @elseif($controlAccount->control_type === 'cash')
                                            <option value="bank">Bank Account</option>
                                            <option value="cash">Cash Account</option>
                                        @elseif($controlAccount->control_type === 'inventory')
                                            <option value="item">Inventory Item</option>
                                            <option value="category">Item Category</option>
                                        @elseif($controlAccount->control_type === 'fixed_assets')
                                            <option value="asset">Fixed Asset</option>
                                            <option value="category">Asset Category</option>
                                        @else
                                            <option value="other">Other</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="edit_name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_name" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_opening_balance">Opening Balance</label>
                                    <input type="number" class="form-control" id="edit_opening_balance"
                                        name="opening_balance" step="0.01">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit_metadata">Additional Information (JSON)</label>
                            <textarea class="form-control" id="edit_metadata" name="metadata" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="edit_is_active" name="is_active"
                                    value="1">
                                <label class="form-check-label" for="edit_is_active">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Subsidiary Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            var table = $('#subsidiary-accounts-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('control-accounts/' . $controlAccount->id . '/subsidiary-accounts/data') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'subsidiary_code',
                        name: 'subsidiary_code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'subsidiary_type',
                        name: 'subsidiary_type'
                    },
                    {
                        data: 'opening_balance',
                        name: 'opening_balance',
                        className: 'text-right',
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: 'current_balance',
                        name: 'current_balance',
                        className: 'text-right',
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: 'last_transaction_date',
                        name: 'last_transaction_date',
                        render: function(data) {
                            return data ? moment(data).format('DD/MM/YYYY') : 'Never';
                        }
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        render: function(data) {
                            return data == 1 ?
                                '<span class="badge badge-success">Active</span>' :
                                '<span class="badge badge-danger">Inactive</span>';
                        }
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [0, 'asc']
                ],
                pageLength: 25,
                responsive: true
            });

            // Add Subsidiary Account Form
            $('#addSubsidiaryForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ url('control-accounts/' . $controlAccount->id . '/subsidiary-accounts') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#addSubsidiaryModal').modal('hide');
                            $('#addSubsidiaryForm')[0].reset();
                            table.ajax.reload(null, false);
                            toastr.success('Subsidiary account added successfully.');
                        } else {
                            toastr.error(response.message ||
                                'Failed to add subsidiary account.');
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON?.errors;
                        if (errors) {
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error(
                                'An error occurred while adding the subsidiary account.');
                        }
                    }
                });
            });

            // Edit Subsidiary Account
            $('#subsidiary-accounts-table').on('click', '.edit-btn', function() {
                var subsidiaryId = $(this).data('id');

                $.ajax({
                    url: "{{ url('control-accounts/' . $controlAccount->id . '/subsidiary-accounts') }}/" +
                        subsidiaryId,
                    type: 'GET',
                    success: function(response) {
                        $('#edit_subsidiary_id').val(response.id);
                        $('#edit_subsidiary_code').val(response.subsidiary_code);
                        $('#edit_subsidiary_type').val(response.subsidiary_type);
                        $('#edit_name').val(response.name);
                        $('#edit_opening_balance').val(response.opening_balance);
                        $('#edit_metadata').val(response.metadata ? JSON.stringify(response
                            .metadata, null, 2) : '');
                        $('#edit_is_active').prop('checked', response.is_active == 1);

                        $('#editSubsidiaryModal').modal('show');
                    },
                    error: function() {
                        toastr.error('Failed to load subsidiary account data.');
                    }
                });
            });

            // Update Subsidiary Account Form
            $('#editSubsidiaryForm').on('submit', function(e) {
                e.preventDefault();
                var subsidiaryId = $('#edit_subsidiary_id').val();

                $.ajax({
                    url: "{{ url('control-accounts/' . $controlAccount->id . '/subsidiary-accounts') }}/" +
                        subsidiaryId,
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#editSubsidiaryModal').modal('hide');
                            table.ajax.reload(null, false);
                            toastr.success('Subsidiary account updated successfully.');
                        } else {
                            toastr.error(response.message ||
                                'Failed to update subsidiary account.');
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON?.errors;
                        if (errors) {
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error(
                                'An error occurred while updating the subsidiary account.');
                        }
                    }
                });
            });

            // Delete Subsidiary Account
            $('#subsidiary-accounts-table').on('click', '.delete-btn', function(e) {
                e.preventDefault();
                var deleteUrl = $(this).data('delete-url');
                var subsidiaryName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to delete subsidiary account "${subsidiaryName}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    table.ajax.reload(null, false);
                                    toastr.success(
                                        'Subsidiary account deleted successfully.');
                                } else {
                                    toastr.error(response.message ||
                                        'Failed to delete subsidiary account.');
                                }
                            },
                            error: function() {
                                toastr.error(
                                    'An error occurred while deleting the subsidiary account.'
                                    );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
