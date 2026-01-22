<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>

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
        <p>Attendance Report</p>
    </div>

    <div class="meta">
        <strong>Generated on:</strong> {{ now()->setTimezone('Asia/Manila')->format('F d, Y h:i A') }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="35%">Name</th>
                <th width="40%">Team</th>
                <th width="25%">Time In (PH)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($athletes as $athlete)
                <tr>
                    <td>{{ $athlete->name }}</td>
                    <td>{{ $athlete->school->name ?? 'N/A' }}</td>
                    <td>{{ $athlete->time_in
                        ? $athlete->time_in->setTimezone('Asia/Manila')->format('M d, Y h:i A')
                        : '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        © {{ date('Y') }} CAVRAA Monitoring System – SDO Santiago City
    </div>

</body>
</html>
