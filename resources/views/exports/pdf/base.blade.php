<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Report' }} - {{ $company_name ?? 'Prasasta ERP' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .report-subtitle {
            font-size: 14px;
            color: #666;
        }
        
        .report-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
        }
        
        .info-value {
            color: #333;
        }
        
        .content {
            margin-bottom: 30px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        
        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #e9ecef;
            border-radius: 5px;
        }
        
        .summary-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .summary-label {
            font-weight: bold;
            color: #555;
        }
        
        .summary-value {
            color: #333;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-bold {
            font-weight: bold;
        }
        
        .currency {
            text-align: right;
        }
        
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        
        .status-inactive {
            color: #dc3545;
            font-weight: bold;
        }
        
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        
        .status-completed {
            color: #17a2b8;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $company_name ?? 'Prasasta ERP' }}</div>
        <div class="report-title">{{ $title ?? 'Report' }}</div>
        @if(isset($subtitle))
        <div class="report-subtitle">{{ $subtitle }}</div>
        @endif
    </div>
    
    <div class="report-info">
        <div class="info-row">
            <span class="info-label">Generated Date:</span>
            <span class="info-value">{{ $generated_at->format('d F Y H:i:s') }}</span>
        </div>
        @if(isset($period))
        <div class="info-row">
            <span class="info-label">Period:</span>
            <span class="info-value">{{ $period['start_date'] }} - {{ $period['end_date'] }}</span>
        </div>
        @endif
        @if(isset($filters))
        @foreach($filters as $key => $value)
        <div class="info-row">
            <span class="info-label">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
            <span class="info-value">{{ $value }}</span>
        </div>
        @endforeach
        @endif
    </div>
    
    <div class="content">
        @yield('content')
    </div>
    
    @if(isset($summary))
    <div class="summary">
        <div class="summary-title">Summary</div>
        @foreach($summary as $key => $value)
        <div class="summary-row">
            <span class="summary-label">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
            <span class="summary-value">{{ $value }}</span>
        </div>
        @endforeach
    </div>
    @endif
    
    <div class="footer">
        <div>Generated by {{ $company_name ?? 'Prasasta ERP' }} on {{ $generated_at->format('d F Y H:i:s') }}</div>
        <div>Page <span class="page-number"></span></div>
    </div>
</body>
</html>
