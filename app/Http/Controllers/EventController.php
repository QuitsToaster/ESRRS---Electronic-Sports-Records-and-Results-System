<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Sport;
use App\Models\Division;
use App\Models\AuditLog;
use App\Models\Result;
use App\Models\ResultDivision;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
   /* =========================
       INDEX
    ========================= */
    public function index(Request $request)
    {
        $query = Event::with(['sport', 'divisions']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('age_group', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('venue', 'like', "%{$search}%")
                  ->orWhereHas('sport', fn ($s) =>
                      $s->where('name', 'like', "%{$search}%")
                  )
                  ->orWhereHas('divisions', fn ($d) =>
                      $d->where('name', 'like', "%{$search}%")
                  );
            });
        }

        $events = $query->orderBy('start_date')->get();
        return view('events.index', compact('events'));
    }

    /* =========================
       CREATE
    ========================= */
    public function create()
    {
        $sports    = Sport::with('sportTypes')->get();
        $divisions = Division::orderBy('name')->get();

        return view('events.create', compact('sports', 'divisions'));
    }

   public function store(Request $request)
{
    $request->validate([
        'sport_id'      => 'required|exists:sports,id',
        'sport_type_id' => 'required|exists:sport_types,id',
        'age_group'     => 'nullable|string',
        'category'      => 'nullable|string',
        'venue'         => 'nullable|string|max:255',
        'start_date'    => 'nullable|date',
        'divisions'     => 'required|array|min:1',
        'divisions.*'   => 'exists:divisions,id',
        'is_ball_game'  => 'nullable|boolean',
    ]);

    $sportType = \App\Models\SportType::findOrFail($request->sport_type_id);

    $event = Event::create([
        'sport_id'      => $request->sport_id,
        'sport_type_id' => $request->sport_type_id,
        'name'          => $sportType->type_name,
        'age_group'     => $request->age_group,
        'category'      => $request->category,
        'venue'         => $request->venue,
        'start_date'    => $request->start_date,
        'is_ball_game'  => $request->boolean('is_ball_game'), // 🔥 SAVE FLAG
    ]);

    // Sync divisions
    $event->divisions()->sync($request->divisions);

    // 🔥 AUTO-CREATE GAME IF BALL GAME
    if ($event->is_ball_game) {

        $divisionNames = Division::whereIn('id', $request->divisions)
            ->orderBy('name')
            ->pluck('name')
            ->values();

        Game::create([
            'event_id'  => $event->id,
            'team_a'    => $divisionNames[0] ?? 'TBD',
            'team_b'    => $divisionNames[1] ?? 'TBD',
            'status'    => 'Upcoming',
            'game_time' => $request->start_date
                ? $request->start_date . ' 08:00:00'
                : now(),
            'venue'     => $request->venue ?? 'TBA',
        ]);
    }

    AuditLog::create([
        'model_type' => 'Event',
        'model_id'   => $event->id,
        'user_id'    => Auth::id(),
        'old_values' => null,
        'new_values' => json_encode($event->load('divisions')->toArray()),
        'action'     => 'Created Event: ' . $event->name,
    ]);

    return redirect()->route('events.index')
        ->with('success', 'Event created successfully!');
}


    /* =========================
       EDIT
    ========================= */
    public function edit(Event $event)
    {
        $sports    = Sport::with('sportTypes')->get();
        $divisions = Division::orderBy('name')->get();

        // 🔥 THIS IS WHAT WAS MISSING
        $event->load('divisions');

        return view('events.edit', compact('event', 'sports', 'divisions'));
    }

    /* =========================
       UPDATE
    ========================= */
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'sport_id'      => 'required|exists:sports,id',
            'sport_type_id' => 'required|exists:sport_types,id',
            'age_group'     => 'nullable|string',
            'category'      => 'nullable|string',
            'is_ball_game' => 'nullable|boolean',
            'venue'         => 'nullable|string|max:255',
            'start_date'    => 'nullable|date',
            'divisions'     => 'required|array|min:1',
            'divisions.*'   => 'exists:divisions,id',
        ]);

        $oldValues = $event->load('divisions')->toArray();

        $event->update([
            'sport_id'      => $request->sport_id,
            'sport_type_id' => $request->sport_type_id,
            'age_group'     => $request->age_group,
            'category'      => $request->category,
            'is_ball_game' => $request->boolean('is_ball_game'),
            'venue'         => $request->venue,
            'start_date'    => $request->start_date,
        ]);

        $event->divisions()->sync($request->divisions);

        AuditLog::create([
            'model_type' => 'Event',
            'model_id'   => $event->id,
            'user_id'    => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($event->load('divisions')->toArray()),
            'action'     => 'Updated Event: ' . $event->name,
        ]);

        return redirect()->route('events.index')->with('success', 'Event updated successfully!');
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
        'gold_division_id'   => 'required|different:silver_division_id,bronze_division_id',
        'silver_division_id' => 'required|different:gold_division_id,bronze_division_id',
        'bronze_division_id' => 'required|different:gold_division_id,silver_division_id',
    ]);

    // Load divisions
    $goldDivision   = Division::findOrFail($request->gold_division_id);
    $silverDivision = Division::findOrFail($request->silver_division_id);
    $bronzeDivision = Division::findOrFail($request->bronze_division_id);

    // Update event winners columns
    $event->update([
        'gold_division_id'   => $goldDivision->id,
        'silver_division_id' => $silverDivision->id,
        'bronze_division_id' => $bronzeDivision->id,
        'award_date'         => now(),
    ]);

    // Delete old results
    ResultDivision::where('event_id', $event->id)->delete();

    // Insert new winners into results_divisions
    ResultDivision::insert([
        [
            'event_id'   => $event->id,
            'sport_id'   => $event->sport_id,
            'type_id'    => $event->sport_type_id,
            'division'   => $event->age_group,
            'sdo'        => $goldDivision->name,
            'category'   => $event->category,
            'medal'      => 'Gold',
            'awarded_by' => auth()->id(),
        ],
        [
            'event_id'   => $event->id,
            'sport_id'   => $event->sport_id,
            'type_id'    => $event->sport_type_id,
            'division'   => $event->age_group,
            'sdo'        => $silverDivision->name,
            'category'   => $event->category,
            'medal'      => 'Silver',
            'awarded_by' => auth()->id(),
        ],
        [
            'event_id'   => $event->id,
            'sport_id'   => $event->sport_id,
            'type_id'    => $event->sport_type_id,
            'division'   => $event->age_group,
            'sdo'        => $bronzeDivision->name,
            'category'   => $event->category,
            'medal'      => 'Bronze',
            'awarded_by' => auth()->id(),
        ],
    ]);

    return back()->with('success', 'Winners saved successfully.');
}



}
