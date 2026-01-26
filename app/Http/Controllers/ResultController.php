<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\School;
use App\Models\Athlete;
use App\Models\Event;
use App\Models\Sport;
use App\Models\AuditLog;
use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index(Request $request)
{
    $query = Result::with(['school', 'sport', 'event', 'athlete', 'awardedBy']);

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {

            // Athlete name search
            $q->whereHas('athlete', function ($qa) use ($search) {
                $qa->where('last_name', 'like', "%{$search}%")
                   ->orWhere('first_name', 'like', "%{$search}%")
                   ->orWhere('middle_initial', 'like', "%{$search}%");
            })

            // Sport
            ->orWhereHas('sport', function ($qs) use ($search) {
                $qs->where('name', 'like', "%{$search}%");
            })

            // Event
            ->orWhereHas('event', function ($qe) use ($search) {
                $qe->where('name', 'like', "%{$search}%");
            })

            // Result fields
            ->orWhere('division', 'like', "%{$search}%")
            ->orWhere('category', 'like', "%{$search}%")
            ->orWhere('medal', 'like', "%{$search}%");

            // Optional date search (YYYY-MM-DD)
            if (strtotime($search)) {
                $q->orWhereDate('created_at', $search);
            }
        });
    }

    $results = $query->orderBy('created_at', 'desc')->get();

    // Medal Tally per school
    $medalTally = School::withCount([
        'results as gold'   => fn($q) => $q->where('medal', 'Gold'),
        'results as silver' => fn($q) => $q->where('medal', 'Silver'),
        'results as bronze' => fn($q) => $q->where('medal', 'Bronze'),
    ])
    ->get()
    ->map(function ($school) {
        $school->total = $school->gold + $school->silver + $school->bronze;
        return $school;
    })
    ->sort(function ($a, $b) {
        return [$b->gold, $b->silver, $b->bronze] <=> [$a->gold, $a->silver, $a->bronze];
    })
    ->values()
    ->map(function ($school, $index) {
        $school->rank = $index + 1;
        return $school;
    });

    return view('results.index', compact('results', 'medalTally'));
}



    public function create()
{
    return view('results.create', [
        'schools'  => School::orderBy('name')->get(),
        'sports'   => Sport::orderBy('name')->get(),
        'events'   => Event::orderBy('name')->get(),

        // ✅ FIXED: no more "name" column
        'athletes' => Athlete::orderBy('last_name')
                             ->orderBy('first_name')
                             ->get(),
    ]);
}


    public function store(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'sport_id'  => 'required|exists:sports,id',
            'event_id'  => 'required|exists:events,id',
            'division'  => 'required|in:Elementary,Secondary',
            'category'  => 'required|in:Boys,Girls,Mixed',
            'medal'     => 'required|in:Gold,Silver,Bronze',
        ]);

        $result = Result::create([
            'school_id' => $request->school_id,
            'sport_id'  => $request->sport_id,
            'event_id'  => $request->event_id,
            'athlete_id'=> $request->athlete_id,
            'team_name' => $request->team_name,
            'division'  => $request->division,
            'category'  => $request->category,
            'medal'     => $request->medal,
            'awarded_by'=> Auth::id(),
        ]);

        // ✅ Audit log (IMPORTANT)
        AuditLog::create([
            'user_id'    => Auth::id(),
            'model_type' => Result::class,
            'model_id'   => $result->id,
            'action'     => 'Awarded Medal',
            'details'    => "{$request->medal} medal awarded to school ID {$request->school_id}",
        ]);

        return redirect()->route('results.index')
            ->with('success', 'Medal awarded successfully.');
    }

    
    public function export()
    {
        $results = Result::with(['school', 'sport', 'event', 'athlete'])->orderBy('created_at', 'desc')->get();

        $medalTally = School::withCount([
            'results as gold' => fn($q) => $q->where('medal', 'Gold'),
            'results as silver' => fn($q) => $q->where('medal', 'Silver'),
            'results as bronze' => fn($q) => $q->where('medal', 'Bronze'),
        ])->get()
        ->map(fn($school) => tap($school, fn($s) => $s->total = $s->gold + $s->silver + $s->bronze))
        ->sort(function($a, $b) {
            if($b->gold !== $a->gold) return $b->gold <=> $a->gold;
            if($b->silver !== $a->silver) return $b->silver <=> $a->silver;
            return $b->bronze <=> $a->bronze;
        })
        ->values()
        ->map(fn($school, $index) => tap($school, fn($s) => $s->rank = $index + 1));

        $generatedAt = now()->setTimezone('Asia/Manila')->format('M d, Y h:i A');

        $pdf = Pdf::loadView('results.export', compact('results','medalTally','generatedAt'))
                ->setPaper('a4', 'landscape');

        // ✅ Force download instead of opening in browser
        return $pdf->download('awards_report_' . now()->format('Ymd_His') . '.pdf');
    }
}
