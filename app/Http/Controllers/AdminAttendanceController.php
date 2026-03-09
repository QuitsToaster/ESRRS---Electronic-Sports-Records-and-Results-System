<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Attendance;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Load all attendance (athlete or coach) for today
        $attended = Attendance::with(['athlete.school', 'coach.school'])
            ->whereDate('created_at', $today)
            ->get();

        // Get all athlete IDs that attended
        $attendedAthleteIds = $attended->pluck('athlete_id')->filter()->toArray();

        // Athletes who have not attended today
        $notAttended = Athlete::with('school')
            ->whereNotIn('id', $attendedAthleteIds)
            ->get()
            ->sortBy(function ($athlete) {
                return $athlete->full_name;  // sort in PHP using accessor
            });

        // Get all coach IDs that attended
        $attendedCoachIds = $attended->pluck('coach_id')->filter()->toArray();

        // Coaches who have not attended today
        $notAttendedCoaches = Coach::with('school')
            ->whereNotIn('id', $attendedCoachIds)
            ->get()
            ->sortBy(function ($coach) {
                return $coach->full_name; // sort in PHP using accessor
            });

        return view('attendance.index', compact('attended', 'notAttended', 'notAttendedCoaches'));
    }

    public function generateQr(Request $request)
    {
        $request->validate([
            'type' => 'required|in:athlete,coach'
        ]);

        $qrData = json_encode([
            'type' => $request->type,
            'action' => 'attendance',
            'generated_at' => now()->toDateTimeString()
        ]);

        $qrImage = QrCode::format('png')
            ->size(300)
            ->generate($qrData);

        return response($qrImage)->header('Content-Type', 'image/png');
    }

    public function exportPdf()
    {
        $today = Carbon::today();

        // Load all attendance for today
        $attended = Attendance::with(['athlete.school', 'coach.school'])
            ->whereDate('created_at', $today)
            ->get();

        // Athletes who attended
        $attendedAthleteIds = $attended->pluck('athlete_id')->filter()->toArray();

        // Athletes without attendance
        $notAttended = Athlete::with('school')
            ->whereNotIn('id', $attendedAthleteIds)
            ->get()
            ->sortBy(fn($athlete) => $athlete->full_name);

        // Coaches who attended
        $attendedCoachIds = $attended->pluck('coach_id')->filter()->toArray();

        // Coaches without attendance
        $notAttendedCoaches = Coach::with('school')
            ->whereNotIn('id', $attendedCoachIds)
            ->get()
            ->sortBy(fn($coach) => $coach->full_name);

        // Pass all data to export view
        $pdf = PDF::loadView('attendance.export', [
            'attended' => $attended,
            'notAttended' => $notAttended,
            'notAttendedCoaches' => $notAttendedCoaches,
            'generatedAt' => now()->setTimezone('Asia/Manila')->format('M d, Y h:i A')
        ]);

        return $pdf->download('eSRRS-attendance_' . now()->format('Ymd_His') . '.pdf');
    }
}
