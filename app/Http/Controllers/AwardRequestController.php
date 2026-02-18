<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AwardRequest;
use App\Models\Event;

class AwardRequestController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'event_id'   => 'required|exists:events,id',
        'award_type' => 'required',
        'division'   => 'required',
        'payload'    => 'required',
        'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
    ]);

    $user = auth()->user();

    $path = null;
    if ($request->hasFile('attachment')) {
        $path = $request->file('attachment')
            ->store('award_requests', 'public');
    }

    AwardRequest::create([
        'event_id'     => $request->event_id,
        'award_type'   => $request->award_type,
        'division'     => $request->division,
        'category'     => $request->category,
        'sdo'          => $user->division->name,
        'payload'      => json_decode($request->payload, true),
        'attachment'   => $path,
        'submitted_by' => $user->id,
    ]);

    return back()->with('success','Forwarded successfully.');
}

public function preview(AwardRequest $awardRequest)
{
    // Ensure the current user is the manager of the SDO
    $this->authorize('view', $awardRequest);

    // Flatten payload for table display
    $payload = [];
    foreach ($awardRequest->payload as $sportId => $types) {
        foreach ($types as $typeId => $sdos) {
            foreach ($sdos as $sdo => $medals) {
                $payload[] = [
    'award_request_id' => $awardRequest->id, // add this
    'sport_id'   => $sportId,
    'type_id'    => $typeId,
    'sdo'        => $sdo,
    'sport_name' => \App\Models\Sport::find($sportId)?->name ?? '-',
    'type_name'  => \App\Models\SportType::find($typeId)?->type_name ?? '-',
    'Gold'       => $medals['Gold'] ?? 0,
    'Silver'     => $medals['Silver'] ?? 0,
    'Bronze'     => $medals['Bronze'] ?? 0,
];

            }
        }
    }

    return response()->json([
        'payload' => $payload
    ]);
}

public function validateRequest(AwardRequest $awardRequest)
{
    $this->authorize('update', $awardRequest);

    // Save medals to AllAwardResult
    foreach ($awardRequest->payload as $sportId => $types) {
        foreach ($types as $typeId => $sdos) {
            foreach ($sdos as $sdo => $medals) {
                foreach (['Gold','Silver','Bronze'] as $medal) {
                    if (($medals[$medal] ?? 0) > 0) {
                        \App\Models\AllAwardResult::create([
                            'event_id' => $awardRequest->event_id,
                            'sport_id' => $sportId,
                            'sport_type_id' => $typeId,
                            'sdo' => $sdo,
                            'medal' => $medal,
                            'count' => $medals[$medal],
                        ]);
                    }
                }
            }
        }
    }

    $awardRequest->update(['status' => 'Validated', 'validated_by' => auth()->id()]);

    return redirect()->back()->with('success', 'Award request validated and saved successfully.');
}



}
