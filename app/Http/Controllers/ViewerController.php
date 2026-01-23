<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ViewerFeedback;
use App\Models\Event;
use App\Models\Banner;
use App\Models\GalleryPhoto;
use App\Models\Game;
use App\Models\Announcement;

class ViewerController extends Controller
{
    // Home Page
    public function home()
    {
        $eventsCount = Event::count();
        $upcomingEvents = Event::orderBy('start_date', 'asc')->take(5)->get();

        $gamesCount = Game::count();
        $upcomingGames = Game::with('event')->orderBy('game_time', 'asc')->take(5)->get();

        $announcementsCount = Announcement::count();
        $latestAnnouncements = Announcement::latest()->take(5)->get();

        $medalTally = \DB::table('results')
            ->selectRaw('school_id, SUM(medal="Gold") as gold, SUM(medal="Silver") as silver, SUM(medal="Bronze") as bronze')
            ->groupBy('school_id')
            ->get();

        // Fetch banners for the homepage slideshow
        $banners = Banner::orderBy('order')->get();

        return view('viewer.home', compact(
            'eventsCount', 'upcomingEvents',
            'gamesCount', 'upcomingGames',
            'announcementsCount', 'latestAnnouncements',
            'medalTally', 'banners' // <-- pass banners
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

    $events = Event::when($search, function($query, $search) {
        $query->where('name', 'like', "%{$search}%");
    })
    ->orderBy('start_date', 'asc')
    ->get();

    return view('viewer.events', compact('events'));
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

public function gallery(Request $request)
{
    $search = $request->input('search');

    $photos = GalleryPhoto::when($search, function($query, $search) {
        $query->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
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

}