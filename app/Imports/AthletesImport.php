<?php

namespace App\Imports;

use App\Models\Athlete;
use App\Models\School;
use App\Models\Sport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AthletesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            try {

                /* ================= CLEAN VALUES ================= */

                $level = $this->cleanLevel($row['level'] ?? null);
                $sex = $this->cleanSex($row['sex'] ?? null);
                $category = $sex === 'Male' ? 'Boys' : 'Girls';

                $lrn = $this->cleanLRN($row['lrn'] ?? null);

                $lastname = $this->cleanName($row['lastname'] ?? null);
                $firstname = $this->cleanName($row['firstname'] ?? null);
                $mi = strtoupper(trim($row['mi'] ?? ''));

                $schoolCode = trim($row['school_code'] ?? '');

                /* ================= SKIP INVALID ================= */

                if (!$lrn || !$lastname || !$firstname) {
                    continue;
                }

                if (Athlete::where('lrn', $lrn)->exists()) {
                    continue;
                }

                /* ================= SCHOOL ================= */

                $school = School::firstOrCreate(
                    ['school_code' => $schoolCode],
                    [
                        'name' => strtoupper(trim($row['schoolname'] ?? '')),
                        'school_type' => ucfirst(strtolower(trim($row['school_type'] ?? ''))),
                        'school_address' => trim($row['school_address'] ?? ''),
                        'school_division' => strtoupper(trim($row['schdiv'] ?? '')),
                    ]
                );

                /* ================= SPORT ================= */

                $eventName = strtoupper(trim($row['event'] ?? ''));

                $sport = Sport::whereRaw('UPPER(name) = ?', [$eventName])->first();
                $sportId = $sport ? $sport->id : null;

                /* ================= ATHLETE ================= */

                Athlete::create([

                    'last_name' => $lastname,
                    'first_name' => $firstname,
                    'middle_initial' => $mi ?: null,

                    'sex' => $sex,
                    'birthdate' => $this->parseDate($row['bdate_mmddyyyy'] ?? null),

                    'region' => strtoupper(trim($row['region'] ?? '')),

                    'level' => $level,
                    'event' => $eventName,

                    'school_id' => $school->id,
                    'sport_id' => $sportId,

                    'school_name' => strtoupper(trim($row['schoolname'] ?? '')),
                    'school_type' => ucfirst(strtolower(trim($row['school_type'] ?? ''))),
                    'school_address' => trim($row['school_address'] ?? ''),
                    'school_division' => strtoupper(trim($row['schdiv'] ?? '')),
                    'municipality' => strtoupper(trim($row['municipality'] ?? '')),
                    'school_code' => $schoolCode,

                    'division' => $level,
                    'category' => $category,

                    'lrn' => $lrn,
                    'status' => 'Active',

                    'legislative_district' => strtoupper(trim($row['legislative_district'] ?? '')),
                ]);

            } catch (\Exception $e) {

                // Prevent whole import from crashing
                continue;

            }
        }
    }

    /* ================= CLEAN LEVEL ================= */

    private function cleanLevel($level)
    {
        $level = strtoupper(trim($level));

        if ($level === 'ELEMENTARY') {
            return 'Elementary';
        }

        if ($level === 'SECONDARY') {
            return 'Secondary';
        }

        if ($level === 'PARAGAMES') {
            return 'Paragames';
        }

        return null;
    }

    /* ================= CLEAN SEX ================= */

    private function cleanSex($sex)
    {
        $sex = strtoupper(trim($sex));

        if ($sex === 'M' || $sex === 'MALE') {
            return 'Male';
        }

        if ($sex === 'F' || $sex === 'FEMALE') {
            return 'Female';
        }

        return 'Male';
    }

    /* ================= CLEAN NAME ================= */

    private function cleanName($name)
    {
        if (!$name) return null;

        return strtoupper(trim($name));
    }

    /* ================= CLEAN LRN ================= */

    private function cleanLRN($lrn)
    {
        if (!$lrn) return null;

        $lrn = trim($lrn);

        // remove decimal .00
        $lrn = explode('.', $lrn)[0];

        return $lrn;
    }

    /* ================= PARSE DATE ================= */

    private function parseDate($date)
    {
        try {

            if (!$date) return null;

            return Carbon::createFromFormat('m/d/Y', trim($date))->format('Y-m-d');

        } catch (\Exception $e) {

            return null;

        }
    }
}