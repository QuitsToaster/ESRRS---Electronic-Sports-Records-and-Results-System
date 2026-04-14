<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IndividualAwardResult;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use App\Models\AuditLog;
use App\Models\Sport;
use App\Models\SportType;
use App\Models\Athlete;
use Illuminate\Support\Facades\Auth;

class ResultIndividualAwardController extends Controller
{
    public function store(Request $request)
{
    $user = auth()->user();
    $roleName = optional($user->role)->name;

    // ================= STATUS =================
    $status = $roleName === 'Tournament Secretary'
        ? 'pending'
        : 'approved';

    // ================= FILE =================
    $filePath = $request->hasFile('validation_file')
        ? $request->file('validation_file')->store('award-validations', 'public')
        : null;

    $results = $request->input('individual_results', []);

    if (!is_array($results)) {
        return back()->with('error', 'Invalid results format.');
    }

    // ================= EVENT =================
    $event = Event::where('is_active', 1)->first() ?? Event::first();

    if (!$event) {
        return back()->with('error', 'No event available.');
    }

    // ================= PROCESS =================
    foreach ($results as $sportId => $types) {

        if (!is_array($types)) continue;

        foreach ($types as $typeId => $medals) {

            if (!is_array($medals)) continue;

            foreach ($medals as $medalName => $entries) {

    if (!is_array($entries)) continue;

    foreach ($entries as $data) {

        if (!is_array($data)) continue;

        $athleteId = $data['athlete'] ?? null;
        $division  = $data['division'] ?? null;
        $category  = $data['category'] ?? null;

        if (!$athleteId) continue;

        // ================= GET ATHLETE =================
        $athlete = \App\Models\Athlete::find($athleteId);
        if (!$athlete) continue;

        // ================= AUTO SDO =================
        $sdo = $athlete->school_division
            ? 'SDO - ' . $athlete->school_division
            : null;

        // ================= CHECK EXISTING =================
        $existing = IndividualAwardResult::where([
            'event_id' => $event->id,
            'sport_id' => $sportId,
            'type_id'  => $typeId ?: null,
            'medal'    => $medalName,
            'athlete_id' => $athleteId, // ✅ IMPORTANT FOR MULTIPLE
        ])->first();

        $oldValues = $existing ? $existing->toArray() : null;

        // ================= SAVE =================
        $award = IndividualAwardResult::updateOrCreate(
            [
                'event_id' => $event->id,
                'sport_id' => $sportId,
                'type_id'  => $typeId ?: null,
                'medal'    => $medalName,
                'athlete_id' => $athleteId, // ✅ UNIQUE PER ATHLETE
            ],
            [
                'division' => $division,
                'category' => $category,
                'sdo' => $sdo,
                'status' => $status,
                'awarded_by' => $user->id,
                'validation_file' => $filePath,
            ]
        );

        // ================= GET RELATED DATA =================
$sport = Sport::find($sportId);
$type  = $typeId ? SportType::find($typeId) : null;

$athleteName = strtoupper(
    $athlete->last_name . ', ' .
    $athlete->first_name . ' ' .
    ($athlete->middle_initial ?? '')
);

// ================= AUDIT =================
AuditLog::create([
    'model_type' => 'IndividualAwardResult',
    'model_id'   => $award->id,
    'user_id'    => $user->id,
    'action'     => $existing
        ? "Updated Individual Award | Athlete: {$athleteName} | Sport: " . ($sport->name ?? 'N/A') . " | Type: " . ($type->type_name ?? 'N/A') . " | Medal: {$medalName}"
        : "Created Individual Award | Athlete: {$athleteName} | Sport: " . ($sport->name ?? 'N/A') . " | Type: " . ($type->type_name ?? 'N/A') . " | Medal: {$medalName}",
    'old_values' => $oldValues ? json_encode($oldValues) : null,
    'new_values' => json_encode($award->toArray()),
    'description' => json_encode([
        'performed_by' => $user->name,
        'role' => $roleName,
        'event_id' => $event->id,
        'sport' => $sport->name ?? null,
        'type' => $type->type_name ?? null,
        'division' => $division,
        'category' => $category,
        'athlete' => $athleteName,
        'sdo' => $sdo,
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

    // ================= EVENT STATUS =================
    if (in_array($roleName, ['Tournament Manager', 'Super Admin'])) {
        $event->update(['status' => 'Finished']);
    } else {
        $event->update(['status' => 'Ongoing']);
    }

    return back()->with('success', 'Individual awards saved successfully.');
}

    public function approveIndividual(Request $request)
    {
        $award = IndividualAwardResult::findOrFail($request->award_id);
        $old = $award->toArray();

        $award->update([
            'status' => 'approved',
            'revision_message' => null
        ]);

        $user = Auth::user();
        $roleName = optional($user->role)->name;
        $sport = Sport::find($award->sport_id);
        $type  = $award->type_id ? SportType::find($award->type_id) : null;
        $athlete = Athlete::find($award->athlete_id);

        $athleteName = $athlete
            ? strtoupper($athlete->last_name . ', ' . $athlete->first_name . ' ' . ($athlete->middle_initial ?? ''))
            : 'N/A';

        AuditLog::create([
            'model_type' => 'IndividualAwardResult',
            'model_id' => $award->id,
            'user_id' => $user->id,
            'action' => sprintf(
                'Approved Individual Award | Athlete: %s | Sport: %s | Type: %s | Medal: %s | SDO: %s',
                $athleteName,
                $sport->name ?? 'N/A',
                $type->type_name ?? 'N/A',
                strtoupper($award->medal),
                $award->sdo
            ),
            'old_values' => json_encode($old),
            'new_values' => json_encode($award->toArray()),
            'description' => json_encode([
                'approved_by' => $user->name,
                'role' => $roleName,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]),
        ]);

        return back()->with('success', 'Award approved.');
    }

    public function reject(Request $request)
    {
        $award = IndividualAwardResult::findOrFail($request->award_id);
        $old = $award->toArray();

        $award->update([
            'status' => 'needs_revision',
            'revision_message' => $request->message,
        ]);
        $sport = Sport::find($award->sport_id);
        $type  = $award->type_id ? SportType::find($award->type_id) : null;
        $athlete = Athlete::find($award->athlete_id);

        $athleteName = $athlete
            ? strtoupper($athlete->last_name . ', ' . $athlete->first_name . ' ' . ($athlete->middle_initial ?? ''))
            : 'N/A';

        $user = Auth::user();

        AuditLog::create([
            'model_type' => 'IndividualAwardResult',
            'model_id' => $award->id,
            'user_id' => $user->id,
            'action' => sprintf(
    'Rejected Individual Award | Athlete: %s | Sport: %s | Type: %s | Medal: %s | Reason: %s',
    $athleteName,
    $sport->name ?? 'N/A',
    $type->type_name ?? 'N/A',
    strtoupper($award->medal),
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
        $award = IndividualAwardResult::findOrFail($id);
        $old = $award->toArray();
        $user = Auth::user();
        $sport = Sport::find($award->sport_id);
$type  = $award->type_id ? SportType::find($award->type_id) : null;
$athlete = Athlete::find($award->athlete_id);

$athleteName = $athlete
    ? strtoupper($athlete->last_name . ', ' . $athlete->first_name . ' ' . ($athlete->middle_initial ?? ''))
    : 'N/A';

        AuditLog::create([
            'model_type' => 'IndividualAwardResult',
            'model_id'   => $award->id,
            'user_id'    => $user->id,
            'action' => sprintf(
    'Deleted Individual Award for Revision | Athlete: %s | Division: %s | Category: %s | Sport: %s | Type: %s | Medal: %s | SDO: %s',
    $athleteName,
    $award->division,
    $award->category ?? 'N/A',
    $sport->name ?? 'N/A',
    $type->type_name ?? 'N/A',
    strtoupper($award->medal),
    $award->sdo
),
            'old_values' => json_encode($old),
            'new_values' => json_encode([]),
            'description' => json_encode([
                'deleted_by' => $user->name,
                'role' => optional($user->role)->name,
                'reason' => 'needs_revision',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]),
        ]);

        $award->delete();

        return back()->with('success', 'Award sent for revision successfully.');
    }

    public function updateFromRevision(Request $request)
{
    $award = IndividualAwardResult::findOrFail($request->award_id);

    $award->update([
        'athlete_id' => $request->athlete_id, // ✅ update athlete
        'medal' => $request->medal,
        'sdo' => 'SDO - ' . $request->sdo, // ✅ add prefix
        'status' => 'pending',
        'revision_message' => null,
    ]);

    return back()->with('success', 'Award resubmitted successfully.');
}
}