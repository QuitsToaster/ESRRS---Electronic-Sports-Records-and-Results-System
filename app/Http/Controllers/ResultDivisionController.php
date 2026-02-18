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
        $division = $request->get('division', 'Elementary');
        $category = $request->get('category', 'Boys');

        $sports = Sport::with('types')->orderBy('name')->get();
        $divisions = Division::orderBy('name')->get(); // SDOs

        $existingResults = ResultDivision::where('category', $category)
            ->whereHas('event', fn($q) => $q->where('age_group', $division))
            ->get()
            ->groupBy(fn($r) => $r->sport_id . '-' . ($r->type_id ?? 0) . '-' . $r->sdo . '-' . $r->medal);

        return view('results_divisions.create', compact(
            'sports', 'divisions', 'division', 'category', 'existingResults'
        ));
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

    // Medal tally
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

    $rank = 1;
    foreach ($divisions as $division) {
        $division->rank = $rank++;
    }

    // Load Blade view for PDF
    $pdf = PDF::loadView('results_divisions.export', [
        'results' => $results,
        'medalTally' => $divisions,
        'allSports' => \App\Models\Sport::orderBy('name')->get(),
    ])->setPaper('a4', 'landscape');

    // Download PDF
    return $pdf->download('awarded_medals.pdf');
}

public function storeAllAwards(Request $request)
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
                    // Remove previous
                    \App\Models\AllAwardResult::where([
                        'event_id' => $eventId,
                        'sport_id' => $sportId,
                        'type_id' => $typeId,
                        'division' => $request->division,
                        'category' => $request->category,
                        'sdo' => $sdo,
                        'medal' => $medal,
                    ])->delete();

                    // Insert if > 0
                    if ($count > 0) {
                        \App\Models\AllAwardResult::create([
                            'event_id' => $eventId,
                            'sport_id' => $sportId,
                            'type_id' => $typeId,
                            'division' => $request->division,
                            'category' => $request->category,
                            'sdo' => $sdo,
                            'medal' => $medal,
                            'count' => $count,
                            'awarded_by' => auth()->id(),
                        ]);
                    }
                }
            }
        }
    }

    return back()->with('success', 'All Awards results saved successfully.');
}

public function storeIndividualAwards(Request $request)
{
    $eventId = \App\Models\Event::where('is_active', 1)
        ->where('age_group', $request->division)
        ->value('id');

    if (!$eventId) {
        return back()->with('error', "No active event found for division: {$request->division}");
    }

    foreach ($request->individual_results ?? [] as $sportId => $types) {
        foreach ($types as $typeId => $sdos) {
            foreach ($sdos as $sdo => $medals) {
                foreach ($medals as $medal => $athleteId) {
                    if ($athleteId) {
                        \App\Models\IndividualAwardResult::create([
                            'event_id' => $eventId,
                            'sport_id' => $sportId,
                            'type_id' => $typeId,
                            'division' => $request->division,
                            'category' => $request->category,
                            'sdo' => $sdo,
                            'medal' => $medal,
                            'athlete_id' => $athleteId,
                            'awarded_by' => auth()->id(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }

    return back()->with('success', 'Individual awards saved successfully.');
}



}
