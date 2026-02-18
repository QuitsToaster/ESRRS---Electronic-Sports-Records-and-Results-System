<?php

namespace App\Http\Controllers;

use App\Models\ResultDivision;
use App\Models\Division;
use App\Models\Sport;
use App\Models\Event;
use App\Models\AllAwardResult;
use App\Models\IndividualAwardResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class ResultDivisionController extends Controller
{
    public function index(Request $request)
    {
        $query = ResultDivision::with(['sport', 'division_model', 'awardedBy', 'event']);

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

        $divisions = Division::withCount([
            'resultsDivisions as gold' => fn($q) => $q->where('medal', 'Gold'),
            'resultsDivisions as silver' => fn($q) => $q->where('medal', 'Silver'),
            'resultsDivisions as bronze' => fn($q) => $q->where('medal', 'Bronze'),
        ])->get();

        $divisions->transform(function ($division) {
            $division->points = ($division->gold * 3) + ($division->silver * 2) + ($division->bronze);
            return $division;
        });

        $divisions = $divisions->sortByDesc(fn($d) => [$d->points, $d->gold, $d->silver, $d->bronze])->values();

        $rank = 1;
        foreach ($divisions as $division) $division->rank = $rank++;

        return view('results_divisions.index', [
            'results' => $results,
            'medalTally' => $divisions,
        ]);
    }

    public function create(Request $request)
    {
        $division = $request->get('division', 'Elementary');
        $category = $request->get('category', 'Boys');

        $sports = Sport::with('types')->orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();

        $existingResults = ResultDivision::where('category', $category)
            ->whereHas('event', fn($q) => $q->where('age_group', $division))
            ->get()
            ->groupBy(fn($r) => $r->sport_id . '-' . ($r->type_id ?? 0) . '-' . $r->sdo . '-' . $r->medal);

        return view('results_divisions.create', compact(
            'sports', 'divisions', 'division', 'category', 'existingResults'
        ));
    }

    public function storeAllAwards(Request $request)
    {
        $results = $request->input('results', []);
        if (!is_array($results)) $results = [];

        $eventId = Event::where('is_active', 1)
            ->where('age_group', $request->division)
            ->value('id');

        if (!$eventId) {
            return back()->with('error', "No active event found for division: {$request->division}");
        }

        foreach ($results as $sportId => $types) {
            if (!is_array($types)) continue;
            foreach ($types as $typeId => $sdos) {
                $typeId = $typeId == 0 ? null : $typeId;
                if (!is_array($sdos)) continue;

                foreach ($sdos as $sdo => $medals) {
                    if (!is_array($medals)) continue;

                    foreach ($medals as $medal => $count) {
                        // Remove previous
                        AllAwardResult::where([
                            'event_id' => $eventId,
                            'sport_id' => $sportId,
                            'type_id' => $typeId,
                            'division' => $request->division,
                            'category' => $request->category,
                            'sdo' => $sdo,
                            'medal' => $medal,
                        ])->delete();

                        if ((int)$count > 0) {
                            AllAwardResult::create([
                                'event_id' => $eventId,
                                'sport_id' => $sportId,
                                'type_id' => $typeId,
                                'division' => $request->division,
                                'category' => $request->category,
                                'sdo' => $sdo,
                                'medal' => $medal,
                                'count' => (int)$count,
                                'awarded_by' => Auth::id(),
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
        $individual_results = $request->input('individual_results', []);
        if (!is_array($individual_results)) $individual_results = [];

        $eventId = Event::where('is_active', 1)
            ->where('age_group', $request->division)
            ->value('id');

        if (!$eventId) {
            return back()->with('error', "No active event found for division: {$request->division}");
        }

        foreach ($individual_results as $sportId => $types) {
            if (!is_array($types)) continue;

            foreach ($types as $typeId => $medals) {
                if (!is_array($medals)) continue;

                foreach ($medals as $medal => $athleteId) {
                    if ($athleteId) {
                        IndividualAwardResult::create([
                            'event_id' => $eventId,
                            'sport_id' => $sportId,
                            'type_id' => $typeId,
                            'division' => $request->division,
                            'category' => $request->category,
                            'sdo' => $medal === 'Gold' || $medal === 'Silver' || $medal === 'Bronze' ? 'N/A' : 'N/A',
                            'medal' => $medal,
                            'athlete_id' => $athleteId,
                            'awarded_by' => Auth::id(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        return back()->with('success', 'Individual awards saved successfully.');
    }

    public function export()
    {
        $results = ResultDivision::with(['sport', 'division_model', 'awardedBy', 'event'])
                    ->latest()->get();

        $divisions = Division::withCount([
            'resultsDivisions as gold'   => fn($q) => $q->where('medal', 'Gold'),
            'resultsDivisions as silver' => fn($q) => $q->where('medal', 'Silver'),
            'resultsDivisions as bronze' => fn($q) => $q->where('medal', 'Bronze'),
        ])->get();

        $divisions->transform(function ($division) {
            $division->points = ($division->gold * 3) + ($division->silver * 2) + ($division->bronze);
            return $division;
        });

        $divisions = $divisions->sortByDesc(fn($d) => [$d->points, $d->gold, $d->silver, $d->bronze])->values();

        $rank = 1;
        foreach ($divisions as $division) $division->rank = $rank++;

        $pdf = PDF::loadView('results_divisions.export', [
            'results' => $results,
            'medalTally' => $divisions,
            'allSports' => Sport::orderBy('name')->get(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('awarded_medals.pdf');
    }
}
