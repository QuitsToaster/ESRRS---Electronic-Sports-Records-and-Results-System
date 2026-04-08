<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>User Access Profile | Clean Export</title>

    <style>
@page {
    size: letter landscape;
    margin: 0.35in 0.35in;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 8.5pt;
    color: #1a2c3e;
    line-height: 1.35;
}

/* HEADER */
.report-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding-bottom: 6px;
    border-bottom: 1px solid #cbd5e1;
}

.title-section h1 {
    font-size: 18pt;
    font-weight: 700;
}

.title-section .sub {
    font-size: 7pt;
    color: #64748b;
}

.date-meta {
    font-size: 7pt;
    text-align: right;
}

/* ROLE */
.role-group {
    margin-bottom: 12px;
}

.role-header {
    margin-bottom: 6px;
    padding-left: 6px;
    border-left: 3px solid #64748b;
}

.role-name {
    font-size: 11pt;
    font-weight: 700;
}

.role-count {
    font-size: 7pt;
    color: #64748b;
}

/* GRID */
.users-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
}

/* CARD */
.user-card {
    border: 1px solid #cbd5e1;
}

/* HEADER */
.card-header {
    padding: 6px 8px;
}

.user-name {
    font-size: 11pt;
    font-weight: 700;
    color: #0f172a;
}

.user-role-tag {
    font-size: 6.5pt;
    color: #64748b;
}

.user-email {
    font-size: 6.5pt;
    font-family: monospace;
    color: #475569;
}

/* BODY */
.card-body {
    padding: 6px 8px;
}

/* MODERN INFO BLOCK */
.info-box {
    border: 1px solid #e2e8f0;
    padding: 4px 6px;
}

.info-title {
    font-size: 6.5pt;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 2px;
}

.info-content {
    font-size: 8pt;
    color: #0f172a;
}

/* CHIPS */
.chip {
    display: inline-block;
    padding: 1px 6px;
    margin: 1px 2px 1px 0;
    font-size: 6.5pt;
    border: 1px solid #cbd5e1;
}

/* SPORTS */
.group-box {
    border: 1px solid #cbd5e1;
    margin-bottom: 6px;
}

.group-header {
    background: #f1f5f9;
    padding: 3px 5px;
    font-size: 7pt;
    font-weight: 700;
    border-bottom: 1px solid #cbd5e1;
}

.group-table td {
    padding: 3px 5px;
    font-size: 7pt;
    border-bottom: 1px solid #e2e8f0;
}

.empty-value {
    color: #94a3b8;
}

/* FOOTER */
.report-footer {
    margin-top: 10px;
    padding-top: 6px;
    font-size: 6.5pt;
    text-align: center;
    border-top: 1px solid #e2e8f0;
}
</style>
</head>
<body>

<div class="report-container">
    <!-- header: clean, minimal -->
    <div class="report-header">
        <div class="title-section">
            <h1>Access summary</h1>
            <div class="sub">User privileges & sport assignments</div>
        </div>
        <div class="date-meta">
            <div class="date">{{ date('F j, Y') }}</div>
            <div style="font-size: 7pt;">eSRRS generated • confidential</div>
        </div>
    </div>

    @foreach($groupedUsers as $role => $users)
        @php
            $displayRole = $role === 'Tournament Manager' ? 'Tournament Director' : $role;
        @endphp
        <div class="role-group">
            <div class="role-header">
                <div class="role-name">{{ $displayRole }}</div>
                <div class="role-count">{{ count($users) }} user{{ count($users) !== 1 ? 's' : '' }}</div>
            </div>

            <div class="users-grid">
                @foreach($users as $u)
                    @php
                        $levels = $u->level_access ?? [];
                        $categories = $u->category_access ?? [];

                        // sports grouped by label (division+category)
                        $sportsGrouped = collect($u->sports_access ?? [])
                            ->map(function($id) use ($sports) {
                                $sport = $sports->firstWhere('id', $id);
                                if (!$sport) return null;
                                $level = $sport->division ?? '';
                                $category = $sport->category ?? '';
                                $label = trim($level . ' ' . $category);
                                if (empty($label)) $label = 'General';
                                return ['label' => $label, 'name' => $sport->name];
                            })
                            ->filter()
                            ->groupBy('label');

                        // sport types grouped by label
                        $typesGrouped = collect($u->sport_type_access ?? [])
                            ->map(function($id) use ($sportTypes, $sports) {
                                $type = $sportTypes->firstWhere('id', $id);
                                if (!$type) return null;
                                $sport = $sports->firstWhere('id', $type->sport_id);
                                $level = $sport->division ?? '';
                                $category = $sport->category ?? '';
                                $label = trim($level . ' ' . $category);
                                if (empty($label)) $label = 'General';
                                return ['label' => $label, 'name' => $type->type_name];
                            })
                            ->filter()
                            ->groupBy('label');
                    @endphp

                    <div class="user-card">
                        <div class="card-header">
                            <div class="user-name">{{ $u->name }}</div>
                            <div class="user-role-tag">{{ $displayRole }}</div>
                            <div class="user-email">{{ $u->email }}</div>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-body">
                           {{-- ================= COMPACT MODERN INFO ================= --}}
