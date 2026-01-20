<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Sport;
use Illuminate\Http\Request;

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
            'name'=>'required|string',
            'sport_id'=>'required|exists:sports,id',
            'level'=>'required',
            'start_date'=>'required|date',
            'end_date'=>'required|date|after_or_equal:start_date',
            'venue'=>'required|string',
            'age_group'=>'required',
            'category'=>'required',
        ]);

        Event::create($request->all());

        return redirect()->route('events.index')->with('success','Event created successfully!');
    }

    public function edit(Event $event)
    {
        $sports = Sport::all();
        return view('events.edit', compact('event','sports'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'name'=>'required|string',
            'sport_id'=>'required|exists:sports,id',
            'level'=>'required',
            'start_date'=>'required|date',
            'end_date'=>'required|date|after_or_equal:start_date',
            'venue'=>'required|string',
            'age_group'=>'required',
            'category'=>'required',
        ]);

        $event->update($request->all());

        return redirect()->route('events.index')->with('success','Event updated successfully!');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success','Event deleted successfully!');
    }
}
