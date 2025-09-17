@extends('exports.pdf.base')

@section('content')
<div class="summary">
    <div class="summary-title">Payment Aging Summary</div>
    <div class="summary-row">
        <span class="summary-label">Total Overdue Amount:</span>
        <span class="summary-value">Rp {{ number_format($data['total_amount'], 0, ',', '.') }}</span>
    </div>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Aging Range</th>
            <th class="text-right">Amount (Rp)</th>
            <th class="text-center">Count</th>
            <th class="text-center">Percentage</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['aging_data'] as $range => $info)
        <tr>
            <td>{{ $range }}</td>
            <td class="currency">{{ number_format($info['amount'], 0, ',', '.') }}</td>
            <td class="text-center">{{ $info['count'] }}</td>
            <td class="text-center">{{ $info['percentage'] }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>

@if(isset($data['overdue_details']) && count($data['overdue_details']) > 0)
<div class="page-break"></div>
<h3>Overdue Payment Details</h3>
<table class="table">
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Course Name</th>
            <th>Batch Code</th>
            <th class="text-right">Amount (Rp)</th>
            <th>Due Date</th>
            <th class="text-right">Days Overdue</th>
            <th class="text-right">Late Fee (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['overdue_details'] as $payment)
        <tr>
            <td>{{ $payment->enrollment->student->name ?? 'N/A' }}</td>
            <td>{{ $payment->enrollment->batch->course->name ?? 'N/A' }}</td>
            <td>{{ $payment->enrollment->batch->batch_code ?? 'N/A' }}</td>
            <td class="currency">{{ number_format($payment->amount, 0, ',', '.') }}</td>
            <td>{{ $payment->due_date->format('d/m/Y') }}</td>
            <td class="text-right">{{ $payment->due_date->diffInDays(now()) }}</td>
            <td class="currency">{{ number_format($payment->late_fee_amount, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endsection
