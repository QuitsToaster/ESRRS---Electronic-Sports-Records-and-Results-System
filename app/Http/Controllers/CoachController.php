<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coach;
use App\Models\School;
use App\Models\Sport;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class CoachController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');

    $coaches = Coach::with(['school', 'sport'])
        ->when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('school', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('sport', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        })
        ->orderBy('name')
        ->get();

    return view('coaches.index', compact('coaches'));
}


    public function create()
    {
        $schools = School::all();
        $sports = Sport::all();
        return view('coaches.create', compact('schools', 'sports'));
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'school_id' => 'required|exists:schools,id',
        'sport_id' => 'required|exists:sports,id',
        'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $data = $request->only(['name', 'school_id', 'sport_id']);

    // Handle profile photo
    if ($request->hasFile('profile_photo')) {
        $data['profile_photo'] = $request->file('profile_photo')
            ->store('coach_photos', 'public');
    }

    // Generate unique QR code string
    $data['qr_code'] = 'eSRRS-COACH-' . strtoupper(Str::random(8));

    $coach = Coach::create($data);

    // Audit Log
    AuditLog::create([
        'model_type' => 'Coach',
        'model_id' => $coach->id,
        'user_id' => Auth::id(),
        'old_values' => null,
        'new_values' => json_encode($coach->toArray()),
        'action' => 'Created Coach: ' . $coach->name,
    ]);

    return redirect()->route('coaches.index')
        ->with('success', 'Coach added successfully! QR code generated.');
}

public function qr(Coach $coach)
{
    $qr = QrCode::format('png')->size(300)->generate($coach->qr_code);
    return response($qr)->header('Content-Type', 'image/png');
}



    public function edit(Coach $coach)
    {
        $schools = School::all();
        $sports = Sport::all();
        return view('coaches.edit', compact('coach', 'schools', 'sports'));
    }

    public function update(Request $request, Coach $coach)
{
    $request->validate([
        'name' => 'required|string',
        'school_id' => 'required|exists:schools,id',
        'sport_id' => 'required|exists:sports,id',
        'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);

    $oldValues = $coach->toArray(); // store old data

    // Handle profile photo upload
    if ($request->hasFile('profile_photo')) {
        // Delete old photo if exists
        if ($coach->profile_photo && file_exists(storage_path('app/public/' . $coach->profile_photo))) {
            unlink(storage_path('app/public/' . $coach->profile_photo));
        }

        // Store new photo
        $path = $request->file('profile_photo')->store('coaches', 'public');
        $coach->profile_photo = $path;
    }

    // Update other fields
    $coach->name = $request->name;
    $coach->school_id = $request->school_id;
    $coach->sport_id = $request->sport_id;
    $coach->save();

    // Audit log for update
    AuditLog::create([
        'model_type' => 'Coach',
        'model_id' => $coach->id,
        'user_id' => Auth::id(),
        'old_values' => json_encode($oldValues),
        'new_values' => json_encode($coach->toArray()),
        'action' => 'Updated Coach: '.$coach->name,
    ]);

    return redirect()->route('coaches.index')->with('success', 'Coach updated successfully!');
}


    public function destroy(Coach $coach)
    {
        $oldValues = $coach->toArray();

        $coach->delete();

        // Audit log for deletion
        AuditLog::create([
            'model_type' => 'Coach',
            'model_id' => $coach->id,
            'user_id' => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => null,
            'action' => 'Deleted Coach: '.$coach->name,
        ]);

        return redirect()->route('coaches.index')->with('success', 'Coach deleted successfully!');
    }
}
