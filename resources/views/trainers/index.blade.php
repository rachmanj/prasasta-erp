@extends('layouts.main')

@section('title_page')
    Trainers
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Trainers</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Trainers</h4>
                    @can('trainers.create')
                        <button class="btn btn-sm btn-primary float-right" id="btnNew">Create Trainer</button>
                    @endcan
                </div>
                @if (session('success'))
                    <script>
                        toastr.success(@json(session('success')));
                    </script>
                @endif
                <div class="card-body">
                    <table class="table table-striped table-sm mb-0" id="trainer-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Type</th>
                                <th>Specialization</th>
                                <th>Hourly Rate</th>
                                <th>Batch Rate</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="trainerModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Trainer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="trainerForm" action="#" method="post">
                    <div class="modal-body">
                        <input type="hidden" id="trainer_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Code <span class="text-danger">*</span></label>
                                    <input id="code" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input id="name" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" id="email" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input id="phone" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Type <span class="text-danger">*</span></label>
                                    <select id="type" class="form-control" required>
                                        <option value="internal">Internal</option>
                                        <option value="external">External</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <select id="status" class="form-control" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="suspended">Suspended</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Specialization</label>
                            <input id="specialization" class="form-control">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hourly Rate</label>
                                    <input type="number" id="hourly_rate" class="form-control" min="0"
                                        step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Batch Rate</label>
                                    <input type="number" id="batch_rate" class="form-control" min="0"
                                        step="0.01">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            var table = $('#trainer-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('trainers.data') }}',
                columns: [{
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'type_display',
                        name: 'type',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'specialization',
                        name: 'specialization'
                    },
                    {
                        data: 'hourly_rate_display',
                        name: 'hourly_rate',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'batch_rate_display',
                        name: 'batch_rate',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#btnNew').on('click', function() {
                $('#trainerModal form')[0].reset();
                $('#type').val('internal');
                $('#status').val('active');
                $('#trainerModal').modal('show');
                $('#trainerModal form').attr('action', '{{ route('trainers.store') }}').data('method',
                    'POST');
            });

            $(document).on('click', '.btn-edit', function() {
                const btn = $(this);
                $('#trainer_id').val(btn.data('id'));
                $('#code').val(btn.data('code'));
                $('#name').val(btn.data('name'));
                $('#email').val(btn.data('email'));
                $('#phone').val(btn.data('phone'));
                $('#type').val(btn.data('type'));
                $('#specialization').val(btn.data('specialization'));
                $('#hourly_rate').val(btn.data('hourly-rate'));
                $('#batch_rate').val(btn.data('batch-rate'));
                $('#status').val(btn.data('status'));
                $('#trainerModal').modal('show');
                $('#trainerModal form').attr('action', btn.data('url')).data('method', 'PATCH');
            });

            $(document).on('click', '.btn-delete', function() {
                const btn = $(this);
                const trainerId = btn.data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: btn.data('url'),
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            success: function(response) {
                                toastr.success('Trainer deleted successfully');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                const error = xhr.responseJSON?.error ||
                                    'Failed to delete trainer';
                                toastr.error(error);
                            }
                        });
                    }
                });
            });

            $('#trainerForm').on('submit', async function(e) {
                e.preventDefault();
                const form = $(this);
                const url = form.attr('action');
                const method = form.data('method') || 'POST';
                const payload = {
                    code: $('#code').val(),
                    name: $('#name').val(),
                    email: $('#email').val(),
                    phone: $('#phone').val(),
                    type: $('#type').val(),
                    specialization: $('#specialization').val(),
                    hourly_rate: $('#hourly_rate').val() || null,
                    batch_rate: $('#batch_rate').val() || null,
                    status: $('#status').val(),
                    _token: '{{ csrf_token() }}'
                };
                try {
                    await $.ajax({
                        url,
                        method,
                        data: payload,
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    $('#trainerModal').modal('hide');
                    toastr.success('Saved');
                    table.ajax.reload();
                } catch (err) {
                    const error = err.responseJSON?.error || 'Failed to save';
                    toastr.error(error);
                }
            });
        });
    </script>
@endsection
