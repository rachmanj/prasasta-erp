@extends('layouts.main')

@section('title_page')
    Course Categories
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Course Categories</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Course Categories</h4>
                    @can('course_categories.manage')
                        <button class="btn btn-sm btn-primary float-right" id="btnNew">Create Category</button>
                    @endcan
                </div>
                @if (session('success'))
                    <script>
                        toastr.success(@json(session('success')));
                    </script>
                @endif
                <div class="card-body">
                    <table class="table table-striped table-sm mb-0" id="category-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Parent Category</th>
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
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Course Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="categoryForm" action="#" method="post">
                    <div class="modal-body">
                        <input type="hidden" id="category_id">
                        <div class="form-group">
                            <label>Code</label>
                            <input id="code" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input id="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea id="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Parent Category</label>
                            <select id="parent_id" class="form-control">
                                <option value="">-- Select Parent Category --</option>
                            </select>
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
            var table = $('#category-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('course-categories.data') }}',
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
                        data: 'parent_name',
                        name: 'parent_name'
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

            // Load parent categories for dropdown
            function loadParentCategories(excludeId = null) {
                $.get('{{ route('course-categories.data') }}', function(data) {
                    var options = '<option value="">-- Select Parent Category --</option>';
                    data.data.forEach(function(category) {
                        if (!excludeId || category.id != excludeId) {
                            options += '<option value="' + category.id + '">' + category.name +
                                '</option>';
                        }
                    });
                    $('#parent_id').html(options);
                });
            }

            $('#btnNew').on('click', function() {
                $('#categoryModal form')[0].reset();
                $('#is_active').prop('checked', true);
                loadParentCategories();
                $('#categoryModal').modal('show');
                $('#categoryModal form').attr('action', '{{ route('course-categories.store') }}').data(
                    'method', 'POST');
            });

            $(document).on('click', '.btn-edit', function() {
                const btn = $(this);
                $('#category_id').val(btn.data('id'));
                $('#code').val(btn.data('code'));
                $('#name').val(btn.data('name'));
                $('#description').val(btn.data('description'));
                $('#parent_id').val(btn.data('parent-id'));
                $('#is_active').prop('checked', btn.data('is-active') == '1');
                loadParentCategories(btn.data('id')); // Exclude current category from parent options
                $('#categoryModal').modal('show');
                $('#categoryModal form').attr('action', btn.data('url')).data('method', 'PATCH');
            });

            $('#categoryForm').on('submit', async function(e) {
                e.preventDefault();
                const form = $(this);
                const url = form.attr('action');
                const method = form.data('method') || 'POST';
                const payload = {
                    code: $('#code').val(),
                    name: $('#name').val(),
                    description: $('#description').val(),
                    parent_id: $('#parent_id').val() || null,
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
