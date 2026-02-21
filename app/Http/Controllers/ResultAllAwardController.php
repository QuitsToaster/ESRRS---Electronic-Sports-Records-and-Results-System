<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AllAwardResult;
use App\Models\Event;

class ResultAllAwardController extends Controller
{
    public function store(Request $request)
{
    $user = auth()->user();

    $status = $user->role->name === 'Tournament Secretary' ? 'pending' : 'approved';

    $eventId = Event::where('is_active', 1)->value('id');

    if (!$eventId) {
        return back()->with('error', 'No active event found. Please activate an event first.');
    }

    foreach ($request->results as $sportId => $types) {
        foreach ($types as $typeId => $sdos) {
            foreach ($sdos as $sdo => $medals) {
                foreach ($medals as $medal => $count) {

                    AllAwardResult::updateOrCreate(
                        [
                            'event_id' => $eventId,
                            'division' => $request->division,
                            'category' => $request->category,
                            'sport_id' => $sportId,
                            'type_id' => $typeId,
                            'sdo' => $sdo,
                            'medal' => $medal,
                        ],
                        [
                            'count' => $count,
                            'status' => $status,
                            'awarded_by' => $user->id, 
                        ]
                    );

                }
            }
        }
    }

    return back()->with('success', 'All awards saved successfully.');
}

public function approveAll(Request $request){
    AllAwardResult::where('id', $request->award_id)->update(['status'=>'approved']);
    return back()->with('success','Award approved successfully!');
}

}

