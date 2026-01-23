<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Athlete;
use App\Models\School;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Sport;
use App\Models\AuditLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AthleteController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');

    $athletes = Athlete::with(['school', 'sport'])
        ->when($search, function($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('school', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('sport', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhere('division', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%");
        });

    // Apply ordering and get results
    $athletes = $athletes->orderBy('name')->get();

    return view('athletes.index', compact('athletes'));
}


    public function create()
    {
        $schools = School::all();
        $sports = Sport::all();
        return view('athletes.create', compact('schools', 'sports'));
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'school_id' => 'required|exists:schools,id',
        'sport_id' => 'required|exists:sports,id',
        'division' => 'required|in:Elementary,Secondary',
        'category' => 'required|in:Boys,Girls',
        'status' => 'required|in:Active,Disqualified,Injured',
        'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Handle profile photo upload
    $photoPath = null;
    if ($request->hasFile('profile_photo')) {
        $photoPath = $request->file('profile_photo')
            ->store('athletes', 'public');
    }

    // Generate QR
    $qrValue = 'CEMS-' . strtoupper(Str::random(8));

    $athlete = Athlete::create([
        'name' => $request->name,
        'school_id' => $request->school_id,
        'sport_id' => $request->sport_id,
        'division' => $request->division,
        'category' => $request->category,
        'status' => $request->status,
        'qr_code' => $qrValue,
        'profile_photo' => $photoPath,
    ]);

    AuditLog::create([
        'model_type' => 'Athlete',
        'model_id' => $athlete->id,
        'user_id' => Auth::id(),
        'old_values' => null,
        'new_values' => json_encode($athlete->toArray()),
        'action' => 'Created Athlete: ' . $athlete->name,
    ]);

    return redirect()->route('athletes.index')
        ->with('success', 'Athlete added successfully!');
}




    public function edit(Athlete $athlete)
    {
        $schools = School::all();
        $sports = Sport::all();
        return view('athletes.edit', compact('athlete', 'schools', 'sports'));
    }

   public function update(Request $request, Athlete $athlete)
{
    $request->validate([
        'name' => 'required|string',
        'school_id' => 'required|exists:schools,id',
        'sport_id' => 'required|exists:sports,id',
        'division' => 'required|in:Elementary,Secondary',
        'category' => 'required|in:Boys,Girls',
        'status' => 'required|in:Active,Disqualified,Injured',
        'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $oldValues = $athlete->toArray();

    if ($request->hasFile('profile_photo')) {
        $athlete->profile_photo = $request->file('profile_photo')
            ->store('athletes', 'public');
    }

    $athlete->update($request->except('profile_photo'));

    AuditLog::create([
        'model_type' => 'Athlete',
        'model_id' => $athlete->id,
        'user_id' => Auth::id(),
        'old_values' => json_encode($oldValues),
        'new_values' => json_encode($athlete->toArray()),
        'action' => 'Updated Athlete: ' . $athlete->name,
    ]);

    return redirect()->route('athletes.index')
        ->with('success', 'Athlete updated successfully!');
}


    public function destroy(Athlete $athlete)
    {
        $oldValues = $athlete->toArray();

        $athlete->delete();

        // Audit log for deletion
        AuditLog::create([
            'model_type' => 'Athlete',
            'model_id' => $athlete->id,
            'user_id' => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => null,
            'action' => 'Deleted Athlete: '.$athlete->name,
        ]);

        return redirect()->route('athletes.index')->with('success', 'Athlete deleted successfully!');
    }

    public function qr(Athlete $athlete)
{
    // Generate QR code image
    $qr = QrCode::format('png')->size(300)->generate($athlete->qr_code);

    // Return as response with proper headers
    return response($qr)->header('Content-Type', 'image/png');
}
}
