<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Athlete;
use App\Models\Attendance;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Attendance records for today
        $attended = Attendance::with('athlete.school')
            ->whereDate('created_at', $today)
            ->get();

        // Athletes who have NOT attended today
        $attendedIds = $attended->pluck('athlete_id')->toArray();

        $notAttended = Athlete::with('school')
            ->whereNotIn('id', $attendedIds)
            ->orderBy('name')
            ->get();

        return view('attendance.index', compact('attended', 'notAttended'));
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

    // Attended today
    $attended = Attendance::with('athlete.school')
        ->whereDate('created_at', $today)
        ->get();

    $attendedIds = $attended->pluck('athlete_id')->toArray();

    // Not attended
    $notAttended = Athlete::with('school')
        ->whereNotIn('id', $attendedIds)
        ->orderBy('name')
        ->get();

    $pdf = PDF::loadView('attendance.export', [
        'attended' => $attended,
        'notAttended' => $notAttended,
        'generatedAt' => now()->setTimezone('Asia/Manila')->format('M d, Y h:i A')
    ]);

    return $pdf->download('attendance_' . now()->format('Ymd_His') . '.pdf');
}

}
