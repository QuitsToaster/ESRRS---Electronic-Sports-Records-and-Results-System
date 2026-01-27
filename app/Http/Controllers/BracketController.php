<?php

namespace App\Http\Controllers;

use App\Models\Bracket;
use App\Models\Event;
use Illuminate\Http\Request;

class BracketController extends Controller
{
    public function create()
    {
        $events = Event::all();
        return view('games.partials.create_bracket', compact('events'));
    }

    public function store(Request $request)
{
    $request->validate([
        'event_id' => 'required|exists:events,id',
        'teams' => 'required|array|min:2', // At least 2 teams
        'teams.*' => 'required|string',
    ]);

    $teams = $request->teams;

    // Build matches (pairs of teams)
    $matches = [];
    for ($i = 0; $i < count($teams); $i += 2) {
        $matches[] = [
            'team_a' => $teams[$i],
            'team_b' => $teams[$i + 1] ?? null, // if odd, last team gets null
        ];
    }

    Bracket::create([
        'event_id' => $request->event_id,
        'structure' => json_encode($matches),
    ]);

    return redirect()->route('games.index')->with('success', 'Bracket created successfully!');
}

}
