<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Athletes List</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; vertical-align: middle; }
        th { background: #f0f0f0; }
        img.profile { width: 50px; height: 50px; object-fit: cover; border-radius: 50%; }
        img.qr { width: 60px; height: 60px; }
        h2 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; margin-top: 0; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h2>List of Athletes</h2>
    <p>Generated on {{ date('F d, Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Profile</th>
                <th>Name</th>
                <th>Sport</th>
                <th>Region</th>
                <th>Division</th>
                <th>QR Code</th>
            </tr>
        </thead>
        <tbody>
            @foreach($athletes as $athlete)
            <tr>
                <td>
                    @if($athlete->profile_photo)
                        <img src="{{ public_path('storage/'.$athlete->profile_photo) }}" class="profile">
                    @else
                        <div style="width:50px;height:50px;background:#ccc;text-align:center;line-height:50px;font-weight:bold;">
                            {{ strtoupper(substr($athlete->last_name,0,1)) }}
                        </div>
                    @endif
                </td>
                <td>{{ $athlete->last_name }}, {{ $athlete->first_name }} {{ $athlete->middle_initial }}</td>
                <td>{{ $athlete->sport->name ?? '—' }}</td>
                <td>{{ $athlete->region ?? '—' }}</td>
                <td>{{ $athlete->division }}</td>
                <td>
                    {!! QrCode::size(60)->generate($athlete->qr_code) !!}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
