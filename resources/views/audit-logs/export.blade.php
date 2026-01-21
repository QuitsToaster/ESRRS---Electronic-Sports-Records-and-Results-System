<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Audit Log Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 4px;
        }

        .header p {
            font-size: 12px;
            margin: 0;
        }

        .meta {
            margin-bottom: 15px;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #f3f4f6;
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>CAVRAA Event Monitoring System</h1>
        <p>Audit Log Report</p>
    </div>

    <div class="meta">
        <strong>Generated on:</strong> {{ $generatedAt }} (Philippine Time)
    </div>

    <table>
        <thead>
            <tr>
                <th width="25%">User</th>
                <th width="45%">Action</th>
                <th width="30%">Date & Time (PH)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>
                        {{ $log->created_at
                            ->setTimezone('Asia/Manila')
                            ->format('M d, Y h:i A') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        © {{ date('Y') }} CAVRAA Monitoring System – SDO Santiago City
    </div>

</body>
</html>
