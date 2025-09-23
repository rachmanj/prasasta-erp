<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Out Voucher - {{ $cashOut->voucher_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .voucher-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .voucher-number {
            font-size: 14px;
            color: #666;
        }

        .content {
            margin-bottom: 30px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .info-table .label {
            background-color: #f5f5f5;
            font-weight: bold;
            width: 30%;
        }

        .lines-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .lines-table th,
        .lines-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .lines-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .lines-table .amount {
            text-align: right;
        }

        .total-section {
            margin-top: 20px;
            text-align: right;
        }

        .total-amount {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .amount-words {
            font-style: italic;
            margin-bottom: 20px;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
        }

        @media print {
            body {
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-name">PRASASTA ERP</div>
        <div class="voucher-title">CASH OUT VOUCHER</div>
        <div class="voucher-number">No: {{ $cashOut->voucher_number }}</div>
    </div>

    <div class="content">
        <table class="info-table">
            <tr>
                <td class="label">Date</td>
                <td>{{ $cashOut->date->format('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Description</td>
                <td>{{ $cashOut->description ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Cash Account</td>
                <td>{{ $cashOut->cashAccount->code }} - {{ $cashOut->cashAccount->name }}</td>
            </tr>
            @if ($cashOut->project)
                <tr>
                    <td class="label">Project</td>
                    <td>{{ $cashOut->project->code }} - {{ $cashOut->project->name }}</td>
                </tr>
            @endif
            @if ($cashOut->fund)
                <tr>
                    <td class="label">Fund</td>
                    <td>{{ $cashOut->fund->code }} - {{ $cashOut->fund->name }}</td>
                </tr>
            @endif
            @if ($cashOut->department)
                <tr>
                    <td class="label">Department</td>
                    <td>{{ $cashOut->department->code }} - {{ $cashOut->department->name }}</td>
                </tr>
            @endif
            <tr>
                <td class="label">Created By</td>
                <td>{{ $cashOut->creator->name }}</td>
            </tr>
        </table>

        <h4>Expense Lines:</h4>
        <table class="lines-table">
            <thead>
                <tr>
                    <th>Account</th>
                    <th>Account Name</th>
                    <th>Memo</th>
                    <th class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cashOut->lines as $line)
                    <tr>
                        <td>{{ $line->account->code }}</td>
                        <td>{{ $line->account->name }}</td>
                        <td>{{ $line->memo ?? '-' }}</td>
                        <td class="amount">Rp {{ number_format($line->amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-amount">
                Total Amount: Rp {{ number_format($cashOut->total_amount, 0, ',', '.') }}
            </div>
            <div class="amount-words">
                {{ $terbilang }}
            </div>
        </div>
    </div>

    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line">Prepared By</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Approved By</div>
        </div>
    </div>

    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px;">Print Voucher</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; margin-left: 10px;">Close</button>
    </div>
</body>

</html>
