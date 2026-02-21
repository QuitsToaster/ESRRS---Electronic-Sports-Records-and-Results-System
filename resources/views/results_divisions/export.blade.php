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
        h3 { margin-bottom: 5px; }
        .signatures { font-size: 10px; margin-top: 30px; }
        .sign-block { display: inline-block; width: 24%; text-align: center; vertical-align: top; margin-bottom: 10px; }
    </style>
</head>
<body>

<h1 style="text-align: center;">Awarded Medals</h1>

{{-- Medal Summary by Division --}}
<h2>Medal Summary by SDO</h2>
<table>
    <thead>
        <tr>
            <th>Rank</th>
            <th>SDO</th>
            <th>Gold</th>
            <th>Silver</th>
            <th>Bronze</th>
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
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Detailed Results --}}
@foreach($levels as $level)
<h2>{{ $level }} Division Results</h2>
<table>
    <thead>
        <tr>
            <th>Event</th>
            <th>Category</th>
            <th>Sport</th>
            <th>Medal</th>
            <th>SDO</th>
            <th>Awarded By</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pdfResults as $result)
            @if($result['division'] === $level)
            <tr>
                <td>{{ $result['event'] }}</td>
                <td>{{ $result['category'] ?? '-' }}</td>
                <td>{{ $result['sport'] ?? '-' }}</td>
                <td>{{ $result['medal'] ?? '-' }}</td>
                <td>{{ $result['sdo'] }}</td>
                <td>{{ $result['awarded_by'] }}</td>
                <td>{{ $result['date'] }}</td>
            </tr>
            @endif
        @endforeach
    </tbody>
</table>
@endforeach

{{-- Signatures --}}
<div class="signatures">
    <h3>Prepared by: GAMES RESULTS & RSAC</h3>

    <div class="sign-block">
        EMERSON R. RESPONZO<br>MEMBER, GAMES RESULT
    </div>
    <div class="sign-block">
        JAMI DAVE G. SERRANO<br>MEMBER, GAMES RESULT
    </div>
    <div class="sign-block">
        RONNIE B. BIBAS<br>MEMBER, GAMES RESULT
    </div>
    <div class="sign-block">
        ROXALYN A. AGUSTIN<br>MEMBER, GAMES RESULT
    </div>

    <div class="sign-block">
        JAYWARD C. BUCAYU<br>MEMBER, GAMES RESULT
    </div>
    <div class="sign-block">
        FERDINAND ASTELERO<br>MEMBER, GAMES RESULT
    </div>
    <div class="sign-block">
        KYM CLYDE MORO<br>MEMBER, RSAC
    </div>
    <div class="sign-block">
        BERNADETTE MIRANDA<br>MEMBER, RSAC
    </div>

    <div class="sign-block">
        ROLDAN LOPEZ<br>MEMBER, RSAC
    </div>
    <div class="sign-block">
        FAITH NOEL REYES<br>MEMBER, RSAC
    </div>
    <div class="sign-block">
        DANTE M. CAPUCHINO<br>MEMBER, RSAC
    </div>
    <div class="sign-block">
        FANIVER MARCELO<br>MEMBER, RSAC
    </div>

    <div class="sign-block">
        REYMA CASARENO<br>MEMBER, RSAC
    </div>
    <div class="sign-block">
        RANDY B. MINA<br>MEMBER, RSAC
    </div>
    <div class="sign-block">
        ATTY. JEREMY MARAMAG<br>MEMBER, RSAC
    </div>
    <div class="sign-block">
        ATTY. ABEGAIL D. JACOB<br>Legal Officer
    </div>

    <div class="sign-block">
        DR. KAE PARAGAS MAMBA<br>Medical Officer
    </div>
    <div class="sign-block">
        DR. RYAN R. PACQUING<br>CO-CHAIRPERSON, RSAC
    </div>
    <div class="sign-block">
        DENIS M. AGBAYANI<br>CHAIRPERSON, RSAC & GAMES RESULT
    </div>

    <div class="sign-block">
        JOSELITO L. NARAG<br>Tournament Director
    </div>
    <div class="sign-block">
        FLORANTE E. VERGARA<br>ASSISTANT REGIONAL DIRECTOR
    </div>
    <div class="sign-block">
        BENJAMIN D. PARAGAS<br>REGIONAL DIRECTOR
    </div>
</div>

</body>
</html>
