<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AllAwardResult;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class ResultAllAwardController extends Controller
{
    public function store(Request $request)
{
    $user = auth()->user();
    $roleName = optional($user->role)->name;

    $status = $roleName === 'Tournament Secretary' ? 'pending' : 'approved';
    $eventId = $request->event_id;

    if (!$eventId || !Event::where('id', $eventId)->exists()) {
        return back()->with('error', 'Invalid event selected.');
    }

    $filePath = $request->hasFile('validation_file')
        ? $request->file('validation_file')->store('award-validations', 'public')
        : null;

    // Main division for the current tab (Elementary / Secondary / Paragames)
    $mainDivision = $request->division ?? 'N/A';

    // Loop through form results
    foreach ($request->input('results', []) as $sportId => $types) {
        foreach ($types as $typeId => $medals) {
            foreach ($medals as $medalName => $selectedSdos) {
                foreach ($selectedSdos as $sdoValue) {

                    // Skip if no selection
                    if (empty($sdoValue)) continue;

                    // Save each selected division as "sdo"
                    $existing = AllAwardResult::where([
                        'event_id' => $eventId,
                        'division' => $request->division,          // Elementary / Secondary / Paragames
                        'category' => $request->category ?? null,
                        'sport_id' => $sportId,
                        'type_id'  => $typeId,
                        'sdo'      => $sdoValue,               // Dropdown selection goes here
                        'medal'    => $medalName,
                    ])->first();

                    $oldValues = $existing ? $existing->toArray() : null;

                    $award = AllAwardResult::updateOrCreate(
                        [
                            'event_id' => $eventId,
                            'division' => $request->division,
                            'category' => $request->category ?? null,
                            'sport_id' => $sportId,
                            'type_id'  => $typeId,
                            'sdo'      => $sdoValue,
                            'medal'    => $medalName,
                        ],
                        [
                            'count' => 1,
                            'status' => $status,
                            'awarded_by' => $user->id,
                            'validation_file' => $filePath,
                        ]
                    );

                    // Audit log
                    AuditLog::create([
                        'model_type' => 'AllAwardResult',
                        'model_id'   => $award->id,
                        'user_id'    => $user->id,
                        'action'     => $existing
                            ? "Updated & Forwarded All Award | Division: $mainDivision | Medal: $medalName | SDO: $sdoValue"
                            : "Created & Forwarded All Award | Division: $mainDivision | Medal: $medalName | SDO: $sdoValue",
                        'old_values' => $oldValues ? json_encode($oldValues) : null,
                        'new_values' => json_encode($award->toArray()),
                        'description' => json_encode([
                            'performed_by' => $user->name,
                            'role' => $roleName,
                            'event_id' => $eventId,
                            'division' => $request->division,
                            'category' => $request->category,
                            'sport_id' => $sportId,
                            'type_id' => $typeId,
                            'sdo' => $sdoValue,
                            'medal' => $medalName,
                            'status' => $status,
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                        ]),
                    ]);
                }
            }
        }
    }

    return back()->with('success', 'Awards saved successfully.');
}

    public function approveAll(Request $request)
    {
        $award = AllAwardResult::findOrFail($request->award_id);
        $old = $award->toArray();

        $award->update([
            'status' => 'approved',
            'revision_message' => null
        ]);

        $user = Auth::user();
        $roleName = optional($user->role)->name;

        AuditLog::create([
            'model_type' => 'AllAwardResult',
            'model_id' => $award->id,
            'user_id' => Auth::id(),
            'action' => sprintf(
                'Approved All Award | Division: %s | Sport ID: %s | Medal: %s | SDO: %s',
                $award->division,
                $award->sport_id,
                strtoupper($award->medal),
                $award->sdo
            ),
            'old_values' => json_encode($old),
            'new_values' => json_encode($award->toArray()),
            'description' => json_encode([
                'approved_by' => $user?->name,
                'role' => $roleName,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]),
        ]);

        return back()->with('success', 'Award approved.');
    }

    public function reject(Request $request)
    {
        $award = AllAwardResult::findOrFail($request->award_id);
        $old = $award->toArray();

        $award->update([
            'status' => 'needs_revision',
            'revision_message' => $request->message,
        ]);

        AuditLog::create([
            'model_type' => 'AllAwardResult',
            'model_id' => $award->id,
            'user_id' => Auth::id(),
            'action' => sprintf(
                'Rejected All Award | Division: %s | Sport ID: %s | Medal: %s | SDO: %s | Reason: %s',
                $award->division,
                $award->sport_id,
                strtoupper($award->medal),
                $award->sdo,
                $request->message
            ),
            'old_values' => json_encode($old),
            'new_values' => json_encode($award->toArray()),
        ]);

        return back()->with('success', 'Award marked as needs revision.');
    }

    public function revise($id)
    {
        $award = AllAwardResult::findOrFail($id);
        $old = $award->toArray();

        AuditLog::create([
            'model_type' => 'AllAwardResult',
            'model_id' => $award->id,
            'user_id' => Auth::id(),
            'action' => sprintf(
                'Deleted All Award for Revision | Division: %s | Sport ID: %s | Medal: %s | SDO: %s',
                $award->division,
                $award->sport_id,
                strtoupper($award->medal),
                $award->sdo
            ),
            'old_values' => json_encode($old),
            'new_values' => json_encode([]),
        ]);

        $award->delete();

        return back()->with('success', 'Award sent for revision successfully.');
    }
}