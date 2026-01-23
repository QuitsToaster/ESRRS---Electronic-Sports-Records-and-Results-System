<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Athlete;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Show the attendance scanning page
    public function index()
    {
        return view('attendance.check');
    }

    // Handle QR code submission
    public function store(Request $request)
{
    $request->validate([
        'qr_code' => 'required|string',
    ]);

    $qr = $request->qr_code;

    $today = Carbon::today();

    // Check athlete first
    $athlete = \App\Models\Athlete::where('qr_code', $qr)->first();
    if ($athlete) {
        $already = \App\Models\Attendance::where('athlete_id', $athlete->id)
                    ->whereDate('created_at', $today)->first();
        if ($already) return back()->with('already_scanned_name', $athlete->name);

        \App\Models\Attendance::create([
            'athlete_id' => $athlete->id,
            'time_in' => now(),
        ]);

        return back()->with('success', $athlete->name . ' attendance recorded.');
    }

    // Check coach next
    $coach = \App\Models\Coach::where('qr_code', $qr)->first();
    if ($coach) {
        $already = \App\Models\Attendance::where('coach_id', $coach->id)
                    ->whereDate('created_at', $today)->first();
        if ($already) return back()->with('already_scanned_name', $coach->name);

        \App\Models\Attendance::create([
            'coach_id' => $coach->id,
            'time_in' => now(),
        ]);

        return back()->with('success', $coach->name . ' attendance recorded.');
    }

    // If not found
    return back()->with('error', 'QR code not recognized. Athlete or Coach not registered.');
}

}
