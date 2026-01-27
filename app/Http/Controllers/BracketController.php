<?php

namespace App\Http\Controllers;

use App\Models\Bracket;
use App\Models\Event;
use App\Models\Game;
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
            'teams'    => 'required|array|min:2',
            'teams.*'  => 'required|string',
        ]);

        $teams = array_values(array_filter($request->teams));

        // 1️⃣ Build Round 1 matches
        $matches = [];
        for ($i = 0; $i < count($teams); $i += 2) {
            if (isset($teams[$i + 1])) {
                $matches[] = [
                    'team_a' => $teams[$i],
                    'team_b' => $teams[$i + 1],
                ];
            }
        }

        // 2️⃣ Save bracket structure
        Bracket::create([
            'event_id' => $request->event_id,
            'structure' => json_encode($matches),
        ]);

        // 3️⃣ Create games WITH ROUND
        foreach ($matches as $match) {
            Game::create([
                'event_id' => $request->event_id,
                'team_a'   => $match['team_a'],
                'team_b'   => $match['team_b'],
                'round'    => 'Round 1', // ✅ REQUIRED
                'status'   => 'Upcoming',
                'game_time'=> now()->addDay(),
                'venue'    => 'Main Court',
            ]);
        }

        return redirect()
            ->route('games.index')
            ->with('success', 'Bracket created and games added to Round 1!');
    }
}
