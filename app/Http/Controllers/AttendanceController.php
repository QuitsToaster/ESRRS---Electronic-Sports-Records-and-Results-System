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

        // Find athlete by QR code
        $athlete = Athlete::where('qr_code', $request->qr_code)->first();

        if (!$athlete) {
            return back()->with('error', 'QR code not recognized. Athlete not registered.');
        }

        // Check if athlete already has an attendance record today
        $today = Carbon::today();
        $attendance = Attendance::where('athlete_id', $athlete->id)
            ->whereDate('created_at', $today)
            ->first();

        if (!$attendance) {
            // First scan = log time_in
            Attendance::create([
                'athlete_id' => $athlete->id,
                'time_in' => now(),
            ]);

            return back()->with('success', $athlete->name . ' attendance recorded (Time In).');
        } elseif ($attendance && !$attendance->time_out) {
            // Second scan = log time_out
            $attendance->update([
                'time_out' => now(),
            ]);

            return back()->with('success', $athlete->name . ' attendance updated (Time Out).');
        } else {
            // Already has time_in and time_out
            return back()->with('error', $athlete->name . ' already completed attendance today.');
        }
    }
}
