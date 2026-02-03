<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Athlete;
use App\Models\School;
use App\Models\Sport;
use App\Models\AuditLog;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AthletesImport;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
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
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('middle_initial', 'like', "%{$search}%")
                  ->orWhere('region', 'like', "%{$search}%")
                  ->orWhere('school_type', 'like', "%{$search}%")
                  ->orWhere('lrn', 'like', "%{$search}%")
                  ->orWhereHas('school', fn($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('sport', fn($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhere('division', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        })
        ->orderBy('last_name')
        ->orderBy('first_name')
        ->paginate(25); // <-- Use pagination (25 per page)

    // Keep the search parameter in pagination links
    $athletes->appends($request->all());

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
            'first_name'      => 'required|string|max:255',
            'middle_initial'  => 'nullable|string|max:1',
            'last_name'       => 'required|string|max:255',
            'sex'             => 'required|in:Male,Female',
            'birthdate'       => 'nullable|date',

            'school_id'       => 'nullable|exists:schools,id',
            'school_name'     => 'nullable|string|max:255',
            'school_logo'     => 'nullable|image|mimes:png,jpg,jpeg|max:2048',

            'sport_id'        => 'nullable|exists:sports,id',
            'sport_name'      => 'nullable|string|max:255',

            'division'        => 'required|in:Elementary,Secondary',
            'category'        => 'required|in:Boys,Girls',
            'status'          => 'required|in:Active,Injured,Disqualified',

            'profile_photo'   => 'nullable|image|max:2048',
            'lrn'             => 'nullable|string|max:20|unique:athletes,lrn',
            'school_type'     => 'nullable|in:Public,Private',
        ]);

        /* ================= SCHOOL AUTO-CREATE ================= */
        if (!$request->school_id && $request->school_name) {
            $logoPath = null;
            if ($request->hasFile('school_logo')) {
                $logoPath = $request->file('school_logo')->store('logos', 'public');
            }

            $school = School::firstOrCreate(
                ['name' => $request->school_name],
                [
                    'division'       => $request->division,          // Elementary/Secondary
                    'school_division'=> $request->school_division,   // City/Region
                    'school_type'    => $request->school_type,
                    'school_address' => $request->school_address,
                    'school_code'    => $request->school_code,
                    'logo'           => $logoPath,
                ]
            );

            $schoolId = $school->id;
        } else {
            $schoolId = $request->school_id;
        }

        /* ================= SPORT AUTO-CREATE ================= */
        if (!$request->sport_id && $request->sport_name) {
            $sport = Sport::firstOrCreate(
                ['name' => $request->sport_name],
                [
                    'division' => $request->division,   // athlete's division
                    'category' => $request->category,   // athlete's category
                ]
            );
            $sportId = $sport->id;
        } else {
            $sportId = $request->sport_id;
        }

        /* ================= ATHLETE PHOTO ================= */
        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')->store('athletes', 'public');
        }

        Athlete::create([
            'first_name'        => $request->first_name,
            'middle_initial'    => $request->middle_initial,
            'last_name'         => $request->last_name,
            'sex'               => $request->sex,
            'birthdate'         => $request->birthdate,

            'school_id'         => $schoolId,
            'sport_id'          => $sportId,

            'division'          => $request->division,
            'category'          => $request->category,
            'status'            => $request->status,

            'region'            => $request->region,
            'school_type'       => $request->school_type,
            'school_address'    => $request->school_address,
            'school_division'   => $request->school_division,
            'school_code'       => $request->school_code,
            'lrn'               => $request->lrn,

            'profile_photo'     => $photoPath,
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

        if ($request->hasFile('profile_photo')) {
            $athlete->profile_photo = $request->file('profile_photo')->store('athletes', 'public');
        }

        $athlete->update([
            'first_name'        => $request->first_name,
            'middle_initial'    => $request->middle_initial,
            'last_name'         => $request->last_name,
            'sex'               => $request->sex,
            'birthdate'         => $request->birthdate,
            'school_id'         => $request->school_id,
            'sport_id'          => $request->sport_id,
            'division'          => $request->division,
            'category'          => $request->category,
            'status'            => $request->status,
            'region'            => $request->region,
            'school_type'       => $request->school_type,
            'school_address'    => $request->school_address,
            'school_division'   => $request->school_division,
            'school_code'       => $request->school_code,
            'lrn'               => $request->lrn,
        ]);

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
            'action'     => 'Deleted Athlete: ' . "{$athlete->lastname}, {$athlete->firstname}",
        ]);

        return redirect()->route('athletes.index')
            ->with('success', 'Athlete deleted successfully!');
    }

    public function qr(Athlete $athlete)
    {
        $qr = QrCode::format('png')->size(300)->generate($athlete->qr_code);
        return response($qr)->header('Content-Type', 'image/png');
    }

    public function bulkUpload(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:csv,xlsx'
    ]);

    Excel::import(new AthletesImport, $request->file('file'));

    return redirect()->route('athletes.index')
        ->with('success', 'Athletes uploaded successfully!');
}

public function print()
{
    $athletes = Athlete::with(['school', 'sport'])->orderBy('last_name')->get();

    $pdf = Pdf::loadView('athletes.print', compact('athletes'))
              ->setPaper('a4', 'portrait');

    return $pdf->stream('athletes.pdf'); // opens in browser
    // or use ->download('athletes.pdf') to force download
}

}
