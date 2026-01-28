<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    // Show the attendance scanning page (this is what users see)
    public function index()
    {
        return view('attendance.check');
    }

    // Handle QR code submission from user self-scanning
    public function store(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $qr = $request->qr_code;
        $now = Carbon::now();
        $today = Carbon::today();

        DB::beginTransaction();

        try {
            // Check athlete first
            $athlete = Athlete::where('qr_code', $qr)->first();
            
            if ($athlete) {
                return $this->handleAttendance($athlete, 'athlete', $now, $today);
            }

            // Check coach next
            $coach = Coach::where('qr_code', $qr)->first();
            
            if ($coach) {
                return $this->handleAttendance($coach, 'coach', $now, $today);
            }

            DB::rollBack();
            return back()->with('error', 'QR code not recognized. Please try again.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'System error. Please try again.');
        }
    }

    private function handleAttendance($user, $type, $now, $today)
    {
        $userIdField = $type . '_id';
        $userName = $user->name;

        // Find existing attendance for today
        $existing = Attendance::where($userIdField, $user->id)
            ->whereDate('created_at', $today)
            ->first();

        if ($existing) {
            // User already scanned today
            if ($existing->time_out) {
                // Already timed out
                DB::rollBack();
                return back()->with('error', 
                    "$userName has already timed out at " . 
                    Carbon::parse($existing->time_out)->format('h:i A')
                );
            } else {
                // Has time_in, needs time_out
                $existing->update(['time_out' => $now]);
                DB::commit();
                return back()->with('success', 
                    "Time out recorded for $userName at " . $now->format('h:i A')
                );
            }
        } else {
            // First scan - create time_in
            Attendance::create([
                $userIdField => $user->id,
                'time_in' => $now,
            ]);
            
            DB::commit();
            return back()->with('success', 
                "Time in recorded for $userName at " . $now->format('h:i A')
            );
        }
    }
}