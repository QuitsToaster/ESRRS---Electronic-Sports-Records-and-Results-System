<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;

class HistoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = optional($user->role)->name;

        $allAwards = collect();
        $individualAwards = collect();

        // ================= GET ALL USER LOGS =================
        $logs = AuditLog::with('user')
            ->where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('action', 'like', '%Award%')
                  ->orWhere('action', 'like', '%Approved%')
                  ->orWhere('action', 'like', '%Rejected%');
            })
            ->orderByDesc('created_at')
            ->get();

        foreach ($logs as $log) {

            $action = $log->action;

            // ================= DETERMINE TYPE =================
            if (str_contains($action, 'Individual Award')) {

                // ================= INDIVIDUAL =================
                $individualAwards->push((object)[
                    'history_action' => $this->getActionType($action),
                    'history_date'   => $log->created_at,
                    'history_log'    => $action,

                    // Parsed display fields
                    'athlete' => $this->extract('Athlete', $action),
                    'sport'   => $this->extract('Sport', $action),
                    'type'    => $this->extract('Type', $action),
                    'medal'   => $this->extract('Medal', $action),
                    'sdo'     => $this->cleanSdo($this->extract('SDO', $action)),
                ]);

            } else {

                // ================= ALL AWARDS =================
                $allAwards->push((object)[
                    'history_action' => $this->getActionType($action),
                    'history_date'   => $log->created_at,
                    'history_log'    => $action,

                    'event_id' => $this->extract('Event ID', $action),
                    'medal'    => $this->extract('Medal', $action),
                    'sdo'      => $this->cleanSdo($this->extract('SDO', $action)),
                ]);
            }
        }

        return view('history.index', compact('allAwards', 'individualAwards'));
    }

    // ================= HELPER: ACTION TYPE =================
    private function getActionType($action)
    {
        $action = strtoupper($action);

        if (str_contains($action, 'APPROVED')) return 'Approved';
        if (str_contains($action, 'REJECTED')) return 'Rejected';
        if (str_contains($action, 'AWARD')) return 'Submitted';

        return 'Action';
    }

    // ================= HELPER: EXTRACT FIELD =================
    private function extract($field, $text)
    {
        preg_match("/{$field}:\s*(.+?)(\||Reason:|$)/i", $text, $match);
        return trim($match[1] ?? '');
    }

    // ================= HELPER: CLEAN SDO =================
    private function cleanSdo($sdo)
    {
        return preg_replace('/SDO\s*-\s*SDO\s*-\s*/i', 'SDO - ', $sdo);
    }
}