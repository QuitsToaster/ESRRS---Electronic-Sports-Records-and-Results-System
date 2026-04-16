<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Medal Awards - Official Results</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            background: #fff;
            color: #1e2a3e;
            padding: 0.6in 0.5in;
            margin: 0;
        }

        /* professional print & PDF layout */
        @media print {
            body {
                padding: 0.5in;
                margin: 0;
                background: white;
            }
            .page-break {
                page-break-before: always;
            }
            .signatures {
                page-break-inside: avoid;
            }
            table {
                page-break-inside: avoid;
            }
            h2 {
                page-break-after: avoid;
            }
        }

        /* main header */
        .report-header {
            text-align: center;
            margin-bottom: 28px;
            border-bottom: 2px solid #2c5f8a;
            padding-bottom: 12px;
        }
        .report-header h1 {
            font-size: 26px;
            letter-spacing: 1px;
            color: #1e4663;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .report-header .sub {
            font-size: 13px;
            color: #2c5f8a;
            font-weight: 500;
            text-transform: uppercase;
        }

        /* typography */
        h2 {
            font-size: 16pt;
            font-weight: 600;
            margin: 20px 0 12px 0;
            color: #1e4663;
            border-left: 5px solid #d4a017;
            padding-left: 12px;
            background: #f9fafc;
        }
        h3 {
            font-size: 13pt;
            font-weight: 600;
            margin: 16px 0 6px 0;
            color: #2c3e50;
        }

        /* tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 24px 0;
            font-size: 10.5pt;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        th {
            background: #eef2f7;
            border: 1px solid #bdc4cc;
            padding: 8px 6px;
            font-weight: 700;
            text-align: center;
            color: #1e4663;
            font-size: 10.5pt;
        }
        td {
            border: 1px solid #cbd5e1;
            padding: 6px 6px;
            text-align: left;
            vertical-align: top;
        }
        td:first-child, th:first-child {
            padding-left: 8px;
        }
        .table-medal-summary td:nth-child(3),
        .table-medal-summary td:nth-child(4),
        .table-medal-summary td:nth-child(5),
        .table-medal-summary th:nth-child(3),
        .table-medal-summary th:nth-child(4),
        .table-medal-summary th:nth-child(5) {
            text-align: center;
        }
        .rank-col {
            text-align: center;
            font-weight: 600;
        }

        /* medal icons / styling */
        .medal-badge {
            font-weight: 600;
        }
        .gold {
            color: #b76e0e;
        }
        .silver {
            color: #5a6e7c;
        }
        .bronze {
            color: #b45116;
        }

        /* signature block professional */
        .signatures {
            margin-top: 45px;
            padding-top: 15px;
            border-top: 1px solid #b9c3ce;
        }
        .signatures h3 {
            font-size: 11pt;
            margin-bottom: 20px;
            font-weight: 500;
            border-left: none;
            padding-left: 0;
            background: none;
            color: #1f3b4c;
        }
        .sign-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 8px 12px;
            margin-top: 12px;
        }
        .sign-block {
            display: inline-block;
            width: 23%;
            min-width: 160px;
            text-align: center;
            vertical-align: top;
            margin-bottom: 28px;
            background: #ffffff;
            border: none;
        }
        .sign-space {
            height: 44px;
            border-bottom: 1px solid #8f9eb2;
            width: 85%;
            margin: 0 auto 5px auto;
        }
        .sign-name {
            font-weight: 700;
            font-size: 10pt;
            color: #1e2f41;
            margin-top: 6px;
        }
        .sign-role {
            font-size: 9pt;
            color: #4a5b6e;
            font-weight: 500;
        }
        .director-block {
            width: 48%;
            margin-top: 10px;
        }
        .director-block .sign-space {
            height: 55px;
            border-bottom: 1.5px solid #1e4663;
            width: 70%;
        }
        .director-block .sign-name {
            font-size: 11pt;
            color: #003057;
        }
        .director-block .sign-role {
            font-size: 9.5pt;
            letter-spacing: 0.5px;
        }

        /* footer note */
        .footer-note {
            font-size: 8pt;
            text-align: center;
            margin-top: 40px;
            color: #6c7a8e;
            border-top: 1px dotted #ccd7e4;
            padding-top: 10px;
        }

        /* cleaner event tables */
        .event-detail td {
            padding: 5px 8px;
        }
        .event-category {
            font-weight: 500;
        }

        .page-break {
            page-break-before: always;
        }

        .signatures {
            page-break-inside: avoid;
        }

        .sign-cell {
            width: 25%;
            text-align: center;
            padding: 28px 8px;
            vertical-align: top;
        }

        .sign-space {
            height: 45px;
            border-bottom: 1px solid #8f9eb2;
            width: 80%;
            margin: 0 auto 6px auto;
        }

        .director-line {
            height: 55px;
            border-bottom: 1.5px solid #1e4663;
            width: 70%;
        }

        .director-name {
            font-size: 11pt;
            font-weight: 700;
        }

        /* ================= REMOVE TABLE LINES FOR SIGNATURES ONLY ================= */
        .signatures table,
        .signatures tr,
        .signatures td {
            border: none !important;
        }

        .signatures table {
            border-collapse: collapse;
        }

        /* keep spacing clean */
        .signatures td {
            padding: 20px 5px;
        }
    </style>
