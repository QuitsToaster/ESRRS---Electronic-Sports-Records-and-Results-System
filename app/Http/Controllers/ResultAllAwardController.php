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

        if ($user->role->name === 'Tournament Secretary') {
            $request->validate([
                'selected_sdo' => 'required|string',
                'results' => 'required|array',
            ]);
        }

        $status = $user->role->name === 'Tournament Secretary'
            ? 'pending'
            : 'approved';

        $eventId = Event::where('is_active', 1)->value('id');

        if (!$eventId) {
            return back()->with('error', 'No active event found.');
        }

        $filePath = null;
        if ($user->role->name === 'Tournament Secretary' && $request->hasFile('validation_file')) {
            $filePath = $request->file('validation_file')
                ->store('award-validations', 'public');
        }

        $selectedSdo = $request->selected_sdo;

        foreach ($request->results as $sportId => $types) {
            foreach ($types as $typeId => $sdos) {
                foreach ($sdos as $fakeSdo => $medals) {
                    foreach ($medals as $medal => $count) {

                        if ((int) $count !== 1) continue;

                        $existing = AllAwardResult::where([
                            'event_id' => $eventId,
                            'division' => $request->division,
                            'category' => $request->category ?: null,
                            'sport_id' => $sportId,
                            'type_id'  => $typeId,
                            'sdo'      => $selectedSdo,
                            'medal'    => $medal,
                        ])->first();

                        $oldValues = $existing ? $existing->toArray() : null;

                        $award = AllAwardResult::updateOrCreate(
                            [
                                'event_id' => $eventId,
                                'division' => $request->division,
                                'category' => $request->category ?: null,
                                'sport_id' => $sportId,
                                'type_id'  => $typeId,
                                'sdo'      => $selectedSdo,
                                'medal'    => $medal,
                            ],
                            [
                                'count' => 1,
                                'status' => $status,
                                'awarded_by' => $user->id,
                                'validation_file' => $filePath,
                            ]
                        );

                        AuditLog::create([
    'model_type' => 'AllAwardResult',
    'model_id'   => $award->id,
    'user_id'    => $user->id,
    'action'     => sprintf(
        '%s | Division: %s | Category: %s | Sport ID: %s | Type ID: %s | Medal: %s | SDO: %s | Status: %s',
        $existing ? 'Updated & Forwarded All Award' : 'Created & Forwarded All Award',
        $request->division,
        $request->category ?? 'N/A',
        $sportId,
        $typeId,
        strtoupper($medal),
        $selectedSdo,
        strtoupper($status)
    ),
    'old_values' => $oldValues ? json_encode($oldValues) : null,
    'new_values' => json_encode($award->toArray()),
    'description' => json_encode([
        'performed_by' => $user->name,
        'role' => $user->role->name,
        'event_id' => $eventId,
        'division' => $request->division,
        'category' => $request->category,
        'sport_id' => $sportId,
        'type_id' => $typeId,
        'sdo' => $selectedSdo,
        'medal' => $medal,
        'status' => $status,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
    ]),
]);
                    }
                }
            }
        }

        return back()->with('success', 'Awards forwarded successfully.');
    }

    public function approveAll(Request $request)
    {
        $award = AllAwardResult::findOrFail($request->award_id);
        $old = $award->toArray();

        $award->update([
            'status' => 'approved',
            'revision_message' => null
        ]);

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
        'approved_by' => Auth::user()->name,
        'role' => Auth::user()->role->name,
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