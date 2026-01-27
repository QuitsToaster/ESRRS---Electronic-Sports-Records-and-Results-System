<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\School;
use App\Models\Sport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $query = Result::with(['school', 'sport', 'awardedBy']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->orWhereHas('sport', fn($qs) => $qs->where('name', 'like', "%{$search}%"))
                  ->orWhere('division', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('medal', 'like', "%{$search}%");
            });
        }

        $results = $query->latest()->get();

        $medalTally = School::withCount([
    'results as gold'   => fn($q) => $q->where('medal', 'Gold'),
    'results as silver' => fn($q) => $q->where('medal', 'Silver'),
    'results as bronze' => fn($q) => $q->where('medal', 'Bronze'),
])->get();

// Calculate points for ranking (Gold=3, Silver=2, Bronze=1)
$medalTally->transform(function($school) {
    $school->points = ($school->gold * 3) + ($school->silver * 2) + ($school->bronze * 1);
    return $school;
});

// Sort by points descending, then gold, silver, bronze count
$medalTally = $medalTally->sortByDesc(function($school) {
    return [$school->points, $school->gold, $school->silver, $school->bronze];
})->values();

// Assign rank
$rank = 1;
foreach($medalTally as $school) {
    $school->rank = $rank++;
}


        return view('results.index', compact('results', 'medalTally'));
    }

    /**
     * ✅ SHOW ALL SPORTS + TYPES
     */
    public function create(Request $request)
{
    $division = $request->get('division', 'Elementary');
    $category = $request->get('category', 'Boys');

    // Load all sports with types
    $sports = Sport::with('types')
        ->orderBy('name')
        ->get();

    $schools = School::orderBy('name')->get();

    // ✅ Fetch existing results for this division & category
    $existingResults = Result::where('division', $division)
        ->where('category', $category)
        ->get()
        ->groupBy(function($item) {
            return $item->sport_id . '-' . $item->type_id . '-' . $item->school_id . '-' . $item->medal;
        });

    return view('results.create', compact(
        'sports',
        'schools',
        'division',
        'category',
        'existingResults'
    ));
}


    /**
     * ✅ SAVE RESULTS BASED ON FILTER
     */
    public function store(Request $request)
{
    $eventId = \App\Models\Event::where('is_active', 1)->value('id');

    if (!$eventId) {
        return back()->with('error', 'No active event found.');
    }

    foreach ($request->results ?? [] as $sportId => $types) {
        foreach ($types as $typeId => $schools) {
            $typeId = $typeId == 0 ? null : $typeId;

            foreach ($schools as $schoolId => $medals) {
                foreach ($medals as $medal => $count) {

                    // ✅ Delete only existing results for THIS division/category
                    Result::where('event_id', $eventId)
                        ->where('sport_id', $sportId)
                        ->where('type_id', $typeId)
                        ->where('school_id', $schoolId)
                        ->where('medal', $medal)
                        ->where('division', $request->division)
                        ->where('category', $request->category)
                        ->delete();

                    // Insert new results
                    if ($count > 0) {
                        for ($i = 0; $i < $count; $i++) {
                            Result::create([
                                'event_id'   => $eventId,
                                'sport_id'   => $sportId,
                                'type_id'    => $typeId,
                                'school_id'  => $schoolId,
                                'division'   => $request->division,
                                'category'   => $request->category,
                                'medal'      => $medal,
                                'awarded_by' => Auth::id(),
                            ]);
                        }
                    }
                }
            }
        }
    }

    return redirect()
        ->route('results.create', [
            'division' => $request->division,
            'category' => $request->category
        ])
        ->with('success', 'Results saved successfully.');
}



    public function export()
    {
        $results = Result::with(['school', 'sport'])->latest()->get();

        $medalTally = School::withCount([
            'results as gold'   => fn($q) => $q->where('medal', 'Gold'),
            'results as silver' => fn($q) => $q->where('medal', 'Silver'),
            'results as bronze' => fn($q) => $q->where('medal', 'Bronze'),
        ])->get();

        $generatedAt = now()->format('M d, Y h:i A');

        $pdf = Pdf::loadView('results.export', compact('results','medalTally','generatedAt'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('awards_report.pdf');
    }
}
