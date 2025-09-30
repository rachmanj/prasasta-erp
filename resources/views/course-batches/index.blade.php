@extends('layouts.main')

@section('title_page')
    Course Batches
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Course Batches</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Course Batches</h4>
                    @can('course_batches.create')
                        <button class="btn btn-sm btn-primary float-right" id="btnNew">Create Batch</button>
                    @endcan
                </div>
                @if (session('success'))
                    <script>
                        toastr.success(@json(session('success')));
                    </script>
                @endif
                <div class="card-body">
                    <table class="table table-striped table-sm mb-0" id="batch-table">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Batch Code</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Duration</th>
                                <th>Location</th>
                                <th>Capacity</th>
                                <th>Enrolled</th>
                                <th>Available</th>
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
    <div class="modal fade" id="batchModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Course Batch</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="batchForm" action="#" method="post">
                    <div class="modal-body">
                        <input type="hidden" id="batch_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Course <span class="text-danger">*</span></label>
                                    <select id="course_id" class="form-control" required>
                                        <option value="">-- Select Course --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Batch Code <span class="text-danger">*</span></label>
                                    <input id="batch_code" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Start Date <span class="text-danger">*</span></label>
                                    <input type="date" id="start_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>End Date <span class="text-danger">*</span></label>
                                    <input type="date" id="end_date" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Location</label>
                                    <input id="location" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <select id="status" class="form-control" required>
                                        <option value="planned">Planned</option>
                                        <option value="ongoing">Ongoing</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Capacity <span class="text-danger">*</span></label>
                                    <input type="number" id="capacity" class="form-control" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Trainer ID</label>
                                    <input type="number" id="trainer_id" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Schedule</label>
                            <textarea id="schedule" class="form-control" rows="3" placeholder="e.g., Monday-Friday, 9:00 AM - 5:00 PM"></textarea>
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
            var table = $('#batch-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('course-batches.data') }}',
                columns: [{
                        data: 'course_name',
                        name: 'course_name'
                    },
                    {
                        data: 'batch_code',
                        name: 'batch_code'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'duration_days',
                        name: 'duration_days'
                    },
                    {
                        data: 'location',
                        name: 'location'
                    },
                    {
                        data: 'capacity',
                        name: 'capacity'
                    },
                    {
                        data: 'enrollment_count',
                        name: 'enrollment_count'
                    },
                    {
                        data: 'available_slots',
                        name: 'available_slots'
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

            // Load courses for dropdown
            function loadCourses() {
                $.get('{{ route('courses.data') }}', function(data) {
                    var options = '<option value="">-- Select Course --</option>';
                    data.data.forEach(function(course) {
                        options += '<option value="' + course.id + '">' + course.name + ' (' +
                            course.code + ')</option>';
                    });
                    $('#course_id').html(options);
                });
            }

            $('#btnNew').on('click', function() {
                $('#batchModal form')[0].reset();
                $('#status').val('planned');
                loadCourses();
                $('#batchModal').modal('show');
                $('#batchModal form').attr('action', '{{ route('course-batches.store') }}').data('method',
                    'POST');
            });

            $(document).on('click', '.btn-edit', function() {
                const btn = $(this);
                $('#batch_id').val(btn.data('id'));
                $('#course_id').val(btn.data('course-id'));
                $('#batch_code').val(btn.data('batch-code'));
                $('#start_date').val(btn.data('start-date'));
                $('#end_date').val(btn.data('end-date'));
                $('#location').val(btn.data('location'));
                $('#trainer_id').val(btn.data('trainer-id'));
                $('#capacity').val(btn.data('capacity'));
                $('#status').val(btn.data('status'));
                loadCourses();
                $('#batchModal').modal('show');
                $('#batchModal form').attr('action', btn.data('url')).data('method', 'PATCH');
            });

            $(document).on('click', '.btn-start', function() {
                const btn = $(this);
                const batchId = btn.data('id');

                Swal.fire({
                    title: 'Start Course Batch?',
                    text: "This will change the batch status to 'ongoing' and trigger revenue recognition for all enrolled students.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, start batch!',
                    cancelButtonText: 'Cancel'
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
                                toastr.success(response.message ||
                                    'Batch started successfully');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                const error = xhr.responseJSON?.error ||
                                    'Failed to start course batch';
                                toastr.error(error);
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-delete', function() {
                const btn = $(this);
                const batchId = btn.data('id');

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
                                toastr.success('Course batch deleted successfully');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                const error = xhr.responseJSON?.error ||
                                    'Failed to delete course batch';
                                toastr.error(error);
                            }
                        });
                    }
                });
            });

            $('#batchForm').on('submit', async function(e) {
                e.preventDefault();
                const form = $(this);
                const url = form.attr('action');
                const method = form.data('method') || 'POST';
                const payload = {
                    course_id: $('#course_id').val(),
                    batch_code: $('#batch_code').val(),
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    location: $('#location').val(),
                    trainer_id: $('#trainer_id').val() || null,
                    capacity: $('#capacity').val(),
                    status: $('#status').val(),
                    schedule: $('#schedule').val(),
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
                    $('#batchModal').modal('hide');
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
