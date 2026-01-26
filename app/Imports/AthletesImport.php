<?php

namespace App\Imports;

use App\Models\Athlete;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AthletesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Athlete([
            'first_name'      => $row['first_name'],
            'middle_initial'  => $row['middle_initial'],
            'last_name'       => $row['last_name'],
            'sex'             => $row['sex'],
            'birthdate'       => $row['birthdate'] ?? null,
            'school_id'       => $row['school_id'],
            'sport_id'        => $row['sport_id'],
            'division'        => $row['division'],
            'category'        => $row['category'],
            'status'          => $row['status'] ?? 'Active',
            'qr_code'         => 'CEMS-' . strtoupper(Str::random(8)),
        ]);
    }
}
