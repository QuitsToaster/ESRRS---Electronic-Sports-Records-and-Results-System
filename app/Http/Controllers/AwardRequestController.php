<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AwardRequest;
use Illuminate\Support\Facades\DB;

class AwardRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'event_id'   => 'required',
            'award_type' => 'required',
            'division'   => 'required',
            'payload'    => 'required',
        ]);

        $user = auth()->user();

        // ✅ SIMPLE, DIRECT INSERT (NO MASS ASSIGNMENT ISSUES)
        DB::table('award_requests')->insert([
            'event_id'     => $request->event_id,
            'award_type'   => $request->award_type,
            'division'     => $request->division,
            'category'     => $request->category,
            'sdo'          => $user->division->name,
            'payload'      => $request->payload, // ✅ STORE STRING
            'status'       => 'pending',          // ✅ ENUM SAFE
            'submitted_by' => $user->id,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return back()->with('success', 'Award request forwarded successfully.');
    }

    public function validateRequest(AwardRequest $awardRequest)
    {
        $payload = json_decode($awardRequest->payload, true);

        foreach ($payload as $sportId => $types) {
            foreach ($types as $typeId => $sdos) {
                foreach ($sdos as $sdo => $medals) {
                    foreach (['Gold','Silver','Bronze'] as $medal) {
                        if (($medals[$medal] ?? 0) > 0) {
                            \App\Models\AllAwardResult::create([
                                'event_id'      => $awardRequest->event_id,
                                'sport_id'      => $sportId,
                                'sport_type_id' => $typeId,
                                'sdo'           => $sdo,
                                'medal'         => $medal,
                                'count'         => $medals[$medal],
                            ]);
                        }
                    }
                }
            }
        }

        $awardRequest->update([
            'status'       => 'approved',
            'validated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Award request validated and saved.');
    }
}
