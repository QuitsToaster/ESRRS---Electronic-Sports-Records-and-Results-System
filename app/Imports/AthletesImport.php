<?php

namespace App\Imports;

use App\Models\Athlete;
use App\Models\School;
use App\Models\Sport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class AthletesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            // Skip if LRN already exists
            if (!empty($row['lrn']) && Athlete::where('lrn', $row['lrn'])->exists()) {
                continue;
            }

            /* ================= SCHOOL ================= */
            $school = School::firstOrCreate(
                ['school_code' => $row['school_code']],
                [
                    'name'            => $row['schoolname'],
                    'school_type'     => ucfirst(strtolower($row['school_type'])),
                    'school_address'  => $row['school_address'],
                    'school_division' => $row['schdiv'],
                ]
            );

            /* ================= SPORT ================= */
            $sport = Sport::firstOrCreate(
                ['name' => strtoupper($row['event'])],
                [
                    'division' => ucfirst(strtolower($row['level'])),
                    'category' => $row['sex'] === 'M' ? 'Boys' : 'Girls',
                ]
            );

            /* ================= ATHLETE ================= */
            Athlete::create([
                'last_name'       => strtoupper($row['lastname']),
                'first_name'      => strtoupper($row['firstname']),
                'middle_initial'  => $row['mi'] ?? null,

                'sex'             => $row['sex'] === 'M' ? 'Male' : 'Female',
                'birthdate'       => $this->parseDate($row['bdate_mmddyyyy']),

                'region'          => $row['region'],
                'level'           => $row['level'],
                'event'           => $row['event'],

                'school_id'       => $school->id,
                'sport_id'        => $sport->id,

                'school_name'     => $row['schoolname'],
                'school_type'     => ucfirst(strtolower($row['school_type'])),
                'school_address'  => $row['school_address'],
                'school_division' => $row['schdiv'],
                'school_code'     => $row['school_code'],

                'division'        => ucfirst(strtolower($row['level'])), // Elementary / Secondary
                'category'        => $row['sex'] === 'M' ? 'Boys' : 'Girls',

                'lrn'             => $row['lrn'],
                'status'          => 'Active',
            ]);
        }
    }

    private function parseDate($date)
    {
        try {
            return Carbon::createFromFormat('m/d/Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
