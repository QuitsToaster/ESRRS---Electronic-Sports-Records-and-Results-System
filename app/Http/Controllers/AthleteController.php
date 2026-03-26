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
        $user = auth()->user();

        $search         = $request->search;
        $sex            = $request->sex;
        $sport          = $request->sport;
        $division       = $request->division;
        $schoolDivision = $request->school_division;

        // ==========================
        // DETERMINE DIVISION OFFICIAL
        // ==========================
        $officialDivision = null;

        if ($user->role && $user->role->name === 'Division Official') {
            // Extract "Batanes" from "SDO - Batanes Official"
            if (preg_match('/SDO\s*-\s*(.+?)\s*Official/i', $user->name, $matches)) {
                $officialDivision = trim($matches[1]); // "Batanes"
            }
        }

        // ==========================
        // BASE QUERY
        // ==========================
        $query = Athlete::with(['school', 'sport'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('middle_initial', 'like', "%{$search}%")
                      ->orWhere('lrn', 'like', "%{$search}%")
                      ->orWhere('division', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%")
                      ->orWhereHas('school', fn($sq) => $sq->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('sport', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($sex, fn($q) => $q->where('sex', $sex))
            ->when($sport, fn($q) => $q->where('sport_id', $sport))
            ->when($division, fn($q) => $q->where('division', $division))
            ->when($schoolDivision, fn($q) => $q->where('school_division', $schoolDivision));

        // ==========================
        // FORCE DIVISION RESTRICTION
        // ==========================
        if ($officialDivision) {
            $query->where('school_division', $officialDivision);
        }

        $athleteCount = $query->count();

        $athletes = $query->orderBy('last_name')
            ->paginate(25)
            ->appends($request->all());

        $sports = Sport::orderBy('name')->get();

        // ==========================
        // DIVISION DROPDOWN
        // ==========================
        if ($officialDivision) {
            // Only show their own division
            $divisions = collect([$officialDivision]);
        } else {
            $divisions = School::whereNotNull('school_division')
                ->distinct()
                ->orderBy('school_division')
                ->pluck('school_division');
        }

        return view('athletes.index', compact(
            'athletes',
            'sports',
            'athleteCount',
            'divisions'
        ));
    }



    public function create()
    {
        $schools = School::all();
        $sports = Sport::all();
        return view('athletes.create', compact('schools', 'sports'));
    }

   public function store(Request $request)
{
    try {

        $request->validate([
            'first_name'      => 'nullable|string|max:255',
            'middle_initial'  => 'nullable|string|max:1',
            'last_name'       => 'nullable|string|max:255',
            'sex'             => 'nullable|in:Male,Female',
            'birthdate'       => 'nullable|date',

            'school_id'       => 'nullable|exists:schools,id',
            'school_name'     => 'nullable|string|max:255',
            'school_logo'     => 'nullable|image|mimes:png,jpg,jpeg|max:2048',

            'sport_id'        => 'nullable|exists:sports,id',
            'sport_name'      => 'nullable|string|max:255',
            'municipality'    => 'nullable|string|max:255',

            'division'        => 'nullable|in:Elementary,Secondary,Paragames',
            'category'        => 'nullable|in:Boys,Girls',
            'status'          => 'nullable|in:Active,Injured,Disqualified',

            'profile_photo'   => 'nullable|image|max:2048',
            'lrn'             => 'nullable|string|max:20',
            'school_type'     => 'nullable|in:Public,Private',
        ]);

        $val = function ($field) use ($request) {
            return $request->$field ? $request->$field : 'N/A';
        };

        /* ================= SCHOOL ================= */
        if (!$request->school_id && $request->school_name) {

            $logoPath = null;
            if ($request->hasFile('school_logo')) {
                $logoPath = $request->file('school_logo')->store('logos', 'public');
            }

            $school = School::firstOrCreate(
                ['name' => $request->school_name],
                [
                    'division'        => $val('division'),
                    'school_division' => $val('school_division'),
                    'school_type'     => $val('school_type'),
                    'school_address'  => $val('school_address'),
                    'school_code'     => $val('school_code'),
                    'logo'            => $logoPath,
                ]
            );

            $schoolId = $school->id;

        } else {
            $schoolId = $request->school_id;
        }

        $sportId = $request->sport_id ?? null;

        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')->store('athletes', 'public');
        }

        Athlete::create([
            'first_name'        => $val('first_name'),
            'middle_initial'    => $val('middle_initial'),
            'last_name'         => $val('last_name'),
            'sex'               => $val('sex'),
            'birthdate'         => $request->birthdate ?? null,
            'school_id'         => $schoolId,
            'sport_id'          => $sportId,
            'division'          => $val('division'),
            'category'          => $val('category'),
            'municipality'      => $val('municipality'),
            'status'            => $val('status'),
            'region'            => $val('region'),
            'school_type'       => $val('school_type'),
            'school_address'    => $val('school_address'),
            'school_division'   => $val('school_division'),
            'school_code'       => $val('school_code'),
            'lrn'               => $request->lrn ?: 'N/A',
            'profile_photo'     => $photoPath,
            'legislative_district' => $val('legislative_district'),
        ]);

        return redirect()->route('athletes.index')
            ->with('success', 'Athlete added successfully!');

    } catch (\Throwable $e) {

    $message = 'Failed to save athlete.';

    // 🔥 HANDLE DUPLICATE LRN
    if (str_contains($e->getMessage(), 'athletes_lrn_unique')) {
        $message = 'Duplicate LRN detected. This LRN is already assigned to another athlete.';
    }

    // 🔥 HANDLE FOREIGN KEY (INVALID SCHOOL / SPORT)
    elseif (str_contains($e->getMessage(), 'foreign key')) {
        $message = 'Invalid selection detected (School or Sport may not exist).';
    }

    // 🔥 HANDLE IMAGE ERRORS
    elseif (str_contains($e->getMessage(), 'image')) {
        $message = 'Image upload failed. Please use a valid image file.';
    }

    return back()
        ->withInput()
        ->with('error', $message)
        ->with('debug_error', $e->getMessage()); // optional (for dev only)
}
}

    public function edit(Athlete $athlete)
    {
        $schools = School::all();
        $sports = Sport::all();
        return view('athletes.edit', compact('athlete', 'schools', 'sports'));
    }

    public function update(Request $request, Athlete $athlete)
{
    // ✅ Flexible validation (no required fields)
    $request->validate([
        'first_name'       => 'nullable|string|max:255',
        'middle_initial'   => 'nullable|string|max:1',
        'last_name'        => 'nullable|string|max:255',
        'sex'              => 'nullable|in:Male,Female',
        'birthdate'        => 'nullable|date',
        'school_id'        => 'nullable|exists:schools,id',
        'municipality'     => 'nullable|string|max:255',
        'sport_id'         => 'nullable|exists:sports,id',
        'division'         => 'nullable|in:Elementary,Secondary,Paragames',
        'category'         => 'nullable|in:Boys,Girls',
        'status'           => 'nullable|in:Active,Disqualified,Injured',
        'profile_photo'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'legislative_district' => 'nullable|string|max:255',
        'lrn'              => 'nullable|string|max:20',
        'qr_code'           => 'nullable|string|max:255',
    ]);

    $oldValues = $athlete->toArray();

    // ✅ Handle photo
    $photoPath = $athlete->profile_photo;

    if ($request->hasFile('profile_photo')) {
        $photoPath = $request->file('profile_photo')->store('athletes', 'public');
    }

    // ✅ Only update fields that are NOT NULL (preserve old data)
    $data = array_filter([
        'first_name'            => $request->first_name,
        'middle_initial'        => $request->middle_initial,
        'last_name'             => $request->last_name,
        'sex'                   => $request->sex,
        'birthdate'             => $request->birthdate,
        'school_id'             => $request->school_id,
        'sport_id'              => $request->sport_id,
        'division'              => $request->division,
        'category'              => $request->category,
        'status'                => $request->status,
        'region'                => $request->region,
        'school_type'           => $request->school_type,
        'school_address'        => $request->school_address,
        'school_division'       => $request->school_division,
        'municipality'          => $request->municipality,
        'school_code'           => $request->school_code,
        'lrn'                   => $request->lrn,
        'legislative_district'  => $request->legislative_district,
        'qr_code'               => $request->qr_code,
    ], function ($value) {
        return !is_null($value);
    });

    // ✅ Always include photo (so it updates properly)
    $data['profile_photo'] = $photoPath;

    // ✅ Perform update
    $athlete->update($data);

    // ✅ Audit Log
    AuditLog::create([
        'model_type' => 'Athlete',
        'model_id'   => $athlete->id,
        'user_id'    => Auth::id(),
        'old_values' => json_encode($oldValues),
        'new_values' => json_encode($athlete->fresh()->toArray()),
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

        // ✅ Bulk import: No auto-creation of sports
        Excel::import(new AthletesImport, $request->file('file'));

        return redirect()->route('athletes.index')
            ->with('success', 'Athletes uploaded successfully!');
    }

  public function exportIdPdf(Request $request)
{
    $user = auth()->user();

    $division   = $request->input('school_division');
    $start      = $request->input('start');
    $end        = $request->input('end');
    $athleteId  = $request->input('athlete_id'); // ✅ ADD THIS

    $query = Athlete::with(['school', 'sport'])
        ->orderBy('last_name')
        ->orderBy('first_name');

    // 🔒 Division Official restriction
    $officialDivision = null;

    if ($user->role && $user->role->name === 'Division Official') {
        if (preg_match('/SDO\s*-\s*(.+?)\s*Official/i', $user->name, $matches)) {
            $officialDivision = trim($matches[1]);
            $query->where('school_division', $officialDivision);
        }
    }

    // 🟢 ✅ PRIORITY: SELECTED ATHLETE ONLY
    if ($athleteId) {
        $query->where('id', $athleteId);
    }

    // 🟢 Super Admin Division Filter (only if NOT selecting specific athlete)
    elseif ($division) {
        $query->where('school_division', $division);
    }

    /* ==========================
       ATHLETE RANGE LOGIC
    ========================== */

    elseif ($start && $end) {

        $start = max(1, (int) $start);
        $end   = max($start, (int) $end);

        $offset = $start - 1;
        $limit  = $end - $start + 1;

        $query->skip($offset)->take($limit);
    }

    $athletes = $query->get();

    /* ==========================
       AUDIT LOG
    ========================== */

    $exportedDivision = $officialDivision ?? $division ?? 'All Divisions';

    $actionDescription = "Exported Athlete ID Cards PDF | "
        . "Exported By: {$user->name} | "
        . "Role: " . ($user->role->name ?? 'N/A') . " | "
        . "Total Exported: {$athletes->count()} | "
        . "Division: {$exportedDivision}";

    if ($athleteId) {
        $actionDescription .= " | Selected Athlete ID: {$athleteId}";
    }

    if ($start && $end) {
        $actionDescription .= " | Athlete Range: {$start}-{$end}";
    }

    AuditLog::create([
        'model_type' => 'Athlete',
        'model_id'   => $athleteId ?? 0,
        'user_id'    => $user->id,
        'old_values' => null,
        'new_values' => json_encode([
            'exported_count' => $athletes->count(),
            'division'       => $exportedDivision,
            'athlete_id'     => $athleteId,
            'start'          => $start,
            'end'            => $end,
        ]),
        'action'     => $actionDescription,
    ]);

    /* ==========================
       GENERATE PDF
    ========================== */

    $pdf = Pdf::loadView('athletes.id-cards', compact('athletes'))
        ->setPaper('letter', 'portrait')
        ->setOption('dpi', 96)
        ->setOption('defaultFont', 'DejaVu Sans');

    return $pdf->download(
        'athlete-id-cards-' . $athletes->count() . '.pdf'
    );
}

public function search(Request $request)
{
    $query = strtolower(trim($request->q));

    $query = str_replace(',', ' ', $query);
    $words = array_filter(explode(' ', $query));

    $athletes = Athlete::with('school')
        ->get()
        ->filter(function ($a) use ($words) {

            $first  = strtolower($a->first_name ?? '');
            $middle = strtolower($a->middle_initial ?? '');
            $last   = strtolower($a->last_name ?? '');

            $full1 = trim("$first $middle $last");
            $full2 = trim("$last $first $middle");

            foreach ($words as $w) {
                if (
                    !str_contains($full1, $w) &&
                    !str_contains($full2, $w) &&
                    !str_contains($first, $w) &&
                    !str_contains($last, $w) &&
                    !str_contains($middle, $w)
                ) {
                    return false;
                }
            }

            return true;
        })
        ->take(50) // limit results for performance
        ->values();

    return response()->json($athletes);
}

}
