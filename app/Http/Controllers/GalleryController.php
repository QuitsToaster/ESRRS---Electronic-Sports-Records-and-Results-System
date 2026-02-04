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

    public function create()
    {
        // Get unique folders from existing photos
        $existingFolders = GalleryPhoto::distinct()->pluck('folder')->filter()->toArray();
        return view('gallery.create', compact('existingFolders'));
    }


    public function store(Request $request)
{
    $request->validate([
        'title'       => 'required|string|max:255',
        'description' => 'nullable|string',
        'photos'      => 'required',
        'photos.*'    => 'image|mimes:jpg,jpeg,png|max:4096',
        'folder'      => 'nullable|string',
        'new_folder'  => 'required_if:folder,new|max:255',
    ]);

    // Determine folder
    if ($request->folder === 'new') {
        $folder = trim($request->new_folder);
    } else {
        $folder = trim($request->folder);
    }

    // Fallback
    $folder = $folder ?: 'default';

    // Sanitize folder name
    $folder = preg_replace('/[^a-zA-Z0-9_\- ]/', '', $folder);

    $folderPath = "gallery/{$folder}";

    // ✅ Force directory creation
    if (!Storage::disk('public')->exists($folderPath)) {
        Storage::disk('public')->makeDirectory($folderPath);
    }

    $uploadedPhotos = [];

    foreach ($request->file('photos') as $photo) {
        $uploadedPhotos[] = $photo->store($folderPath, 'public');
    }

    $gallery = GalleryPhoto::create([
        'title'       => $request->title,
        'folder'      => $folder,
        'description' => $request->description,
        'photos'      => $uploadedPhotos,
        'uploaded_by' => Auth::id(),
    ]);

    AuditLog::create([
        'model_type' => 'GalleryPhoto',
        'model_id'   => $gallery->id,
        'user_id'    => Auth::id(),
        'action'     => 'Created Gallery Photo',
        'old_values' => null,
        'new_values' => json_encode($gallery->toArray()),
    ]);

    return redirect()
        ->route('gallery.index')
        ->with('success', 'Photos uploaded successfully!');
}





    public function edit(GalleryPhoto $photo)
{
    // Existing folders for select dropdown
    $existingFolders = GalleryPhoto::distinct()->pluck('folder')->filter()->toArray();
    return view('gallery.edit', compact('photo', 'existingFolders'));
}

   public function update(Request $request, GalleryPhoto $photo)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'photos.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        'delete_photos' => 'nullable|array',
        'folder' => 'nullable|string',
        'new_folder' => 'nullable|string|max:255',
    ]);

    $oldValues = $photo->toArray();

    // Determine folder
    $folder = $request->folder === 'new' ? $request->new_folder : $request->folder;
    $folder = $folder ?: 'default';
    $photo->folder = $folder;

    $folderPath = "gallery/{$folder}";

if (!Storage::disk('public')->exists($folderPath)) {
    Storage::disk('public')->makeDirectory($folderPath);
}


    // Delete selected photos
    if($request->filled('delete_photos')) {
        $photosArr = $photo->photos;
        foreach($request->delete_photos as $index) {
            if(isset($photosArr[$index])) {
                Storage::disk('public')->delete($photosArr[$index]);
                unset($photosArr[$index]);
            }
        }
        $photo->photos = array_values($photosArr);
    }

    // Add new photos
    if($request->hasFile('photos')) {
        $newPhotos = [];
        foreach($request->file('photos') as $p) {
            $newPhotos[] = $p->store("gallery/{$folder}", 'public');
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
