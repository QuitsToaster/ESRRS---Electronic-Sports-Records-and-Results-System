<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IndividualAwardResult;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;

class ResultIndividualAwardController extends Controller
{
    public function store(Request $request)
{
    $user = auth()->user();

    $status = $user->role->name === 'Tournament Secretary'
        ? 'pending'
        : 'approved';

    $eventId = Event::where('is_active', 1)->value('id');

    // ✅ OPTIONAL FILE UPLOAD
    $filePath = null;
    if ($user->role->name === 'Tournament Secretary' && $request->hasFile('validation_file')) {
        $filePath = $request->file('validation_file')
            ->store('award-validations', 'public');
    }

    foreach ($request->individual_results as $sportId => $types) {
        foreach ($types as $typeId => $medals) {
            foreach ($medals as $medal => $athleteId) {

                if (!$athleteId) continue;

                IndividualAwardResult::updateOrCreate(
                    [
                        'event_id' => $eventId,
                        'division' => $request->division,
                        'category' => $request->category,
                        'sport_id' => $sportId,
                        'type_id' => $typeId,
                        'medal' => $medal,
                    ],
                    [
                        'athlete_id' => $athleteId,
                        'sdo' => $user->division_name,
                        'status' => $status,
                        'awarded_by' => $user->id,
                        'validation_file' => $filePath,
                    ]
                );
            }
        }
    }

    return back()->with('success', 'Individual awards forwarded successfully.');
}

    public function approveIndividual(Request $request){
    IndividualAwardResult::where('id', $request->award_id)->update(['status'=>'approved']);
    return back()->with('success','Award approved successfully!');
}
}
