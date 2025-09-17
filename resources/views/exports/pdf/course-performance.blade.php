@extends('exports.pdf.base')

@section('content')
<div class="summary">
    <div class="summary-title">Course Performance Summary</div>
    <div class="summary-row">
        <span class="summary-label">Total Courses:</span>
        <span class="summary-value">{{ $data['summary']['total_courses'] }}</span>
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
        <span class="summary-label">Average Capacity Utilization:</span>
        <span class="summary-value">{{ number_format($data['summary']['average_capacity_utilization'], 2) }}%</span>
    </div>
</div>

<h3>Course Performance Details</h3>
<table class="table">
    <thead>
        <tr>
            <th>Course Name</th>
            <th>Course Code</th>
            <th class="text-center">Batches</th>
            <th class="text-center">Enrollments</th>
            <th class="text-right">Revenue (Rp)</th>
            <th class="text-center">Avg Enrollment/Batch</th>
            <th class="text-center">Capacity Utilization</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['performance_data'] as $course)
        <tr>
            <td>{{ $course['course_name'] }}</td>
            <td>{{ $course['course_code'] }}</td>
            <td class="text-center">{{ $course['batch_count'] }}</td>
            <td class="text-center">{{ $course['total_enrollments'] }}</td>
            <td class="currency">{{ number_format($course['total_revenue'], 0, ',', '.') }}</td>
            <td class="text-center">{{ number_format($course['average_enrollment_per_batch'], 1) }}</td>
            <td class="text-center">
                @if($course['capacity_utilization'] >= 80)
                    <span class="status-active">{{ $course['capacity_utilization'] }}%</span>
                @elseif($course['capacity_utilization'] >= 60)
                    <span class="status-pending">{{ $course['capacity_utilization'] }}%</span>
                @else
                    <span class="status-inactive">{{ $course['capacity_utilization'] }}%</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@if(isset($data['top_courses']) && count($data['top_courses']) > 0)
<div class="page-break"></div>
<h3>Top Performing Courses</h3>
<table class="table">
    <thead>
        <tr>
            <th>Rank</th>
            <th>Course Name</th>
            <th class="text-right">Revenue (Rp)</th>
            <th class="text-center">Enrollments</th>
            <th class="text-center">Capacity Utilization</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['top_courses'] as $index => $course)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ $course['course_name'] }}</td>
            <td class="currency">{{ number_format($course['revenue'], 0, ',', '.') }}</td>
            <td class="text-center">{{ $course['enrollments'] }}</td>
            <td class="text-center">{{ $course['capacity_utilization'] }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endsection
