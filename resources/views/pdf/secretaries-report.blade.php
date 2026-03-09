<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>{{ $title }}</title>

<style>

    @page {
        size: letter portrait;
        margin: 25px;
    }

    body{
        font-family: DejaVu Sans, sans-serif;
        font-size: 11px;
        color:#222;
    }

    .header{
        width:100%;
        margin-bottom:20px;
    }

    .logo{
        width:70px;
    }

    .center{
        text-align:center;
    }

    .report-title{
        font-size:18px;
        font-weight:bold;
        margin-top:5px;
    }

    .subtitle{
        font-size:12px;
        color:#555;
    }

    .generated{
        font-size:10px;
        text-align:right;
        margin-bottom:10px;
    }

    table{
        width:100%;
        border-collapse:collapse;
        margin-top:10px;
    }

    th{
        background:#f3f4f6;
        border:1px solid #d1d5db;
        padding:8px;
        text-align:left;
        font-size:11px;
    }

    td{
        border:1px solid #e5e7eb;
        padding:7px;
        font-size:11px;
    }

    tr:nth-child(even){
        background:#fafafa;
    }

    .footer{
        position:fixed;
        bottom:0;
        left:0;
        right:0;
        text-align:center;
        font-size:9px;
        color:#666;
    }

</style>
</head>

<body>

{{-- ================= HEADER ================= --}}
<table class="header">
<tr>
<td width="90">
<img src="{{ public_path('images/deped-logo.png') }}" class="logo">
</td>

<td class="center">
<div>Republic of the Philippines</div>
<div><strong>Department of Education</strong></div>
<div class="subtitle">Elementary Sports and Recreational Sports System (eSRRS)</div>
<div class="report-title">{{ $title }}</div>
</td>

<td width="90"></td>
</tr>
</table>


<div class="generated">
Generated: {{ now()->format('F d, Y h:i A') }}
</div>


{{-- ================= TABLE ================= --}}
<table>

<thead>
<tr>

<th width="20%">Secretary</th>
<th width="20%">Email</th>
<th width="20%">Sport Access</th>
<th width="15%">Type</th>
<th width="12%">Level</th>
<th width="13%">Category</th>

@if($title == 'Pending Award Submissions')
<th width="10%">Pending</th>
@endif

@if($title == 'Awards Needing Revision')
<th width="10%">Total Revision</th>
@endif

</tr>
</thead>

<tbody>

@foreach($data as $secretary)

<tr>

<td>{{ $secretary->name }}</td>

<td>{{ $secretary->email }}</td>

<td class="px-6 py-4 text-sm text-gray-600">
                        {{ collect($secretary->sports_access ?? [])
                        ->map(fn($id)=>optional($sports->firstWhere('id',$id))->name)
                        ->filter()->implode(', ') ?: 'N/A' }}
                    </td>

<td class="px-6 py-4 text-sm text-gray-600">
                        {{ collect($secretary->sport_type_access ?? [])
                        ->map(fn($id)=>optional($sportTypes->firstWhere('id',$id))->type_name)
                        ->filter()->implode(', ') ?: 'N/A' }}
                    </td>

<td>
{{ implode(', ', $secretary->level_access ?? []) ?: 'N/A' }}
</td>

<td>
{{ implode(', ', $secretary->category_access ?? []) ?: 'N/A' }}
</td>

@if($title == 'Pending Award Submissions')
<td>
{{ ($secretary->all_awards_pending_count ?? 0) + ($secretary->individual_awards_pending_count ?? 0) }}
</td>
@endif

@if($title == 'Awards Needing Revision')
<td>
{{ ($secretary->all_awards_revision_count ?? 0) + ($secretary->individual_awards_revision_count ?? 0) }}
</td>
@endif

</tr>

@endforeach

</tbody>

</table>


{{-- ================= FOOTER ================= --}}
<div class="footer">
Elementary Sports and Recreational Sports System (eSRRS) • Department of Education
</div>

</body>
</html>