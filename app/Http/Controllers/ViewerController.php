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
use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Announcement;

class ViewerController extends Controller
{
    public function home()
{
    $eventsCount = Event::count();
    $upcomingEvents = Event::orderBy('start_date', 'asc')->take(5)->get();

    $gamesCount = Game::count();
    $upcomingGames = Game::with('event')->orderBy('game_time', 'asc')->take(5)->get();

    $announcementsCount = Announcement::count();
    $latestAnnouncements = Announcement::latest()->take(5)->get();

    // Medal tally with rank sorting (Gold > Silver > Bronze)
    $medalTally = \DB::table('results')
        ->selectRaw('school_id, SUM(medal="Gold") as gold, SUM(medal="Silver") as silver, SUM(medal="Bronze") as bronze')
        ->groupBy('school_id')
        ->get()
        ->sortByDesc(function($school){
            return [$school->gold, $school->silver, $school->bronze];
        })
        ->values(); // reindex after sort

    // Fetch banners for the homepage slideshow
    $banners = Banner::orderBy('order')->get();

    return view('viewer.home', compact(
        'eventsCount', 'upcomingEvents',
        'gamesCount', 'upcomingGames',
        'announcementsCount', 'latestAnnouncements',
        'medalTally', 'banners'
    ));
}


   public function participants(Request $request)
    {
        $search = $request->input('search');

        $schools = \App\Models\School::with([
            'coaches',
            'athletes.sport', // still eager load for display
        ])
        ->when($search, function ($query, $search) {
            // Filter schools by name only
            $query->where('name', 'like', "%{$search}%");
        })
        ->orderBy('name')
        ->get();

        return view('viewer.participants', compact('schools', 'search'));
    }


    public function participantShow(\App\Models\School $school, Request $request)
    {
        $search   = $request->input('search');
        $sportId  = $request->input('sport');
        $division = $request->input('division');

        // Load coaches + sport
        $school->load('coaches.sport');

        // Load athletes with sport, applying search & filters
        $school->athletes = $school->athletes()
            ->with('sport')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->when($sportId, function ($q) use ($sportId) {
                $q->where('sport_id', $sportId);
            })
            ->when($division, function ($q) use ($division) {
                $q->where('division', $division);
            })
            ->get();

        // Get all sports and divisions for filter options
        $sports    = \App\Models\Sport::orderBy('name')->get();
        $divisions = $school->athletes->pluck('division')->unique()->sort();

        return view('viewer.participants-show', compact(
            'school', 'search', 'sportId', 'division', 'sports', 'divisions'
        ));
    }

    // Events Page with search
public function events(Request $request)
{
    $search = $request->input('search');

    // Keep Eloquent models for Blade
    $events = Event::with(['schools', 'goldWinner', 'silverWinner', 'bronzeWinner', 'sport'])
        ->when($search, function($query, $search) {
            $query->where('name', 'like', "%{$search}%");
        })
        ->orderBy('start_date', 'asc')
        ->get();

    // Prepare JSON version for AlpineJS modal
    $eventsJson = $events->map(function($event) {
        return [
            'id' => $event->id,
            'name' => $event->name,
            'start_date' => $event->start_date?->toDateString(),
            'venue' => $event->venue,
            'age_group' => $event->age_group,
            'category' => $event->category,
            'sport' => $event->sport ? ['name' => $event->sport->name] : null,
            'schools' => $event->schools->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->toArray(),
            'goldWinner' => $event->goldWinner ? ['id' => $event->goldWinner->id, 'name' => $event->goldWinner->name] : null,
            'silverWinner' => $event->silverWinner ? ['id' => $event->silverWinner->id, 'name' => $event->silverWinner->name] : null,
            'bronzeWinner' => $event->bronzeWinner ? ['id' => $event->bronzeWinner->id, 'name' => $event->bronzeWinner->name] : null,
        ];
    });

    return view('viewer.events', compact('events', 'eventsJson'));
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

    $announcements = Announcement::with('event')
        ->when($search, function($query, $search) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('event', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
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
    $medalTally = \DB::table('results')
        ->selectRaw('school_id, SUM(medal="Gold") as gold, SUM(medal="Silver") as silver, SUM(medal="Bronze") as bronze')
        ->groupBy('school_id')
        ->get()
        ->sortByDesc(function($school){
            return [$school->gold, $school->silver, $school->bronze];
        })
        ->values();

    // Fetch schools names
    $schools = \App\Models\School::whereIn('id', $medalTally->pluck('school_id'))->get()->keyBy('id');

    return view('viewer.medal-tally', compact('medalTally', 'schools'));
}


}