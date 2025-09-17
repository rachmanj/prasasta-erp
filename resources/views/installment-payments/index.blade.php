@extends('layouts.main')

@section('title_page')
    Installment Payments
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Installment Payments</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Installment Payments</h4>
                    <div class="float-right">
                        @can('installment_payments.create')
                            <button class="btn btn-sm btn-primary" id="btnNew">Create Installment</button>
                        @endcan
                        <button class="btn btn-sm btn-warning" id="btnUpdateOverdue">Update Overdue</button>
                    </div>
                </div>
                @if (session('success'))
                    <script>
                        toastr.success(@json(session('success')));
                    </script>
                @endif
                <div class="card-body">
                    <table class="table table-striped table-sm mb-0" id="installment-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Batch</th>
                                <th>Installment #</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Paid Amount</th>
                                <th>Late Fee</th>
                                <th>Total</th>
                                <th>Days Overdue</th>
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
    <div class="modal fade" id="installmentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Installment Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="installmentForm" action="#" method="post">
                    <div class="modal-body">
                        <input type="hidden" id="installment_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Enrollment <span class="text-danger">*</span></label>
                                    <select id="enrollment_id" class="form-control" required>
                                        <option value="">-- Select Enrollment --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Installment Number <span class="text-danger">*</span></label>
                                    <input type="number" id="installment_number" class="form-control" min="0"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Amount <span class="text-danger">*</span></label>
                                    <input type="number" id="amount" class="form-control" min="0" step="0.01"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Due Date <span class="text-danger">*</span></label>
                                    <input type="date" id="due_date" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea id="notes" class="form-control" rows="3"></textarea>
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

    <!-- Process Payment Modal -->
    <div class="modal fade" id="processPaymentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Process Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="processPaymentForm" action="#" method="post">
                    <div class="modal-body">
                        <input type="hidden" id="process_installment_id">
                        <div class="form-group">
                            <label>Paid Amount <span class="text-danger">*</span></label>
                            <input type="number" id="paid_amount" class="form-control" min="0" step="0.01"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select id="payment_method" class="form-control">
                                <option value="">-- Select Method --</option>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Reference Number</label>
                            <input id="reference_number" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Process Payment</button>
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
            var table = $('#installment-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('installment-payments.data') }}',
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
                        data: 'installment_number',
                        name: 'installment_number'
                    },
                    {
                        data: 'amount_display',
                        name: 'amount_display',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'due_date',
                        name: 'due_date'
                    },
                    {
                        data: 'paid_amount_display',
                        name: 'paid_amount_display',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'late_fee_display',
                        name: 'late_fee_display',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'total_amount_display',
                        name: 'total_amount_display',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'days_overdue',
                        name: 'days_overdue',
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

            $('#btnNew').on('click', function() {
                $('#installmentModal form')[0].reset();
                loadEnrollments();
                $('#installmentModal').modal('show');
                $('#installmentModal form').attr('action', '{{ route('installment-payments.store') }}')
                    .data('method', 'POST');
            });

            $('#btnUpdateOverdue').on('click', function() {
                $.ajax({
                    url: '{{ route('installment-payments.update-overdue') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON?.error ||
                            'Failed to update overdue installments';
                        toastr.error(error);
                    }
                });
            });

            $(document).on('click', '.btn-edit', function() {
                const btn = $(this);
                $('#installment_id').val(btn.data('id'));
                $('#enrollment_id').val(btn.data('enrollment-id'));
                $('#installment_number').val(btn.data('installment-number'));
                $('#amount').val(btn.data('amount'));
                $('#due_date').val(btn.data('due-date'));
                $('#notes').val(btn.data('notes'));
                loadEnrollments();
                $('#installmentModal').modal('show');
                $('#installmentModal form').attr('action', btn.data('url')).data('method', 'PATCH');
            });

            $(document).on('click', '.btn-process-payment', function() {
                const btn = $(this);
                $('#process_installment_id').val(btn.data('id'));
                $('#processPaymentModal').modal('show');
                $('#processPaymentForm').attr('action', btn.data('url')).data('method', 'POST');
            });

            $(document).on('click', '.btn-delete', function() {
                const btn = $(this);
                const installmentId = btn.data('id');

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
                                    'Installment payment deleted successfully');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                const error = xhr.responseJSON?.error ||
                                    'Failed to delete installment payment';
                                toastr.error(error);
                            }
                        });
                    }
                });
            });

            $('#installmentForm').on('submit', async function(e) {
                e.preventDefault();
                const form = $(this);
                const url = form.attr('action');
                const method = form.data('method') || 'POST';
                const payload = {
                    enrollment_id: $('#enrollment_id').val(),
                    installment_number: $('#installment_number').val(),
                    amount: $('#amount').val(),
                    due_date: $('#due_date').val(),
                    notes: $('#notes').val(),
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
                    $('#installmentModal').modal('hide');
                    toastr.success('Saved');
                    table.ajax.reload();
                } catch (err) {
                    const error = err.responseJSON?.error || 'Failed to save';
                    toastr.error(error);
                }
            });

            $('#processPaymentForm').on('submit', async function(e) {
                e.preventDefault();
                const form = $(this);
                const url = form.attr('action');
                const method = form.data('method') || 'POST';
                const payload = {
                    paid_amount: $('#paid_amount').val(),
                    payment_method: $('#payment_method').val(),
                    reference_number: $('#reference_number').val(),
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
                    $('#processPaymentModal').modal('hide');
                    toastr.success('Payment processed successfully');
                    table.ajax.reload();
                } catch (err) {
                    const error = err.responseJSON?.error || 'Failed to process payment';
                    toastr.error(error);
                }
            });
        });
    </script>
@endsection
