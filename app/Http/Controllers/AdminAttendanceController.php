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
            ->orderBy('name')
            ->get();

        $attendedCoachIds = $attended->pluck('coach_id')->filter()->toArray();

        $notAttendedCoaches = Coach::with('school')
            ->whereNotIn('id', $attendedCoachIds)
            ->orderBy('name')
            ->get();

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
        ->orderBy('name')
        ->get();

    // Coaches who attended
    $attendedCoachIds = $attended->pluck('coach_id')->filter()->toArray();

    // Coaches without attendance
    $notAttendedCoaches = Coach::with('school')
        ->whereNotIn('id', $attendedCoachIds)
        ->orderBy('name')
        ->get();

    // Pass all data to export view
    $pdf = PDF::loadView('attendance.export', [
        'attended' => $attended,
        'notAttended' => $notAttended,
        'notAttendedCoaches' => $notAttendedCoaches,
        'generatedAt' => now()->setTimezone('Asia/Manila')->format('M d, Y h:i A')
    ]);

    return $pdf->download('attendance_' . now()->format('Ymd_His') . '.pdf');
}

}
