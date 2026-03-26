<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Attendance;
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

        $code = $request->qr_code;

// ✅ Normalize scanned QR
$code = strtoupper($code);
$code = preg_replace('/[^A-Z0-9]/', '', $code); // remove EVERYTHING except letters & numbers

// 🔍 Normalize DB values during comparison
$athlete = Athlete::all()->first(function ($a) use ($code) {
    $dbCode = strtoupper($a->qr_code);
    $dbCode = preg_replace('/[^A-Z0-9]/', '', $dbCode);

    return $dbCode === $code;
});

$coach = null;

if (!$athlete) {
    $coach = Coach::all()->first(function ($c) use ($code) {
        $dbCode = strtoupper($c->qr_code);
        $dbCode = preg_replace('/[^A-Z0-9]/', '', $dbCode);

        return $dbCode === $code;
    });
}

        if (!$athlete && !$coach) {
            return back()->with('error', 'QR Code not recognized.');
        }

        $now = now();

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
                'time_out' => $now
            ]);

            return back()->with('success', 'Time Out recorded.');
        }

        // Create TIME IN
        Attendance::create([
            'athlete_id' => $athlete->id ?? null,
            'coach_id' => $coach->id ?? null,
            'time_in' => $now,
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