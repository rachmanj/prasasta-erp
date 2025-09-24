@extends('layouts.main')

@section('title_page')
    Inventory Categories
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Inventory Categories</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">List</h4>
                    @can('inventory.categories.create')
                        <button class="btn btn-sm btn-primary float-right" id="btnNewCategory">Create</button>
                    @endcan
                </div>
                @if (session('success'))
                    <script>
                        toastr.success(@json(session('success')));
                    </script>
                @endif
                <div class="card-body">
                    <table class="table table-striped table-sm mb-0" id="categoriesTable">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Items Count</th>
                                <th>Status</th>
                                <th>Created</th>
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
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="categoryForm" action="#" method="post">
                    <div class="modal-body">
                        <input type="hidden" id="category_id">
                        <div class="form-group">
                            <label>Code</label>
                            <input id="ccode" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input id="cname" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea id="cdescription" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" checked>
                                <label class="form-check-label" for="is_active">Active</label>
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
            var table = $('#categoriesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('inventory.categories.data') }}',
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
                        data: 'items_count',
                        name: 'items_count',
                        orderable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#btnNewCategory').on('click', function() {
                $('#categoryModal form')[0].reset();
                $('#categoryModal').modal('show');
                $('#categoryModal form').attr('action', '{{ route('inventory.categories.store') }}').data(
                    'method', 'POST');
            });

            $(document).on('click', '.btn-edit', function() {
                const btn = $(this);
                $('#category_id').val(btn.data('id'));
                $('#ccode').val(btn.data('code'));
                $('#cname').val(btn.data('name'));
                $('#cdescription').val(btn.data('description'));
                $('#is_active').prop('checked', btn.data('is_active'));
                $('#categoryModal').modal('show');
                $('#categoryModal form').attr('action', btn.data('url')).data('method', 'PATCH');
            });

            $('#categoryForm').on('submit', async function(e) {
                e.preventDefault();
                const form = $(this);
                const url = form.attr('action');
                const method = form.data('method') || 'POST';
                const payload = {
                    code: $('#ccode').val(),
                    name: $('#cname').val(),
                    description: $('#cdescription').val(),
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
                    $('#categoryModal').modal('hide');
                    toastr.success('Saved');
                    table.ajax.reload();
                } catch (err) {
                    toastr.error('Failed to save');
                }
            });
        });
    </script>
@endsection
