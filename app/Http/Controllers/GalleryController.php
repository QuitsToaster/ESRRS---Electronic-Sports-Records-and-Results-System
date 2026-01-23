<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GalleryPhoto;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    // Show all photos
    public function index()
    {
        $photos = GalleryPhoto::latest()->get();
        return view('gallery.index', compact('photos'));
    }

    // Show form to add photos
    public function create()
    {
        return view('gallery.create');
    }

    // Store uploaded photos
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photos.*' => 'required|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $uploadedPhotos = [];
        if($request->hasFile('photos')) {
            foreach($request->file('photos') as $photo) {
                $uploadedPhotos[] = $photo->store('gallery', 'public');
            }
        }

        $gallery = GalleryPhoto::create([
            'title' => $request->title,
            'description' => $request->description,
            'photos' => $uploadedPhotos,
            'uploaded_by' => Auth::id(),
        ]);

        // Audit log
        AuditLog::create([
            'model_type' => 'GalleryPhoto',
            'model_id' => $gallery->id,
            'user_id' => Auth::id(),
            'action' => 'Created Gallery Photo',
            'old_values' => null,
            'new_values' => json_encode($gallery->toArray()),
        ]);

        return redirect()->route('gallery.index')->with('success', 'Photos uploaded successfully!');
    }

    // Show form to edit photo
    public function edit(GalleryPhoto $photo)
    {
        return view('gallery.edit', compact('photo'));
    }

    // Update gallery photo
    public function update(Request $request, GalleryPhoto $photo)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photos.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'delete_photos' => 'nullable|array',
        ]);

        $oldValues = $photo->toArray();

        // Delete selected photos
        if($request->filled('delete_photos')) {
            $photos = $photo->photos;
            foreach($request->delete_photos as $index) {
                if(isset($photos[$index])) {
                    Storage::disk('public')->delete($photos[$index]);
                    unset($photos[$index]);
                }
            }
            // Re-index array
            $photo->photos = array_values($photos);
        }

        // Add new photos
        if($request->hasFile('photos')) {
            $newPhotos = [];
            foreach($request->file('photos') as $p) {
                $newPhotos[] = $p->store('gallery', 'public');
            }
            $photo->photos = array_merge($photo->photos ?? [], $newPhotos);
        }

        $photo->title = $request->title;
        $photo->description = $request->description;
        $photo->save();

        // Audit log
        AuditLog::create([
            'model_type' => 'GalleryPhoto',
            'model_id' => $photo->id,
            'user_id' => Auth::id(),
            'action' => 'Updated Gallery Photo',
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($photo->toArray()),
        ]);

        return redirect()->route('gallery.index')->with('success', 'Gallery updated successfully!');
    }

    // Delete gallery photo
    public function destroy(GalleryPhoto $photo)
    {
        $oldValues = $photo->toArray();

        // Delete images from storage
        foreach($photo->photos as $p) {
            if(Storage::disk('public')->exists($p)) {
                Storage::disk('public')->delete($p);
            }
        }

        $photo->delete();

        // Audit log
        AuditLog::create([
            'model_type' => 'GalleryPhoto',
            'model_id' => $photo->id,
            'user_id' => Auth::id(),
            'action' => 'Deleted Gallery Photo',
            'old_values' => json_encode($oldValues),
            'new_values' => null,
        ]);

        return redirect()->route('gallery.index')->with('success', 'Photo deleted successfully!');
    }
}