</head>
<body>

<div class="report-header">
    <h1>OFFICIAL MEDAL TALLY</h1>

    {{-- ✅ NEW LABEL --}}
    <div style="
        font-size: 12px;
        font-weight: 700;
        color: #ffffff;
        background: {{ ($reportType ?? '') === 'PARAGAMES' ? '#7b1fa2' : '#1e4663' }};
        display: inline-block;
        padding: 4px 12px;
        border-radius: 4px;
        margin-bottom: 6px;
        letter-spacing: 1px;
    ">
        {{ ($reportType ?? '') === 'PARAGAMES' ? 'PARAGAMES' : 'REGULAR GAMES' }}
    </div>

    <div class="sub">{{ date('Y') }} CAVRAA · RESULTS & MEDAL TALLY</div>

    <div style="font-size: 9pt; margin-top: 8px;">
        DepEd · GAMES RESULTS & RSAC
    </div>
</div>

{{-- ================= MEDAL SUMMARY BY SDO (Division) ================= --}}
<h2>MEDAL SUMMARY BY SDO</h2>
<table class="table-medal-summary">
    <thead>
        <tr>
            <th style="width:8%">Rank</th>
            <th style="width:42%">SDO</th>
            <th style="width:15%">Gold</th>
            <th style="width:15%">Silver</th>
            <th style="width:20%">Bronze</th>
        </tr>
    </thead>
    <tbody>
@if(!empty($medalTally) && count($medalTally))
    @foreach($medalTally as $division)
    <tr>
        <td class="rank-col">{{ $division->rank }}</td>
        <td><strong>{{ $division->name }}</strong></td>
        <td style="text-align:center; font-weight:600;">{{ $division->gold }}</td>
        <td style="text-align:center; font-weight:600;">{{ $division->silver }}</td>
        <td style="text-align:center; font-weight:600;">{{ $division->bronze }}</td>
    </tr>
    @endforeach
@else
    <tr>
        <td colspan="5" style="text-align:center; color:#6b7280;">
            No medal data available
        </td>
    </tr>
@endif
</tbody>
</table>

{{-- ================= GROUPED RESULTS (ALL + INDIVIDUAL) ================= --}}

{{-- ⚠️ IMPORTANT:
     This Blade now expects $groupedData from the controller.
     NO database queries here anymore (prevents HTTP 500).
--}}

@foreach($groupedData as $key => $items)

@php
    [$division, $event, $sport, $category] = explode('||', $key);
@endphp

{{-- ================= DIVISION HEADER ================= --}}
<h2>{{ $division }} DIVISION</h2>

