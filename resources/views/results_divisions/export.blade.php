<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Awards Report</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background-color: #f3f4f6;
            text-align: left;
        }

        .medal-breakdown td {
            text-align: center;
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
        <p>Awarded Medals Report</p>
    </div>

    <div class="meta">
        <strong>Generated on:</strong> {{ $generatedAt }} (Philippine Time)
    </div>

    <h2>Medal Summary / Tally</h2>
    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>Team</th>
                <th>Gold</th>
                <th>Silver</th>
                <th>Bronze</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($medalTally as $school)
                <tr>
                    <td>{{ $school->rank }}</td>
                    <td>{{ $school->name }}</td>
                    <td>{{ $school->gold }}</td>
                    <td>{{ $school->silver }}</td>
                    <td>{{ $school->bronze }}</td>
                    <td>{{ ($school->gold*3 + $school->silver*2 + $school->bronze) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>All Awards</h2>
    <table>
        <thead>
            <tr>
                <th>Event</th>
                <th>Sport</th>
                <th>Team</th>
                <th>Division</th>
                <th>Category</th>
                <th>Medal</th>
                <th>Athlete / Team</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
                <tr>
                    <td>{{ $result->event->name ?? '-' }}</td>
                    <td>{{ $result->sport->name ?? '-' }}</td>
                    <td>{{ $result->school->name ?? '-' }}</td>
                    <td>{{ $result->division }}</td>
                    <td>{{ $result->category }}</td>
                    <td>{{ $result->medal }}</td>
                    <td>{{ $result->athlete->name ?? $result->team_name ?? '-' }}</td>
                    <td>
                        {{ 
                            $result->created_at 
                                ? $result->created_at->format('M d, Y') 
                                : ($result->event?->award_date?->format('M d, Y') ?? '-') 
                        }}
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
