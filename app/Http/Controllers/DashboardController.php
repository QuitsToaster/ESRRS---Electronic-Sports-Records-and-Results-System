<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Athlete;
use App\Models\Event;
use App\Models\Sport;
use App\Models\Coach;
use App\Models\Game;

class DashboardController extends Controller
{
    public function index()
    {
        $schoolsCount = School::count();
        $athletesCount = Athlete::count();
        $eventsCount = Event::count();
        $sportsCount = Sport::count();
        $coachesCount = Coach::count();
        $gamesCount = Game::count();
        $ongoingGamesCount = Game::where('status', 'Ongoing')->count();

        // Fetch live games (Upcoming + Ongoing)
        $liveGames = Game::whereIn('status', ['Upcoming', 'Ongoing'])
                        ->orderBy('start_time', 'asc')
                        ->get();

        return view('dashboard', compact(
            'schoolsCount', 'athletesCount', 'eventsCount', 'sportsCount', 
            'coachesCount', 'gamesCount', 'ongoingGamesCount', 'liveGames'
        ));
    }
}
