<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Athlete;
use App\Models\School;
use App\Models\Sport;
use App\Models\AuditLog;
use App\Models\IndividualAwardResult; 
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

    $search         = trim($request->search);
    $sex            = $request->sex;
    $sport          = $request->sport;
    $division       = $request->division;
    $schoolDivision = $request->school_division;

    // ==========================
    // DETERMINE DIVISION OFFICIAL
    // ==========================
    $officialDivision = null;

    if ($user->role && $user->role->name === 'Division Official') {
        if (preg_match('/SDO\s*-\s*(.+?)\s*Official/i', $user->name, $matches)) {
            $officialDivision = trim($matches[1]);
        }
    }

    // ==========================
    // BASE QUERY
    // ==========================
    $query = Athlete::with(['school', 'sport'])

        ->when($search, function ($query) use ($search) {

            // 🔥 normalize search
            $clean = strtolower(str_replace(',', ' ', $search));
            $words = array_filter(explode(' ', $clean));

            $query->where(function ($q) use ($words, $search) {

                foreach ($words as $word) {

                    $q->where(function ($sub) use ($word) {

                        $sub->whereRaw('LOWER(first_name) LIKE ?', ["%{$word}%"])
                            ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$word}%"])
                            ->orWhereRaw('LOWER(middle_initial) LIKE ?', ["%{$word}%"])

                            // 🔥 NEW FIELDS
                            ->orWhereRaw('LOWER(event) LIKE ?', ["%{$word}%"])
                            ->orWhereRaw('LOWER(municipality) LIKE ?', ["%{$word}%"])
                            ->orWhereRaw('LOWER(category) LIKE ?', ["%{$word}%"])
                            ->orWhereRaw('LOWER(status) LIKE ?', ["%{$word}%"])
                            ->orWhereRaw('LOWER(lrn) LIKE ?', ["%{$word}%"])

                            // 🔥 RELATIONS
                            ->orWhereHas('school', function ($sq) use ($word) {
                                $sq->whereRaw('LOWER(name) LIKE ?', ["%{$word}%"]);
                            })
                            ->orWhereHas('sport', function ($sq) use ($word) {
                                $sq->whereRaw('LOWER(name) LIKE ?', ["%{$word}%"]);
                            });

                    });

                }

                // 🔥 FULL NAME MATCH (both orders)
                $q->orWhereRaw("
                    LOWER(CONCAT(first_name,' ',last_name)) LIKE ?
                    OR LOWER(CONCAT(last_name,' ',first_name)) LIKE ?
                ", ["%{$search}%", "%{$search}%"]);

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
        ->orderBy('first_name')
        ->paginate(25)
        ->appends($request->all());

    $sports = Sport::orderBy('name')->get();

    // ==========================
    // DIVISION DROPDOWN
    // ==========================
    if ($officialDivision) {
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

        /* ================= SPORT ================= */

        $sportId   = null;
        $eventName = null;

        // ✅ PRIORITY 1: Existing sport selected
        if ($request->sport_id) {
            $sport = Sport::find($request->sport_id);

            if ($sport) {
                $sportId   = $sport->id;
                $eventName = $sport->name; // 🔥 SAVE TO EVENT
            }
        }

        // ✅ PRIORITY 2: New sport typed
        elseif ($request->sport_name) {

            $sport = Sport::firstOrCreate([
                'name' => $request->sport_name
            ]);

            $sportId   = $sport->id;
            $eventName = $sport->name; // 🔥 SAVE TO EVENT
        }

        /* ================= PHOTO ================= */
        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')->store('athletes', 'public');
        }

        /* ================= CREATE ATHLETE ================= */
        Athlete::create([
            'first_name'        => $val('first_name'),
            'middle_initial'    => $val('middle_initial'),
            'last_name'         => $val('last_name'),
            'sex'               => $val('sex'),
            'birthdate'         => $request->birthdate ?? null,

            'school_id'         => $schoolId,

            // ✅ BOTH SAVED
            'sport_id'          => $sportId,
            'event'             => $eventName, // 🔥 FIX HERE

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

        if (str_contains($e->getMessage(), 'athletes_lrn_unique')) {
            $message = 'Duplicate LRN detected. This LRN is already assigned.';
        } elseif (str_contains($e->getMessage(), 'foreign key')) {
            $message = 'Invalid School or Sport selected.';
        } elseif (str_contains($e->getMessage(), 'image')) {
            $message = 'Image upload failed.';
        }

        return back()
            ->withInput()
            ->with('error', $message)
            ->with('debug_error', $e->getMessage());
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
    $request->validate([
        'first_name'       => 'nullable|string|max:255',
        'middle_initial'   => 'nullable|string|max:1',
        'last_name'        => 'nullable|string|max:255',
        'sex'              => 'nullable|in:Male,Female',
        'birthdate'        => 'nullable|date',
        'school_id'        => 'nullable|exists:schools,id',
        'municipality'     => 'nullable|string|max:255',

        // ✅ ADD THIS
        'sport_id'         => 'nullable|exists:sports,id',
        'sport_name'       => 'nullable|string|max:255',

        'division'         => 'nullable|in:Elementary,Secondary,Paragames',
        'category'         => 'nullable|in:Boys,Girls',
        'status'           => 'nullable|in:Active,Disqualified,Injured',
        'profile_photo'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'legislative_district' => 'nullable|string|max:255',
        'lrn'              => 'nullable|string|max:20',
        'qr_code'          => 'nullable|string|max:255',
    ]);

    $oldValues = $athlete->toArray();

    /* ================= PHOTO ================= */
    $photoPath = $athlete->profile_photo;

    if ($request->hasFile('profile_photo')) {
        $photoPath = $request->file('profile_photo')->store('athletes', 'public');
    }

    /* ================= SPORT FIX ================= */
    $sportId   = $athlete->sport_id;
    $eventName = $athlete->event;

    // ✅ Existing sport selected
    if ($request->sport_id) {
        $sport = Sport::find($request->sport_id);

        if ($sport) {
            $sportId   = $sport->id;
            $eventName = $sport->name;
        }
    }

    // ✅ New sport typed
    elseif ($request->sport_name) {

        $sport = Sport::firstOrCreate([
            'name' => $request->sport_name
        ]);

        $sportId   = $sport->id;
        $eventName = $sport->name;
    }

    /* ================= UPDATE DATA ================= */
    $data = array_filter([
        'first_name'            => $request->first_name,
        'middle_initial'        => $request->middle_initial,
        'last_name'             => $request->last_name,
        'sex'                   => $request->sex,
        'birthdate'             => $request->birthdate,
        'school_id'             => $request->school_id,

        // ✅ FIXED HERE
        'sport_id'              => $sportId,
        'event'                 => $eventName,

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

    $data['profile_photo'] = $photoPath;

    $athlete->update($data);

    /* ================= AUDIT ================= */
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
    $qr = QrCode::format('png')
    ->size(300)
    ->generate(url('/scan/' . $athlete->qr_code));

    return response($qr)->header('Content-Type', 'image/png');
}

  public function bulkUpload(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:csv,xlsx'
    ]);

    $file = $request->file('file');

    $import = new AthletesImport();

    Excel::import($import, $file);

    return redirect()->route('athletes.index')
        ->with('success', "Upload completed. {$import->successCount} rows imported.")
        ->with('import_errors', $import->errors)
        ->with('preview_data', array_slice($import->previewData, 0, 10)); // show first 10
}

  public function exportIdPdf(Request $request)
{
    $user = auth()->user();

    $division   = $request->input('school_division');
    $event      = $request->input('event'); 
    $level      = $request->input('level');     // ✅ NEW
    $category   = $request->input('category'); 
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

    if ($event) {
        $query->where('event', $event);
    }

    if ($level) {
        $query->where('division', $level); // column = division
    }

    if ($category) {
        $query->where('category', $category);
    }
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

public function getEventsByDivision(Request $request)
{
    $division = $request->division;

    $events = Athlete::where('school_division', $division)
        ->whereNotNull('event')
        ->distinct()
        ->pluck('event')
        ->filter()
        ->values();

    return response()->json($events);
}

/**
 * Get the count of athletes based on filter criteria for Super Admin
 */
public function getFilterCount(Request $request)
{
    $user = auth()->user();
    
    // Get filter parameters
    $division = $request->input('school_division');
    $level = $request->input('level');
    $event = $request->input('event');
    $category = $request->input('category');
    $start = $request->input('start');
    $end = $request->input('end');
    
    // Base query
    $query = Athlete::query();
    
    // 🔒 Division Official restriction (if applicable)
    $officialDivision = null;
    
    if ($user->role && $user->role->name === 'Division Official') {
        if (preg_match('/SDO\s*-\s*(.+?)\s*Official/i', $user->name, $matches)) {
            $officialDivision = trim($matches[1]);
            $query->where('school_division', $officialDivision);
        }
    }
    
    // Apply filters
    if ($division) {
        $query->where('school_division', $division);
    }
    
    if ($level) {
        $query->where('division', $level);
    }
    
    if ($event) {
        $query->where('event', $event);
    }
    
    if ($category) {
        $query->where('category', $category);
    }
    
    // Apply range filter if both start and end are provided
    if ($start && $end) {
        $start = max(1, (int) $start);
        $end = max($start, (int) $end);
        
        // For range filtering, we need to consider the athlete's ID or index
        // Assuming you have an auto-incrementing ID, you can use it for range
        $query->whereBetween('id', [$start, $end]);
    }
    
    // Get the count
    $count = $query->count();
    
    return response()->json([
        'count' => $count,
        'filters' => [
            'division' => $division,
            'level' => $level,
            'event' => $event,
            'category' => $category,
            'start' => $start,
            'end' => $end,
        ]
    ]);
}

public function publicView($qr)
{
    // ==========================
    // ✅ CLEAN QR INPUT
    // ==========================
    $clean = strtoupper($qr);

    // remove spaces, new lines, tabs
    $clean = preg_replace('/[\s\r\n\t]+/', '', $clean);

    // extract ONLY valid QR pattern
    if (preg_match('/(ESRRS-[A-Z0-9-]+)/', $clean, $match)) {
        $clean = $match[1];
    }

    // ==========================
    // ✅ FIND ATHLETE (SMART MATCH)
    // ==========================
    $athlete = Athlete::whereRaw("
        REPLACE(REPLACE(REPLACE(UPPER(qr_code), ' ', ''), '\n', ''), '\r', '')
        LIKE ?
    ", ["%{$clean}%"])->first();

    if (!$athlete) {
        abort(404, 'Athlete not found for QR: ' . $clean);
    }

    // ==========================
    // MEDALS (UNCHANGED)
    // ==========================
    $goldCount = IndividualAwardResult::where('athlete_id', $athlete->id)
        ->where('medal', 'Gold')
        ->count();

    $silverCount = IndividualAwardResult::where('athlete_id', $athlete->id)
        ->where('medal', 'Silver')
        ->count();

    $bronzeCount = IndividualAwardResult::where('athlete_id', $athlete->id)
        ->where('medal', 'Bronze')
        ->count();

    $medals = IndividualAwardResult::with(['sport', 'event', 'type'])
        ->where('athlete_id', $athlete->id)
        ->where('status', 'approved')
        ->get()
        ->groupBy('medal');

    return view('athletes.public-view', compact(
        'athlete',
        'goldCount',
        'silverCount',
        'bronzeCount',
        'medals'
    ));
}



}
