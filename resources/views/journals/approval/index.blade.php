@extends('layouts.main')

@section('title_page')
    Journal Approval
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Journal Approval</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Pending Journal Approvals</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="card card-outline card-info search-card">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-search"></i> Advanced Search</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label>Date From</label>
                                                    <input type="date" id="filter_from" class="form-control" />
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Date To</label>
                                                    <input type="date" id="filter_to" class="form-control" />
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Description</label>
                                                    <input type="text" id="filter_desc" class="form-control"
                                                        placeholder="Search description" />
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end justify-content-end">
                                                    <button class="btn btn-info mr-2" id="apply_search"><i
                                                            class="fas fa-search"></i> Apply</button>
                                                    <button class="btn btn-secondary" id="clear_search"><i
                                                            class="fas fa-times"></i> Clear</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if (session('success'))
                                <script>
                                    toastr.success(@json(session('success')));
                                </script>
                            @endif

                            <table class="table table-bordered table-striped table-sm" id="approval-table">
                                <thead>
                                    <tr>
                                        <th style="width:160px;">Journal No</th>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th class="text-right">Debit</th>
                                        <th class="text-right">Credit</th>
                                        <th style="width:100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Approval Confirmation Modal -->
    <div class="modal fade" id="approvalModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Journal Approval</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve and post this journal?</p>
                    <p class="text-warning"><strong>This action cannot be undone.</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form id="approval-form" method="POST" style="display: inline;">
                        @csrf
                        <input type="checkbox" name="confirmation" value="1" required style="display: none;" checked>
                        <button type="submit" class="btn btn-success">Approve & Post</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            var table = $('#approval-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('journals.approval.data') }}',
                    data: function(d) {
                        d.from = $('#filter_from').val();
                        d.to = $('#filter_to').val();
                        d.desc = $('#filter_desc').val();
                    }
                },
                columns: [{
                        data: 'journal_no',
                        name: 'journal_no'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'total_debit',
                        name: 'total_debit',
                        className: 'text-right',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'total_credit',
                        name: 'total_credit',
                        className: 'text-right',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'desc'],
                    [0, 'desc']
                ],
                pageLength: 25,
                responsive: true
            });

            $('#apply_search').on('click', function() {
                table.ajax.reload();
            });
            $('#clear_search').on('click', function() {
                $('#filter_from').val('');
                $('#filter_to').val('');
                $('#filter_desc').val('');
                table.ajax.reload();
            });

            $(document).on('click', '.approve-button', function() {
                var journalId = $(this).data('id');
                var url = $(this).data('url');

                Swal.fire({
                    title: 'Approve Journal?',
                    text: 'This will post the journal entry and cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, approve',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Approving journal entry',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit the form
                        var form = $('<form method="POST" action="' + url +
                            '">@csrf<input type="hidden" name="confirmation" value="1"></form>');
                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
