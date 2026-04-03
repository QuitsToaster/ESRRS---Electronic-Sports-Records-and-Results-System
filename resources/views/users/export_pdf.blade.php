<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Users Report</title>

    <style>
        @page {
            size: letter landscape;
            margin: 0.5in;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8.5pt;
            color: #1e293b;
        }

        h1 {
            font-size: 16pt;
            margin-bottom: 4px;
        }

        .subtitle {
            font-size: 8pt;
            color: #64748b;
            margin-bottom: 10px;
        }

        .role-title {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 6px;
            padding: 6px;
            background: #f1f5f9;
            border-left: 4px solid #3b82f6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 15px;
        }

        th {
            background: #f8fafc;
            font-size: 7pt;
            padding: 6px;
            border: 1px solid #e2e8f0;
            text-align: left;
        }

        td {
            font-size: 7.5pt;
            padding: 6px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
            word-wrap: break-word;
            word-break: break-word;
        }

        /* Balanced column widths */
        th:nth-child(1) { width: 14%; }
        th:nth-child(2) { width: 18%; }
        th:nth-child(3) { width: 10%; }
        th:nth-child(4) { width: 10%; }
        th:nth-child(5) { width: 10%; }
        th:nth-child(6) { width: 19%; }
        th:nth-child(7) { width: 19%; }

        .chip {
            display: inline-block;
            background: #eef2ff;
            padding: 2px 6px;
            margin: 2px 2px 2px 0;
            border-radius: 10px;
            font-size: 6.5pt;
        }

        .empty {
            color: #94a3b8;
            font-style: italic;
        }

        .footer {
            margin-top: 20px;
            font-size: 7pt;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>

<body>

<h1>Users Report</h1>
<div class="subtitle">
    Generated on {{ date('F d, Y') }}
</div>

@foreach($groupedUsers as $role => $users)

    @php
        $displayRole = $role === 'Tournament Manager' ? 'Tournament Director' : $role;
    @endphp

    <div class="role-title">
        {{ $displayRole }} ({{ count($users) }} users)
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Level</th>
                <th>Category</th>
                <th>Sports</th>
                <th>Sport Types</th>
            </tr>
        </thead>

        <tbody>
            @foreach($users as $u)

                @php
                    $sportsNames = collect($u->sports_access ?? [])
                        ->map(fn($id) => optional($sports->firstWhere('id', $id))->name)
                        ->filter();

                    $sportTypeNames = collect($u->sport_type_access ?? [])
                        ->map(fn($id) => optional($sportTypes->firstWhere('id', $id))->type_name)
                        ->filter()
                        ->unique();

                    $levels = $u->level_access ?? [];
                    $categories = $u->category_access ?? [];
                @endphp

                <tr>
                    <td>{{ $u->name }}</td>

                    <td>{{ $u->email }}</td>

                    <td>{{ $u->contact_number ?: '—' }}</td>

                    {{-- LEVEL --}}
                    <td>
                        @if(count($levels))
                            @foreach($levels as $l)
                                <span class="chip">{{ $l }}</span>
                            @endforeach
                        @else
                            <span class="empty">—</span>
                        @endif
                    </td>

                    {{-- CATEGORY --}}
                    <td>
                        @if(count($categories))
                            @foreach($categories as $c)
                                <span class="chip">{{ $c }}</span>
                            @endforeach
                        @else
                            <span class="empty">—</span>
                        @endif
                    </td>

                    {{-- SPORTS --}}
                    <td>
                        @if($sportsNames->count())
                            @foreach($sportsNames as $s)
                                <span class="chip">{{ $s }}</span>
                            @endforeach
                        @else
                            <span class="empty">—</span>
                        @endif
                    </td>

                    {{-- SPORT TYPES --}}
                    <td>
                        @if($sportTypeNames->count())
                            @foreach($sportTypeNames as $t)
                                <span class="chip">{{ $t }}</span>
                            @endforeach
                        @else
                            <span class="empty">—</span>
                        @endif
                    </td>
                </tr>

            @endforeach
        </tbody>
    </table>

@endforeach

<div class="footer">
    System Generated Report • Confidential
</div>

</body>
</html>