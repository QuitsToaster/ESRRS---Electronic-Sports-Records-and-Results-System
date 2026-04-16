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
            <th>Event</th>
            <th>Medal</th>
            <th>SDO</th>
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

    <style>
        .sign-block {
            display: inline-block;
            width: 24%;
            text-align: center;
            vertical-align: top;
            margin-bottom: 25px;
        }

        .sign-space {
            height: 40px; /* 🔥 space for signature */
        }

        .sign-name {
            font-weight: bold;
            margin-top: 5px;
        }

        .sign-role {
            font-size: 10px;
        }
    </style>

    {{-- ================= LEADERS ================= --}}
    <div class="sign-block">
        <div class="sign-space"></div>
        <div class="sign-name">DENIS M. AGBAYANI</div>
        <div class="sign-role">Chairperson</div>
    </div>

    <div class="sign-block">
        <div class="sign-space"></div>
        <div class="sign-name">DR. KAE PARAGAS MAMBA</div>
        <div class="sign-role">Medical Officer</div>
    </div>

    <div class="sign-block">
        <div class="sign-space"></div>
        <div class="sign-name">DR. JANNIFER TULIAO</div>
        <div class="sign-role">Dental Officer</div>
    </div>

    <div class="sign-block">
        <div class="sign-space"></div>
        <div class="sign-name">KEVIN VILLACETE</div>
        <div class="sign-role">Legal Officer</div>
    </div>

    {{-- ================= MEMBERS ================= --}}
    @foreach([
        ['KYM CLYDE MORO','Member'],
        ['BERNADETTE MIRANDA','Member'],
        ['FAITH NOEL REYES','Member'],
        ['ROLDAN LOPEZ','Member'],
        ['DANTE P. CAPUCHINO','Member'],
        ['JONATHAN PITA','Member'],
        ['REYMA CASARENO','Member'],
        ['JOEL DOMINGO','Member'],
        ['ATTY. JEREMY MARAMAG','Member'],
        ['EMERSON RESPONZO','Data Group'],
        ['ROSANDRO A. GADDI','Data Group'],
        ['ANGELICA RESPONZO','Data Group'],
        ['RONNIE BIBAS','Records'],
        ['ABRAHAM C. ACOSTA','Records'],
        ['NOR ALDEEN QADDOUMI','ICT / Games Results'],
        ['JAYWARD C. BUCAYU','ICT / Games Results'],
        ['RICARDO Q. CABUGAO','ICT / Games Results'],
        ['KRISTIAN E. SACCUAN','ICT / Games Results'],
        ['ANDREW TORIBIO B. TAACA','ICT / Games Results'],
        ['BRYAN LLOYD T. TAN','ICT / Games Results'],
        ['GANO SPENCER D. CHUMILANG','ICT / Games Results'],
        ['TIMOTHY JOHN M. LARDIZABAL','ICT / Games Results'],
        ['JAN ELLISSON G. SEBASTIAN','ICT / Games Results'],
    ] as $person)
        <div class="sign-block">
            <div class="sign-space"></div>
            <div class="sign-name">{{ $person[0] }}</div>
            <div class="sign-role">{{ $person[1] }}</div>
        </div>
    @endforeach

    {{-- ================= DIRECTORS ================= --}}
    <div class="sign-block" style="width: 48%;">
        <div class="sign-space" style="height:60px;"></div>
        <div class="sign-name">FLORANTE E. VERGARA</div>
        <div class="sign-role">ASSISTANT REGIONAL DIRECTOR</div>
    </div>

    <div class="sign-block" style="width: 48%;">
        <div class="sign-space" style="height:60px;"></div>
        <div class="sign-name">BENJAMIN D. PARAGAS</div>
        <div class="sign-role">REGIONAL DIRECTOR</div>
    </div>
</div>

</body>
</html>
