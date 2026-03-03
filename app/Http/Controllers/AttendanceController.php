<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Admin Attendance Dashboard
     */
    public function index(Request $request)
    {
        $today = Carbon::today();
        $user = Auth::user();

        // ================= GET USER DIVISION =================
        $userDivision = null;

        if ($user && stripos($user->name, 'SDO -') === 0) {
            $afterPrefix = trim(substr($user->name, 5)); // remove "SDO - "
            
            // Match the actual city from athletes table
            $allDivisions = Athlete::whereNotNull('school_division')
                ->distinct()
                ->pluck('school_division')
                ->toArray();

            foreach ($allDivisions as $division) {
                if (stripos($afterPrefix, $division) !== false) {
                    $userDivision = $division;
                    break;
                }
            }
        }

        // Force division for SDO users
        if ($userDivision) {
            $selectedDivision = $userDivision;
            $divisions = collect([$userDivision]);
        } else {
            $selectedDivision = $request->division;
            $divisions = Athlete::whereNotNull('school_division')
                ->distinct()
                ->orderBy('school_division')
                ->pluck('school_division');
        }

        // ================= ATTENDED TODAY =================
        $attended = Attendance::with(['athlete', 'coach.school'])
            ->whereDate('created_at', $today)
            ->when($selectedDivision, function ($query) use ($selectedDivision) {
                $query->where(function ($q) use ($selectedDivision) {
                    $q->whereHas('athlete', function ($q2) use ($selectedDivision) {
                        $q2->where('school_division', $selectedDivision);
                    })
                    ->orWhereHas('coach.school', function ($q2) use ($selectedDivision) {
                        $q2->where('division', $selectedDivision);
                    });
                });
            })
            ->get();

        // ================= NOT ATTENDED ATHLETES =================
        $notAttended = Athlete::when($selectedDivision, function ($query) use ($selectedDivision) {
                $query->where('school_division', $selectedDivision);
            })
            ->whereDoesntHave('attendances', function ($query) use ($today) {
                $query->whereDate('created_at', $today);
            })
            ->get();

        // ================= NOT ATTENDED COACHES =================
        $notAttendedCoaches = Coach::with('school')
            ->when($selectedDivision, function ($query) use ($selectedDivision) {
                $query->whereHas('school', function ($q) use ($selectedDivision) {
                    $q->where('division', $selectedDivision);
                });
            })
            ->whereDoesntHave('attendances', function ($query) use ($today) {
                $query->whereDate('created_at', $today);
            })
            ->get();

        return view('attendance.index', compact(
            'attended',
            'notAttended',
            'notAttendedCoaches',
            'divisions',
            'selectedDivision',
            'userDivision'
        ));
    }

    /**
     * Public QR Code Scanner Page
     */
    public function checkAttendance()
    {
        return view('attendance.check'); // points to check.blade.php
    }

    /**
     * Handle QR Code Submission
     */
    public function store(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $code = trim($request->qr_code);
        $today = Carbon::today();

        // Try to find Athlete first
        $athlete = Athlete::where('qr_code', $code)->first();
        $coach = null;

        if (!$athlete) {
            // Try Coach if no athlete found
            $coach = Coach::where('qr_code', $code)->first();
        }

        if (!$athlete && !$coach) {
            return redirect()->back()->with('error', 'QR code not recognized.');
        }

        // Determine if user already has attendance today
        $attendanceQuery = Attendance::whereDate('created_at', $today);
        $attendanceQuery->when($athlete, fn($q) => $q->where('athlete_id', $athlete->id));
        $attendanceQuery->when($coach, fn($q) => $q->where('coach_id', $coach->id));
        $existing = $attendanceQuery->first();

        if ($existing) {
            // If already timed in, update time_out
            if (!$existing->time_out) {
                $existing->time_out = now();
                $existing->save();
                return redirect()->back()->with('success', 'Time Out recorded successfully.');
            } else {
                return redirect()->back()->with('error', 'Attendance already recorded for today.');
            }
        } else {
            // Record new attendance (time_in)
            Attendance::create([
                'athlete_id' => $athlete->id ?? null,
                'coach_id'   => $coach->id ?? null,
                'time_in'    => now(),
            ]);

            return redirect()->back()->with('success', 'Time In recorded successfully.');
        }
    }
}