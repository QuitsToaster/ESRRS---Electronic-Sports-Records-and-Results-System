<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ViewerFeedback;
use App\Models\Event;
use App\Models\Banner;
use App\Models\GalleryPhoto;
use App\Models\Game;
use App\Models\School;
use App\Models\Division; 
use App\Models\AllAwardResult;
use App\Models\IndividualAwardResult;
use App\Models\ResultDivision;
use App\Models\Sport;
use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Announcement;

class ViewerController extends Controller
{
public function home()
{
    // ================= EVENTS =================
    $allUpcomingEvents = Event::with('sport')->orderBy('start_date')->get();
    $latestUpcomingEvents = Event::with('sport')->orderBy('start_date')->take(3)->get();

    // ================= GAMES =================
    $allUpcomingGames = Game::with('event')->where('status', 'Upcoming')->orderBy('game_time')->get();
    $latestUpcomingGames = Game::with('event')->where('status', 'Upcoming')->orderBy('game_time')->take(2)->get();
    $liveGames = Game::with('event')->where('status', 'Ongoing')->orderBy('game_time')->get();

    // ================= ANNOUNCEMENTS =================
    $allAnnouncements = Announcement::latest()->get();
    $latestAnnouncements = Announcement::latest()->take(2)->get();

    // ================= MEDAL TALLY =================
    $medalTally = \App\Models\Division::withCount([
        'resultsDivisions as gold'   => fn ($q) => $q->where('medal', 'Gold')->whereHas('sport', fn($s) => $s->where('type','regular')),
        'resultsDivisions as silver' => fn ($q) => $q->where('medal', 'Silver')->whereHas('sport', fn($s) => $s->where('type','regular')),
        'resultsDivisions as bronze' => fn ($q) => $q->where('medal', 'Bronze')->whereHas('sport', fn($s) => $s->where('type','regular')),
    ])->get();

    $medalTallyPara = \App\Models\Division::withCount([
        'resultsDivisions as gold'   => fn ($q) => $q->where('medal', 'Gold')->whereHas('sport', fn($s) => $s->where('type','para')),
        'resultsDivisions as silver' => fn ($q) => $q->where('medal', 'Silver')->whereHas('sport', fn($s) => $s->where('type','para')),
        'resultsDivisions as bronze' => fn ($q) => $q->where('medal', 'Bronze')->whereHas('sport', fn($s) => $s->where('type','para')),
    ])->get();

    // Transform results
    foreach ([$medalTally, $medalTallyPara] as $collection) {
        $collection->transform(function ($division) {
            $division->total = $division->gold + $division->silver + $division->bronze;
            $division->results = $division->resultsDivisions()
    ->with([
        'sport',
        'sportType', // ✅ ADD THIS
        'event' => fn ($q) => $q->select(
            'id', 'name', 'age_group', 'category', 'award_date', 'start_date'
        )
    ])->get();

            return $division;
        });
        $collection = $collection->sortByDesc(fn ($d) => [$d->total, $d->gold, $d->silver, $d->bronze])->values();
    }

    $banners = Banner::orderBy('order')->get();

    return view('viewer.home', compact(
        'allUpcomingEvents',
        'latestUpcomingEvents',
        'allUpcomingGames',
        'latestUpcomingGames',
        'liveGames',
        'allAnnouncements',
        'latestAnnouncements',
        'medalTally',
        'medalTallyPara',
        'banners'
    ));
}











   public function participants(Request $request)
{
    $search = $request->input('search');

    $divisions = \App\Models\Division::query()
        ->when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%");
        })
        ->orderBy('name')
        ->get();

    return view('viewer.participants', compact('divisions', 'search'));
}



