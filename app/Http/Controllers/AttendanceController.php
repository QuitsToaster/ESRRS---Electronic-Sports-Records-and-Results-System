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
    public function index(Request $request)
    {
        $today = Carbon::today();
        $user = Auth::user();

        // ================= GET USER DIVISION =================
        // Extract city from user name like "SDO - Santiago City Official"
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
}