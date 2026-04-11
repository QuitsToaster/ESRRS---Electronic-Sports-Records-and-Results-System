<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Sport;
use App\Models\Division;
use App\Models\AuditLog;
use App\Models\Result;
use App\Models\ResultDivision;
use App\Models\Announcement;
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

    // -----------------------------
    // SEARCH FILTER
    // -----------------------------
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

    // -----------------------------
    // STATUS FILTER
    // -----------------------------
    $status = $request->status;

    if ($request->filled('status')) {
        $query->where('status', $status);
    }

    // -----------------------------
// GET EVENTS
// -----------------------------
$events = $query->orderBy('start_date')->paginate(10); // adjust 10 as needed
    // -----------------------------
    // COUNTS
    // -----------------------------
    $totalEvents = Event::count();
    $displayCount = $events->count();

    return view('events.index', compact(
        'events',
        'totalEvents',
        'displayCount',
        'status'
    ));
}

    /* =========================
       CREATE
    ========================= */
    public function create()
    {
        $sports = Sport::with('sportTypes')->orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();

        return view('events.create', compact('sports', 'divisions'));
    }

   /* =========================
       STORE
    ========================= */
    public function store(Request $request)
    {
        $request->validate([
            'sport_id'      => 'required|exists:sports,id',
            'sport_type_id' => 'required|exists:sport_types,id',
            'age_group'     => 'required|string',
            'category'      => 'nullable|string',
            'venue'         => 'nullable|string|max:255',
            'start_date'    => 'nullable|date',
            'divisions'     => 'required|array|min:1',
            'divisions.*'   => 'exists:divisions,id',
            'games'         => 'nullable|array',
            'games.*'       => 'nullable|string|max:255',
        ]);

        $sport     = Sport::findOrFail($request->sport_id);
        $sportType = \App\Models\SportType::findOrFail($request->sport_type_id);

        $ageGroup = $sport->division === 'Para Games'
            ? 'Para Games'
            : $request->age_group;

        $event = Event::create([
            'sport_id'      => $sport->id,
            'sport_type_id' => $sportType->id,
            'name'          => $sportType->type_name,
            'custom_name'   => $request->custom_name,
            'status'        => 'Upcoming',
            'age_group'     => $ageGroup,
            'category'      => $request->category,
            'venue'         => $request->venue,
            'start_date'    => $request->start_date,
            // ✅ SAVE TIME
            'event_time'    => $request->event_time,
            'games'         => array_values(array_filter($request->games ?? [])),
        ]);

        $event->divisions()->sync($request->divisions);

        // -------------------------------
        // CREATE ANNOUNCEMENT FOR NEW EVENT
        // -------------------------------
        Announcement::create([
            'title'        => "New Event: {$event->name}",
            'content'      => "Event: {$event->name}\nSport ID: {$event->sport_id}\nStart Date: {$event->start_date}\nGames: " . implode(', ', $event->games) . "\nStatus: {$event->status}\nStatus Reason: {$event->status_reason}",
            'type'         => 'Event',
            'event_id'     => $event->id,
            'user_id'      => Auth::id(),
            'published_at' => now(),
        ]);

        return redirect()->route('events.index')
            ->with('success', 'Event created successfully and announcement added!');
    }




    /* =========================
       EDIT
    ========================= */
    public function edit(Event $event)
    {
        $sports    = Sport::with('sportTypes')->get();
        $divisions = Division::orderBy('name')->get();
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
            'status'        => 'required|in:Upcoming,Ongoing,Delayed,Finished,Finished and Awarded',
            'age_group'     => 'nullable|string',
            'category'      => 'nullable|string',
            'is_ball_game'  => 'nullable|boolean',
            'venue'         => 'nullable|string|max:255',
            'start_date'    => 'nullable|date',
            'divisions'     => 'required|array|min:1',
            'divisions.*'   => 'exists:divisions,id',
            'games'         => 'nullable|array',
            'games.*'       => 'nullable|string|max:255',
            'custom_name' => 'nullable|string|max:255',
'event_time'  => 'nullable',
        ]);

        $oldValues = $event->load('divisions')->toArray();

        $event->update([
            'sport_id'      => $request->sport_id,
            'sport_type_id' => $request->sport_type_id,
            'status'        => $request->status,
            'age_group'     => $request->age_group,
            'category'      => $request->category,
            'is_ball_game'  => $request->boolean('is_ball_game'),
            'venue'         => $request->venue,
            'start_date'    => $request->start_date,
            'games'         => array_values(array_filter($request->games ?? [])),

            'custom_name'   => $request->custom_name,
    'event_time'    => $request->event_time,
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

        // -------------------------------
        // CREATE OR UPDATE ANNOUNCEMENT FOR EVENT UPDATE
        // -------------------------------
        $announcementContent = "Event has been updated: {$event->name}\nSport ID: {$event->sport_id}\nStart Date: {$event->start_date}\nGames: " . implode(', ', $event->games) . "\nStatus: {$event->status}\nStatus Reason: {$event->status_reason}";

        $announcement = Announcement::updateOrCreate(
            ['event_id' => $event->id, 'type' => 'Event'],
            [
                'title'        => "Event Updated: {$event->name}",
                'content'      => $announcementContent,
                'user_id'      => Auth::id(),
                'published_at' => now(),
            ]
        );

        return redirect()->route('events.index')->with('success', 'Event updated successfully and announcement updated!');
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

/* =========================
       UPDATE STATUS
    ========================= */
    public function updateStatus(Request $request, Event $event)
    {
        $request->validate([
            'status' => 'required|in:Upcoming,Ongoing,Delayed,Finished,Finished and Awarded',
            'status_reason' => 'nullable|string|max:500',
        ]);

        $oldValues = $event->toArray();

        $event->update([
            'status' => $request->status,
            'status_reason' => $request->status === 'Delayed' ? $request->status_reason : null,
        ]);

        AuditLog::create([
            'model_type' => 'Event',
            'model_id'   => $event->id,
            'user_id'    => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($event->toArray()),
            'action'     => 'Updated Event Status: ' . $event->name,
        ]);

        // -------------------------------
        // ANNOUNCEMENT FOR STATUS CHANGE
        // -------------------------------
        $announcementContent = "Event status updated: {$event->name}\nStatus: {$event->status}\nStatus Reason: {$event->status_reason}";

        $announcement = Announcement::updateOrCreate(
            ['event_id' => $event->id, 'type' => 'Event'],
            [
                'title'        => "Event Status Updated: {$event->name}",
                'content'      => $announcementContent,
                'user_id'      => Auth::id(),
                'published_at' => now(),
            ]
        );

        return back()->with('success', 'Event status updated and announcement created/updated.');
    }

public function saveGameWinners(Request $request, Event $event)
{
    $request->validate([
        'game_winners' => 'required|array',
        'game_winners.*' => 'nullable|exists:divisions,id',
    ]);

    $oldValues = is_array($event->game_winners) ? $event->game_winners : [];
    $incomingWinners = $request->game_winners ?? [];

    // Preserve already saved winners and only fill missing ones
    $finalWinners = $oldValues;

    foreach ($incomingWinners as $index => $divisionId) {
        if (!empty($divisionId)) {
            $finalWinners[$index] = $divisionId;
        } elseif (!isset($finalWinners[$index])) {
            $finalWinners[$index] = null;
        }
    }

    // Make sure array indexes match game indexes
    if (is_array($event->games)) {
        foreach ($event->games as $index => $game) {
            if (!array_key_exists($index, $finalWinners)) {
                $finalWinners[$index] = null;
            }
        }
        ksort($finalWinners);
        $finalWinners = array_values($finalWinners);
    }

    $event->update([
        'game_winners' => $finalWinners,
    ]);

    AuditLog::create([
        'model_type' => 'Event',
        'model_id'   => $event->id,
        'user_id'    => auth()->id(),
        'old_values' => json_encode($oldValues),
        'new_values' => json_encode($finalWinners),
        'action'     => 'Updated Game Winners',
    ]);

    // -------------------------------
    // CREATE OR UPDATE ANNOUNCEMENT FOR WINNERS
    // -------------------------------
    $announcementContent = "Game winners have been updated for the event: {$event->name}.\n\n";

    foreach ($finalWinners as $index => $divisionId) {
        if ($divisionId) {
            $division = Division::find($divisionId);
            $announcementContent .= "Game " . ($index + 1) . " Winner: " . ($division?->name ?? 'Unknown Division') . "\n";
        }
    }

    if (!empty(trim($announcementContent))) {
        $announcement = Announcement::where('event_id', $event->id)
            ->where('type', 'Results')
            ->first();

        if ($announcement) {
            $oldAnnouncement = $announcement->toArray();

            $announcement->update([
                'content'      => $announcementContent,
                'published_at' => now(),
                'user_id'      => auth()->id(),
                'title'        => "Results Updated: {$event->name}",
            ]);

            AuditLog::create([
                'model_type' => 'Announcement',
                'model_id'   => $announcement->id,
                'user_id'    => auth()->id(),
                'old_values' => json_encode($oldAnnouncement),
                'new_values' => json_encode($announcement->fresh()->toArray()),
                'action'     => 'Updated Results Announcement',
            ]);
        } else {
            $announcement = Announcement::create([
                'title'        => "Results Updated: {$event->name}",
                'content'      => $announcementContent,
                'type'         => 'Results',
                'event_id'     => $event->id,
                'user_id'      => auth()->id(),
                'published_at' => now(),
            ]);

            AuditLog::create([
                'model_type' => 'Announcement',
                'model_id'   => $announcement->id,
                'user_id'    => auth()->id(),
                'old_values' => null,
                'new_values' => json_encode($announcement->toArray()),
                'action'     => 'Created Results Announcement',
            ]);
        }
    }

    return back()->with('success', 'Game winners saved successfully and previous winners were preserved.');
}

public function markAwarded($id)
{
    $event = \App\Models\Event::findOrFail($id);

    // 🔒 SECURITY CHECK
    if(auth()->user()->role->name !== 'Awards Committee') {
        abort(403);
    }

    // Only allow if already finished
    if($event->status !== 'Finished') {
        return back()->with('error', 'Event must be finished first.');
    }

    $event->status = 'Finished and Awarded';
    $event->save();

    return back()->with('success', 'Event marked as Awarded.');
}

public function bulkUpload(Request $request)
{
    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt',
    ]);

    $file = fopen($request->file('csv_file')->getRealPath(), 'r');

    $header = fgetcsv($file);
    $header = array_map(fn($h) => strtolower(trim($h)), $header);

    $lastDay = null;
    $lastTime = null;

    $created = 0;

    while (($row = fgetcsv($file)) !== false) {

        $data = array_combine($header, $row);

        // -----------------------------
        // NORMALIZE INPUT
        // -----------------------------
        $day = trim($data['day'] ?? '') ?: $lastDay;
        $timeRaw = trim($data['time'] ?? '') ?: $lastTime;
        $eventName = trim($data['event'] ?? '');
        $type = trim($data['type of event'] ?? '');
        $category = ucfirst(strtolower(trim($data['category'] ?? '')));
        $gender = ucfirst(strtolower(trim($data['gender'] ?? '')));
        $venue = trim($data['venue'] ?? null);

        if ($day) $lastDay = $day;
        if ($timeRaw) $lastTime = $timeRaw;

        if (!$eventName) continue;

        // -----------------------------
        // PARSE DATE
        // -----------------------------
        try {
            $parsedDate = $day ? \Carbon\Carbon::parse($day) : null;
        } catch (\Exception $e) {
            $parsedDate = null;
        }

        // -----------------------------
        // 🧠 SMART TIME PARSER (RANGE SUPPORT)
        // -----------------------------
        $parsedTime = null;

        if ($timeRaw) {
            try {
                // Normalize text
                $cleanTime = strtolower($timeRaw);

                // remove dots (A.M → AM)
                $cleanTime = str_replace('.', '', $cleanTime);

                // replace "to" with "-"
                $cleanTime = str_replace(' to ', '-', $cleanTime);

                // split range
                $parts = preg_split('/\s*-\s*/', $cleanTime);

                // get FIRST time only
                $startTime = trim($parts[0]);

                $parsedTime = \Carbon\Carbon::parse($startTime)->format('H:i:s');

            } catch (\Exception $e) {
                $parsedTime = null;
            }
        }

        // -----------------------------
        // FIND SPORT
        // -----------------------------
        $sport = Sport::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($eventName) . '%'])->first();

        if (!$sport) {
            $sport = Sport::create([
                'name' => ucfirst($eventName),
                'division' => 'Regular'
            ]);
        }

        // -----------------------------
        // FIND SPORT TYPE
        // -----------------------------
        $sportType = $sport->sportTypes()
            ->whereRaw('LOWER(type_name) LIKE ?', ['%' . strtolower($type) . '%'])
            ->first();

        if (!$sportType && $type) {
            $sportType = \App\Models\SportType::create([
                'sport_id' => $sport->id,
                'type_name' => ucfirst($type),
            ]);
        }

        if (!$sportType) continue;

        // -----------------------------
        // CREATE EVENT
        // -----------------------------
        Event::create([
            'sport_id'      => $sport->id,
            'sport_type_id' => $sportType->id,
            'name'          => $sportType->type_name,
            'custom_name'   => $eventName,
            'status'        => 'Upcoming',
            'age_group'     => $category,
            'category'      => $gender,
            'venue'         => $venue,
            'start_date'    => $parsedDate,
            'event_time'    => $parsedTime,
        ]);

        $created++;
    }

    fclose($file);

    return back()->with('success', "Bulk upload complete! {$created} events created.");
}

}
