<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Athlete;
use App\Models\Event;
use App\Models\Sport;
use App\Models\Coach;
use App\Models\User;
use App\Models\Game;
use App\Models\Official;
use App\Models\Announcement;
use App\Models\Division;
use App\Models\Result; 

class DashboardController extends Controller
{
    public function index()
{
    $athletesCount = Athlete::count();
    $eventsCount = Event::count();
    $sportsCount = Sport::count();
    $coachesCount = Coach::count();
    $gamesCount = Game::count();
    $divisionsCount = Division::count();
    $usersCount = User::count();

    // Live Games (Upcoming + Ongoing) - LIMIT 3
    $liveGames = Game::whereIn('status', ['Upcoming', 'Ongoing', 'Finished'])
                     ->orderBy('game_time', 'asc')
                     ->take(3)
                     ->get();

    // Medal Tally per Division using results_divisions table
    $medalTally = Division::withCount([
        'resultsDivisions as gold' => function ($query) {
            $query->where('medal', 'Gold');
        },
        'resultsDivisions as silver' => function ($query) {
            $query->where('medal', 'Silver');
        },
        'resultsDivisions as bronze' => function ($query) {
            $query->where('medal', 'Bronze');
        },
    ])->get()
    ->map(function ($division) {
        $division->total = $division->gold + $division->silver + $division->bronze;
        return $division;
    })
    ->sort(function ($a, $b) {
        // Sort by Gold > Silver > Bronze
        if ($b->gold !== $a->gold) {
            return $b->gold <=> $a->gold;
        }
        if ($b->silver !== $a->silver) {
            return $b->silver <=> $a->silver;
        }
        return $b->bronze <=> $a->bronze;
    })
    ->values()
    ->map(function ($division, $index) {
        $division->rank = $index + 1;
        return $division;
    });

    // Latest 3 Announcements
    $latestAnnouncements = Announcement::with('event', 'user')
                                        ->latest('published_at')
                                        ->take(3)
                                        ->get();

                                        // ================= EVENT STATUS COUNTS =================
$eventStatusCounts = Event::select('status', \DB::raw('COUNT(*) as total'))
    ->whereIn('status', ['Upcoming', 'Ongoing', 'Finished', 'Delayed'])
    ->groupBy('status')
    ->pluck('total', 'status');

// Ensure all statuses exist (default 0)
$eventStatusCounts = collect([
    'Upcoming' => $eventStatusCounts['Upcoming'] ?? 0,
    'Ongoing'  => $eventStatusCounts['Ongoing'] ?? 0,
    'Finished' => $eventStatusCounts['Finished'] ?? 0,
    'Delayed'  => $eventStatusCounts['Delayed'] ?? 0,
]);

    return view('dashboard', compact(
        'athletesCount', 'eventsCount', 'sportsCount',
        'coachesCount', 'gamesCount', 'divisionsCount', 'liveGames',
        'medalTally', 'latestAnnouncements','eventStatusCounts','usersCount'
    ));
}
}
