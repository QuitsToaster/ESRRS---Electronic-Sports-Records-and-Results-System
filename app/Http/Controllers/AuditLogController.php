<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\Sport;
use App\Models\SportType;
use App\Models\Athlete;
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

    // ================= FIX OLD LOGS =================
    $logs->getCollection()->transform(function ($log) {

        $action = $log->action;

        // ================= EXTRACT IDS =================
        preg_match('/Sport(?: ID)?:\s*(\d+)/i', $action, $sportMatch);
        preg_match('/Type(?: ID)?:\s*(\d+)/i', $action, $typeMatch);
        preg_match('/Athlete(?: ID)?:\s*(\d+)/i', $action, $athleteMatch);

        $sportId   = $sportMatch[1] ?? null;
        $typeId    = $typeMatch[1] ?? null;
        $athleteId = $athleteMatch[1] ?? null;

        // ================= FETCH DATA =================
        $sport   = $sportId ? \App\Models\Sport::find($sportId) : null;
        $type    = $typeId ? \App\Models\SportType::find($typeId) : null;
        $athlete = $athleteId ? \App\Models\Athlete::find($athleteId) : null;

        $athleteName = $athlete
            ? strtoupper($athlete->last_name . ', ' . $athlete->first_name . ' ' . ($athlete->middle_initial ?? ''))
            : null;

        // ================= REPLACE TEXT =================
        if ($sport) {
            $action = preg_replace('/Sport(?: ID)?:\s*\d+/i', 'Sport: ' . $sport->name, $action);
        }

        if ($type) {
            $action = preg_replace('/Type(?: ID)?:\s*\d+/i', 'Type: ' . $type->type_name, $action);
        }

        if ($athlete && $athleteName) {
            $action = preg_replace('/Athlete(?: ID)?:\s*\d+/i', 'Athlete: ' . $athleteName, $action);
        }

        // ================= APPLY FIX =================
        $log->action = $action;

        return $log;
    });

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

        return $pdf->download('eSRRS-audit_logs_' . now()->format('Ymd_His') . '.pdf');
    }
}