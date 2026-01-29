<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\SystemDeveloper;
use App\Models\LayoutSetting;
use App\Models\FooterSetting;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{

public function layout()
{
    $banners = Banner::orderBy('order')->get();
    $adminLogo  = LayoutSetting::where('key', 'admin_sidebar_logo')->first();
    $viewerLogo = LayoutSetting::where('key', 'viewer_top_logo')->first();
    $developers = SystemDeveloper::all();
    $welcomeImage = LayoutSetting::where('key', 'welcome_image')->first();
    $footerSetting = FooterSetting::first(); // load footer

    return view('settings.layout', compact(
        'banners',
        'adminLogo',
        'viewerLogo',
        'developers',
        'footerSetting',
        'welcomeImage'
    ));
}

public function saveLayout(Request $request)
{
    // existing validations...
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
        'welcome_image' => 'nullable|image|max:5120',
        'footer_logo' => 'nullable|image|max:2048',
        'footer_logo_name' => 'nullable|string|max:255',
        'social_name.*' => 'nullable|string|max:50',
        'social_link.*' => 'nullable|url|max:255',
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

         /* =======================
   Save Footer
=======================*/
$footer = FooterSetting::first();

$data = [
    'logo_name' => $request->footer_logo_name ?? '',
    'socials' => [],
    'logo' => $footer->logo ?? null, // default existing logo
];

// Handle uploaded logo
if ($request->hasFile('footer_logo')) {
    $path = $request->file('footer_logo')->store('footer', 'public');
    $data['logo'] = $path;
}

// Handle socials
$socialNames = $request->social_name ?? [];
$socialLinks = $request->social_link ?? [];

foreach ($socialNames as $i => $name) {
    if (empty($name)) continue;
    $data['socials'][] = [
        'name' => $name,
        'link' => $socialLinks[$i] ?? '',
    ];
}

// Save or create
if ($footer) {
    $footer->update($data);
} else {
    FooterSetting::create($data);
}

/* =======================
   Save Welcome Page Image
=======================*/
if ($request->hasFile('welcome_image')) {
    $path = $request->file('welcome_image')->store('welcome', 'public');

    LayoutSetting::updateOrCreate(
        ['key' => 'welcome_image'],
        ['value' => $path]
    );
}



return back()->with('success', 'Layout settings saved successfully!');
}
}
