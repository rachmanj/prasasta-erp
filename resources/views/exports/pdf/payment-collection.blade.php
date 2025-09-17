@extends('exports.pdf.base')

@section('content')
<div class="summary">
    <div class="summary-title">Collection Summary</div>
    <div class="summary-row">
        <span class="summary-label">Total Collected:</span>
        <span class="summary-value">Rp {{ number_format($data['summary']['total_collected'], 0, ',', '.') }}</span>
    </div>
    <div class="summary-row">
        <span class="summary-label">Total Late Fees:</span>
        <span class="summary-value">Rp {{ number_format($data['summary']['total_late_fees'], 0, ',', '.') }}</span>
    </div>
    <div class="summary-row">
        <span class="summary-label">Payment Count:</span>
        <span class="summary-value">{{ $data['summary']['payment_count'] }}</span>
    </div>
    <div class="summary-row">
        <span class="summary-label">Average Payment:</span>
        <span class="summary-value">Rp {{ number_format($data['summary']['average_payment'], 0, ',', '.') }}</span>
    </div>
</div>

@if(isset($data['payment_methods']) && count($data['payment_methods']) > 0)
<h3>Payment Method Distribution</h3>
<table class="table">
    <thead>
        <tr>
            <th>Payment Method</th>
            <th class="text-center">Count</th>
            <th class="text-right">Amount (Rp)</th>
            <th class="text-center">Percentage</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['payment_methods'] as $method => $info)
        <tr>
            <td>{{ ucfirst($method) }}</td>
            <td class="text-center">{{ $info['count'] }}</td>
            <td class="currency">{{ number_format($info['amount'], 0, ',', '.') }}</td>
            <td class="text-center">{{ $info['percentage'] }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<h3>Payment Collection Details</h3>
<table class="table">
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Course Name</th>
            <th>Payment Date</th>
            <th class="text-right">Amount (Rp)</th>
            <th>Payment Method</th>
            <th class="text-right">Late Fee (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['payments'] as $payment)
        <tr>
            <td>{{ $payment->enrollment->student->name ?? 'N/A' }}</td>
            <td>{{ $payment->enrollment->batch->course->name ?? 'N/A' }}</td>
            <td>{{ $payment->paid_date ? $payment->paid_date->format('d/m/Y') : 'N/A' }}</td>
            <td class="currency">{{ number_format($payment->paid_amount, 0, ',', '.') }}</td>
            <td>{{ $payment->payment_method ?? 'N/A' }}</td>
            <td class="currency">{{ number_format($payment->late_fee_amount, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
