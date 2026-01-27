<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Event;
use App\Models\School;
use App\Models\AuditLog;
use App\Models\GameTeam;
use App\Models\Bracket;
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
        ->orderBy('round')
        ->orderBy('game_time')
        ->get();

    // Only brackets that actually have games
    $brackets = Bracket::with('event')->get();

    return view('games.index', compact('games', 'brackets'));
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

    // Count existing Round 1 games for this event
    $roundGames = Game::where('event_id', $request->event_id)
        ->where('round', 'Round 1')
        ->count();

    $game = Game::create([
        'event_id' => $request->event_id,
        'team_a'   => $request->team_a,
        'team_b'   => $request->team_b,
        'round'    => 'Round 1',
        'match_number' => $roundGames + 1, // auto-increment match number
        'status'   => 'Upcoming',
        'game_time'=> $request->game_time,
        'venue'    => $request->venue,
    ]);

    AuditLog::create([
        'model_type' => 'Game',
        'model_id' => $game->id,
        'user_id' => Auth::id(),
        'old_values' => null,
        'new_values' => json_encode($game->toArray()),
        'action' => 'Created Game: '.$game->team_a.' vs '.$game->team_b,
    ]);

    return redirect()->route('games.index')
        ->with('success', 'Game created and added to bracket!');
}



    public function edit(Game $game)
    {
        $events = Event::all();
        $schools = School::orderBy('name')->get();

        return view('games.edit', compact('game', 'events', 'schools'));
    }

    public function update(Request $request, Game $game)
{
    // 1️⃣ Validate input
    $request->validate([
        'event_id' => 'required|exists:events,id',
        'team_a'   => 'required|string|different:team_b',
        'team_b'   => 'required|string',
        'score_a'  => 'nullable|integer',
        'score_b'  => 'nullable|integer',
        'status'   => 'required|in:Upcoming,Ongoing,Finished',
        'game_time'=> 'required|date',
        'venue'    => 'required|string',
        'additional_teams.*' => 'nullable|string', // Validate additional teams
    ]);

    // 2️⃣ Save old values for audit
    $oldValues = $game->toArray();

    // 3️⃣ Update main game info
    $gameData = $request->only(['event_id', 'team_a', 'team_b', 'score_a', 'score_b', 'status', 'game_time', 'venue']);
    $game->update($gameData);

    // 4️⃣ Sync additional teams
    // Delete existing additional teams
    $game->teams()->delete();

    // Insert new ones
    if ($request->has('additional_teams')) {
        foreach ($request->additional_teams as $teamName) {
            if ($teamName) {
                \App\Models\GameTeam::create([
                    'game_id' => $game->id,
                    'team_name' => $teamName,
                ]);
            }
        }
    }

    // 5️⃣ Create audit log
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
    $event = $game->event;

    $scoringTypes = [
        'Basketball' => ['Quarter 1', 'Quarter 2', 'Quarter 3', 'Quarter 4'],
        'Volleyball' => ['Set 1', 'Set 2', 'Set 3', 'Set 4', 'Set 5'],
        'Soccer'     => ['First Half', 'Second Half'],
    ];

    $periods = $scoringTypes[$event->name] ?? ['Period 1', 'Period 2', 'Period 3', 'Period 4'];

    $scores = $game->scores ?? [
        'team_a' => [],
        'team_b' => [],
    ];

    $currentPeriodIndex = count($scores['team_a']);
    $currentPeriodIndex = min($currentPeriodIndex, count($periods) - 1);

    $currentPeriod = $periods[$currentPeriodIndex];

    $currentScoreA = $game->score_a ?? 0;
    $currentScoreB = $game->score_b ?? 0;

    return view('games.manage', compact(
        'game',
        'periods',
        'currentPeriodIndex',
        'currentPeriod',
        'currentScoreA',
        'currentScoreB'
    ));
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

    $scores = $game->scores ?? [
        'team_a' => [],
        'team_b' => [],
    ];

    $scores['team_a'][$periodIndex] = $request->score_a;
    $scores['team_b'][$periodIndex] = $request->score_b;

    $game->update([
        'scores'  => $scores,
        'score_a'=> 0, // reset live score
        'score_b'=> 0,
    ]);

    return redirect()
        ->route('games.manage', $game->id)
        ->with('success', 'Period saved. Next period started.');
}



public function liveScore(Request $request, Game $game)
{
    $game->update([
        'score_a' => $request->score_a,
        'score_b' => $request->score_b,
    ]);

    return response()->json(['success' => true]);
}

