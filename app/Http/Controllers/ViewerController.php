<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ViewerFeedback;
use App\Models\Event;
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

        // Assuming you already have medals tallies aggregated in your Event or Result model
        $medalTally = \DB::table('results')
            ->selectRaw('school_id, SUM(medal="Gold") as gold, SUM(medal="Silver") as silver, SUM(medal="Bronze") as bronze')
            ->groupBy('school_id')
            ->get();

        return view('viewer.home', compact(
            'eventsCount', 'upcomingEvents',
            'gamesCount', 'upcomingGames',
            'announcementsCount', 'latestAnnouncements',
            'medalTally'
        ));
    }

    // Events Page
    public function events()
    {
       $events = Event::orderBy('start_date', 'asc')->get();
        return view('viewer.events', compact('events'));
    }

    // Games Page
    public function games()
    {
        $games = Game::orderBy('game_time', 'asc')->get();
        return view('viewer.games', compact('games'));
    }

    // Announcements Page
    public function announcements()
    {
        $announcements = Announcement::latest()->get();
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
}
