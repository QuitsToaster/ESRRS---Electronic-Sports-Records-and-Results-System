<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Medal Awards</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; }
        th { background: #eee; }
        h2 { margin-bottom: 10px; }
    </style>
</head>
<body>

<h1 style="text-align: center;">Awarded Medals</h1>

<h2>Medal Summary by Division</h2>
<table>
    <thead>
        <tr>
            <th>Rank</th>
            <th>Division</th>
            <th>Gold</th>
            <th>Silver</th>
            <th>Bronze</th>
            <th>Total Points</th>
        </tr>
    </thead>
    <tbody>
        @foreach($medalTally as $division)
        <tr>
            <td>{{ $division->rank }}</td>
            <td>{{ $division->name }}</td>
            <td>{{ $division->gold }}</td>
            <td>{{ $division->silver }}</td>
            <td>{{ $division->bronze }}</td>
            <td>{{ $division->points }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@foreach(['Elementary','Secondary'] as $level)
<h2>{{ $level }} Division Results</h2>
@foreach($allSports as $sport)
    <h3>{{ $sport->name }}</h3>
    @php
        $sportResults = $results->where('division', $level)->where('sport_id', $sport->id);
    @endphp
    @if($sportResults->isNotEmpty())
    <table>
        <thead>
            <tr>
                <th>Event</th>
                <th>Category</th>
                <th>Medal</th>
                <th>Awarded By</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sportResults as $result)
            <tr>
                <td>{{ $result->event->name ?? '-' }}</td>
                <td>{{ $result->category }}</td>
                <td>{{ $result->medal }}</td>
                <td>{{ $result->awardedBy->name ?? '-' }}</td>
                <td>
    {{ 
        $result->created_at
            ? $result->created_at->format('M d, Y')
            : ($result->event?->award_date
                ? $result->event->award_date->format('M d, Y')
                : '-') 
    }}
</td>

            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
@endforeach
@endforeach

</body>
</html>
