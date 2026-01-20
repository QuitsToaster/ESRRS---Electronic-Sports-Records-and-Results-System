<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Event;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::with('event')->orderBy('game_time', 'asc')->get();
        return view('games.index', compact('games'));
    }

    public function create()
    {
        $events = Event::all();
        return view('games.create', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'team_a' => 'required|string',
            'team_b' => 'required|string',
            'game_time' => 'required|date',
            'venue' => 'required|string',
        ]);

        $game = Game::create($request->all());

        // Audit log for creation
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
        return view('games.edit', compact('game', 'events'));
    }

    public function update(Request $request, Game $game)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'team_a' => 'required|string',
            'team_b' => 'required|string',
            'score_a' => 'nullable|integer',
            'score_b' => 'nullable|integer',
            'status' => 'required|in:Upcoming,Ongoing,Finished',
            'game_time' => 'required|date',
            'venue' => 'required|string',
        ]);

        $oldValues = $game->toArray(); // store old data

        $game->update($request->all());

        // Audit log for update
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

        // Audit log for deletion
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
}
