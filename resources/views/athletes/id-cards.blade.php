<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Athlete ID Cards</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        @page {
            size: letter portrait;
            margin: 10mm;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 10px;
            text-align: center;
        }

        /* ✅ 3.5 x 5 inches */
        .card {
            width: 3.5in;
            height: 5in;
            position: relative;
            background-size: cover;
            background-position: center;
        }

        /* FRONT TEXT */
        .name {
            position: absolute;
            top: 2.9in;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            color: #02367B;
        }

        .school {
            position: absolute;
            top: 3.35in;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
            font-weight: 500;
            color: #02367B;
        }

        .division {
            position: absolute;
            top: 3.55in;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
            font-weight: 500;
            color: #02367B;
        }

        .sport {
            position: absolute;
            top: 3.80in;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: white;
        }

        .level {
            position: absolute;
            top: 4.35in;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            color: #02367B;
        }

        /* PHOTO */
        .photo {
            position: absolute;
            top: 1.0in;
            left: 50%;
            transform: translateX(-50%);
            width: 1.3in;
            height: 1.6in;
            object-fit: cover;
            border-radius: 6px;
        }

        /* BACK QR */
        .qr-back {
            position: absolute;
            top: 0.7in;
            left: 50%;
            transform: translateX(-50%);
        }

        .qr-back img {
            width: 1.3in;
            height: 1.3in;
        }

        .qr-number {
            margin-top: 8px;
            font-size: 10px;
            font-weight: 600;
            text-align: center;
            letter-spacing: 1px;
            color: #02367B;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

@foreach($athletes as $athlete)

<table>
<tr>

    {{-- FRONT --}}
    <td>
        <div class="card"
             style="background-image: url('{{ public_path('storage/id_templates/id-template-front.png') }}');">

            {{-- PHOTO --}}
            @if($athlete->profile_photo)
                <img class="photo"
                     src="{{ public_path('storage/'.$athlete->profile_photo) }}">
            @endif

            {{-- NAME --}}
            <div class="name">
                {{ $athlete->last_name }}, {{ $athlete->first_name }}
            </div>

            {{-- SCHOOL --}}
            <div class="school">
                {{ $athlete->school->name ?? '' }}
            </div>

            {{-- DIVISION --}}
            <div class="division">
                SDO - {{ $athlete->school_division ?? '' }}
            </div>

            {{-- SPORT/EVENT --}}
            <div class="sport">
                {{ $athlete->event ?? '' }}
            </div>

            {{-- LEVEL/CATEGORY --}}
            <div class="level">
                {{ $athlete->level ?? '' }} {{ $athlete->category ?? '' }}
            </div>

        </div>
    </td>

    {{-- BACK --}}
<td>
    <div class="card"
         style="background-image: url('{{ public_path('storage/id_templates/id-template-back.png') }}');">

        <div class="qr-back">

            {{-- Bigger QR --}}
            <img src="data:image/png;base64,{{ base64_encode(
                QrCode::format('png')->size(300)->generate($athlete->qr_code)
            ) }}">

            {{-- QR Code Text Below --}}
            <div class="qr-number">
                {{ $athlete->qr_code }}
            </div>

        </div>

    </div>
</td>

</tr>
</table>

<div class="page-break"></div>

@endforeach

</body>
</html>