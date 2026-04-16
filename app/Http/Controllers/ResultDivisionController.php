<?php

namespace App\Http\Controllers;

use App\Models\ResultDivision;
use App\Models\Division;
use App\Models\Sport;
use PDF; // Add at the top
use App\Models\Event;
use App\Models\AllAwardResult;
use App\Models\IndividualAwardResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ZipArchive;
use Illuminate\Support\Facades\Storage;


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

    // ✅ LOAD ALL EVENTS (NOT PAGINATED)
    $eventsQuery = \App\Models\Event::where('is_active', 1);

    // 🔒 NON-SUPER ADMIN FILTER
    if ($user->role->name !== 'Super Admin') {
        $sportIds = is_string($user->sports_access)
            ? json_decode($user->sports_access, true)
            : $user->sports_access;

        $eventsQuery->whereIn('sport_id', $sportIds ?? []);
    }

    $events = $eventsQuery->orderBy('name')->get(); // ✅ NO PAGINATION

    // SPORTS (KEEP IF USED ELSEWHERE)
    $sportsQuery = \App\Models\Sport::with('types')->orderBy('name');

    if ($user->role->name !== 'Super Admin') {
        $sportsQuery->whereIn('id', $user->sports_access ?? []);
    }

    $sports = $sportsQuery->get();

    // ATHLETES (LOAD PROPERLY FOR INDIVIDUAL AWARDS)
    $athletes = \App\Models\Athlete::with('school')->orderBy('last_name')->get();

    // DIVISIONS
    $divisionsQuery = \App\Models\Division::orderBy('name');

    if ($user->role->name !== 'Super Admin') {
        $divisionsQuery->where('name', $user->division_name);
    }

    $divisions = $divisionsQuery->get();

    return view('results_divisions.create', compact(
        'divisions',
        'tabs',
        'sports',
        'athletes',
        'events' // ✅ VERY IMPORTANT
    ));
}


    // -----------------------------
    // STORE RESULTS
    // -----------------------------
    // public function store(Request $request)
    // {
    //     $eventId = \App\Models\Event::where('is_active', 1)
    //         ->where('age_group', $request->division)
    //         ->value('id');

    //     if (!$eventId) {
    //         return back()->with('error', "No active event found for division: {$request->division}");
    //     }

    //     foreach ($request->results ?? [] as $sportId => $types) {
    //         foreach ($types as $typeId => $sdos) {
    //             $typeId = $typeId == 0 ? null : $typeId;

    //             foreach ($sdos as $sdo => $medals) {
    //                 foreach ($medals as $medal => $count) {
    //                     // Delete previous
    //                     ResultDivision::where([
    //                         'event_id' => $eventId,
    //                         'sport_id' => $sportId,
    //                         'type_id' => $typeId,
    //                         'division' => $request->division,
    //                         'sdo' => $sdo,
    //                         'category' => $request->category,
    //                         'medal' => $medal,
    //                     ])->delete();

    //                     if ($count > 0) {
    //                         ResultDivision::insert(
    //                             array_fill(0, $count, [
    //                                 'event_id' => $eventId,
    //                                 'sport_id' => $sportId,
    //                                 'type_id' => $typeId,
    //                                 'division' => $request->division,
    //                                 'sdo' => $sdo,
    //                                 'category' => $request->category,
    //                                 'medal' => $medal,
    //                                 'awarded_by' => Auth::id(),
    //                                 'created_at' => now(),
    //                                 'updated_at' => now(),
    //                             ])
    //                         );
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     return back()->with('success', 'Results saved successfully.');
    // }

