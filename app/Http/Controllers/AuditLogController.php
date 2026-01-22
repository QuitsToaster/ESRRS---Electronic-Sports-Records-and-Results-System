<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AuditLogController extends Controller
{
    /**
     * Display audit logs in the system
     */
  public function index(Request $request)
{
    $search     = $request->input('search');
    $start_date = $request->input('start_date');
    $end_date   = $request->input('end_date');

    $logs = AuditLog::with('user')
        ->when($search, function ($query, $search) {
            $query->where('model_type', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        })
        ->when($start_date, function ($query, $start_date) {
            $query->whereDate('created_at', '>=', $start_date);
        })
        ->when($end_date, function ($query, $end_date) {
            $query->whereDate('created_at', '<=', $end_date);
        })
        ->orderBy('created_at', 'desc')
        ->get();

    return view('audit-logs.index', compact('logs'));
}



    public function exportPdf(Request $request)
{
    $search     = $request->input('search');
    $start_date = $request->input('start_date');
    $end_date   = $request->input('end_date');

    $logs = AuditLog::with('user')
        ->when($search, function ($query, $search) {
            $query->where('model_type', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        })
        ->when($start_date, function ($query, $start_date) {
            $query->whereDate('created_at', '>=', $start_date);
        })
        ->when($end_date, function ($query, $end_date) {
            $query->whereDate('created_at', '<=', $end_date);
        })
        ->orderBy('created_at', 'desc')
        ->get();

    $generatedAt = Carbon::now('Asia/Manila')
        ->format('M d, Y h:i A');

    $pdf = Pdf::loadView('audit-logs.export', [
        'logs'        => $logs,
        'generatedAt' => $generatedAt,
    ])->setPaper('letter', 'portrait');

    return $pdf->download('audit_logs_' . now()->format('Ymd_His') . '.pdf');
}

}
