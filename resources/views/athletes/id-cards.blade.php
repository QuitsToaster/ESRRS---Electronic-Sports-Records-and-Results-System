<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Athlete ID Cards</title>

    <style>
        @page {
            size: letter portrait; /* SHORT BOND */
            margin: 15mm;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #111;
        }

        /* ================= PAGE ================= */
        .page {
            page-break-after: always;
        }

        .row {
            display: flex;
            justify-content: center;
            gap: 16mm;
            margin-bottom: 18mm;
        }

        /* ================= CARD ================= */
        .card {
            width: 67mm;
            height: 99mm;
            border: 1.2px solid #1f2937;
            border-radius: 10px;
            padding: 8px;
            box-sizing: border-box;
            position: relative;
        }

        /* ================= FRONT ================= */
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

        .photo {
            margin-bottom: 6px;
        }

        .photo img {
            width: 28mm;
            height: 34mm;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #1f2937;
        }

        .no-photo {
            width: 28mm;
            height: 34mm;
            border: 1px dashed #9ca3af;
            line-height: 34mm;
            font-size: 8px;
            color: #6b7280;
            border-radius: 6px;
            margin: 0 auto;
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

        /* ================= QR ================= */
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
            display: block;
            margin: 0 auto 3px;
        }

        .qr-text {
            font-size: 7px;
            word-break: break-all;
        }

        /* ================= BACK ================= */
        .back {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            padding: 4mm;
        }

        .back h4 {
            font-size: 11px;
            margin-bottom: 6px;
        }

        .rules {
            font-size: 8px;
            line-height: 1.5;
            margin-bottom: 10px;
        }

        .signature {
            border-top: 1px solid #000;
            font-size: 8px;
            padding-top: 4px;
            margin-bottom: 6px;
        }

        .footer {
            font-size: 7px;
            color: #6b7280;
        }
    </style>
</head>
<body>

@php
    // 2 athletes per page
    $pairs = $athletes->chunk(2);
@endphp

@foreach($pairs as $pair)
<div class="page">

    {{-- ================= TOP ROW (ATHLETE 1) ================= --}}
    @if(isset($pair[0]))
    <div class="row">

        {{-- FRONT --}}
        <div class="card">
            <div class="header">ATHLETE ID</div>

            <div class="content">
                <div class="photo">
                    @if($pair[0]->profile_photo)
                        <img src="{{ public_path('storage/'.$pair[0]->profile_photo) }}">
                    @else
                        <div class="no-photo">NO PHOTO</div>
                    @endif
                </div>

                <div class="name">
                    {{ $pair[0]->last_name }}, {{ $pair[0]->first_name }}
                </div>

                <div class="detail">Sport: {{ $pair[0]->sport->name ?? '—' }}</div>
                <div class="detail">School: {{ $pair[0]->school->name ?? '—' }}</div>
                <div class="detail">Category: {{ $pair[0]->category ?? '—' }}</div>
                <div class="detail">Division: {{ $pair[0]->school_division ?? '—' }}</div>
            </div>

            <div class="qr">
                <img src="data:image/png;base64,{{ base64_encode(
                    QrCode::format('png')->size(140)->generate($pair[0]->qr_code)
                ) }}">
                <div class="qr-text">{{ $pair[0]->qr_code }}</div>
            </div>
        </div>

        {{-- BACK --}}
        <div class="card">
            <div class="back">
                <h4>IMPORTANT NOTICE</h4>
                <div class="rules">
                    • This ID is non-transferable<br>
                    • Present this ID during events<br>
                    • QR code is required for attendance<br>
                    • Lost ID must be reported immediately
                </div>
                <div class="signature">Authorized Signature</div>
                <div class="footer">Official Athlete Identification</div>
            </div>
        </div>

    </div>
    @endif

    {{-- ================= BOTTOM ROW (ATHLETE 2) ================= --}}
    @if(isset($pair[1]))
    <div class="row">

        {{-- FRONT --}}
        <div class="card">
            <div class="header">ATHLETE ID</div>

            <div class="content">
                <div class="photo">
                    @if($pair[1]->profile_photo)
                        <img src="{{ public_path('storage/'.$pair[1]->profile_photo) }}">
                    @else
                        <div class="no-photo">NO PHOTO</div>
                    @endif
                </div>

                <div class="name">
                    {{ $pair[1]->last_name }}, {{ $pair[1]->first_name }}
                </div>

                <div class="detail">{{ $pair[1]->sport->name ?? '—' }}</div>
                <div class="detail">{{ $pair[1]->category ?? '—' }}</div>
                <div class="detail">{{ $pair[1]->school_division ?? '—' }}</div>
            </div>

            <div class="qr">
                <img src="data:image/png;base64,{{ base64_encode(
                    QrCode::format('png')->size(140)->generate($pair[1]->qr_code)
                ) }}">
                <div class="qr-text">{{ $pair[1]->qr_code }}</div>
            </div>
        </div>

        {{-- BACK --}}
        <div class="card">
            <div class="back">
                <h4>IMPORTANT NOTICE</h4>
                <div class="rules">
                    • This ID is non-transferable<br>
                    • Present this ID during events<br>
                    • QR code is required for attendance<br>
                    • Lost ID must be reported immediately
                </div>
                <div class="signature">Authorized Signature</div>
                <div class="footer">Official Athlete Identification</div>
            </div>
        </div>

    </div>
    @endif

</div>
@endforeach

</body>
</html>