public function participantShow(Division $division, Request $request)
{
    /* =========================================================
       TEAM AWARDS (all_award_results)
    ========================================================== */

    $teamRegular = AllAwardResult::with(['sport', 'event'])
        ->where('sdo', $division->name)
        ->whereIn('division', ['Elementary', 'Secondary'])
        ->get();

    $teamParagames = AllAwardResult::with(['sport', 'event'])
        ->where('sdo', $division->name)
        ->where('division', 'Paragames')
        ->get();


    /* =========================================================
   INDIVIDUAL AWARDS (individual_award_results)
========================================================== */

// Regular Individual Awards filtered by SDO
$individualRegular = IndividualAwardResult::with(['sport','event','athlete'])
    ->whereIn('division', ['Elementary','Secondary'])
    ->where('sdo', $division->name)
    ->get();

// Paragames Individual Awards filtered by SDO
$individualParagames = IndividualAwardResult::with(['sport','event','athlete'])
    ->where('division', 'Paragames')
    ->where('sdo', $division->name)
    ->get();


    /* =========================================================
       MERGED TEAM RESULTS
    ========================================================== */

    $regularTeamAwards = $teamRegular->map(fn ($r) => (object) [
        'medal'  => $r->medal,
        'event'  => $r->event,
        'sport'  => $r->sport,
        'date'   => $r->created_at,
    ]);

    $paragamesTeamAwards = $teamParagames->map(fn ($r) => (object) [
        'medal'  => $r->medal,
        'event'  => $r->event,
        'sport'  => $r->sport,
        'date'   => $r->created_at,
    ]);


    /* =========================================================
       MEDAL COUNTS (TEAM + INDIVIDUAL)
    ========================================================== */

    $regularCounts = [
        'Gold'   => $teamRegular->where('medal','Gold')->count()
                    + $individualRegular->where('medal','Gold')->count(),
        'Silver' => $teamRegular->where('medal','Silver')->count()
                    + $individualRegular->where('medal','Silver')->count(),
        'Bronze' => $teamRegular->where('medal','Bronze')->count()
                    + $individualRegular->where('medal','Bronze')->count(),
    ];

    $paragamesCounts = [
        'Gold'   => $teamParagames->where('medal','Gold')->count()
                    + $individualParagames->where('medal','Gold')->count(),
        'Silver' => $teamParagames->where('medal','Silver')->count()
                    + $individualParagames->where('medal','Silver')->count(),
        'Bronze' => $teamParagames->where('medal','Bronze')->count()
                    + $individualParagames->where('medal','Bronze')->count(),
    ];


    return view('viewer.participants-show', compact(
        'division',
        'regularTeamAwards',
        'paragamesTeamAwards',
        'individualRegular',
        'individualParagames',
        'regularCounts',
        'paragamesCounts'
    ));
}




 public function events(Request $request)
{
    $search = $request->input('search');
    $status = $request->input('status');

    /*
    |--------------------------------------------------------------------------
    | BASE QUERY
    |--------------------------------------------------------------------------
    */
    $baseQuery = \App\Models\Event::with([
        'divisions',
        'goldWinner',
        'silverWinner',
        'bronzeWinner',
        'sport'
    ]);

    /*
    |--------------------------------------------------------------------------
    | APPLY FILTERS
    |--------------------------------------------------------------------------
    */
    $filteredQuery = (clone $baseQuery)
        ->when($search, fn ($q) =>
            $q->where('name', 'like', "%{$search}%")
        )
        ->when($status, fn ($q) =>
            $q->where('status', $status)
        );

    /*
    |--------------------------------------------------------------------------
    | SINGLE COUNT + LABEL
    |--------------------------------------------------------------------------
    */
    $eventsCount = $filteredQuery->count();

    // Dynamic label
    if ($status) {
        $eventsLabel = ucfirst($status) . ' Events';
    } elseif ($search) {
        $eventsLabel = 'Search Results';
    } else {
        $eventsLabel = 'Total Events';
    }

    /*
    |--------------------------------------------------------------------------
    | FETCH EVENTS
    |--------------------------------------------------------------------------
    */
    $events = $filteredQuery
        ->orderBy('start_date')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | EVENTS JSON
    |--------------------------------------------------------------------------
    */
    $eventsJson = $events->map(function ($event) {

        $resolvedGameWinners = [];

        if (is_array($event->game_winners)) {
            foreach ($event->game_winners as $index => $divisionId) {
                $division = $event->divisions->firstWhere('id', $divisionId);
                $resolvedGameWinners[$index] = $division?->name;
            }
        }

        return [
            'id'           => $event->id,
            'name'         => $event->name,
            'start_date'   => $event->start_date?->toDateString(),
            'venue'        => $event->venue,
            'age_group'    => $event->age_group,
            'category'     => $event->category,
            'games'        => $event->games ?? [],
            'game_winners' => $resolvedGameWinners,
            'sport'        => $event->sport ? ['name' => $event->sport->name] : null,
            'goldWinner'   => $event->goldWinner ? ['name' => $event->goldWinner->name] : null,
            'silverWinner' => $event->silverWinner ? ['name' => $event->silverWinner->name] : null,
            'bronzeWinner' => $event->bronzeWinner ? ['name' => $event->bronzeWinner->name] : null,
        ];
    });

    $gallery_photos = \App\Models\GalleryPhoto::latest()->get();

    return view('viewer.events', compact(
        'events',
        'eventsJson',
        'gallery_photos',
        'eventsCount',
        'eventsLabel'
    ));
}




    // Games Page with search
