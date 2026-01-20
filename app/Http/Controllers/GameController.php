<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Event;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::with('event')->get();
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

        Game::create($request->all());

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

        $game->update($request->all());

        return redirect()->route('games.index')->with('success', 'Game updated successfully!');
    }

    public function destroy(Game $game)
    {
        $game->delete();
        return redirect()->route('games.index')->with('success', 'Game deleted successfully!');
    }
}
