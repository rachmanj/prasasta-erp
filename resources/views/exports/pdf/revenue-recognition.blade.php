@extends('exports.pdf.base')

@section('content')
<div class="summary">
    <div class="summary-title">Revenue Recognition Summary</div>
    <div class="summary-row">
        <span class="summary-label">Total Deferred Revenue:</span>
        <span class="summary-value">Rp {{ number_format($data['summary']['total_deferred'], 0, ',', '.') }}</span>
    </div>
    <div class="summary-row">
        <span class="summary-label">Total Recognized Revenue:</span>
        <span class="summary-value">Rp {{ number_format($data['summary']['total_recognized'], 0, ',', '.') }}</span>
    </div>
    <div class="summary-row">
        <span class="summary-label">Total Reversed Revenue:</span>
        <span class="summary-value">Rp {{ number_format($data['summary']['total_reversed'], 0, ',', '.') }}</span>
    </div>
    <div class="summary-row">
        <span class="summary-label">Net Revenue:</span>
        <span class="summary-value text-bold">Rp {{ number_format($data['summary']['net_revenue'], 0, ',', '.') }}</span>
    </div>
</div>

@if(isset($data['course_revenue']) && count($data['course_revenue']) > 0)
<h3>Revenue by Course</h3>
<table class="table">
    <thead>
        <tr>
            <th>Course Name</th>
            <th class="text-right">Deferred (Rp)</th>
            <th class="text-right">Recognized (Rp)</th>
            <th class="text-right">Reversed (Rp)</th>
            <th class="text-right">Net Revenue (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['course_revenue'] as $course => $revenue)
        <tr>
            <td>{{ $course }}</td>
            <td class="currency">{{ number_format($revenue['deferred'], 0, ',', '.') }}</td>
            <td class="currency">{{ number_format($revenue['recognized'], 0, ',', '.') }}</td>
            <td class="currency">{{ number_format($revenue['reversed'], 0, ',', '.') }}</td>
            <td class="currency text-bold">{{ number_format($revenue['net'], 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<h3>Revenue Recognition Details</h3>
<table class="table">
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Course Name</th>
            <th>Recognition Date</th>
            <th class="text-right">Amount (Rp)</th>
            <th>Type</th>
            <th>Status</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['revenue_data'] as $revenue)
        <tr>
            <td>{{ $revenue->enrollment->student->name ?? 'N/A' }}</td>
            <td>{{ $revenue->enrollment->batch->course->name ?? 'N/A' }}</td>
            <td>{{ $revenue->recognition_date->format('d/m/Y') }}</td>
            <td class="currency">{{ number_format($revenue->amount, 0, ',', '.') }}</td>
            <td>
                @if($revenue->type === 'recognized')
                    <span class="status-completed">{{ ucfirst($revenue->type) }}</span>
                @elseif($revenue->type === 'deferred')
                    <span class="status-pending">{{ ucfirst($revenue->type) }}</span>
                @else
                    <span class="status-inactive">{{ ucfirst($revenue->type) }}</span>
                @endif
            </td>
            <td>
                @if($revenue->posted_status === 'posted')
                    <span class="status-completed">{{ ucfirst($revenue->posted_status) }}</span>
                @elseif($revenue->posted_status === 'pending')
                    <span class="status-pending">{{ ucfirst($revenue->posted_status) }}</span>
                @else
                    <span class="status-inactive">{{ ucfirst($revenue->posted_status) }}</span>
                @endif
            </td>
            <td>{{ $revenue->description ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
