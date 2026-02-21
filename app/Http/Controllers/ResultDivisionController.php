<?php

namespace App\Http\Controllers;

use App\Models\ResultDivision;
use App\Models\Division;
use App\Models\Sport;
use PDF; // Add at the top
use App\Models\Event;
use App\Models\AllAwardResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultDivisionController extends Controller
{
    // -----------------------------
    // INDEX PAGE - list all results
    // -----------------------------
    public function index(Request $request)
    {
        $query = ResultDivision::with(['sport', 'division_model', 'awardedBy', 'event']);

        // Optional search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->orWhereHas('sport', fn($qs) => $qs->where('name', 'like', "%{$search}%"))
                  ->orWhere('division', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('medal', 'like', "%{$search}%")
                  ->orWhere('sdo', 'like', "%{$search}%")
                  ->orWhereHas('event', fn($qe) => $qe->where('name', 'like', "%{$search}%"));
            });
        }

        $results = $query->latest()->get();

        // -----------------------------
        // MEDAL TALLY BY DIVISION
        // -----------------------------
        $divisions = Division::withCount([
            'resultsDivisions as gold'   => fn($q) => $q->where('medal', 'Gold'),
            'resultsDivisions as silver' => fn($q) => $q->where('medal', 'Silver'),
            'resultsDivisions as bronze' => fn($q) => $q->where('medal', 'Bronze'),
        ])->get();

        // Calculate points (Gold=3, Silver=2, Bronze=1)
        $divisions->transform(function ($division) {
            $division->points = ($division->gold * 3) + ($division->silver * 2) + ($division->bronze * 1);
            return $division;
        });

        // Sort by points descending, then gold, silver, bronze
        $divisions = $divisions->sortByDesc(fn($d) => [$d->points, $d->gold, $d->silver, $d->bronze])->values();

        // Assign rank
        $rank = 1;
        foreach ($divisions as $division) {
            $division->rank = $rank++;
        }

        return view('results_divisions.index', [
            'results' => $results,
            'medalTally' => $divisions,
        ]);
    }

    // -----------------------------
    // CREATE PAGE
    // -----------------------------
    public function create(Request $request)
{
    $user = auth()->user();

    $tabs = [
        'Elementary Boys'   => ['division' => 'Elementary', 'category' => 'Boys'],
        'Elementary Girls'  => ['division' => 'Elementary', 'category' => 'Girls'],
        'Secondary Boys'    => ['division' => 'Secondary', 'category' => 'Boys'],
        'Secondary Girls'   => ['division' => 'Secondary', 'category' => 'Girls'],
        'Para Games'        => ['division' => 'Paragames', 'category' => null],
    ];

    $athletes = \App\Models\Athlete::orderBy('last_name')->get();

    $divisionsQuery = Division::orderBy('name');

    // If user is NOT super admin, filter by user's division_name
    if ($user->role->name !== 'Super Admin') {
        $divisionsQuery->where('name', $user->division_name);
        // Filter athletes as well
        $athletes = $athletes->filter(function ($a) use ($user) {
            return $a->division == $user->level_access[0] // level_access usually array
                && ($user->category_access ? in_array($a->category, $user->category_access) : true)
                && in_array($a->sport_id, $user->sports_access);
        });
    }

    $divisions = $divisionsQuery->get();

    return view('results_divisions.create', compact('divisions', 'athletes', 'tabs'));
}


    // -----------------------------
    // STORE RESULTS
    // -----------------------------
    public function store(Request $request)
    {
        $eventId = \App\Models\Event::where('is_active', 1)
            ->where('age_group', $request->division)
            ->value('id');

        if (!$eventId) {
            return back()->with('error', "No active event found for division: {$request->division}");
        }

        foreach ($request->results ?? [] as $sportId => $types) {
            foreach ($types as $typeId => $sdos) {
                $typeId = $typeId == 0 ? null : $typeId;

                foreach ($sdos as $sdo => $medals) {
                    foreach ($medals as $medal => $count) {
                        // Delete previous
                        ResultDivision::where([
                            'event_id' => $eventId,
                            'sport_id' => $sportId,
                            'type_id' => $typeId,
                            'division' => $request->division,
                            'sdo' => $sdo,
                            'category' => $request->category,
                            'medal' => $medal,
                        ])->delete();

                        if ($count > 0) {
                            ResultDivision::insert(
                                array_fill(0, $count, [
                                    'event_id' => $eventId,
                                    'sport_id' => $sportId,
                                    'type_id' => $typeId,
                                    'division' => $request->division,
                                    'sdo' => $sdo,
                                    'category' => $request->category,
                                    'medal' => $medal,
                                    'awarded_by' => Auth::id(),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ])
                            );
                        }
                    }
                }
            }
        }

        return back()->with('success', 'Results saved successfully.');
    }

    public function export()
{
    // Get all results with relations
    $results = ResultDivision::with(['sport', 'division_model', 'awardedBy', 'event'])
                ->latest()->get();

    // Get all sports
    $allSports = \App\Models\Sport::orderBy('name')->get();

    // Medal tally by division
    $divisions = Division::withCount([
        'resultsDivisions as gold'   => fn($q) => $q->where('medal', 'Gold'),
        'resultsDivisions as silver' => fn($q) => $q->where('medal', 'Silver'),
        'resultsDivisions as bronze' => fn($q) => $q->where('medal', 'Bronze'),
    ])->get();

    $divisions->transform(function ($division) {
        $division->points = ($division->gold * 3) + ($division->silver * 2) + ($division->bronze * 1);
        return $division;
    });

    $divisions = $divisions->sortByDesc(fn($d) => [$d->points, $d->gold, $d->silver, $d->bronze])->values();

    // Assign ranks
    $rank = 1;
    foreach ($divisions as $division) {
        $division->rank = $rank++;
    }

    // Group results by division + sport + category for PDF
    $groupedResults = $results->groupBy(function ($item) {
        return $item->division . '|' . $item->category . '|' . $item->sport_id;
    });

    return PDF::loadView('results_divisions.export', [
        'medalTally'     => $divisions,
        'allSports'      => $allSports,
        'groupedResults' => $groupedResults,
        'levels'         => ['Elementary', 'Secondary', 'Para Games'],
    ])->setPaper('a4', 'landscape')->download('awarded_medals.pdf');
}


