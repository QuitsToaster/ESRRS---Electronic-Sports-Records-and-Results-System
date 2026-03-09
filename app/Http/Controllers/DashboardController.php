<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Athlete;
use App\Models\Event;
use App\Models\SportType;
use App\Models\Sport;
use App\Models\Coach;
use App\Models\User;
use App\Models\Game;
use App\Models\AllAwardResult;
use App\Models\IndividualAwardResult;
use App\Models\Official;
use App\Models\Announcement;
use App\Models\Division;
use App\Models\Result; 
use Barryvdh\DomPDF\Facade\Pdf;

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

/* =========================================================
 | TOURNAMENT SECRETARIES WITH NEEDS REVISION AWARDS
 ========================================================= */

$secretaryRevisionList = User::whereHas('role', function($q){
        $q->where('name','Tournament Secretary');
    })
    ->whereHas('allAwards', function($q){
        $q->where('status','needs_revision');
    })
    ->orWhereHas('individualAwards', function($q){
        $q->where('status','needs_revision');
    })
    ->withCount([
        'allAwards as all_awards_revision_count' => function($q){
            $q->where('status','needs_revision');
        },
        'individualAwards as individual_awards_revision_count' => function($q){
            $q->where('status','needs_revision');
        }
    ])
    ->get();

    /* =========================================================
 | TOURNAMENT SECRETARIES WHO HAVEN'T AWARDED YET
 ========================================================= */

$secretariesNoAwards = User::whereHas('role', function ($q) {
        $q->where('name', 'Tournament Secretary');
    })
    ->whereDoesntHave('allAwards')
    ->whereDoesntHave('individualAwards')
    ->get();

    $sports = Sport::all();
$sportTypes = \App\Models\SportType::all();

/* =========================================================
 | TOURNAMENT SECRETARIES WHO FORWARDED AWARDS (PENDING)
 ========================================================= */

$secretariesPendingAwards = User::whereHas('role', function ($q) {
        $q->where('name', 'Tournament Secretary');
    })
    ->where(function ($q) {
        $q->whereHas('allAwards', function ($sub) {
            $sub->where('status', 'pending');
        })
        ->orWhereHas('individualAwards', function ($sub) {
            $sub->where('status', 'pending');
        });
    })
    ->withCount([
        'allAwards as all_awards_pending_count' => function ($q) {
            $q->where('status', 'pending');
        },
        'individualAwards as individual_awards_pending_count' => function ($q) {
            $q->where('status', 'pending');
        }
    ])
    ->get();

    /* =========================================================
 | AWARD STATUS COUNTS (ALL AWARDS)
 ========================================================= */

$allAwardsPending = AllAwardResult::where('status','pending')->count();
$allAwardsApproved = AllAwardResult::where('status','approved')->count();
$allAwardsRevision = AllAwardResult::where('status','needs_revision')->count();

/* =========================================================
 | AWARD STATUS COUNTS (INDIVIDUAL AWARDS)
 ========================================================= */

$individualAwardsPending = IndividualAwardResult::where('status','pending')->count();
$individualAwardsApproved = IndividualAwardResult::where('status','approved')->count();
$individualAwardsRevision = IndividualAwardResult::where('status','needs_revision')->count();

    return view('dashboard', compact(
    'athletesCount','eventsCount','sportsCount',
    'coachesCount','gamesCount','divisionsCount','liveGames',
    'medalTally','latestAnnouncements','eventStatusCounts','usersCount',
    'secretaryRevisionList','secretariesNoAwards','secretariesPendingAwards',
    'sports','sportTypes',

    // NEW
    'allAwardsPending','allAwardsApproved','allAwardsRevision',
    'individualAwardsPending','individualAwardsApproved','individualAwardsRevision'
));
}

public function exportSecretariesPdf($type)
{
    if ($type == 'pending') {

        $data = User::whereHas('role', fn($q) => $q->where('name', 'Tournament Secretary'))

            ->where(function($q){
                $q->whereHas('allAwards', fn($q)=>$q->where('status','pending'))
                  ->orWhereHas('individualAwards', fn($q)=>$q->where('status','pending'));
            })

            ->withCount([
                'allAwards as all_awards_pending_count' => fn($q)=>$q->where('status','pending'),
                'individualAwards as individual_awards_pending_count' => fn($q)=>$q->where('status','pending')
            ])

            ->get();

        $title = "Pending Award Submissions";
    }

    elseif ($type == 'revision') {

        $data = User::whereHas('role', fn($q)=>$q->where('name','Tournament Secretary'))

            ->where(function($q){
                $q->whereHas('allAwards', fn($q)=>$q->where('status','needs_revision'))
                  ->orWhereHas('individualAwards', fn($q)=>$q->where('status','needs_revision'));
            })

            ->withCount([
                'allAwards as all_awards_revision_count' => fn($q)=>$q->where('status','needs_revision'),
                'individualAwards as individual_awards_revision_count' => fn($q)=>$q->where('status','needs_revision')
            ])

            ->get();

        $title = "Awards Needing Revision";
    }

    else {

        $data = User::whereHas('role', fn($q)=>$q->where('name','Tournament Secretary'))
            ->whereDoesntHave('allAwards')
            ->whereDoesntHave('individualAwards')
            ->get();

        $title = "Secretaries Without Submissions";
    }

    $sports = Sport::all();
    $sportTypes = SportType::all();

    $pdf = Pdf::loadView(
        'pdf.secretaries-report',
        compact('data','title','sports','sportTypes')
    )->setPaper('letter','portrait');

    return $pdf->download('secretaries-report.pdf');
}
}
