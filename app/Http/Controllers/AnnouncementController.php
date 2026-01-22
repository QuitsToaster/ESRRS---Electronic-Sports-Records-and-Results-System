<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use App\Models\AuditLog;

class AnnouncementController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');

    $announcements = Announcement::with(['files', 'event', 'user'])
        ->when($search, function ($query, $search) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhereHas('event', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        })
        ->latest('published_at')
        ->get();

    return view('announcements.index', compact('announcements'));
}


    public function create()
    {
        $events = Event::all();
        return view('announcements.create', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'content'   => 'required|string',
            'type'      => 'required|in:General,Schedule Update,Venue Update,Results',
            'event_id'  => 'nullable|exists:events,id',
            'files.*'   => 'nullable|file|mimes:jpg,png,pdf,docx'
        ]);

        $announcement = Announcement::create([
            'title'        => $request->title,
            'content'      => $request->content,
            'type'         => $request->type,
            'event_id'     => $request->event_id,
            'user_id'      => auth()->id(),
            'published_at' => now(),
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('announcements', 'public');

                $announcement->files()->create([
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            }
        }

        AuditLog::create([
            'model_type' => 'Announcement',
            'model_id'   => $announcement->id,
            'user_id'    => auth()->id(),
            'action'     => 'Created Announcement',
            'old_values' => null,
            'new_values' => json_encode($announcement->toArray()),
        ]);

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement posted successfully.');
    }

    public function edit(Announcement $announcement)
    {
        $events = Event::all();
        return view('announcements.edit', compact('announcement', 'events'));
    }

    
public function update(Request $request, Announcement $announcement)
{
    $request->validate([
        'title'     => 'required|string|max:255',
        'content'   => 'required|string',
        'type'      => 'required|in:General,Schedule Update,Venue Update,Results',
        'event_id'  => 'nullable|exists:events,id',
        'files.*'   => 'nullable|file|mimes:jpg,png,pdf,docx'
    ]);

    $oldValues = $announcement->toArray();

    // Update announcement details
    $announcement->update([
        'title'    => $request->title,
        'content'  => $request->content,
        'type'     => $request->type,
        'event_id' => $request->event_id,
    ]);

    /**
     * ------------------------------------------------
     * DELETE SELECTED ATTACHMENTS
     * ------------------------------------------------
     */
    if ($request->filled('delete_files')) {
        $filesToDelete = $announcement->files()
            ->whereIn('id', $request->delete_files)
            ->get();

        foreach ($filesToDelete as $file) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
        }
    }

    /**
     * ------------------------------------------------
     * DELETE ALL EXISTING FILES IF CHECKED
     * ------------------------------------------------
     */
    if ($request->has('remove_existing_files') && $request->hasFile('files')) {
        foreach ($announcement->files as $file) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
        }
    }

    /**
     * ------------------------------------------------
     * SAVE NEW FILES
     * ------------------------------------------------
     */
    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            $path = $file->store('announcements', 'public');

            $announcement->files()->create([
                'file_path' => $path,
                'file_type' => $file->getClientOriginalExtension(),
            ]);
        }
    }

    // Audit Log
    AuditLog::create([
        'model_type' => 'Announcement',
        'model_id'   => $announcement->id,
        'user_id'    => auth()->id(),
        'action'     => 'Updated Announcement',
        'old_values' => json_encode($oldValues),
        'new_values' => json_encode($announcement->fresh()->toArray()),
    ]);

    return redirect()->route('announcements.index')
        ->with('success', 'Announcement updated successfully.');
}

    public function destroy(Announcement $announcement)
    {
        $oldValues = $announcement->toArray();

        $announcement->delete();

        AuditLog::create([
            'model_type' => 'Announcement',
            'model_id'   => $announcement->id,
            'user_id'    => auth()->id(),
            'action'     => 'Deleted Announcement',
            'old_values' => json_encode($oldValues),
            'new_values' => null,
        ]);

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }
}
