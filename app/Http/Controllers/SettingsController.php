<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\SystemDeveloper;
use App\Models\LayoutSetting;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function layout()
    {
        $banners = Banner::orderBy('order')->get();
        $adminLogo  = LayoutSetting::where('key', 'admin_sidebar_logo')->first();
        $viewerLogo = LayoutSetting::where('key', 'viewer_top_logo')->first();
        $developers = SystemDeveloper::all();

        return view('settings.layout', compact(
            'banners',
            'adminLogo',
            'viewerLogo',
            'developers'
        ));
    }

    public function saveLayout(Request $request)
    {
        $request->validate([
            'images.*' => 'nullable|image|max:5120',
            'admin_logo' => 'nullable|image|max:2048',
            'viewer_logo' => 'nullable|image|max:2048',
            'developer_photo.*' => 'nullable|image|max:2048',
            'developer_name.*' => 'nullable|string|max:255',
            'developer_email.*' => 'nullable|email|max:255',
            'developer_contact.*' => 'nullable|string|max:50',
            'developer_role.*' => 'nullable|string|max:255',
            'developer_description.*' => 'nullable|string|max:1000',
        ]);

        /* =======================
           Save Banner Images
        ========================*/
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $imageFile) {
                if (!$imageFile) continue;

                $path = $imageFile->store('banners', 'public');

                Banner::updateOrCreate(
                    ['order' => $index],
                    ['image' => $path]
                );
            }
        }

        /* =======================
           Save Admin Sidebar Logo
        ========================*/
        if ($request->hasFile('admin_logo')) {
            $path = $request->file('admin_logo')->store('logos', 'public');

            LayoutSetting::updateOrCreate(
                ['key' => 'admin_sidebar_logo'],
                ['value' => $path]
            );
        }

        /* =======================
           Save Viewer Top Logo
        ========================*/
        if ($request->hasFile('viewer_logo')) {
            $path = $request->file('viewer_logo')->store('logos', 'public');

            LayoutSetting::updateOrCreate(
                ['key' => 'viewer_top_logo'],
                ['value' => $path]
            );
        }

        /* =======================
           Save Developers
        ========================*/
        if ($request->has('developer_name')) {
            foreach ($request->developer_name as $index => $name) {
                if (empty($name)) continue;

                $developerId = $request->developer_id[$index] ?? null;

                // If ID exists, update. If not, create new.
                if ($developerId) {
                    $developer = SystemDeveloper::find($developerId);
                    if ($developer) {
                        $developer->update([
                            'name' => $name,
                            'email' => $request->developer_email[$index] ?? null,
                            'contact' => $request->developer_contact[$index] ?? null,
                            'role' => $request->developer_role[$index] ?? null,
                            'description' => $request->developer_description[$index] ?? null,
                        ]);
                    }
                } else {
                    $developer = SystemDeveloper::create([
                        'name' => $name,
                        'email' => $request->developer_email[$index] ?? null,
                        'contact' => $request->developer_contact[$index] ?? null,
                        'role' => $request->developer_role[$index] ?? null,
                        'description' => $request->developer_description[$index] ?? null,
                    ]);
                }

                // Save developer photo if uploaded
                if (isset($request->file('developer_photo')[$index])) {
                    $photoFile = $request->file('developer_photo')[$index];
                    if ($photoFile) {
                        $path = $photoFile->store('developers', 'public');
                        $developer->update(['photo' => $path]);
                    }
                }
            }
        }

        return back()->with('success', 'Layout settings saved successfully!');
    }
}
