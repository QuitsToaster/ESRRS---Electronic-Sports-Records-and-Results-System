<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Athlete ID Cards</title>

    <style>
        @page {
            size: letter portrait;
            margin: 8mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* PAGE CONTAINER = EXACT HEIGHT FIT FOR 2 CARDS */
        .page {
            height: 10.2in;
        }

        table {
    width: auto; /* prevents stretching */
    border-collapse: collapse;
    border-spacing: 0; /* removes hidden spacing */
    margin: 0 auto; /* keeps centered */
}

        td {
            padding: 0; /* remove gap */
            margin: 0;
            text-align: center;
            vertical-align: top;
        }

        /* CARD SIZE */
        .card {
            width: 3.5in;
            height: 5in;
            position: relative;
            background-size: cover;
            background-position: center;
            display: block;
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
            top: 3.70in;
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
            text-align: center;
        }

        .qr-back img {
            width: 1.3in;
            height: 1.3in;
        }

        .qr-number {
            margin-top: 8px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1px;
            color: #02367B;
        }

        .row-spacing {
            height: 0.2in;
        }
    </style>
</head>
<body>

@foreach($athletes->chunk(2) as $pair)

<div class="page" @if(!$loop->last) style="page-break-after: always;" @endif>

    @foreach($pair as $athlete)

        <table>
            <tr>

                {{-- FRONT --}}
                <td>
                    <div class="card"
                        style="background-image: url('{{ public_path('storage/id_templates/id-template-front.png') }}');">

                        @if($athlete->profile_photo)
                            <img class="photo"
                                src="{{ public_path('storage/'.$athlete->profile_photo) }}">
                        @endif

                        <div class="name">
                            {{ $athlete->last_name }}, {{ $athlete->first_name }}
                        </div>

                        <div class="school">
                            {{ $athlete->school->name ?? '' }}
                        </div>

                        <div class="division">
                            SDO - {{ $athlete->school_division ?? '' }}
                        </div>

                        <div class="sport">
                            {{ $athlete->event ?? '' }}
                        </div>

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

                            <img src="data:image/png;base64,{{ base64_encode(
                                QrCode::format('png')->size(300)->generate($athlete->qr_code)
                            ) }}">

                            <div class="qr-number">
                                {{ $athlete->qr_code }}
                            </div>

                        </div>

                    </div>
                </td>

            </tr>
        </table>

        <div class="row-spacing"></div>

    @endforeach

</div>

@endforeach

</body>
</html>