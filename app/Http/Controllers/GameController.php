<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Event;
use App\Models\School;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $games = Game::with('event')
            ->when($search, function ($query, $search) {
                $query->whereHas('event', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('team_a', 'like', "%{$search}%")
                    ->orWhere('team_b', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('venue', 'like', "%{$search}%");
            })
            ->orderBy('game_time')
            ->get();

        return view('games.index', compact('games'));
    }

    public function create()
    {
        $events = Event::all();
        $schools = School::orderBy('name')->get();

        return view('games.create', compact('events', 'schools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'team_a'   => 'required|string|different:team_b',
            'team_b'   => 'required|string',
            'game_time'=> 'required|date',
            'venue'    => 'required|string',
        ]);

        $game = Game::create($request->all());

        AuditLog::create([
            'model_type' => 'Game',
            'model_id' => $game->id,
            'user_id' => Auth::id(),
            'old_values' => null,
            'new_values' => json_encode($game->toArray()),
            'action' => 'Created Game: '.$game->team_a.' vs '.$game->team_b,
        ]);

        return redirect()->route('games.index')->with('success', 'Game created successfully!');
    }

    public function edit(Game $game)
    {
        $events = Event::all();
        $schools = School::orderBy('name')->get();

        return view('games.edit', compact('game', 'events', 'schools'));
    }

    public function update(Request $request, Game $game)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'team_a'   => 'required|string|different:team_b',
            'team_b'   => 'required|string',
            'score_a'  => 'nullable|integer',
            'score_b'  => 'nullable|integer',
            'status'   => 'required|in:Upcoming,Ongoing,Finished',
            'game_time'=> 'required|date',
            'venue'    => 'required|string',
        ]);

        $oldValues = $game->toArray();
        $game->update($request->all());

        AuditLog::create([
            'model_type' => 'Game',
            'model_id' => $game->id,
            'user_id' => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($game->toArray()),
            'action' => 'Updated Game: '.$game->team_a.' vs '.$game->team_b,
        ]);

        return redirect()->route('games.index')->with('success', 'Game updated successfully!');
    }

    public function destroy(Game $game)
    {
        $oldValues = $game->toArray();
        $game->delete();

        AuditLog::create([
            'model_type' => 'Game',
            'model_id' => $game->id,
            'user_id' => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => null,
            'action' => 'Deleted Game: '.$game->team_a.' vs '.$game->team_b,
        ]);

        return redirect()->route('games.index')->with('success', 'Game deleted successfully!');
    }

    public function manage(Game $game)
{
    // Determine the scoring type based on event/sport
    $event = $game->event;
    $scoringTypes = [
        'Basketball' => ['Quarter 1', 'Quarter 2', 'Quarter 3', 'Quarter 4'],
        'Volleyball'  => ['Set 1', 'Set 2', 'Set 3', 'Set 4', 'Set 5'],
        'Soccer'      => ['First Half', 'Second Half'],
        // Add more sports here
    ];

    $periods = $scoringTypes[$event->name] ?? ['Period 1', 'Period 2', 'Period 3', 'Period 4'];

    return view('games.manage', compact('game', 'periods'));
}

public function updateScore(Request $request, Game $game)
{
    $request->validate([
        'scores_a' => 'required|array',
        'scores_b' => 'required|array',
        'scores_a.*' => 'nullable|integer|min:0',
        'scores_b.*' => 'nullable|integer|min:0',
    ]);

    // Save scores as JSON
    $game->scores = json_encode([
        'team_a' => $request->scores_a,
        'team_b' => $request->scores_b
    ]);

    // Optional: calculate total score for quick display
    $game->score_a = array_sum($request->scores_a);
    $game->score_b = array_sum($request->scores_b);

    $game->save();

    return redirect()->route('games.manage', $game->id)->with('success', 'Scores updated successfully!');
}

public function updatePeriodScore(Request $request, Game $game, $periodIndex)
{
    $request->validate([
        'score_a' => 'required|integer|min:0',
        'score_b' => 'required|integer|min:0',
    ]);

    // Decode existing scores or initialize
    $scores = json_decode($game->scores, true) ?? ['team_a' => [], 'team_b' => []];

    // Update specific period
    $scores['team_a'][$periodIndex] = $request->score_a;
    $scores['team_b'][$periodIndex] = $request->score_b;

    // Save back to DB
    $game->scores = json_encode($scores);
    $game->score_a = array_sum($scores['team_a']);
    $game->score_b = array_sum($scores['team_b']);
    $game->save();

    return redirect()->route('games.manage', $game->id)
                     ->with('success', "Period " . ($periodIndex + 1) . " scores updated successfully!");
}

public function liveScore(Request $request, Game $game)
{
    $game->update([
        'score_a' => $request->score_a,
        'score_b' => $request->score_b,
    ]);

    return response()->json(['success' => true]);
}


}
