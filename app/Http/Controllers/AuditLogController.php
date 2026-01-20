<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index()
    {
        // Fetch all audit logs ordered by latest first
        $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->get();

        return view('audit-logs.index', compact('logs'));
    }
}
