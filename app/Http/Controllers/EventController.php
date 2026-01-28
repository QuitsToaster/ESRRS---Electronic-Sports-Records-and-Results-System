<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Sport;
use App\Models\School;
use App\Models\AuditLog;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
   public function index(Request $request)
{
    $query = Event::with(['sport', 'schools']);

    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhereHas('sport', function($sq) use ($search) {
                  $sq->where('name', 'like', "%{$search}%");
              })
              ->orWhere('age_group', 'like', "%{$search}%")
              ->orWhere('category', 'like', "%{$search}%")
              ->orWhere('venue', 'like', "%{$search}%")
              ->orWhereHas('schools', function ($sq) use ($search) {
                  $sq->where('name', 'like', "%{$search}%");
              });
        });
    }

    $events = $query->orderBy('start_date')->get();

    return view('events.index', compact('events'));
}


   public function create()
{
    $sports = Sport::with('sportTypes')->get();
    $schools = School::orderBy('name')->get();

    return view('events.create', compact('sports', 'schools'));
}



    public function store(Request $request)
{
    $request->validate([
    'sport_id'       => 'required|exists:sports,id',
    'sport_type_id'  => 'required|exists:sport_types,id',
    'age_group'      => 'nullable|string',
    'category'       => 'nullable|string',
    'venue'          => 'nullable|string|max:255',
    'start_date'     => 'nullable|date',
    'schools'        => 'required|array|min:1',
    'schools.*'      => 'exists:schools,id',
]);

$sportType = \App\Models\SportType::find($request->sport_type_id);

$event = Event::create([
    'sport_id'      => $request->sport_id,
    'sport_type_id' => $request->sport_type_id,
    'name'          => $sportType->type_name, // derived
    'age_group'     => $request->age_group,
    'category'      => $request->category,
    'venue'         => $request->venue,
    'start_date'    => $request->start_date,
]);

$event->schools()->sync($request->schools);


    AuditLog::create([
        'model_type' => 'Event',
        'model_id'   => $event->id,
        'user_id'    => Auth::id(),
        'old_values' => null,
        'new_values' => json_encode($event->load('schools')->toArray()),
        'action'     => 'Created Event: ' . $event->name,
    ]);

    return redirect()->route('events.index')->with('success', 'Event created successfully!');
}


    public function edit(Event $event)
{
    $sports  = Sport::all();
    $schools = School::orderBy('name')->get();

    // load schools already attached
    $event->load('schools');

    return view('events.edit', compact('event', 'sports', 'schools'));
}


    public function update(Request $request, Event $event)
{
    $request->validate([
        'name'        => 'required|string|max:255',
        'sport_id'    => 'required|exists:sports,id',
        'level'       => 'nullable|string',
        'age_group'   => 'nullable|string',
        'category'    => 'nullable|string',
        'venue'       => 'nullable|string|max:255',
        'start_date'  => 'nullable|date',
        'schools'     => 'required|array|min:1',
        'schools.*'   => 'exists:schools,id',
    ]);

    $oldValues = $event->load('schools')->toArray();

    $event->update($request->only([
        'name',
        'sport_id',
        'level',
        'age_group',
        'category',
        'venue',
        'start_date',
    ]));

    // Sync teams
    $event->schools()->sync($request->schools);

    AuditLog::create([
        'model_type' => 'Event',
        'model_id'   => $event->id,
        'user_id'    => Auth::id(),
        'old_values' => json_encode($oldValues),
        'new_values' => json_encode($event->load('schools')->toArray()),
        'action'     => 'Updated Event: ' . $event->name,
    ]);

    return redirect()->route('events.index')
        ->with('success', 'Event updated successfully!');
}


    public function destroy(Event $event)
    {
        $oldValues = $event->toArray();
        $event->delete();

        AuditLog::create([
            'model_type' => 'Event',
            'model_id'   => $event->id,
            'user_id'    => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => null,
            'action'     => 'Deleted Event: ' . $event->name,
        ]);

        return redirect()->route('events.index')->with('success', 'Event deleted successfully!');
    }

    public function manage(Event $event)
{
    $event->load([
        'sport',
        'schools',
        'goldWinner',
        'silverWinner',
        'bronzeWinner',
    ]);

    return view('events.manage', compact('event'));
}

public function saveWinners(Request $request, Event $event)
{
    $request->validate([
        'gold_school_id'   => 'required|different:silver_school_id,bronze_school_id',
        'silver_school_id' => 'required|different:gold_school_id,bronze_school_id',
        'bronze_school_id' => 'required|different:gold_school_id,silver_school_id',
    ]);

    // Update winners AND award_date
    $event->update([
        'gold_school_id'   => $request->gold_school_id,
        'silver_school_id' => $request->silver_school_id,
        'bronze_school_id' => $request->bronze_school_id,
        'award_date'       => now(), // ✅ set award date
    ]);

    // Sync results table
    Result::where('event_id', $event->id)->delete();

    Result::insert([
        [
            'event_id'   => $event->id,
            'school_id'  => $request->gold_school_id,
            'sport_id'   => $event->sport_id,
            'division'   => $event->age_group,
            'category'   => $event->category,
            'medal'      => 'Gold',
            'awarded_by' => auth()->id(),
        ],
        [
            'event_id'   => $event->id,
            'school_id'  => $request->silver_school_id,
            'sport_id'   => $event->sport_id,
            'division'   => $event->age_group,
            'category'   => $event->category,
            'medal'      => 'Silver',
            'awarded_by' => auth()->id(),
        ],
        [
            'event_id'   => $event->id,
            'school_id'  => $request->bronze_school_id,
            'sport_id'   => $event->sport_id,
            'division'   => $event->age_group,
            'category'   => $event->category,
            'medal'      => 'Bronze',
            'awarded_by' => auth()->id(),
        ],
    ]);

    return back()->with('success', 'Winners saved, award date recorded, and medal tally updated.');
}



}
