@extends('layouts.main')

@section('title_page')
    Courses
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Courses</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Courses</h4>
                    @can('courses.create')
                        <button class="btn btn-sm btn-primary float-right" id="btnNew">Create Course</button>
                    @endcan
                </div>
                @if (session('success'))
                    <script>
                        toastr.success(@json(session('success')));
                    </script>
                @endif
                <div class="card-body">
                    <table class="table table-striped table-sm mb-0" id="course-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Duration</th>
                                <th>Capacity</th>
                                <th>Price</th>
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
    <div class="modal fade" id="courseModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Course</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="courseForm" action="#" method="post">
                    <div class="modal-body">
                        <input type="hidden" id="course_id">
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
                                    <label>Category <span class="text-danger">*</span></label>
                                    <select id="category_id" class="form-control" required>
                                        <option value="">-- Select Category --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <select id="status" class="form-control" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="discontinued">Discontinued</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Duration (Hours) <span class="text-danger">*</span></label>
                                    <input type="number" id="duration_hours" class="form-control" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Capacity <span class="text-danger">*</span></label>
                                    <input type="number" id="capacity" class="form-control" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Base Price <span class="text-danger">*</span></label>
                                    <input type="number" id="base_price" class="form-control" min="0" step="0.01"
                                        required>
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
            var table = $('#course-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('courses.data') }}',
                columns: [{
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'category_name',
                        name: 'category_name'
                    },
                    {
                        data: 'duration_display',
                        name: 'duration_display'
                    },
                    {
                        data: 'capacity',
                        name: 'capacity'
                    },
                    {
                        data: 'price_display',
                        name: 'price_display'
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

            // Load categories for dropdown
            function loadCategories() {
                $.get('{{ route('course-categories.data') }}', function(data) {
                    var options = '<option value="">-- Select Category --</option>';
                    data.data.forEach(function(category) {
                        options += '<option value="' + category.id + '">' + category.name +
                            '</option>';
                    });
                    $('#category_id').html(options);
                });
            }

            $('#btnNew').on('click', function() {
                $('#courseModal form')[0].reset();
                $('#status').val('active');
                loadCategories();
                $('#courseModal').modal('show');
                $('#courseModal form').attr('action', '{{ route('courses.store') }}').data('method',
                'POST');
            });

            $(document).on('click', '.btn-edit', function() {
                const btn = $(this);
                $('#course_id').val(btn.data('id'));
                $('#code').val(btn.data('code'));
                $('#name').val(btn.data('name'));
                $('#description').val(btn.data('description'));
                $('#category_id').val(btn.data('category-id'));
                $('#duration_hours').val(btn.data('duration-hours'));
                $('#capacity').val(btn.data('capacity'));
                $('#base_price').val(btn.data('base-price'));
                $('#status').val(btn.data('status'));
                loadCategories();
                $('#courseModal').modal('show');
                $('#courseModal form').attr('action', btn.data('url')).data('method', 'PATCH');
            });

            $(document).on('click', '.btn-delete', function() {
                const btn = $(this);
                const courseId = btn.data('id');

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
                                toastr.success('Course deleted successfully');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                const error = xhr.responseJSON?.error ||
                                    'Failed to delete course';
                                toastr.error(error);
                            }
                        });
                    }
                });
            });

            $('#courseForm').on('submit', async function(e) {
                e.preventDefault();
                const form = $(this);
                const url = form.attr('action');
                const method = form.data('method') || 'POST';
                const payload = {
                    code: $('#code').val(),
                    name: $('#name').val(),
                    description: $('#description').val(),
                    category_id: $('#category_id').val(),
                    duration_hours: $('#duration_hours').val(),
                    capacity: $('#capacity').val(),
                    base_price: $('#base_price').val(),
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
                    $('#courseModal').modal('hide');
                    toastr.success('Saved');
                    table.ajax.reload();
                } catch (err) {
                    toastr.error('Failed to save');
                }
            });
        });
    </script>
@endsection
