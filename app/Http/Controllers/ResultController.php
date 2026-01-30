<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Division;
use App\Models\Sport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $query = Result::with(['sport', 'awardedBy', 'event', 'division_model']);

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

        // -----------------------------
        // ✅ MEDAL TALLY BY DIVISION
        // -----------------------------
        $divisions = Division::withCount([
            'results as gold'   => fn($q) => $q->where('medal', 'Gold'),
            'results as silver' => fn($q) => $q->where('medal', 'Silver'),
            'results as bronze' => fn($q) => $q->where('medal', 'Bronze'),
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

        return view('results.index', [
            'results' => $results,
            'medalTally' => $divisions,
        ]);
    }

    // -----------------------------
    // ✅ CREATE PAGE
    // -----------------------------
    public function create(Request $request)
    {
        $division = $request->get('division', 'Elementary');
        $category = $request->get('category', 'Boys');

        $sports = Sport::with('types')->orderBy('name')->get();
        $divisions = \App\Models\Division::orderBy('name')->get();

        // Existing results
        $existingResults = Result::where('division', $division)
            ->where('category', $category)
            ->get()
            ->groupBy(fn ($r) =>
                $r->sport_id . '-' .
                ($r->type_id ?? 0) . '-' .
                $r->division . '-' .
                $r->medal
            );

        // Event-based results
        $eventResults = [];
        $events = \App\Models\Event::where('age_group', $division)
            ->where('category', $category)
            ->whereNotNull('gold_school_id')
            ->get();

        foreach ($events as $event) {
            $map = [
                'Gold'   => $event->gold_school_id,
                'Silver' => $event->silver_school_id,
                'Bronze' => $event->bronze_school_id,
            ];

            foreach ($map as $medal => $schoolId) {
                if (!$schoolId) continue;
                $key = $event->sport_id . '-0-' . $schoolId . '-' . $medal;
                $eventResults[$key] = 1;
            }
        }

        return view('results.create', compact(
            'sports',
            'divisions',
            'division',
            'category',
            'existingResults',
            'eventResults'
        ));
    }

    // -----------------------------
    // ✅ STORE RESULTS
    // -----------------------------
    public function store(Request $request)
    {
        $eventId = \App\Models\Event::where('is_active', 1)->value('id');

        if (!$eventId) {
            return back()->with('error', 'No active event found.');
        }

        foreach ($request->results ?? [] as $sportId => $types) {
            foreach ($types as $typeId => $divisions) {
                $typeId = $typeId == 0 ? null : $typeId;

                foreach ($divisions as $divisionName => $medals) {
                    foreach ($medals as $medal => $count) {

                        // Delete existing
                        Result::where('event_id', $eventId)
                            ->where('sport_id', $sportId)
                            ->where('type_id', $typeId)
                            ->where('division', $divisionName)
                            ->where('medal', $medal)
                            ->where('category', $request->category)
                            ->delete();

                        // Insert new
                        if ($count > 0) {
                            for ($i = 0; $i < $count; $i++) {
                                Result::create([
                                    'event_id'   => $eventId,
                                    'sport_id'   => $sportId,
                                    'type_id'    => $typeId,
                                    'division'   => $divisionName,
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

    // -----------------------------
    // ✅ EXPORT
    // -----------------------------
    public function export()
    {
        $results = Result::with(['sport', 'division_model'])->latest()->get();

        $divisions = Division::withCount([
            'results as gold'   => fn($q) => $q->where('medal', 'Gold'),
            'results as silver' => fn($q) => $q->where('medal', 'Silver'),
            'results as bronze' => fn($q) => $q->where('medal', 'Bronze'),
        ])->get();

        $generatedAt = now()->format('M d, Y h:i A');

        $pdf = Pdf::loadView('results.export', compact('results','divisions','generatedAt'))
            ->setPaper('letter', 'landscape');

        return $pdf->download('awards_report.pdf');
    }
}
