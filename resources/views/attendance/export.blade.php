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
            margin-bottom: 20px;
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

        h2 {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>CAVRAA Event Monitoring System</h1>
        <p>Attendance Report</p>
    </div>

    <div class="meta">
        <strong>Generated on:</strong> {{ $generatedAt }}
    </div>

    {{-- Attended Today --}}
    <h2>Attendance Today</h2>
    <table>
        <thead>
            <tr>
                <th width="30%">Name</th>
                <th width="20%">Role</th>
                <th width="30%">Team</th>
                <th width="20%">Time In</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attended as $record)
                @php
                    $role = $record->athlete ? 'Athlete' : ($record->coach ? 'Coach' : '-');
                    $name = $record->athlete->name ?? $record->coach->name ?? '-';
                    $team = $record->athlete->school->name ?? $record->coach->school->name ?? 'N/A';
                    $timeIn = $record->time_in ? \Carbon\Carbon::parse($record->time_in)
                                ->setTimezone('Asia/Manila')->format('h:i A') : '—';
                @endphp
                <tr>
                    <td>{{ $name }}</td>
                    <td>{{ $role }}</td>
                    <td>{{ $team }}</td>
                    <td>{{ $timeIn }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Not Attended Athletes --}}
    <h2>Athletes without Attendance Today</h2>
    <table>
        <thead>
            <tr>
                <th width="50%">Name</th>
                <th width="50%">Team</th>
            </tr>
        </thead>
        <tbody>
            @foreach($notAttended as $athlete)
                <tr>
                    <td>{{ $athlete->name }}</td>
                    <td>{{ $athlete->school->name ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Not Attended Coaches --}}
    <h2>Coaches without Attendance Today</h2>
    <table>
        <thead>
            <tr>
                <th width="50%">Name</th>
                <th width="50%">Team</th>
            </tr>
        </thead>
        <tbody>
            @foreach($notAttendedCoaches as $coach)
                <tr>
                    <td>{{ $coach->name }}</td>
                    <td>{{ $coach->school->name ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        © {{ date('Y') }} CAVRAA Monitoring System – SDO Santiago City
    </div>

</body>
</html>
