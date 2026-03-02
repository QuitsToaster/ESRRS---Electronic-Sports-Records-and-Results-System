<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Athlete ID Cards</title>

    <style>
        @page {
            size: letter portrait;
            margin: 15mm;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 0;
        }

        .card {
            width: 67mm;
            height: 99mm;
            border: 1.2px solid #1f2937;
            border-radius: 10px;
            padding: 8px;
            box-sizing: border-box;
            position: relative;
        }

        .header {
            background: #1f2937;
            color: #fff;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            padding: 4px;
            border-radius: 6px;
            margin-bottom: 6px;
        }

        .content {
            text-align: center;
        }

        .photo img {
            width: 28mm;
            height: 34mm;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #1f2937;
            margin-bottom: 6px;
        }

        .no-photo {
            width: 28mm;
            height: 34mm;
            border: 1px dashed #9ca3af;
            line-height: 34mm;
            font-size: 8px;
            color: #6b7280;
            border-radius: 6px;
            margin: 0 auto 6px;
        }

        .name {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .detail {
            font-size: 9px;
            margin-bottom: 2px;
        }

        .qr {
            position: absolute;
            bottom: 6px;
            left: 0;
            right: 0;
            text-align: center;
            border-top: 1px dashed #9ca3af;
            padding-top: 5px;
        }

        .qr img {
            width: 22mm;
            height: 22mm;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

@foreach($athletes->chunk(2) as $chunk)

    @php
        // 🔒 RE-INDEX SAFELY
        $pair = $chunk->values();
    @endphp

    @if($pair->isNotEmpty())
    <table>
        <tr>
            {{-- ATHLETE 1 FRONT --}}
            <td>
                <div class="card">
                    <div class="header">ATHLETE ID</div>

                    <div class="content">
                        @if($pair->get(0)->profile_photo ?? false)
                            <img class="photo" src="{{ public_path('storage/'.$pair->get(0)->profile_photo) }}">
                        @else
                            <div class="no-photo">NO PHOTO</div>
                        @endif

                        <div class="name">
                            {{ $pair->get(0)->last_name }}, {{ $pair->get(0)->first_name }}
                        </div>

                        <div class="detail">{{ $pair->get(0)->sport->name ?? '—' }}</div>
                        <div class="detail">{{ $pair->get(0)->school->name ?? '—' }}</div>
                        <div class="detail">SDO - {{ $pair->get(0)->school_division ?? '—' }}</div>
                    </div>

                    <div class="qr">
                        <img src="data:image/png;base64,{{ base64_encode(
                            QrCode::format('png')->size(140)->generate($pair->get(0)->qr_code)
                        ) }}">
                    </div>
                </div>
            </td>

            {{-- ATHLETE 1 BACK --}}
            <td>
                <div class="card">
                    <div class="content">
                        <strong>IMPORTANT NOTICE</strong><br><br>
                        • This ID is non-transferable<br>
                        • Present during events<br>
                        • QR required for attendance
                    </div>
                </div>
            </td>
        </tr>

        {{-- ATHLETE 2 (IF EXISTS) --}}
        @if($pair->count() > 1)
        <tr>
            <td>
                <div class="card">
                    <div class="header">ATHLETE ID</div>

                    <div class="content">
                        @if($pair->get(1)->profile_photo ?? false)
                            <img class="photo" src="{{ public_path('storage/'.$pair->get(1)->profile_photo) }}">
                        @else
                            <div class="no-photo">NO PHOTO</div>
                        @endif

                        <div class="name">
                            {{ $pair->get(1)->last_name }}, {{ $pair->get(1)->first_name }}
                        </div>

                        <div class="detail">{{ $pair->get(1)->sport->name ?? '—' }}</div>
                        <div class="detail">SDO - {{ $pair->get(1)->school_division ?? '—' }}</div>
                    </div>

                    <div class="qr">
                        <img src="data:image/png;base64,{{ base64_encode(
                            QrCode::format('png')->size(140)->generate($pair->get(1)->qr_code)
                        ) }}">
                    </div>
                </div>
            </td>

            <td>
                <div class="card">
                    <div class="content">
                        <strong>IMPORTANT NOTICE</strong><br><br>
                        • This ID is non-transferable<br>
                        • Present during events<br>
                        • QR required for attendance
                    </div>
                </div>
            </td>
        </tr>
        @endif
    </table>

    <div class="page-break"></div>
    @endif

@endforeach

</body>
</html>