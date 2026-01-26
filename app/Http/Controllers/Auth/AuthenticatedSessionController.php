<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // If user has access, redirect to the first module they have access to
        if (!empty($user->access) && is_array($user->access) && count($user->access) > 0) {
            // Map module names to routes (you may need to adjust these route names)
            $moduleRoutes = [
                'dashboard' => 'dashboard',
                'attendance' => 'attendance.index',
                'results' => 'results.index',
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

            // Find the first module the user has access to that exists in the routes mapping
            foreach ($user->access as $module) {
                if (isset($moduleRoutes[$module])) {
                    return redirect()->route($moduleRoutes[$module]);
                }
            }
        }

        // Default fallback
        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
