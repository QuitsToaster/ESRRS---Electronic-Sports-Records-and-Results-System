<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Athlete;
use App\Models\Event;
use App\Models\Sport;
use App\Models\Coach;
use App\Models\Game;
use App\Models\Official;
use App\Models\Announcement;
use App\Models\Division;
use App\Models\Result; 

class DashboardController extends Controller
{
    public function index()
    {
        $schoolsCount = School::count();
        $athletesCount = Athlete::count();
        $eventsCount = Event::count();
        $officialsCount = Official::count();
        $sportsCount = Sport::count();
        $coachesCount = Coach::count();
        $gamesCount = Game::count();
        $divisionsCount = Division::count();

        // ✅ Correct column: use 'status' for ongoing games
        $ongoingGamesCount = Game::where('status', 'Ongoing')->count();

        // Live Games (Upcoming + Ongoing) - LIMIT 3
$liveGames = Game::whereIn('status', ['Upcoming', 'Ongoing', 'Finished'])
                 ->orderBy('game_time', 'asc')
                 ->take(3) // <- limit to first 3 games
                 ->get();


        // Medal Tally per school
        $medalTally = School::withCount([
            'results as gold' => function ($query) { $query->where('medal', 'Gold'); },
            'results as silver' => function ($query) { $query->where('medal', 'Silver'); },
            'results as bronze' => function ($query) { $query->where('medal', 'Bronze'); },
        ])->get()
        ->map(function ($school) {
            $school->total = $school->gold + $school->silver + $school->bronze;
            return $school;
        })
        ->sort(function ($a, $b) {
            // Sort by Gold first, then Silver, then Bronze
            if ($b->gold !== $a->gold) {
                return $b->gold <=> $a->gold;
            }
            if ($b->silver !== $a->silver) {
                return $b->silver <=> $a->silver;
            }
            return $b->bronze <=> $a->bronze;
        })
        ->values()
        ->map(function ($school, $index) {
            $school->rank = $index + 1;
            return $school;
        });

        // Latest 2 Announcements
        $latestAnnouncements = Announcement::with('event', 'user')
                                    ->latest('published_at')
                                    ->take(3)
                                    ->get();

        return view('dashboard', compact(
            'schoolsCount', 'athletesCount', 'eventsCount', 'sportsCount',
            'coachesCount', 'officialsCount', 'gamesCount', 'ongoingGamesCount', 'liveGames',
            'medalTally', 'latestAnnouncements', 'divisionsCount'
        ));
    }
}
