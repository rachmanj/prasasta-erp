@extends('exports.pdf.base')

@section('content')
<div class="summary">
    <div class="summary-title">Trainer Performance Summary</div>
    <div class="summary-row">
        <span class="summary-label">Total Trainers:</span>
        <span class="summary-value">{{ $data['summary']['total_trainers'] }}</span>
    </div>
    <div class="summary-row">
        <span class="summary-label">Total Batches:</span>
        <span class="summary-value">{{ $data['summary']['total_batches'] }}</span>
    </div>
    <div class="summary-row">
        <span class="summary-label">Total Enrollments:</span>
        <span class="summary-value">{{ $data['summary']['total_enrollments'] }}</span>
    </div>
    <div class="summary-row">
        <span class="summary-label">Total Revenue:</span>
        <span class="summary-value">Rp {{ number_format($data['summary']['total_revenue'], 0, ',', '.') }}</span>
    </div>
    <div class="summary-row">
        <span class="summary-label">Total Trainer Revenue:</span>
        <span class="summary-value">Rp {{ number_format($data['summary']['total_trainer_revenue'], 0, ',', '.') }}</span>
    </div>
</div>

<h3>Trainer Performance Details</h3>
<table class="table">
    <thead>
        <tr>
            <th>Trainer Name</th>
            <th>Type</th>
            <th class="text-center">Batches</th>
            <th class="text-center">Enrollments</th>
            <th class="text-right">Total Revenue (Rp)</th>
            <th class="text-right">Trainer Revenue (Rp)</th>
            <th class="text-center">Revenue Share %</th>
            <th class="text-right">Hourly Rate (Rp)</th>
            <th class="text-right">Batch Rate (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['performance_data'] as $trainer)
        <tr>
            <td>{{ $trainer['trainer_name'] }}</td>
            <td>
                @if($trainer['trainer_type'] === 'internal')
                    <span class="status-active">{{ ucfirst($trainer['trainer_type']) }}</span>
                @else
                    <span class="status-pending">{{ ucfirst($trainer['trainer_type']) }}</span>
                @endif
            </td>
            <td class="text-center">{{ $trainer['batch_count'] }}</td>
            <td class="text-center">{{ $trainer['total_enrollments'] }}</td>
            <td class="currency">{{ number_format($trainer['total_revenue'], 0, ',', '.') }}</td>
            <td class="currency">{{ number_format($trainer['trainer_revenue'], 0, ',', '.') }}</td>
            <td class="text-center">{{ $trainer['revenue_share_percentage'] }}%</td>
            <td class="currency">{{ number_format($trainer['hourly_rate'] ?? 0, 0, ',', '.') }}</td>
            <td class="currency">{{ number_format($trainer['batch_rate'] ?? 0, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@if(isset($data['utilization_data']) && count($data['utilization_data']) > 0)
<div class="page-break"></div>
<h3>Trainer Utilization Analysis</h3>
<table class="table">
    <thead>
        <tr>
            <th>Trainer Name</th>
            <th>Type</th>
            <th class="text-center">Batch Count</th>
            <th class="text-center">Total Hours</th>
            <th class="text-center">Total Enrollments</th>
            <th class="text-right">Total Revenue (Rp)</th>
            <th class="text-right">Estimated Earnings (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['utilization_data'] as $trainer)
        <tr>
            <td>{{ $trainer['trainer_name'] }}</td>
            <td>
                @if($trainer['trainer_type'] === 'internal')
                    <span class="status-active">{{ ucfirst($trainer['trainer_type']) }}</span>
                @else
                    <span class="status-pending">{{ ucfirst($trainer['trainer_type']) }}</span>
                @endif
            </td>
            <td class="text-center">{{ $trainer['batch_count'] }}</td>
            <td class="text-center">{{ $trainer['total_hours'] }}</td>
            <td class="text-center">{{ $trainer['total_enrollments'] }}</td>
            <td class="currency">{{ number_format($trainer['total_revenue'], 0, ',', '.') }}</td>
            <td class="currency">{{ number_format($trainer['estimated_earnings'], 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endsection