public function updateTimer(Request $request, Game $game)
{
    $request->validate([
        'game_duration' => 'required|integer|min:1',
        'shot_clock'    => 'nullable|integer|min:1',
    ]);

    $game->game_duration = $request->game_duration;
    if ($request->shot_clock) {
        $game->shot_clock = $request->shot_clock;
    }
    $game->save();

    return response()->json(['success' => true]);
}


public function updateLiveVideo(Request $request, Game $game)
{
    $request->validate([
        'live_video_url' => 'nullable|url'
    ]);

    $game->live_video_url = $request->live_video_url;
    $game->save();

    return redirect()->route('games.manage', $game->id)
                     ->with('success', 'Live video link updated successfully!');
}

// Start game (sets status to Ongoing)
public function startGame(Game $game)
{
    if ($game->status === 'Upcoming') {
        $oldValues = $game->toArray();
        $game->status = 'Ongoing';
        $game->save();

        AuditLog::create([
            'model_type' => 'Game',
            'model_id' => $game->id,
            'user_id' => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($game->toArray()),
            'action' => 'Started Game: '.$game->team_a.' vs '.$game->team_b,
        ]);
    }

    return redirect()->route('games.manage', $game->id)
                     ->with('success', 'Game started!');
}

// Set winner
public function setWinner(Request $request, Game $game)
{
    $request->validate([
        'winner' => 'required|in:'.$game->team_a.','.$game->team_b
    ]);

    $oldValues = $game->toArray();

    $game->winner = $request->winner;
    $game->save();

    $this->advanceWinner($game);

    AuditLog::create([
        'model_type' => 'Game',
        'model_id' => $game->id,
        'user_id' => Auth::id(),
        'old_values' => json_encode($oldValues),
        'new_values' => json_encode($game->toArray()),
        'action' => 'Set Winner: '.$game->winner.' for game '.$game->team_a.' vs '.$game->team_b,
    ]);

    return redirect()->route('games.manage', $game->id)
                     ->with('success', 'Winner selected: '.$game->winner);
}

private function advanceWinner(Game $game)
{
    if (!$game->winner) return;

    // Map current round to next round
    $roundMap = [
        'Round 1'      => 'Quarterfinal',
        'Quarterfinal' => 'Semifinal',
        'Semifinal'    => 'Final',
    ];

    if (!isset($roundMap[$game->round])) return;

    $nextRound = $roundMap[$game->round];

    // Winners of two consecutive matches face each other
    $nextMatchNumber = ceil($game->match_number / 2);

    // Find or create the next game for this match
    $nextGame = Game::firstOrCreate(
        [
            'event_id' => $game->event_id,
            'round'    => $nextRound,
            'match_number' => $nextMatchNumber,
        ],
        [
            'team_a' => null,
            'team_b' => null,
            'status' => 'Upcoming',
            'game_time' => now()->addDay(),
            'venue' => 'Main Court',
        ]
    );

    // Assign winner to correct slot
    if ($game->match_number % 2 === 1) {
        $nextGame->team_a = $game->winner;
    } else {
        $nextGame->team_b = $game->winner;
    }

    $nextGame->save();

    // Generate Bronze match after semifinals
    if ($game->round === 'Semifinal') {
        $this->generateBronzeMatch($game->event_id);
    }
}






private function generateBronzeMatch($eventId)
{
    $semis = Game::where('event_id', $eventId)
        ->where('round', 'Semifinal')
        ->whereNotNull('winner')
        ->get();

    if ($semis->count() !== 2) return;

    $losers = $semis->map(fn ($g) =>
        $g->winner === $g->team_a ? $g->team_b : $g->team_a
    );

    Game::firstOrCreate(
        ['event_id' => $eventId, 'round' => 'Bronze'],
        [
            'team_a' => $losers[0],
            'team_b' => $losers[1],
            'status' => 'Upcoming',
            'game_time' => now()->addDay(),
            'venue' => 'Main Court',
        ]
    );
}



public function finishGame(Game $game)
{
    // Only allow finishing if not already finished
    if ($game->status !== 'Finished') {
        $oldValues = $game->toArray();

        $game->status = 'Finished';
        $game->save();

        $this->generateBronzeMatch($game->event_id);

        AuditLog::create([
            'model_type' => 'Game',
            'model_id' => $game->id,
            'user_id' => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($game->toArray()),
            'action' => 'Finished Game: '.$game->team_a.' vs '.$game->team_b,
        ]);
    }

    return redirect()->route('games.manage', $game->id)
                     ->with('success', 'Game marked as Finished!');
}



}