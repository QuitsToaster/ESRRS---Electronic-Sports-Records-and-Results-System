<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Athlete;
use App\Models\School;
use PDF; // Import DomPDF facade

class AdminAttendanceController extends Controller
{
    /**
     * Show attendance management page
     */
    public function index()
    {
        $athletes = Athlete::with('school')->orderBy('created_at', 'desc')->get();

        return view('attendance.index', compact('athletes'));
    }

    /**
     * Generate QR code for attendance (placeholder)
     */
    public function generateQr()
    {
        return back()->with('success', 'QR code generated (placeholder)');
    }

    /**
     * Export attendance as PDF
     */
    public function exportPdf()
    {
        $athletes = Athlete::with('school')->orderBy('created_at', 'desc')->get();

        $pdf = PDF::loadView('attendance.export', compact('athletes'));

        return $pdf->download('attendance_' . now()->format('Ymd_His') . '.pdf');
    }
}
