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

        // Status depends on role
        $status = $roleName === 'Tournament Secretary' ? 'pending' : 'approved';

        $filePath = $request->hasFile('validation_file')
            ? $request->file('validation_file')->store('award-validations', 'public')
            : null;

        $results = $request->input('results', []);

        if (!is_array($results)) {
            return back()->with('error', 'Invalid results format.');
        }

        $processedEventIds = []; // Track events that got awards

        foreach ($results as $eventId => $sports) {
            $event = Event::find($eventId);
            if (!$event) continue; // Skip if event not found
            if (!is_array($sports)) continue;

            foreach ($sports as $sportId => $medals) {
                if (!is_array($medals)) continue;

                foreach ($medals as $medalName => $selectedSdos) {
                    if (!is_array($selectedSdos)) {
                        $selectedSdos = [$selectedSdos];
                    }

                    foreach ($selectedSdos as $sdoValue) {
    if (empty($sdoValue)) continue;

    // ✅ GET division & category FROM EVENT (FIX)
    $division = $event->age_group;
    $category = $event->category;

    $existing = AllAwardResult::where([
        'event_id' => $eventId,
        'division' => $division,
        'category' => $category,
        'sport_id' => $sportId,
        'type_id' => $event->sport_type_id,
        'sdo' => $sdoValue,
        'medal' => $medalName,
    ])->first();

    $oldValues = $existing ? $existing->toArray() : null;

    $award = AllAwardResult::updateOrCreate(
        [
            'event_id' => $eventId,
            'division' => $division,   // ✅ FIXED
            'category' => $category,   // ✅ FIXED
            'sport_id' => $sportId,
            'type_id' => $event->sport_type_id,
            'sdo' => $sdoValue,
            'medal' => $medalName,
        ],
        [
            'count' => 1,
            'status' => $status,
            'awarded_by' => $user->id,
            'validation_file' => $filePath,
            'event_name' => $event->name,
        ]
    );

    AuditLog::create([
        'model_type' => 'AllAwardResult',
        'model_id'   => $award->id,
        'user_id'    => $user->id,
        'action'     => $existing
            ? "Updated All Award | Event: $eventId | Sport: $sportId | Medal: $medalName"
            : "Created All Award | Event: $eventId | Sport: $sportId | Medal: $medalName",
        'old_values' => $oldValues ? json_encode($oldValues) : null,
        'new_values' => json_encode($award->toArray()),
        'description' => json_encode([
            'performed_by' => $user->name,
            'role' => $roleName,
            'event_id' => $eventId,
            'sport_id' => $sportId,
            'type_id' => $event->sport_type_id,
            'division' => $division,   // ✅ FIXED
            'category' => $category,   // ✅ FIXED
            'sdo' => $sdoValue,
            'medal' => $medalName,
            'status' => $status,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]),
    ]);

    $processedEventIds[] = $eventId;
}
                }
            }
        }

        // ✅ Update event status based on role
foreach (array_unique($processedEventIds) as $eventId) {

    if (in_array($roleName, ['Tournament Manager', 'Super Admin'])) {
        $eventStatus = 'Finished'; // ✅ UPDATED
    } else {
        $eventStatus = 'Ongoing';
    }

    Event::where('id', $eventId)->update(['status' => $eventStatus]);
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

        // ✅ If Tournament Manager or Super Admin approves → Finished
if (in_array($roleName, ['Tournament Manager', 'Super Admin'])) {
    Event::where('id', $award->event_id)->update([
        'status' => 'Finished' // ✅ UPDATED
    ]);
}

        AuditLog::create([
            'model_type' => 'AllAwardResult',
            'model_id' => $award->id,
            'user_id' => $user->id,
            'action' => sprintf(
                'Approved All Award | Event ID: %s | Medal: %s | SDO: %s',
                $award->event_id,
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

        $user = Auth::user();

        AuditLog::create([
            'model_type' => 'AllAwardResult',
            'model_id' => $award->id,
            'user_id' => $user->id,
            'action' => sprintf(
                'Rejected All Award | Event ID: %s | Medal: %s | SDO: %s | Reason: %s',
                $award->event_id,
                strtoupper($award->medal),
                $award->sdo,
                $request->message
            ),
            'old_values' => json_encode($old),
            'new_values' => json_encode($award->toArray()),
            'description' => json_encode([
                'rejected_by' => $user->name,
                'role' => optional($user->role)->name,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]),
        ]);

        return back()->with('success', 'Award marked as needs revision.');
    }

    public function revise($id)
    {
        $award = AllAwardResult::findOrFail($id);
        $old = $award->toArray();
        $user = Auth::user();

        AuditLog::create([
            'model_type' => 'AllAwardResult',
            'model_id' => $award->id,
            'user_id' => $user->id,
            'action' => sprintf(
                'Deleted All Award for Revision | Event ID: %s | Medal: %s | SDO: %s',
                $award->event_id,
                strtoupper($award->medal),
                $award->sdo
            ),
            'old_values' => json_encode($old),
            'new_values' => json_encode([]),
            'description' => json_encode([
                'deleted_by' => $user->name,
                'role' => optional($user->role)->name,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]),
        ]);

        $award->delete();

        return back()->with('success', 'Award sent for revision successfully.');
    }

    public function forwardForValidation(Request $request)
    {
        $user = auth()->user();

        if (optional($user->role)->name !== 'Tournament Secretary') {
            return back()->with('error', 'Unauthorized action.');
        }

        $eventIds = explode(',', $request->event_ids ?? '');
        $division = $request->division;
        $category = $request->category;
        $filePath = $request->hasFile('validation_file')
            ? $request->file('validation_file')->store('award-validations', 'public')
            : null;

        foreach ($eventIds as $eventId) {
            $event = Event::find($eventId);
            if (!$event) continue;

            $awards = AllAwardResult::where('event_id', $eventId)
                ->where('division', $division)
                ->where('category', $category)
                ->get();

            foreach ($awards as $award) {
                $award->update([
                    'status' => 'pending',
                    'validation_file' => $filePath,
                    'type_id' => $event->sport_type_id, // save type_id
                    'event_name' => $event->name,       // save event name
                ]);

                AuditLog::create([
                    'model_type' => 'AllAwardResult',
                    'model_id' => $award->id,
                    'user_id' => $user->id,
                    'action' => sprintf(
                        'Forwarded All Award for Validation | Event ID: %s | Medal: %s | SDO: %s',
                        $award->event_id,
                        strtoupper($award->medal),
                        $award->sdo
                    ),
                    'old_values' => null,
                    'new_values' => json_encode($award->toArray()),
                    'description' => json_encode([
                        'forwarded_by' => $user->name,
                        'role' => optional($user->role)->name,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]),
                ]);

                // Keep event status as Ongoing when forwarded
                $event->update(['status' => 'Ongoing']);
            }
        }

        return back()->with('success', 'All awards forwarded for validation successfully.');
    }

    public function updateFromRevision(Request $request)
    {
        $award = AllAwardResult::findOrFail($request->award_id);

        $event = Event::find($award->event_id);

        $award->update([
            'medal' => $request->medal,
            'sdo' => $request->sdo,
            'status' => 'pending',
            'revision_message' => null,
            'type_id' => $event->sport_type_id ?? null,
            'event_name' => $event->name ?? null,
        ]);

        return back()->with('success', 'Award resubmitted successfully.');
    }
}