{{-- ================= SPORT + CATEGORY HEADER ================= --}}
<h3 style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">

    <span>{{ $sport }}</span>

    <span style="color:#9ca3af;">·</span>

    {{-- 🔥 HIGHLIGHTED CATEGORY --}}
    <span style="
        background: #1e4663;
        color: #ffffff;
        padding: 2px 10px;
        font-size: 10pt;
        font-weight: 700;
        border-radius: 4px;
        letter-spacing: 0.5px;
    ">
        {{ strtoupper($category) }}
    </span>

</h3>

{{-- ================= TABLE ================= --}}
<table>
    <thead>
        <tr>
            <th style="width:16%">Event</th>
            <th style="width:10%">Medal</th>
            <th style="width:16%">SDO</th>
            <th style="width:18%">Athlete</th>
            <th style="width:16%">School</th>
            <th style="width:12%">Municipality</th>
            <th style="width:12%">District</th>
        </tr>
    </thead>
    <tbody>

        @foreach($items as $row)
        <tr>
            <td>{{ $row['sport'] }}</td>

            <td style="text-align:center;">
                {{ $row['medal'] }}
            </td>

            <td>{{ $row['sdo'] }}</td>

            <td>
                {{ $row['athlete'] ?? '—' }}
            </td>

            <td>
                {{ $row['school'] ?? '—' }}
            </td>

            <td>
                {{ $row['municipality'] ?? '—' }}
            </td>

            <td>
                {{ $row['district'] ?? '—' }}
            </td>
        </tr>
        @endforeach

    </tbody>
</table>

@endforeach

{{-- ================= SIGNATURE PAGE (FIXED - DOMPDF SAFE) ================= --}}
<div style="page-break-before: always;"></div>

<div class="signatures" style="margin-top:0; border-top:none;">

    <h2 style="border-left:none; padding-left:0; background:none; margin-top:0;">
        CERTIFICATION & APPROVAL
    </h2>

    <div style="font-size:10pt; margin: 10px 0 20px 0;">
        <strong>Prepared by: GAMES RESULTS & RSAC COMMITTEE</strong> · Official Results Division
    </div>

    {{-- ================= SIGNATURE GRID (TABLE = STABLE) ================= --}}
    <table style="width:100%; border:none;">
        <tbody>

            {{-- LEADERS --}}
            <tr>
                <td class="sign-cell">
                    <div class="sign-space"></div>
                    <div class="sign-name">DENIS M. AGBAYANI</div>
                    <div class="sign-role">Chairperson</div>
                </td>
                <td class="sign-cell">
                    <div class="sign-space"></div>
                    <div class="sign-name">DR. KAE PARAGAS MAMBA</div>
                    <div class="sign-role">Medical Officer</div>
                </td>
                <td class="sign-cell">
                    <div class="sign-space"></div>
                    <div class="sign-name">DR. JANNIFER TULIAO</div>
                    <div class="sign-role">Dental Officer</div>
                </td>
                <td class="sign-cell">
                    <div class="sign-space"></div>
                    <div class="sign-name">KEVIN VILLACETE</div>
                    <div class="sign-role">Legal Officer</div>
                </td>
            </tr>

            {{-- MEMBERS LOOP --}}
            @php
                $people = [
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
                    ['NOR ALDEEN QADDOUMI','ICT / Games Results'],
                    ['JAYWARD C. BUCAYU','ICT / Games Results'],
                    ['RICARDO Q. CABUGAO','ICT / Games Results'],
                    ['KRISTIAN E. SACCUAN','ICT / Games Results'],
                    ['ANDREW TORIBIO B. TAACA','ICT / Games Results'],
                    ['BRYAN LLOYD T. TAN','ICT / Games Results'],
                    ['GANO SPENCER D. CHUMILANG','ICT / Games Results'],
                    ['TIMOTHY JOHN M. LARDIZABAL','ICT / Games Results'],
                    ['JAN ELLISSON G. SEBASTIAN','ICT / Games Results'],
                ];
            @endphp

            @foreach(array_chunk($people, 4) as $row)
            <tr>
                @foreach($row as $person)
                <td class="sign-cell">
                    <div class="sign-space"></div>
                    <div class="sign-name">{{ $person[0] }}</div>
                    <div class="sign-role">{{ $person[1] }}</div>
                </td>
                @endforeach
            </tr>
            @endforeach

        </tbody>
    </table>

    {{-- ================= DIRECTORS ================= --}}
    <table style="width:100%; margin-top:30px; border:none;">
        <tr>
            <td style="width:50%; text-align:center;">
                <div class="sign-space director-line"></div>
                <div class="sign-name director-name">FLORANTE E. VERGARA</div>
                <div class="sign-role">ASSISTANT REGIONAL DIRECTOR</div>
            </td>
            <td style="width:50%; text-align:center;">
                <div class="sign-space director-line"></div>
                <div class="sign-name director-name">BENJAMIN D. PARAGAS</div>
                <div class="sign-role">REGIONAL DIRECTOR</div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        This document is electronically generated and validated by the Games Results & RSAC. 
        All medal counts are official as of final results.
    </div>

