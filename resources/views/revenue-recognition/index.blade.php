@extends('layouts.main')

@section('title_page')
    Revenue Recognition
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Revenue Recognition</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Revenue Recognition</h4>
                    @can('revenue_recognition.create')
                        <button class="btn btn-sm btn-primary float-right" id="btnNew">Create Revenue Recognition</button>
                    @endcan
                </div>
                @if (session('success'))
                    <script>
                        toastr.success(@json(session('success')));
                    </script>
                @endif
                <div class="card-body">
                    <table class="table table-striped table-sm mb-0" id="revenue-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Batch</th>
                                <th>Recognition Date</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Posted Status</th>
                                <th>Description</th>
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
    <div class="modal fade" id="revenueModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Revenue Recognition</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="revenueForm" action="#" method="post">
                    <div class="modal-body">
                        <input type="hidden" id="revenue_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Enrollment</label>
                                    <select id="enrollment_id" class="form-control">
                                        <option value="">-- Select Enrollment --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Batch <span class="text-danger">*</span></label>
                                    <select id="batch_id" class="form-control" required>
                                        <option value="">-- Select Batch --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Recognition Date <span class="text-danger">*</span></label>
                                    <input type="date" id="recognition_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Amount <span class="text-danger">*</span></label>
                                    <input type="number" id="amount" class="form-control" min="0" step="0.01"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Type <span class="text-danger">*</span></label>
                            <select id="type" class="form-control" required>
                                <option value="deferred">Deferred</option>
                                <option value="recognized">Recognized</option>
                                <option value="reversed">Reversed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea id="description" class="form-control" rows="3"></textarea>
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
            var table = $('#revenue-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('revenue-recognition.data') }}',
                columns: [{
                        data: 'student_name',
                        name: 'student_name'
                    },
                    {
                        data: 'course_name',
                        name: 'course_name'
                    },
                    {
                        data: 'batch_code',
                        name: 'batch_code'
                    },
                    {
                        data: 'recognition_date',
                        name: 'recognition_date'
                    },
                    {
                        data: 'amount_display',
                        name: 'amount_display',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type',
                        name: 'type',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'posted_status',
                        name: 'posted_status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Load enrollments for dropdown
            function loadEnrollments() {
                $.get('{{ route('enrollments.data') }}', function(data) {
                    var options = '<option value="">-- Select Enrollment --</option>';
                    data.data.forEach(function(enrollment) {
                        options += '<option value="' + enrollment.id + '">' + enrollment
                            .student_name + ' - ' + enrollment.course_name + '</option>';
                    });
                    $('#enrollment_id').html(options);
                });
            }

            // Load batches for dropdown
            function loadBatches() {
                $.get('{{ route('course-batches.data') }}', function(data) {
                    var options = '<option value="">-- Select Batch --</option>';
                    data.data.forEach(function(batch) {
                        options += '<option value="' + batch.id + '">' + batch.course_name + ' - ' +
                            batch.batch_code + '</option>';
                    });
                    $('#batch_id').html(options);
                });
            }

            $('#btnNew').on('click', function() {
                $('#revenueModal form')[0].reset();
                $('#type').val('deferred');
                $('#recognition_date').val(new Date().toISOString().split('T')[0]);
                loadEnrollments();
                loadBatches();
                $('#revenueModal').modal('show');
                $('#revenueModal form').attr('action', '{{ route('revenue-recognition.store') }}').data(
                    'method', 'POST');
            });

            $(document).on('click', '.btn-edit', function() {
                const btn = $(this);
                $('#revenue_id').val(btn.data('id'));
                $('#enrollment_id').val(btn.data('enrollment-id'));
                $('#batch_id').val(btn.data('batch-id'));
                $('#recognition_date').val(btn.data('recognition-date'));
                $('#amount').val(btn.data('amount'));
                $('#type').val(btn.data('type'));
                $('#description').val(btn.data('description'));
                loadEnrollments();
                loadBatches();
                $('#revenueModal').modal('show');
                $('#revenueModal form').attr('action', btn.data('url')).data('method', 'PATCH');
            });

            $(document).on('click', '.btn-recognize', function() {
                const btn = $(this);
                const revenueId = btn.data('id');

                Swal.fire({
                    title: 'Recognize Revenue?',
                    text: "This will change the revenue from deferred to recognized.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, recognize it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: btn.data('url'),
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            success: function(response) {
                                toastr.success('Revenue recognized successfully');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                const error = xhr.responseJSON?.error ||
                                    'Failed to recognize revenue';
                                toastr.error(error);
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-reverse', function() {
                const btn = $(this);
                const revenueId = btn.data('id');

                Swal.fire({
                    title: 'Reverse Revenue?',
                    text: "This will reverse the revenue recognition.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, reverse it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: btn.data('url'),
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            success: function(response) {
                                toastr.success(
                                    'Revenue recognition reversed successfully');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                const error = xhr.responseJSON?.error ||
                                    'Failed to reverse revenue recognition';
                                toastr.error(error);
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-delete', function() {
                const btn = $(this);
                const revenueId = btn.data('id');

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
                                toastr.success(
                                    'Revenue recognition deleted successfully');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                const error = xhr.responseJSON?.error ||
                                    'Failed to delete revenue recognition';
                                toastr.error(error);
                            }
                        });
                    }
                });
            });

            $('#revenueForm').on('submit', async function(e) {
                e.preventDefault();
                const form = $(this);
                const url = form.attr('action');
                const method = form.data('method') || 'POST';
                const payload = {
                    enrollment_id: $('#enrollment_id').val() || null,
                    batch_id: $('#batch_id').val(),
                    recognition_date: $('#recognition_date').val(),
                    amount: $('#amount').val(),
                    type: $('#type').val(),
                    description: $('#description').val(),
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
                    $('#revenueModal').modal('hide');
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
