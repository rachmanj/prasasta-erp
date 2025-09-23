@extends('layouts.main')

@section('title_page')
    Cash Out Transactions
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('banking.dashboard.index') }}">Banking</a></li>
    <li class="breadcrumb-item active">Cash Out</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Cash Out Transactions</h4>
                    <a href="{{ route('banking.cash-out.create') }}" class="btn btn-sm btn-primary float-right">New Cash
                        Out</a>
                </div>

                @if (session('success'))
                    <script>
                        toastr.success(@json(session('success')));
                    </script>
                @endif

                <div class="card-body">
                    <table class="table table-striped table-sm mb-0" id="cash-out-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Voucher No</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Cash Account</th>
                                <th>Dimensions</th>
                                <th>Creator</th>
                                <th class="text-right">Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#cash-out-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('banking.cash-out.data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'voucher_number',
                        name: 'co.voucher_number'
                    },
                    {
                        data: 'date',
                        name: 'co.date'
                    },
                    {
                        data: 'description',
                        name: 'co.description'
                    },
                    {
                        data: 'cash_account',
                        name: 'cash_account',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'dimensions',
                        name: 'dimensions',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'creator_name',
                        name: 'u.name'
                    },
                    {
                        data: 'total_amount',
                        name: 'co.total_amount',
                        className: 'text-right',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'co.status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [2, 'desc']
                ]
            });
        });
    </script>
@endpush
