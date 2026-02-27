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
            // Skip empty rows
            if (!$row['name']) continue;

            $name     = trim($row['name']);
            $typeName = trim($row['type_of_sport'] ?? '');
            $level    = trim($row['level'] ?? '');
            $category = trim($row['category'] ?? '');

            if (!$name || !$level) continue; // category may be empty for Paragames

            // Determine if this is a Paragames entry
            $isParagames = strtolower($level) === 'paragames';

            // For Paragames, ignore category
            $categoryToSave = $isParagames ? null : $category;

            // Create or update sport
            $sport = Sport::updateOrCreate(
                ['name' => $name, 'division' => $level, 'category' => $categoryToSave],
                ['level' => $level]
            );

            // Add sport type
            if ($typeName) {
                SportType::firstOrCreate([
                    'sport_id' => $sport->id,
                    'type_name' => $typeName
                ]);
            }

            // Audit log
            AuditLog::create([
                'model_type' => 'Sport',
                'model_id' => $sport->id,
                'user_id' => $user->id,
                'old_values' => null,
                'new_values' => json_encode($sport->toArray()),
                'action' => "Imported Sport via CSV: {$name} | Type: {$typeName} | Level: {$level}" .
                            ($isParagames ? '' : " | Category: {$category}")
            ]);
        }
    }
}