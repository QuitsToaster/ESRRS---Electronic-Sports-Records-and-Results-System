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
        // Validate input
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        // Logout current user first
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Attempt login with new credentials
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect based on access
            if (!empty($user->access) && is_array($user->access) && count($user->access) > 0) {
                $moduleRoutes = [
                    'dashboard' => 'dashboard',
                    'attendance' => 'attendance.index',
                    'results'        => 'results_divisions.index',
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

                foreach ($user->access as $module) {
                    if (isset($moduleRoutes[$module])) {
                        return redirect()->route($moduleRoutes[$module]);
                    }
                }
            }

            // Default fallback
            return redirect()->route('dashboard');
        }

        // Login failed
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}
