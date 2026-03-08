<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Role;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AuditLogController extends Controller
{
    /**
     * Display audit logs
     */
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $start_date = $request->input('start_date');
        $end_date   = $request->input('end_date');
        $roleId     = $request->input('role_id');

        $logs = AuditLog::with('user.role')
            ->when($search, function ($query, $search) {
                $query->where('model_type', 'like', "%{$search}%")
                      ->orWhere('action', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            })
            ->when($start_date, fn ($q) => $q->whereDate('created_at', '>=', $start_date))
            ->when($end_date, fn ($q) => $q->whereDate('created_at', '<=', $end_date))
            ->when($roleId, function ($query, $roleId) {
                $query->whereHas('user', function ($q) use ($roleId) {
                    $q->where('role_id', $roleId);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        $roles = Role::orderBy('name')->get();

        return view('audit-logs.index', compact('logs', 'roles'));
    }

    /**
     * Export audit logs to PDF
     */
    public function exportPdf(Request $request)
    {
        $search     = $request->input('search');
        $start_date = $request->input('start_date');
        $end_date   = $request->input('end_date');
        $roleId     = $request->input('role_id');

        $logs = AuditLog::with('user.role')
            ->when($search, function ($query, $search) {
                $query->where('model_type', 'like', "%{$search}%")
                      ->orWhere('action', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            })
            ->when($start_date, fn ($q) => $q->whereDate('created_at', '>=', $start_date))
            ->when($end_date, fn ($q) => $q->whereDate('created_at', '<=', $end_date))
            ->when($roleId, function ($query, $roleId) {
                $query->whereHas('user', function ($q) use ($roleId) {
                    $q->where('role_id', $roleId);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        $generatedAt = Carbon::now('Asia/Manila')->format('M d, Y h:i A');

        $pdf = Pdf::loadView('audit-logs.export', [
            'logs'        => $logs,
            'generatedAt' => $generatedAt,
        ])->setPaper('letter', 'portrait');

        return $pdf->download('audit_logs_' . now()->format('Ymd_His') . '.pdf');
    }
}