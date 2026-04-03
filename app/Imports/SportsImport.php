<?php

namespace App\Imports;

use App\Models\Sport;
use App\Models\SportType;
use App\Models\AuditLog;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class SportsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
{
    $user = Auth::user();

    foreach ($rows as $row) {

        if (!$row['name']) continue;

        $name     = trim($row['name']);
        $typeName = trim($row['type_of_sport'] ?? '');
        $level    = trim($row['level'] ?? '');
        $category = ucfirst(strtolower(trim($row['category'] ?? '')));

        if (!$name || !$level) continue;

        $isParagames = strtolower($level) === 'paragames';

        // ✅ FIXED LOGIC (handles Mixed properly)
        if ($isParagames) {

            if ($category === 'Mixed') {
                $categoriesToSave = ['Mixed'];
            } elseif (in_array($category, ['Boys', 'Girls'])) {
                $categoriesToSave = [$category];
            } else {
                // default fallback
                $categoriesToSave = ['Boys', 'Girls'];
            }

        } else {
            $categoriesToSave = [$category];
        }

        foreach ($categoriesToSave as $catToSave) {

            $sport = Sport::updateOrCreate(
                [
                    'name'     => $name,
                    'division' => $level,
                    'category' => $catToSave
                ],
                [
                    'level' => $level
                ]
            );

            if ($typeName) {
                SportType::firstOrCreate([
                    'sport_id' => $sport->id,
                    'type_name' => $typeName
                ]);
            }

            AuditLog::create([
                'model_type' => 'Sport',
                'model_id' => $sport->id,
                'user_id' => $user->id,
                'old_values' => null,
                'new_values' => json_encode($sport->toArray()),
                'action' => "Imported Sport via CSV: {$name} | Type: {$typeName} | Level: {$level} | Category: {$catToSave}"
            ]);
        }
    }
}
}