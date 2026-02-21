<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AllAwardResult;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;

class ResultAllAwardController extends Controller
{
   public function store(Request $request)
{
    $user = auth()->user();

    $status = $user->role->name === 'Tournament Secretary'
        ? 'pending'
        : 'approved';

    $eventId = Event::where('is_active', 1)->value('id');

    if (!$eventId) {
        return back()->with('error', 'No active event found.');
    }

    // ✅ OPTIONAL FILE UPLOAD
    $filePath = null;
    if ($user->role->name === 'Tournament Secretary' && $request->hasFile('validation_file')) {
        $filePath = $request->file('validation_file')
            ->store('award-validations', 'public');
    }

    foreach ($request->results as $sportId => $types) {
        foreach ($types as $typeId => $sdos) {
            foreach ($sdos as $sdo => $medals) {
                foreach ($medals as $medal => $count) {

                    if ((int)$count !== 1) continue;

                    AllAwardResult::updateOrCreate(
                        [
                            'event_id' => $eventId,
                            'division' => $request->division,
                            'category' => $request->category ?: null,
                            'sport_id' => $sportId,
                            'type_id'  => $typeId,
                            'sdo'      => $sdo,
                            'medal'    => $medal,
                        ],
                        [
                            'count' => 1,
                            'status' => $status,
                            'awarded_by' => $user->id,
                            'validation_file' => $filePath,
                        ]
                    );
                }
            }
        }
    }

    return back()->with('success', 'Awards forwarded successfully.');
}

    public function approveAll(Request $request)
    {
        AllAwardResult::where('id', $request->award_id)
            ->update(['status' => 'approved']);

        return back()->with('success', 'Award approved successfully!');
    }
}