// public function storeAllAwards(Request $request)
// {
//     $user = auth()->user();

//     $eventId = Event::where('is_active', 1)
//         ->where('age_group', $request->division)
//         ->value('id');

//     if (!$eventId) {
//         return back()->with('error', "No active event found for division: {$request->division}");
//     }

//     // 🔑 STATUS RULE
//     $status = ($user->role->name === 'Tournament Secretary')
//         ? 'pending'
//         : 'approved';

//     foreach ($request->results ?? [] as $sportId => $types) {
//         foreach ($types as $typeId => $sdos) {

//             $typeId = $typeId == 0 ? null : $typeId;

//             foreach ($sdos as $sdo => $medals) {
//                 foreach ($medals as $medal => $count) {

//                     AllAwardResult::where([
//                         'event_id' => $eventId,
//                         'sport_id' => $sportId,
//                         'type_id' => $typeId,
//                         'division' => $request->division,
//                         'category' => $request->category,
//                         'sdo' => $sdo,
//                         'medal' => $medal,
//                     ])->delete();

//                     if ($count > 0) {
//                         AllAwardResult::create([
//                             'event_id' => $eventId,
//                             'sport_id' => $sportId,
//                             'type_id' => $typeId,
//                             'division' => $request->division,
//                             'category' => $request->category,
//                             'sdo' => $sdo,
//                             'medal' => $medal,
//                             'count' => $count,
//                             'awarded_by' => $user->id,
//                             'status' => $status, // ✅ HERE
//                         ]);
//                     }
//                 }
//             }
//         }
//     }

//     return back()->with('success', 'All Awards submitted successfully (Pending Approval).');
// }


// public function storeIndividualAwards(Request $request)
// {
//     $user = auth()->user();

//     $eventId = Event::where('is_active', 1)
//         ->where('age_group', $request->division)
//         ->value('id');

//     if (!$eventId) {
//         return back()->with('error', "No active event found for division: {$request->division}");
//     }

//     $status = ($user->role->name === 'Tournament Secretary')
//         ? 'pending'
//         : 'approved';

//     foreach ($request->individual_results ?? [] as $sportId => $types) {
//         foreach ($types as $typeId => $medals) {

//             $typeId = $typeId == 0 ? null : $typeId;

//             foreach ($medals as $medal => $athleteId) {

//                 if (!$athleteId) continue;

//                 IndividualAwardResult::where([
//                     'event_id' => $eventId,
//                     'sport_id' => $sportId,
//                     'type_id' => $typeId,
//                     'division' => $request->division,
//                     'category' => $request->category,
//                     'medal' => $medal,
//                 ])->delete();

//                 IndividualAwardResult::create([
//                     'event_id'   => $eventId,
//                     'sport_id'   => $sportId,
//                     'type_id'    => $typeId,
//                     'division'   => $request->division,
//                     'category'   => $request->category,
//                     'sdo'        => null,
//                     'medal'      => $medal,
//                     'athlete_id' => $athleteId,
//                     'awarded_by' => $user->id,
//                     'status'     => $status, // ✅ HERE
//                 ]);
//             }
//         }
//     }

//     return back()->with('success', 'Individual Awards submitted successfully (Pending Approval).');
// }





}