</div>

</body>
</html>

{{-- ===================== DYNAMIC DATA SIMULATION ===================== --}}
@php
    /*
    |--------------------------------------------------------------------------
    | PROFESSIONAL PDF GENERATION – DATA CONTEXT FOR BLADE
    | This section provides realistic sample data for the medal tally, levels,
    | and detailed results to emulate a complete official report.
    | Replace with actual DB/controller data when integrated.
    |--------------------------------------------------------------------------
    */
    if(!isset($medalTally)) {
        // Mock medal tally objects (Division standings)
        $medalTally = collect([
            (object) ['rank' => 1, 'name' => 'SDO Quezon City', 'gold' => 24, 'silver' => 18, 'bronze' => 12],
            (object) ['rank' => 2, 'name' => 'SDO Manila', 'gold' => 19, 'silver' => 22, 'bronze' => 15],
            (object) ['rank' => 3, 'name' => 'SDO Caloocan', 'gold' => 16, 'silver' => 14, 'bronze' => 19],
            (object) ['rank' => 4, 'name' => 'SDO Pasig', 'gold' => 14, 'silver' => 12, 'bronze' => 10],
            (object) ['rank' => 5, 'name' => 'SDO Taguig-Pateros', 'gold' => 12, 'silver' => 9, 'bronze' => 14],
            (object) ['rank' => 6, 'name' => 'SDO Mandaluyong', 'gold' => 8, 'silver' => 11, 'bronze' => 7],
            (object) ['rank' => 7, 'name' => 'SDO Marikina', 'gold' => 6, 'silver' => 10, 'bronze' => 9],
            (object) ['rank' => 8, 'name' => 'SDO Muntinlupa', 'gold' => 5, 'silver' => 4, 'bronze' => 8],
        ]);
    }

    if(!isset($levels)) {
        $levels = ['Elementary', 'Junior High School', 'Senior High School'];
    }

    if(!isset($pdfResults)) {
        $pdfResults = [
            // Elementary division
            ['division' => 'Elementary', 'event' => 'Athletics 100m', 'category' => 'Boys', 'sport' => 'Track & Field', 'medal' => 'Gold', 'sdo' => 'SDO Quezon City', 'date' => '2025-02-10'],
            ['division' => 'Elementary', 'event' => 'Athletics 100m', 'category' => 'Girls', 'sport' => 'Track & Field', 'medal' => 'Silver', 'sdo' => 'SDO Manila', 'date' => '2025-02-10'],
            ['division' => 'Elementary', 'event' => 'Swimming 50m Freestyle', 'category' => 'Boys', 'sport' => 'Aquatics', 'medal' => 'Gold', 'sdo' => 'SDO Pasig', 'date' => '2025-02-11'],
            ['division' => 'Elementary', 'event' => 'Swimming 50m Freestyle', 'category' => 'Girls', 'sport' => 'Aquatics', 'medal' => 'Bronze', 'sdo' => 'SDO Caloocan', 'date' => '2025-02-11'],
            ['division' => 'Elementary', 'event' => 'Badminton Singles', 'category' => 'Mixed', 'sport' => 'Badminton', 'medal' => 'Gold', 'sdo' => 'SDO Quezon City', 'date' => '2025-02-12'],
            ['division' => 'Elementary', 'event' => 'Chess Team Standard', 'category' => 'Open', 'sport' => 'Chess', 'medal' => 'Silver', 'sdo' => 'SDO Taguig-Pateros', 'date' => '2025-02-09'],
            // JHS division
            ['division' => 'Junior High School', 'event' => 'Basketball 3x3', 'category' => 'Boys', 'sport' => 'Basketball', 'medal' => 'Gold', 'sdo' => 'SDO Manila', 'date' => '2025-02-13'],
            ['division' => 'Junior High School', 'event' => 'Basketball 3x3', 'category' => 'Girls', 'sport' => 'Basketball', 'medal' => 'Silver', 'sdo' => 'SDO Caloocan', 'date' => '2025-02-13'],
            ['division' => 'Junior High School', 'event' => 'Volleyball Indoor', 'category' => 'Boys', 'sport' => 'Volleyball', 'medal' => 'Gold', 'sdo' => 'SDO Quezon City', 'date' => '2025-02-14'],
            ['division' => 'Junior High School', 'event' => 'Volleyball Indoor', 'category' => 'Girls', 'sport' => 'Volleyball', 'medal' => 'Bronze', 'sdo' => 'SDO Mandaluyong', 'date' => '2025-02-14'],
            ['division' => 'Junior High School', 'event' => 'Table Tennis Doubles', 'category' => 'Mixed', 'sport' => 'Table Tennis', 'medal' => 'Gold', 'sdo' => 'SDO Pasig', 'date' => '2025-02-12'],
            ['division' => 'Junior High School', 'event' => 'Football 7-a-side', 'category' => 'Boys', 'sport' => 'Football', 'medal' => 'Silver', 'sdo' => 'SDO Marikina', 'date' => '2025-02-15'],
            // Senior High School division
            ['division' => 'Senior High School', 'event' => 'Baseball', 'category' => 'Men', 'sport' => 'Baseball', 'medal' => 'Gold', 'sdo' => 'SDO Quezon City', 'date' => '2025-02-16'],
            ['division' => 'Senior High School', 'event' => 'Softball', 'category' => 'Women', 'sport' => 'Softball', 'medal' => 'Gold', 'sdo' => 'SDO Manila', 'date' => '2025-02-16'],
            ['division' => 'Senior High School', 'event' => 'Arnis Anyo', 'category' => 'Individual', 'sport' => 'Arnis', 'medal' => 'Silver', 'sdo' => 'SDO Caloocan', 'date' => '2025-02-17'],
            ['division' => 'Senior High School', 'event' => 'Taekwondo Poomsae', 'category' => 'Female', 'sport' => 'Taekwondo', 'medal' => 'Bronze', 'sdo' => 'SDO Taguig-Pateros', 'date' => '2025-02-17'],
            ['division' => 'Senior High School', 'event' => 'Chess Rapid', 'category' => 'Boys', 'sport' => 'Chess', 'medal' => 'Gold', 'sdo' => 'SDO Pasig', 'date' => '2025-02-18'],
            ['division' => 'Senior High School', 'event' => 'Dance Sport Latin', 'category' => 'Mixed', 'sport' => 'DanceSport', 'medal' => 'Silver', 'sdo' => 'SDO Muntinlupa', 'date' => '2025-02-18'],
        ];
    }
@endphp

{{--
    IMPORTANT NOTES:
    - The HTML/CSS is fully styled for professional PDF conversion (wkhtmltopdf, browser print, or DomPDF).
    - The blade placeholders @foreach and @if work dynamically. Embedded sample data above ensures beautiful output.
    - No inline script required; pure layout perfect for official reports.
    - Signature blocks provide clean authority lines with adequate spacing for wet signatures or digital stamps.
    - Responsive tables and consistent typography for executive summary.
--}}