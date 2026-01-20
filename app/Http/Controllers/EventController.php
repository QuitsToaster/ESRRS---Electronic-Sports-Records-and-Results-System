<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Sport;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('sport')->get();
        return view('events.index', compact('events'));
    }

    public function create()
    {
        $sports = Sport::all();
        return view('events.create', compact('sports'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'sport_id' => 'required|exists:sports,id',
            'level' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'venue' => 'required|string',
            'age_group' => 'required',
            'category' => 'required',
        ]);

        $event = Event::create($request->all());

        // Audit log for creation
        AuditLog::create([
            'model_type' => 'Event',
            'model_id' => $event->id,
            'user_id' => Auth::id(),
            'old_values' => null,
            'new_values' => json_encode($event->toArray()),
            'action' => 'Created Event: '.$event->name,
        ]);

        return redirect()->route('events.index')->with('success', 'Event created successfully!');
    }

    public function edit(Event $event)
    {
        $sports = Sport::all();
        return view('events.edit', compact('event', 'sports'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'name' => 'required|string',
            'sport_id' => 'required|exists:sports,id',
            'level' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'venue' => 'required|string',
            'age_group' => 'required',
            'category' => 'required',
        ]);

        $oldValues = $event->toArray(); // store old data

        $event->update($request->all());

        // Audit log for update
        AuditLog::create([
            'model_type' => 'Event',
            'model_id' => $event->id,
            'user_id' => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($event->toArray()),
            'action' => 'Updated Event: '.$event->name,
        ]);

        return redirect()->route('events.index')->with('success', 'Event updated successfully!');
    }

    public function destroy(Event $event)
    {
        $oldValues = $event->toArray();

        $event->delete();

        // Audit log for deletion
        AuditLog::create([
            'model_type' => 'Event',
            'model_id' => $event->id,
            'user_id' => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => null,
            'action' => 'Deleted Event: '.$event->name,
        ]);

        return redirect()->route('events.index')->with('success', 'Event deleted successfully!');
    }
}
