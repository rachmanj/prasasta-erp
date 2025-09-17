@extends('layouts.main')

@section('title_page')
    Enrollments
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Enrollments</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Enrollments</h4>
                    @can('enrollments.create')
                        <button class="btn btn-sm btn-primary float-right" id="btnNew">Create Enrollment</button>
                    @endcan
                </div>
                @if (session('success'))
                    <script>
                        toastr.success(@json(session('success')));
                    </script>
                @endif
                <div class="card-body">
                    <table class="table table-striped table-sm mb-0" id="enrollment-table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Course</th>
                                <th>Batch</th>
                                <th>Enrollment Date</th>
                                <th>Total Amount</th>
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
    <div class="modal fade" id="enrollmentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enrollment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="enrollmentForm" action="#" method="post">
                    <div class="modal-body">
                        <input type="hidden" id="enrollment_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Student <span class="text-danger">*</span></label>
                                    <select id="student_id" class="form-control" required>
                                        <option value="">-- Select Student --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Course Batch <span class="text-danger">*</span></label>
                                    <select id="batch_id" class="form-control" required>
                                        <option value="">-- Select Batch --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Enrollment Date <span class="text-danger">*</span></label>
                                    <input type="date" id="enrollment_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <select id="status" class="form-control" required>
                                        <option value="enrolled">Enrolled</option>
                                        <option value="completed">Completed</option>
                                        <option value="dropped">Dropped</option>
                                        <option value="suspended">Suspended</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Total Amount <span class="text-danger">*</span></label>
                                    <input type="number" id="total_amount" class="form-control" min="0"
                                        step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Payment Plan ID</label>
                                    <input type="number" id="payment_plan_id" class="form-control">
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
            var table = $('#enrollment-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('enrollments.data') }}',
                columns: [{
                        data: 'student_id',
                        name: 'student_id'
                    },
                    {
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
                        data: 'enrollment_date',
                        name: 'enrollment_date'
                    },
                    {
                        data: 'amount_display',
                        name: 'amount_display'
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

            // Load students for dropdown
            function loadStudents() {
                $.get('{{ route('customers.data') }}', function(data) {
                    var options = '<option value="">-- Select Student --</option>';
                    data.data.forEach(function(customer) {
                        if (customer.student_id) {
                            options += '<option value="' + customer.id + '">' + customer.name +
                                ' (' + customer.student_id + ')</option>';
                        }
                    });
                    $('#student_id').html(options);
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
                $('#enrollmentModal form')[0].reset();
                $('#status').val('enrolled');
                $('#enrollment_date').val(new Date().toISOString().split('T')[0]);
                loadStudents();
                loadBatches();
                $('#enrollmentModal').modal('show');
                $('#enrollmentModal form').attr('action', '{{ route('enrollments.store') }}').data('method',
                    'POST');
            });

            $(document).on('click', '.btn-edit', function() {
                const btn = $(this);
                $('#enrollment_id').val(btn.data('id'));
                $('#student_id').val(btn.data('student-id'));
                $('#batch_id').val(btn.data('batch-id'));
                $('#enrollment_date').val(btn.data('enrollment-date'));
                $('#status').val(btn.data('status'));
                $('#payment_plan_id').val(btn.data('payment-plan-id'));
                $('#total_amount').val(btn.data('total-amount'));
                loadStudents();
                loadBatches();
                $('#enrollmentModal').modal('show');
                $('#enrollmentModal form').attr('action', btn.data('url')).data('method', 'PATCH');
            });

            $(document).on('click', '.btn-delete', function() {
                const btn = $(this);
                const enrollmentId = btn.data('id');

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
                                toastr.success('Enrollment deleted successfully');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                const error = xhr.responseJSON?.error ||
                                    'Failed to delete enrollment';
                                toastr.error(error);
                            }
                        });
                    }
                });
            });

            $('#enrollmentForm').on('submit', async function(e) {
                e.preventDefault();
                const form = $(this);
                const url = form.attr('action');
                const method = form.data('method') || 'POST';
                const payload = {
                    student_id: $('#student_id').val(),
                    batch_id: $('#batch_id').val(),
                    enrollment_date: $('#enrollment_date').val(),
                    status: $('#status').val(),
                    payment_plan_id: $('#payment_plan_id').val() || null,
                    total_amount: $('#total_amount').val(),
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
                    $('#enrollmentModal').modal('hide');
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
