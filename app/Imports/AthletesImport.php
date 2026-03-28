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
    public $errors = [];
    public $successCount = 0;
    public $previewData = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {

            $rowNumber = $index + 2; // +2 because header row

            try {

                /* ================= PREVIEW DATA ================= */
                $this->previewData[] = $row;

                /* ================= CLEAN VALUES ================= */

                $level = $this->cleanLevel($row['level'] ?? null);
                $sex = $this->cleanSex($row['sex'] ?? null);
                $category = $sex === 'Male' ? 'Boys' : 'Girls';

                $lrn = $this->cleanLRN($row['lrn'] ?? null);

                // ✅ SIMPLE LRN FIX (NO RANDOM)
                if (!$lrn) {
                    $lrn = 'NO_LRN';
                }

                $originalLrn = $lrn;
                $count = 1;

                while (Athlete::where('lrn', $lrn)->exists()) {
                    $lrn = $originalLrn . '-' . $count;
                    $count++;
                }

                $lastname = $this->cleanName($row['lastname'] ?? null) ?? 'N/A';
                $firstname = $this->cleanName($row['firstname'] ?? null) ?? 'N/A';
                $mi = strtoupper(trim($row['mi'] ?? '')) ?: null;

                $schoolCode = trim($row['school_code'] ?? '') ?: 'N/A';

                /* ================= VALIDATION CHECK (FOR REPORT ONLY) ================= */

                if ($lastname === 'N/A' || $firstname === 'N/A') {
                    $this->errors[] = "Row {$rowNumber}: Missing name";
                }

                /* ================= SCHOOL ================= */

                $school = School::firstOrCreate(
                    ['school_code' => $schoolCode],
                    [
                        'name' => strtoupper(trim($row['schoolname'] ?? 'N/A')),
                        'school_type' => ucfirst(strtolower(trim($row['school_type'] ?? 'Public'))),
                        'school_address' => trim($row['school_address'] ?? 'N/A'),
                        'school_division' => strtoupper(trim($row['schdiv'] ?? 'N/A')),
                    ]
                );

                /* ================= SPORT ================= */

                $eventName = strtoupper(trim($row['event'] ?? 'N/A'));

                $sport = Sport::whereRaw('UPPER(name) = ?', [$eventName])->first();
                $sportId = $sport ? $sport->id : null;

                /* ================= SAVE ATHLETE ================= */

                Athlete::create([

                    'last_name' => $lastname,
                    'first_name' => $firstname,
                    'middle_initial' => $mi,

                    'sex' => $sex,
                    'birthdate' => $this->parseDate($row['bdate_mmddyyyy'] ?? null),

                    'region' => strtoupper(trim($row['region'] ?? 'N/A')),

                    'level' => $level,
                    'event' => $eventName,

                    'school_id' => $school->id,
                    'sport_id' => $sportId,

                    'school_name' => strtoupper(trim($row['schoolname'] ?? 'N/A')),
                    'school_type' => ucfirst(strtolower(trim($row['school_type'] ?? 'Public'))),
                    'school_address' => trim($row['school_address'] ?? 'N/A'),
                    'school_division' => strtoupper(trim($row['schdiv'] ?? 'N/A')),
                    'municipality' => strtoupper(trim($row['municipality'] ?? 'N/A')),
                    'school_code' => $schoolCode,

                    'division' => $level ?? 'Elementary',
                    'category' => $category,

                    'lrn' => $lrn,
                    'status' => 'Active',

                    'legislative_district' => strtoupper(trim($row['legislative_district'] ?? 'N/A')),
                ]);

                $this->successCount++;

            } catch (\Exception $e) {

                $this->errors[] = "Row {$rowNumber}: " . $e->getMessage();
                continue;
            }
        }
    }

    /* ================= HELPERS ================= */

    private function cleanLevel($level)
    {
        $level = strtoupper(trim($level));

        return match ($level) {
            'ELEMENTARY' => 'Elementary',
            'SECONDARY' => 'Secondary',
            'PARAGAMES' => 'Paragames',
            default => 'Elementary',
        };
    }

    private function cleanSex($sex)
    {
        $sex = strtoupper(trim($sex));

        return match ($sex) {
            'F', 'FEMALE' => 'Female',
            default => 'Male',
        };
    }

    private function cleanName($name)
    {
        return $name ? strtoupper(trim($name)) : null;
    }

    private function cleanLRN($lrn)
    {
        if (!$lrn) return null;
        return explode('.', trim($lrn))[0];
    }

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