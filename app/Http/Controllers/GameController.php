<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Event;
use App\Models\School;
use App\Models\AuditLog;
use App\Models\GameTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
    $divisions = \App\Models\Division::orderBy('name')->get(); // <-- Changed

    return view('games.create', compact('events', 'divisions'));
}

    public function store(Request $request)
    {
        $request->validate([
            'event_id'  => 'required|exists:events,id',
            'team_a'    => 'required|string|different:team_b',
            'team_b'    => 'required|string',
            'game_time' => 'required|date',
            'venue'     => 'required|string',
        ]);

        $game = Game::create([
            'event_id'  => $request->event_id,
            'team_a'    => $request->team_a,
            'team_b'    => $request->team_b,
            'status'    => 'Upcoming',
            'game_time' => $request->game_time,
            'venue'     => $request->venue,
        ]);

        AuditLog::create([
            'model_type' => 'Game',
            'model_id'   => $game->id,
            'user_id'    => Auth::id(),
            'old_values' => null,
            'new_values' => json_encode($game->toArray()),
            'action'     => 'Created Game: '.$game->team_a.' vs '.$game->team_b,
        ]);

        return redirect()->route('games.index')
            ->with('success', 'Game created successfully!');
    }

   public function edit(Game $game)
{
    $events = Event::all();
    $divisions = \App\Models\Division::orderBy('name')->get(); // <-- Changed

    return view('games.edit', compact('game', 'events', 'divisions'));
}


    public function update(Request $request, Game $game)
    {
        $request->validate([
            'event_id'  => 'required|exists:events,id',
            'team_a'    => 'required|string|different:team_b',
            'team_b'    => 'required|string',
            'score_a'   => 'nullable|integer',
            'score_b'   => 'nullable|integer',
            'status'    => 'required|in:Upcoming,Ongoing,Finished',
            'game_time' => 'required|date',
            'venue'     => 'required|string',
            'additional_teams.*' => 'nullable|string',
        ]);

        $oldValues = $game->toArray();

        $game->update(
            $request->only([
                'event_id',
                'team_a',
                'team_b',
                'score_a',
                'score_b',
                'status',
                'game_time',
                'venue',
            ])
        );

        // Sync additional teams
        $game->teams()->delete();

        if ($request->has('additional_teams')) {
            foreach ($request->additional_teams as $teamName) {
                if ($teamName) {
                    GameTeam::create([
                        'game_id'   => $game->id,
                        'team_name' => $teamName,
                    ]);
                }
            }
        }

        AuditLog::create([
            'model_type' => 'Game',
            'model_id'   => $game->id,
            'user_id'    => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($game->toArray()),
            'action'     => 'Updated Game: '.$game->team_a.' vs '.$game->team_b,
        ]);

        return redirect()->route('games.index')
            ->with('success', 'Game updated successfully!');
    }

    public function destroy(Game $game)
    {
        $oldValues = $game->toArray();
        $game->delete();

        AuditLog::create([
            'model_type' => 'Game',
            'model_id'   => $game->id,
            'user_id'    => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => null,
            'action'     => 'Deleted Game: '.$game->team_a.' vs '.$game->team_b,
        ]);

        return redirect()->route('games.index')
            ->with('success', 'Game deleted successfully!');
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

        return view('games.manage', compact(
            'game',
            'periods'
        ));
    }

    public function updateScore(Request $request, Game $game)
    {
        $request->validate([
            'scores_a'   => 'required|array',
            'scores_b'   => 'required|array',
            'scores_a.*' => 'nullable|integer|min:0',
            'scores_b.*' => 'nullable|integer|min:0',
        ]);

        $game->scores = [
            'team_a' => $request->scores_a,
            'team_b' => $request->scores_b,
        ];

        $game->score_a = array_sum($request->scores_a);
        $game->score_b = array_sum($request->scores_b);
        $game->save();

        return back()->with('success', 'Scores updated successfully!');
    }

    public function startGame(Game $game)
    {
        if ($game->status === 'Upcoming') {
            $oldValues = $game->toArray();
            $game->status = 'Ongoing';
            $game->save();

            AuditLog::create([
                'model_type' => 'Game',
                'model_id'   => $game->id,
                'user_id'    => Auth::id(),
                'old_values' => json_encode($oldValues),
                'new_values' => json_encode($game->toArray()),
                'action'     => 'Started Game: '.$game->team_a.' vs '.$game->team_b,
            ]);
        }

        return back()->with('success', 'Game started!');
    }

   // ================= LIVE SCORE (CURRENT PERIOD ONLY) =================
public function liveScore(Request $request, Game $game)
{
    $request->validate([
        'score_a' => 'required|integer|min:0',
        'score_b' => 'required|integer|min:0',
    ]);

    // Store LIVE score only (NOT total)
    $game->update([
        'score_a' => $request->score_a,
        'score_b' => $request->score_b,
    ]);

    return response()->json(['success' => true]);
}


// ================= FINISH PERIOD =================
public function updatePeriodScore(Request $request, Game $game, $periodIndex)
{
    $request->validate([
        'score_a' => 'required|integer|min:0',
        'score_b' => 'required|integer|min:0',
    ]);

    $scores = $game->scores ?? ['team_a' => [], 'team_b' => []];

    // Save finished period
    $scores['team_a'][$periodIndex] = $request->score_a;
    $scores['team_b'][$periodIndex] = $request->score_b;

    // Save summary ONLY
    $game->scores = $scores;

    // 🔥 RESET LIVE SCORE AFTER PERIOD
    $game->score_a = 0;
    $game->score_b = 0;

    $game->save();

    return back()->with('success', 'Period finished. Scores locked.');
}

public function setWinner(Request $request, Game $game)
{
    $request->validate([
        'winner' => 'required|in:team_a,team_b'
    ]);

    $oldValues = $game->toArray();

    $winnerName = $request->winner === 'team_a'
        ? $game->team_a
        : $game->team_b;

    $game->winner = $winnerName;
    $game->status = 'Finished';
    $game->save();

    AuditLog::create([
        'model_type' => 'Game',
        'model_id'   => $game->id,
        'user_id'    => Auth::id(),
        'old_values' => json_encode($oldValues),
        'new_values' => json_encode($game->toArray()),
        'action'     => 'Set Winner: '.$winnerName.' ('.$game->team_a.' vs '.$game->team_b.')',
    ]);

    return back()->with('success', 'Winner set successfully!');
}


public function updateLiveVideo(Request $request, Game $game)
{
    $request->validate([
        'live_video_url' => 'nullable|url'
    ]);

    $oldValues = $game->toArray();

    $game->update([
        'live_video_url' => $request->live_video_url
    ]);

    AuditLog::create([
        'model_type' => 'Game',
        'model_id'   => $game->id,
        'user_id'    => Auth::id(),
        'old_values' => json_encode($oldValues),
        'new_values' => json_encode($game->toArray()),
        'action'     => 'Updated Live Video URL for '.$game->team_a.' vs '.$game->team_b,
    ]);

    return back()->with('success', 'Live video link updated successfully!');
}


    public function finishGame(Game $game)
    {
        if ($game->status !== 'Finished') {
            $oldValues = $game->toArray();
            $game->status = 'Finished';
            $game->save();

            AuditLog::create([
                'model_type' => 'Game',
                'model_id'   => $game->id,
                'user_id'    => Auth::id(),
                'old_values' => json_encode($oldValues),
                'new_values' => json_encode($game->toArray()),
                'action'     => 'Finished Game: '.$game->team_a.' vs '.$game->team_b,
            ]);
        }

        return back()->with('success', 'Game marked as finished!');
    }
}
