<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Athlete;
use App\Models\School;
use App\Models\Sport;
use App\Models\AuditLog;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AthleteController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $athletes = Athlete::with(['school', 'sport'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%")
                      ->orWhere('middle_initial', 'like', "%{$search}%")
                      ->orWhereHas('school', function ($sq) use ($search) {
                          $sq->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('sport', function ($sq) use ($search) {
                          $sq->where('name', 'like', "%{$search}%");
                      })
                      ->orWhere('division', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

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
            'first_name'        => 'required|string|max:255',
            'middle_initial'   => 'nullable|string|max:1',
            'last_name'         => 'required|string|max:255',
            'sex'              => 'required|in:Male,Female',
            'birthdate'        => 'nullable|date',
            'school_id'        => 'required|exists:schools,id',
            'sport_id'         => 'required|exists:sports,id',
            'division'         => 'required|in:Elementary,Secondary',
            'category'         => 'required|in:Boys,Girls',
            'status'           => 'required|in:Active,Disqualified,Injured',
            'profile_photo'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Upload profile photo
        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')
                ->store('athletes', 'public');
        }

        // Generate QR code value
        $qrValue = 'CEMS-' . strtoupper(Str::random(8));

        $athlete = Athlete::create([
            'first_name'        => $request->first_name,
            'middle_initial'   => $request->middle_initial,
            'last_name'         => $request->last_name,
            'sex'              => $request->sex,
            'birthdate'        => $request->birthdate,
            'school_id'        => $request->school_id,
            'sport_id'         => $request->sport_id,
            'division'         => $request->division,
            'category'         => $request->category,
            'status'           => $request->status,
            'qr_code'          => $qrValue,
            'profile_photo'    => $photoPath,
        ]);

        AuditLog::create([
            'model_type' => 'Athlete',
            'model_id'   => $athlete->id,
            'user_id'    => Auth::id(),
            'old_values' => null,
            'new_values' => json_encode($athlete->toArray()),
            'action'     => 'Created Athlete: ' .
                "{$athlete->lastname}, {$athlete->firstname}",
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
        'first_name'       => 'required|string|max:255',
        'middle_initial'   => 'nullable|string|max:1',
        'last_name'        => 'required|string|max:255',
        'sex'              => 'required|in:Male,Female',
        'birthdate'        => 'nullable|date',
        'school_id'        => 'required|exists:schools,id',
        'sport_id'         => 'required|exists:sports,id',
        'division'         => 'required|in:Elementary,Secondary',
        'category'         => 'required|in:Boys,Girls',
        'status'           => 'required|in:Active,Disqualified,Injured',
        'profile_photo'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $oldValues = $athlete->toArray();

    // Photo upload
    if ($request->hasFile('profile_photo')) {
        $athlete->profile_photo = $request->file('profile_photo')
            ->store('athletes', 'public');
    }

    // Update fields
    $athlete->first_name      = $request->first_name;
    $athlete->middle_initial  = $request->middle_initial;
    $athlete->last_name       = $request->last_name;
    $athlete->sex             = $request->sex;
    $athlete->birthdate       = $request->birthdate;
    $athlete->school_id       = $request->school_id;
    $athlete->sport_id        = $request->sport_id;
    $athlete->division        = $request->division;
    $athlete->category        = $request->category;
    $athlete->status          = $request->status;

    $athlete->save(); // ✅ important

    AuditLog::create([
        'model_type' => 'Athlete',
        'model_id'   => $athlete->id,
        'user_id'    => Auth::id(),
        'old_values' => json_encode($oldValues),
        'new_values' => json_encode($athlete->toArray()),
        'action'     => 'Updated Athlete: ' . "{$athlete->last_name}, {$athlete->first_name}",
    ]);

    return redirect()->route('athletes.index')
        ->with('success', 'Athlete updated successfully!');
}



    public function destroy(Athlete $athlete)
    {
        $oldValues = $athlete->toArray();

        $athlete->delete();

        AuditLog::create([
            'model_type' => 'Athlete',
            'model_id'   => $athlete->id,
            'user_id'    => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => null,
            'action'     => 'Deleted Athlete: ' .
                "{$athlete->lastname}, {$athlete->firstname}",
        ]);

        return redirect()->route('athletes.index')
            ->with('success', 'Athlete deleted successfully!');
    }

    public function qr(Athlete $athlete)
    {
        $qr = QrCode::format('png')
            ->size(300)
            ->generate($athlete->qr_code);

        return response($qr)->header('Content-Type', 'image/png');
    }
}
