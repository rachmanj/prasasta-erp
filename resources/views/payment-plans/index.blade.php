@extends('layouts.main')

@section('title_page')
    Payment Plans
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Payment Plans</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Payment Plans</h4>
                    @can('payment_plans.create')
                        <button class="btn btn-sm btn-primary float-right" id="btnNew">Create Payment Plan</button>
                    @endcan
                </div>
                @if (session('success'))
                    <script>
                        toastr.success(@json(session('success')));
                    </script>
                @endif
                <div class="card-body">
                    <table class="table table-striped table-sm mb-0" id="payment-plan-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Installments</th>
                                <th>Down Payment</th>
                                <th>Late Fee</th>
                                <th>Grace Period</th>
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
    <div class="modal fade" id="paymentPlanModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Plan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="paymentPlanForm" action="#" method="post">
                    <div class="modal-body">
                        <input type="hidden" id="payment_plan_id">
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
                        <div class="form-group">
                            <label>Description</label>
                            <textarea id="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Installment Count <span class="text-danger">*</span></label>
                                    <input type="number" id="installment_count" class="form-control" min="1"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Interval (Days) <span class="text-danger">*</span></label>
                                    <input type="number" id="installment_interval_days" class="form-control" min="1"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Down Payment (%)</label>
                                    <input type="number" id="down_payment_percentage" class="form-control" min="0"
                                        max="100" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Late Fee (%)</label>
                                    <input type="number" id="late_fee_percentage" class="form-control" min="0"
                                        max="100" step="0.01">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Grace Period (Days) <span class="text-danger">*</span></label>
                                    <input type="number" id="grace_period_days" class="form-control" min="0"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <div class="form-check">
                                        <input type="checkbox" id="is_active" class="form-check-input" checked>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
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
            var table = $('#payment-plan-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('payment-plans.data') }}',
                columns: [{
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'installment_display',
                        name: 'installment_display',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'down_payment_display',
                        name: 'down_payment_display',
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
                        data: 'grace_period_days',
                        name: 'grace_period_days'
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
                $('#paymentPlanModal form')[0].reset();
                $('#is_active').prop('checked', true);
                $('#paymentPlanModal').modal('show');
                $('#paymentPlanModal form').attr('action', '{{ route('payment-plans.store') }}').data(
                    'method', 'POST');
            });

            $(document).on('click', '.btn-edit', function() {
                const btn = $(this);
                $('#payment_plan_id').val(btn.data('id'));
                $('#code').val(btn.data('code'));
                $('#name').val(btn.data('name'));
                $('#description').val(btn.data('description'));
                $('#installment_count').val(btn.data('installment-count'));
                $('#installment_interval_days').val(btn.data('installment-interval-days'));
                $('#down_payment_percentage').val(btn.data('down-payment-percentage'));
                $('#late_fee_percentage').val(btn.data('late-fee-percentage'));
                $('#grace_period_days').val(btn.data('grace-period-days'));
                $('#is_active').prop('checked', btn.data('is-active') == '1');
                $('#paymentPlanModal').modal('show');
                $('#paymentPlanModal form').attr('action', btn.data('url')).data('method', 'PATCH');
            });

            $(document).on('click', '.btn-delete', function() {
                const btn = $(this);
                const paymentPlanId = btn.data('id');

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
                                toastr.success('Payment plan deleted successfully');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                const error = xhr.responseJSON?.error ||
                                    'Failed to delete payment plan';
                                toastr.error(error);
                            }
                        });
                    }
                });
            });

            $('#paymentPlanForm').on('submit', async function(e) {
                e.preventDefault();
                const form = $(this);
                const url = form.attr('action');
                const method = form.data('method') || 'POST';
                const payload = {
                    code: $('#code').val(),
                    name: $('#name').val(),
                    description: $('#description').val(),
                    installment_count: $('#installment_count').val(),
                    installment_interval_days: $('#installment_interval_days').val(),
                    down_payment_percentage: $('#down_payment_percentage').val() || null,
                    late_fee_percentage: $('#late_fee_percentage').val() || null,
                    grace_period_days: $('#grace_period_days').val(),
                    is_active: $('#is_active').is(':checked') ? 1 : 0,
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
                    $('#paymentPlanModal').modal('hide');
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
