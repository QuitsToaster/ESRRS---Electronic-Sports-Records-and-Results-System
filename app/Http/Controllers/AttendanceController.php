<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Attendance;
use App\Models\Division;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
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
            $afterPrefix = trim(substr($user->name, 5));

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
        $attended = Attendance::with(['athlete','coach.school'])
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
            ->latest()
            ->paginate(10, ['*'], 'attended_page');

        // ================= NOT ATTENDED ATHLETES =================
        $notAttended = Athlete::when($selectedDivision, function ($query) use ($selectedDivision) {
                $query->where('school_division', $selectedDivision);
            })
            ->whereDoesntHave('attendances', function ($query) use ($today) {
                $query->whereDate('created_at', $today);
            })
            ->paginate(10, ['*'], 'athletes_page');

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
            ->paginate(10, ['*'], 'coaches_page');

        // ================= ATTENDANCE LOGS (NOW FILTERED) =================
        $logs = Attendance::with(['athlete','coach.school'])
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
            ->latest()
            ->paginate(15, ['*'], 'logs_page');

        return view('attendance.index', compact(
            'attended',
            'notAttended',
            'notAttendedCoaches',
            'divisions',
            'selectedDivision',
            'userDivision',
            'logs'
        ));
    }

    /**
     * Public QR Code Scanner Page
     */
    public function checkAttendance()
    {
        return view('attendance.check');
    }

    /**
     * Handle QR Code Submission
     */
    public function store(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $rawInput = $request->qr_code;

// =====================================================
// ✅ STEP 1: RAW INPUT
// =====================================================
$raw = strtoupper(trim($request->qr_code));

// remove quotes + weird characters only
$raw = str_replace(["\n", "\r", "\t", '"', "'"], '', $raw);

// =====================================================
// ✅ STEP 2: EXTRACT QR CORE (VERY FLEXIBLE)
// =====================================================
preg_match('/ESRRS[-\sA-Z0-9]+/', $raw, $match);

$extracted = $match[0] ?? $raw;

// =====================================================
// ✅ STEP 3: NORMALIZE INPUT (REMOVE ALL NON-ALPHANUMERIC)
// THIS IS THE KEY 🔥
// =====================================================
$normalizedInput = preg_replace('/[^A-Z0-9]/', '', $extracted);

// =====================================================
// 🔥 STEP 4: NORMALIZE INPUT (REMOVE NON-ALPHANUMERIC)
// =====================================================
$normalizedInput = preg_replace('/[^A-Z0-9]/', '', $extracted);

// =====================================================
// 🔍 STEP 5: MATCH ATHLETE (DATABASE LEVEL - FAST)
// =====================================================
$athlete = Athlete::whereRaw("
    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
        UPPER(qr_code),
        '-', ''), ' ', ''), '\n', ''), '\r', ''), '\t', '')
    = ?
", [$normalizedInput])->first();

// =====================================================
// 🔍 STEP 6: MATCH COACH
// =====================================================
$coach = null;

if (!$athlete) {
    $coach = Coach::whereRaw("
        REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
            UPPER(qr_code),
            '-', ''), ' ', ''), '\n', ''), '\r', ''), '\t', '')
        = ?
    ", [$normalizedInput])->first();
}

// =====================================================
// ❌ FINAL FAIL
// =====================================================
if (!$athlete && !$coach) {
    return back()->with('error', 'QR Code not recognized.');
}

        // ================= DIVISION FILTER =================

$user = Auth::user();

// Get user's division IDs
$divisionIds = is_array($user->divisions)
    ? $user->divisions
    : json_decode($user->divisions, true);

// Get division names (e.g. "SDO - BATANES")
$userDivisions = Division::whereIn('id', $divisionIds ?? [])
    ->pluck('name')
    ->toArray();

// Normalize: remove "SDO - "
$userDivisions = array_map(function ($d) {
    return strtoupper(trim(str_replace('SDO -', '', $d)));
}, $userDivisions);

// ================= CHECK ATHLETE =================
if ($athlete) {
    $athleteDivision = strtoupper(trim($athlete->school_division));

    if (!in_array($athleteDivision, $userDivisions)) {
        return back()->with('error', 'This QR does not belong to your assigned division.');
    }
}

// ================= CHECK COACH =================
if ($coach && $coach->school) {
    $coachDivision = strtoupper(trim($coach->school->division));

    if (!in_array($coachDivision, $userDivisions)) {
        return back()->with('error', 'This QR does not belong to your assigned division.');
    }
}

        $now = now();

// ✅ GET CURRENT USER (Billeting Quarters)
$currentUser = Auth::user();
$location = $currentUser ? $currentUser->name : 'Unknown';

        $attendanceQuery = Attendance::query();

        if ($athlete) {
            $attendanceQuery->where('athlete_id', $athlete->id);
        }

        if ($coach) {
            $attendanceQuery->where('coach_id', $coach->id);
        }

        // Prevent duplicate scan within 2 seconds
        $recentScan = (clone $attendanceQuery)
            ->latest()
            ->first();

        if ($recentScan && $recentScan->created_at->diffInSeconds($now) < 2) {
            return back()->with('error', 'Duplicate scan detected. Please wait.');
        }

        // Check for OPEN attendance (no time_out)
        $openAttendance = (clone $attendanceQuery)
            ->whereNull('time_out')
            ->latest()
            ->first();

        if ($openAttendance) {

            $openAttendance->update([
                'time_out' => $now,
                'location' => $location,
            ]);

            return back()->with('success', 'Time Out recorded.');
        }

        // Create TIME IN
        Attendance::create([
            'athlete_id' => $athlete->id ?? null,
            'coach_id' => $coach->id ?? null,
            'time_in' => $now,
            'location' => $location,
        ]);

        return back()->with('success', 'Time In recorded.');
    }

    public function exportPdf(Request $request)
{
    $today = Carbon::today();
    $selectedDivision = $request->division;

    // ================= LOGS =================
    $logs = Attendance::with(['athlete','coach.school'])
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
        ->latest()
        ->get();

    // ================= ATTENDED =================
    $attended = Attendance::with(['athlete','coach.school'])
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
    $notAttended = Athlete::whereDoesntHave('attendances', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })
        ->when($selectedDivision, function ($query) use ($selectedDivision) {
            $query->where('school_division', $selectedDivision);
        })
        ->get();

    // ================= NOT ATTENDED COACHES =================
    $notAttendedCoaches = Coach::with('school')
        ->whereDoesntHave('attendances', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })
        ->when($selectedDivision, function ($query) use ($selectedDivision) {
            $query->whereHas('school', function ($q) use ($selectedDivision) {
                $q->where('division', $selectedDivision);
            });
        })
        ->get();

    // ================= GENERATED DATE =================
    $generatedAt = now()->format('F d, Y h:i A');

    // ================= CREATE PDF =================
    $pdf = Pdf::loadView('attendance.export', compact(
        'logs',
        'attended',
        'notAttended',
        'notAttendedCoaches',
        'selectedDivision',
        'generatedAt'
    ));

    return $pdf->download('eSRRS-attendance-report.pdf');
}
}