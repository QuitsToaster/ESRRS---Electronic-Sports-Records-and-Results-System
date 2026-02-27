<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SwitchAccountController extends Controller
{
    public function store(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required','email'],
        'password' => ['required'],
    ]);

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        $user = Auth::user();
        $role = $user->role->name ?? null;

        if (!empty($user->access) && is_array($user->access)) {

            foreach ($user->access as $module) {

                // ✅ RESULTS MODULE (ROLE-AWARE)
                if ($module === 'results') {
                    if (in_array($role, ['Tournament Secretary', 'Tournament Manager'])) {
                        return redirect()->route('results_divisions.create');
                    }
                    return redirect()->route('results_divisions.index');
                }

                // ✅ OTHER MODULES
                $moduleRoutes = [
                    'dashboard' => 'dashboard',
                    'attendance' => 'attendance.index',
                    'teams' => 'participants.index',
                    'events' => 'events.index',
                    'sports' => 'sports.index',
                    'athletes' => 'athletes.index',
                    'coaches' => 'coaches.index',
                    'officials' => 'officials.index',
                    'games' => 'games.index',
                    'gallery' => 'gallery.index',
                    'announcements' => 'announcements.index',
                    'users' => 'users.index',
                    'logs' => 'logs.index',
                    'settings' => 'settings.index',
                ];

                if (isset($moduleRoutes[$module])) {
                    return redirect()->route($moduleRoutes[$module]);
                }
            }
        }

        return redirect()->route('dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
}
}
