@extends('layouts.main')

@section('title_page')
    Journal Approval Review
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('journals.approval.index') }}">Journal Approval</a></li>
    <li class="breadcrumb-item active">Review</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Journal Details</h3>
                            <div class="card-tools">
                                <a href="{{ route('journals.approval.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Journal No:</strong></td>
                                            <td>{{ $journal->journal_no }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($journal->date)->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Description:</strong></td>
                                            <td>{{ $journal->description }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td><span class="badge badge-warning">Draft</span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($journal->created_at)->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Source:</strong></td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $journal->source_type)) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Debit:</strong></td>
                                            <td><strong>{{ number_format($journal->lines->sum('debit'), 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Credit:</strong></td>
                                            <td><strong>{{ number_format($journal->lines->sum('credit'), 2) }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <h5>Journal Lines</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Account</th>
                                            <th>Description</th>
                                            <th>Project</th>
                                            <th>Fund</th>
                                            <th>Department</th>
                                            <th class="text-right">Debit</th>
                                            <th class="text-right">Credit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($journal->lines as $line)
                                            <tr>
                                                <td>
                                                    {{ $line->account->code }}<br>
                                                    <small class="text-muted">{{ $line->account->name }}</small>
                                                </td>
                                                <td>{{ $line->memo }}</td>
                                                <td>
                                                    @if ($line->project)
                                                        {{ $line->project->code }}<br>
                                                        <small class="text-muted">{{ $line->project->name }}</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($line->fund)
                                                        {{ $line->fund->code }}<br>
                                                        <small class="text-muted">{{ $line->fund->name }}</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($line->department)
                                                        {{ $line->department->code }}<br>
                                                        <small class="text-muted">{{ $line->department->name }}</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    @if ($line->debit > 0)
                                                        {{ number_format($line->debit, 2) }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    @if ($line->credit > 0)
                                                        {{ number_format($line->credit, 2) }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-light">
                                            <th colspan="5" class="text-right">TOTAL:</th>
                                            <th class="text-right">{{ number_format($journal->lines->sum('debit'), 2) }}
                                            </th>
                                            <th class="text-right">{{ number_format($journal->lines->sum('credit'), 2) }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <h5><i class="icon fas fa-info"></i> Balance Check</h5>
                                        @if (abs($journal->lines->sum('debit') - $journal->lines->sum('credit')) < 0.01)
                                            <p class="mb-0 text-success"><strong>✓ Journal is balanced</strong> - Debits
                                                equal Credits</p>
                                        @else
                                            <p class="mb-0 text-danger"><strong>✗ Journal is not balanced</strong> - Debits:
                                                {{ number_format($journal->lines->sum('debit'), 2) }}, Credits:
                                                {{ number_format($journal->lines->sum('credit'), 2) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 text-center">
                                    <form method="POST" action="{{ route('journals.approval.approve', $journal->id) }}"
                                        class="d-inline">
                                        @csrf
                                        <input type="hidden" name="confirmation" value="1">
                                        <button type="submit" class="btn btn-success btn-lg"
                                            onclick="return confirm('Are you sure you want to approve and post this journal? This action cannot be undone.')">
                                            <i class="fas fa-check"></i> Approve & Post Journal
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