public function export()
{
    ini_set('memory_limit', '512M');

    // =====================
    // LOAD DIVISIONS
    // =====================
    $divisions = Division::select('name')->orderBy('name')->get();

    // =====================
    // ALL AWARDS
    // =====================
    $allAwards = AllAwardResult::with(['sport:id,name','event:id,name','type:id,type_name'])
        ->where('status','approved')
        ->get();

    // =====================
    // INDIVIDUAL AWARDS
    // =====================
    $individualAwards = IndividualAwardResult::with(['sport:id,name','event:id,name','type:id,type_name','athlete'])
        ->where('status','approved')
        ->get();

    // =====================
    // FORMAT FUNCTION (LIGHTWEIGHT)
    // =====================
    $formatAll = function($items) {
        return $items->map(function($item){
            return [
                'division' => $item->division,
                'category' => $item->category,
                'event'    => $item->sport->name ?? '-',
                'sport'    => ($item->type && $item->type->type_name)
                                ? $item->sport->name.' - '.$item->type->type_name
                                : ($item->sport->name ?? '-'),
                'medal'    => $item->medal,
                'sdo'      => $item->sdo,
                'athlete'  => null,
                'school'   => null,
                'municipality' => null,
                'district' => null,
            ];
        });
    };

    $formatIndividual = function($items) {
        return $items->map(function($item){

            $athlete = $item->athlete;

            return [
                'division' => $item->division,
                'category' => $item->category,
                'event'    => $item->sport->name ?? '-',
                'sport'    => ($item->type && $item->type->type_name)
                                ? $item->sport->name.' - '.$item->type->type_name
                                : ($item->sport->name ?? '-'),
                'medal'    => $item->medal,
                'sdo'      => $item->sdo,
                'athlete'  => $athlete
                    ? strtoupper($athlete->last_name.', '.$athlete->first_name.' '.($athlete->middle_initial ?? ''))
                    : null,
                'school'   => $athlete->school_name ?? null,
                'municipality' => $athlete->municipality ?? null,
                'district' => $athlete->legislative_district ?? null,
            ];
        });
    };

    // =====================
    // SPLIT REGULAR / PARA
    // =====================
    $regularData = $formatAll($allAwards->where('division','!=','Paragames'))
        ->concat($formatIndividual($individualAwards->where('division','!=','Paragames')));

    $paraData = $formatAll($allAwards->where('division','Paragames'))
        ->concat($formatIndividual($individualAwards->where('division','Paragames')));

    // =====================
    // GROUPING FUNCTION
    // =====================
    $groupData = function($data){
        return $data->groupBy(function($item){
            return ($item['division'] ?? 'N/A')
                .'||'.($item['event'] ?? 'N/A')
                .'||'.($item['sport'] ?? 'N/A')
                .'||'.($item['category'] ?? 'N/A');
        });
    };

    $regularGrouped = $groupData($regularData);
    $paraGrouped    = $groupData($paraData);

    // =====================
// BUILD MEDAL TALLY (FIXED)
// =====================
$buildTally = function($data, $divisions) {

    $tally = [];

    foreach ($divisions as $division) {

        $sdo = $division->name;

        $gold   = collect($data)->where('sdo',$sdo)->where('medal','Gold')->count();
        $silver = collect($data)->where('sdo',$sdo)->where('medal','Silver')->count();
        $bronze = collect($data)->where('sdo',$sdo)->where('medal','Bronze')->count();

        $tally[] = (object)[
            'name'   => $sdo,
            'gold'   => $gold,
            'silver' => $silver,
            'bronze' => $bronze,
            'points' => ($gold*3)+($silver*2)+($bronze*1),
        ];
    }

    // sort ranking
    usort($tally, fn($a,$b) =>
        [$b->points,$b->gold,$b->silver,$b->bronze]
        <=>
        [$a->points,$a->gold,$a->silver,$a->bronze]
    );

    foreach ($tally as $i => $row) {
        $row->rank = $i + 1;
    }

    return $tally;
};

// =====================
// BUILD TALLIES
// =====================
$regularTally = $buildTally($regularData, $divisions);
$paraTally    = $buildTally($paraData, $divisions);

// =====================
// PDF GENERATION (FIXED)
// =====================
$regularPdf = PDF::loadView('results_divisions.export', [
    'groupedData' => $regularGrouped,
    'medalTally'  => $regularTally, // ✅ FIX
    'reportType'  => 'REGULAR'
])->setPaper('letter','portrait');

$paraPdf = PDF::loadView('results_divisions.export', [
    'groupedData' => $paraGrouped,
    'medalTally'  => $paraTally, // ✅ FIX
    'reportType'  => 'PARAGAMES'
])->setPaper('letter','portrait');

    // =====================
    // SAVE FILES
    // =====================
    $regularPath = storage_path('app/public/Regular.pdf');
    $paraPath    = storage_path('app/public/Paragames.pdf');

    file_put_contents($regularPath, $regularPdf->output());
    file_put_contents($paraPath, $paraPdf->output());

    // =====================
    // ZIP
    // =====================
    $zipPath = storage_path('app/public/Results.zip');
    $zip = new ZipArchive;

    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        $zip->addFile($regularPath, 'Regular_Results.pdf');
        $zip->addFile($paraPath, 'Paragames_Results.pdf');
        $zip->close();
    }

    return response()->download($zipPath)->deleteFileAfterSend(true);
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
