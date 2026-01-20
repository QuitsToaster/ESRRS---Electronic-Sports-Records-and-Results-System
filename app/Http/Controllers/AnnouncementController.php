<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\AuditLog;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with(['files', 'event', 'user'])
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
            'type'      => 'required|in:General,Schedule,Venue,Result',
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
            'type'      => 'required|in:General,Schedule,Venue,Result',
            'event_id'  => 'nullable|exists:events,id',
            'files.*'   => 'nullable|file|mimes:jpg,png,pdf,docx'
        ]);

        $oldValues = $announcement->toArray();

        $announcement->update([
            'title'    => $request->title,
            'content'  => $request->content,
            'type'     => $request->type,
            'event_id' => $request->event_id,
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
