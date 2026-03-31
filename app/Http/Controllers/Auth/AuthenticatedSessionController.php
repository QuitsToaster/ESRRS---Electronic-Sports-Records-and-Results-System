<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\LayoutSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
{
    $welcomeBg = LayoutSetting::where('key', 'welcome_image')->first();
    return view('auth.login', compact('welcomeBg'));
}

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    $user = Auth::user();
    $role = $user->role->name ?? null;

    // ✅ PRIORITY REDIRECT FOR BILLETING QUARTERS
    if ($role === 'Billeting Quarters') {
        return redirect()->route('attendance.check');
    }

    // ✅ NORMAL MODULE-BASED REDIRECTS
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

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
{
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // ✅ Redirect to login page
    return redirect()->route('login');
}
}