<div style="display:flex; gap:4px; margin-bottom:6px;">

    {{-- CONTACT --}}
    <div class="info-box" style="flex:1;">
        <div class="info-title">CONTACT</div>
        <div class="info-content">
            @if($u->contact_number)
                <span style="font-family:monospace;">{{ $u->contact_number }}</span>
            @else
                <span class="empty-value">—</span>
            @endif
        </div>
    </div>

    {{-- LEVEL --}}
    <div class="info-box" style="flex:1;">
        <div class="info-title">LEVEL</div>
        <div>
            @if(count($levels))
                @foreach($levels as $lvl)
                    <span class="chip">{{ $lvl }}</span>
                @endforeach
            @else
                <span class="empty-value">—</span>
            @endif
        </div>
    </div>

    {{-- CATEGORY --}}
    <div class="info-box" style="flex:1;">
        <div class="info-title">CATEGORY</div>
        <div>
            @if(count($categories))
                @foreach($categories as $cat)
                    <span class="chip">{{ $cat }}</span>
                @endforeach
            @else
                <span class="empty-value">—</span>
            @endif
        </div>
    </div>

</div>

                           {{-- ================= SPORTS & TYPES (FORMAL TABLE WITH BORDERS) ================= --}}
<div class="info-row">
    <div class="info-label">Sports</div>

    <div class="info-value">

        @php
            $groupedData = [];

            foreach ($u->sports_access ?? [] as $sportId) {
                $sport = $sports->firstWhere('id', $sportId);
                if (!$sport) continue;

                $label = trim(($sport->division ?? '') . ' ' . ($sport->category ?? ''));
                if (!$label) $label = 'General';

                if (!isset($groupedData[$label])) {
                    $groupedData[$label] = [];
                }

                $groupedData[$label][$sport->id] = [
                    'sport_name' => $sport->name,
                    'types' => []
                ];
            }

            foreach ($u->sport_type_access ?? [] as $typeId) {
                $type = $sportTypes->firstWhere('id', $typeId);
                if (!$type) continue;

                $sport = $sports->firstWhere('id', $type->sport_id);
                if (!$sport) continue;

                $label = trim(($sport->division ?? '') . ' ' . ($sport->category ?? ''));
                if (!$label) $label = 'General';

                if (!isset($groupedData[$label][$sport->id])) {
                    $groupedData[$label][$sport->id] = [
                        'sport_name' => $sport->name,
                        'types' => []
                    ];
                }

                $groupedData[$label][$sport->id]['types'][] = $type->type_name;
            }
        @endphp

        @if(count($groupedData))

            @foreach($groupedData as $label => $sportsGroup)

                {{-- GROUP CONTAINER WITH BORDER --}}
                <div style="margin-bottom:10px; border:1px solid #cbd5e1;">

                    {{-- GROUP HEADER --}}
                    <div style="
                        background:#f1f5f9;
                        padding:4px 6px;
                        font-weight:600;
                        font-size:8pt;
                        color:#1e4a6e;
                        border-bottom:1px solid #cbd5e1;
                    ">
                        {{ $label }}
                    </div>

                    {{-- TABLE --}}
                    <table style="
                        width:100%;
                        border-collapse:collapse;
                        font-size:7.5pt;
                    ">
                        <tbody>

                            @foreach($sportsGroup as $sportData)

                                <tr>

                                    {{-- SPORT NAME --}}
                                    <td style="
                                        width:40%;
                                        padding:4px 6px;
                                        font-weight:600;
                                        color:#2a577b;
                                        border-right:1px solid #e2e8f0;
                                        border-bottom:1px solid #e2e8f0;
                                    ">
                                        {{ $sportData['sport_name'] }}
                                    </td>

                                    {{-- TYPES --}}
                                    <td style="
                                        padding:4px 6px;
                                        color:#1f3f57;
                                        border-bottom:1px solid #e2e8f0;
                                    ">
                                        @if(count($sportData['types']))
                                            @foreach($sportData['types'] as $type)
                                                <div style="margin-bottom:2px;">• {{ $type }}</div>
                                            @endforeach
                                        @else
                                            <span class="empty-value">—</span>
                                        @endif
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>
                    </table>

                </div>

            @endforeach

        @else
            <span class="empty-value">—</span>
        @endif

    </div>
</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <div class="report-footer">
        System generated report · Access rights overview
    </div>
</div>

</body>
</html>