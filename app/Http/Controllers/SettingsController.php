<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function layout()
    {
        $banners = Banner::orderBy('order')->get();
        return view('settings.layout', compact('banners'));
    }

    public function saveLayout(Request $request)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // max 5MB
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $imageFile) {
                $path = $imageFile->store('banners', 'public');

                Banner::updateOrCreate(
                    ['order' => $index],
                    ['image' => $path]
                );
            }
        }

        return back()->with('success', 'Banner images saved successfully!');
    }
}