public function games(Request $request)
{
    $search = $request->input('search');

    $games = Game::with('event')
        ->when($search, function($query, $search) {
            $query->where('team_a', 'like', "%{$search}%")
                  ->orWhere('team_b', 'like', "%{$search}%")
                  ->orWhereHas('event', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        })
        ->orderBy('game_time', 'asc')
        ->get();

    return view('viewer.games', compact('games'));
}

public function gameModal(Game $game)
{
    $schoolA = School::where('name', $game->team_a)->first();
    $schoolB = School::where('name', $game->team_b)->first();

    $athletesA = Athlete::where('school_id', $schoolA?->id)->get();
    $athletesB = Athlete::where('school_id', $schoolB?->id)->get();

    $coachesA = Coach::where('school_id', $schoolA?->id)->get();
    $coachesB = Coach::where('school_id', $schoolB?->id)->get();

    $eventName = strtolower(trim($game->event->name));

    $periods = match($eventName) {
        'basketball' => ['Q1','Q2','Q3','Q4'],
        'volleyball' => ['Set 1','Set 2','Set 3','Set 4','Set 5'],
        default => ['Period 1','Period 2','Period 3','Period 4'] // give 4 by default
    };

    return view('viewer.game-modal', compact(
        'game','periods',
        'athletesA','athletesB',
        'coachesA','coachesB'
    ));
}


public function liveGame(Game $game)
{
    return response()->json([
        'score_a' => $game->score_a ?? 0,
        'score_b' => $game->score_b ?? 0,
    ]);
}

public function liveScores()
{
    return Game::select('id', 'score_a', 'score_b')->get();
}

public function livePeriodScores(Game $game)
{
    // Reload the game fresh to get updated scores
    $game = Game::find($game->id);

    $scores = is_array($game->scores)
        ? $game->scores
        : (is_string($game->scores) ? json_decode($game->scores, true) : []);

    return response()->json([
        'team_a' => $scores['team_a'] ?? [],
        'team_b' => $scores['team_b'] ?? [],
        'total_a' => array_sum($scores['team_a'] ?? []),
        'total_b' => array_sum($scores['team_b'] ?? []),
    ]);
}



public function gallery(Request $request)
{
    $search = $request->input('search');

    $photos = GalleryPhoto::when($search, function($query, $search) {
        $query->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('folder', 'like', "%{$search}%");
    })
    ->latest()
    ->get();

    return view('viewer.gallery', compact('photos'));
}






    public function announcements(Request $request)
{
    $search = $request->input('search');
    $type   = $request->input('type');

    $announcements = Announcement::with('event')
        ->when($search, function($query, $search) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('event', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        })
        ->when($type, function($query, $type) {
            $query->where('type', $type);
        })
        ->latest()
        ->get();

    return view('viewer.announcements', compact('announcements'));
}

     // Feedback Page
    public function feedbacks()
    {
        return view('viewer.feedbacks');
    }

    // Send Feedback
    public function sendFeedback(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // Send email to Bryan
        Mail::to('bryanlloydtan@gmail.com')->send(new ViewerFeedback($request->all()));

        return back()->with('success', 'Thank you for your feedback! We will review it shortly.');
    }

    // Display the QR attendance page
    public function attendancePage()
    {
        return view('viewer.attendance');
    }

    // Handle the scanned QR code
    public function attendanceScan(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = $request->input('code');

        // Here you can lookup the code in your attendance table
        // For now, let's return a mock response
        // You will later implement actual DB logic
        $success = true; // mock
        $message = "Attendance recorded successfully!";

        return response()->json([
            'success' => $success,
            'message' => $message
        ]);
    }

   public function medalTally()
{
    // -----------------------------
    // Medal tally by Division (from results_divisions)
    // -----------------------------
    $medalTally = \App\Models\Division::withCount([
        'resultsDivisions as gold'   => fn($q) => $q->where('medal', 'Gold'),
        'resultsDivisions as silver' => fn($q) => $q->where('medal', 'Silver'),
        'resultsDivisions as bronze' => fn($q) => $q->where('medal', 'Bronze'),
    ])->get();

    // Calculate total medals per division
    $medalTally->transform(function ($division) {
        $division->total = $division->gold + $division->silver + $division->bronze;

        // Load the actual ResultDivision rows for the modal
        $division->results = $division->resultsDivisions()
    ->with([
        'sport',
        'event',
        'sportType', // ✅ ensure sportType is loaded
        'type'       // ✅ optional: in case type_id is directly needed
    ])
    ->get();


        return $division;
    });

    // Sort divisions by total medals, then gold, silver, bronze
    $medalTally = $medalTally
        ->sortByDesc(fn($d) => [$d->total, $d->gold, $d->silver, $d->bronze])
        ->values();

    // Fetch upcoming events & games (optional if you want sidebar)
    $upcomingEvents = \App\Models\Event::orderBy('start_date', 'asc')->take(5)->get();
    $upcomingGames = \App\Models\Game::with('event')->orderBy('game_time', 'asc')->take(5)->get();

    return view('viewer.medal-tally', compact('medalTally', 'upcomingEvents', 'upcomingGames'));
}





}