<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sport;
use App\Models\AuditLog;
use App\Models\AllAwardResult;
use App\Models\IndividualAwardResult;
use App\Models\User;
use App\Models\Role;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SportsImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SportController extends Controller
{
    public function index(Request $request)
    {
        // Get filter values from query
        $level = $request->query('level');      // Elementary / Secondary
        $category = $request->query('category'); // Boys / Girls

        $query = Sport::with('types')->whereIn('division', ['Elementary', 'Secondary']);

        if ($level) {
            $query->where('division', $level);
        }

        if ($category) {
            $query->where('category', $category);
        }

        $sports = $query->orderBy('name')->get();

        // Total count of filtered sports
        $totalSports = $sports->count();

        return view('sports.index', compact('sports', 'totalSports', 'level', 'category'));
    }

    public function create()
    {
        return view('sports.create');
    }

    public function store(Request $request)
{
    $request->validate([
    'name' => [
        'required',
        'string',
    ],
    'level' => 'required|in:Elementary,Secondary',

    // ✅ keep Mixed
    'category' => 'required|in:Boys,Girls,Mixed',

    'type_of_game.*' => 'nullable|string',
]);

    $sport = Sport::create([
        'name'     => $request->name,
        'division' => $request->level,
        'level'    => $request->level,
        'category' => $request->category,
    ]);

    if ($request->type_of_game) {
        foreach ($request->type_of_game as $type) {
            if ($type) {
                $sport->types()->create(['type_name' => $type]);
            }
        }
    }

    AuditLog::create([
        'model_type' => 'Sport',
        'model_id'   => $sport->id,
        'user_id'    => Auth::id(),
        'old_values' => null,
        'new_values' => json_encode($sport->toArray()),
        'action'     => 'Created Sport: ' . $sport->name,
    ]);

    return redirect()->route('sports.index')
        ->with('success', 'Sport added successfully!');
}




    /**
     * ❌ DISABLED: Create tournament accounts automatically
     * (commented, not removed)
     */
    /*
    private function createSportAccounts(Sport $sport)
    {
        $rolesToCreate = [
            'Tournament Secretary' => 'ts',
            'Tournament Manager' => 'tm',
        ];

        $divisions = \App\Models\Division::all();
        $levels = [$sport->division];
        $categories = ['Boys', 'Girls'];

        foreach ($divisions as $division) {
            foreach ($levels as $level) {
                foreach ($categories as $category) {

                    if (strtolower($category) !== strtolower($sport->category)) {
                        continue;
                    }

                    foreach ($rolesToCreate as $roleName => $roleInitials) {
                        $role = Role::firstOrCreate(['name' => $roleName]);

                        $sdoCode = strtolower(substr(str_replace('SDO - ', '', $division->name), 0, 3));

                        $email = strtolower(
                            substr($level, 0, 4) .
                            substr($category, 0, 1) .
                            str_replace(' ', '', strtolower($sport->name)) .
                            '.' . $roleInitials .
                            '.' . $sdoCode .
                            '@gmail.com'
                        );

                        $name = $division->name . ' - ' .
                                $level . ' ' .
                                $category . ' ' .
                                strtoupper($sport->name) . ' ' .
                                $roleName;

                        User::firstOrCreate(
                            ['email' => $email],
                            [
                                'name' => $name,
                                'password' => Hash::make('ESRRS@2026'),
                                'role_id' => $role->id,
                                'division_name' => $division->name,
                                'access' => ['results'],
                                'level_access' => [$level],
                                'category_access' => [$category],
                                'sports_access' => [$sport->id],
                            ]
                        );
                    }
                }
            }
        }
    }
    */

    public function edit($id)
    {
        $sport = Sport::with('types')->findOrFail($id);

        if ($sport->division === 'Paragames') {
            abort(403);
        }

        return view('sports.edit', compact('sport'));
    }

    public function update(Request $request, $id)
{
    $sport = Sport::with('types')->findOrFail($id);

    $request->validate([
        'name' => [
                'required',
                'string',
            ],
        'level' => 'required|in:Elementary,Secondary',
        'category' => 'required|in:Boys,Girls,Mixed',
        'type_of_game.*' => 'nullable|string',
    ]);

    $oldValues = $sport->toArray();

    // ================= UPDATE SPORT =================
    $sport->update([
        'name'     => $request->name,
        'level'    => $request->level,
        'category' => $request->category,
        'division' => $request->level,
    ]);

    // ================= HANDLE TYPES SAFELY =================
    $existingTypes = $sport->types()->get();

    $newTypes = collect($request->type_of_game)
        ->filter(fn($t) => !empty($t))
        ->map(fn($t) => trim($t))
        ->unique()
        ->values();

    foreach ($existingTypes as $type) {

        $isUsedInAll = \App\Models\AllAwardResult::where('type_id', $type->id)->exists();
        $isUsedInInd = \App\Models\IndividualAwardResult::where('type_id', $type->id)->exists();

        $isUsed = $isUsedInAll || $isUsedInInd;

        // ❌ DELETE ONLY IF NOT USED AND NOT IN NEW LIST
        if (!$isUsed && !$newTypes->contains($type->type_name)) {
            $type->delete();
        }
    }

    // ================= ADD NEW TYPES =================
    foreach ($newTypes as $typeName) {
        $exists = $existingTypes->firstWhere('type_name', $typeName);

        if (!$exists) {
            $sport->types()->create([
                'type_name' => $typeName
            ]);
        }
    }

    // ================= AUDIT =================
    AuditLog::create([
        'model_type' => 'Sport',
        'model_id'   => $sport->id,
        'user_id'    => Auth::id(),
        'old_values' => json_encode($oldValues),
        'new_values' => json_encode($sport->toArray()),
        'action'     => 'Updated Sport: ' . $sport->name,
    ]);

    return redirect()->route('sports.index')
        ->with('success', 'Sport updated successfully!');
}

    public function destroy($id)
    {
        $sport = Sport::findOrFail($id);

        if ($sport->division === 'Paragames') {
            abort(403, 'Use Para Games module.');
        }

        $sport->events()->update(['sport_id' => null]);
        \App\Models\Athlete::where('sport_id', $sport->id)->update(['sport_id' => null]);

        AllAwardResult::where('sport_id', $sport->id)->delete();
        IndividualAwardResult::where('sport_id', $sport->id)->delete();

        $sport->types()->delete();
        $sport->delete();

        return redirect()->route('sports.index')
            ->with('success', 'Sport deleted successfully!');
    }

    // ---------------- PARA GAMES ----------------

    public function paraIndex(Request $request)
{
    $category = $request->query('category');

    $query = Sport::with('types')
        ->where('division', 'Paragames');

    if ($category) {
        $query->where('category', $category);
    }

    $sports = $query->orderBy('name')->get();

    $totalParaSports = $sports->count();

    return view('sports.para.index', compact('sports', 'totalParaSports', 'category'));
}

    public function paraCreate()
    {
        return view('sports.para.create');
    }

   // PARA GAMES CREATE
public function paraStore(Request $request)
{
    $request->validate([
        'name' => [
    'required',
    'string',
],

        // ✅ UPDATED (added Mixed)
        'category' => 'required|in:Boys,Girls,Mixed',

        'type_of_game.*' => 'nullable|string',
    ]);

    $sport = Sport::create([
        'name'     => $request->name,
        'division' => 'Paragames',
        'category' => $request->category,
    ]);

    if ($request->type_of_game) {
        foreach ($request->type_of_game as $type) {
            if ($type) {
                $sport->types()->create(['type_name' => $type]);
            }
        }
    }

    AuditLog::create([
        'model_type' => 'Sport',
        'model_id' => $sport->id,
        'user_id' => Auth::id(),
        'old_values' => null,
        'new_values' => json_encode($sport->toArray()),
        'action' => 'Created Para Game: ' . $sport->name,
    ]);

    return redirect()->route('para-sports.index')
        ->with('success', 'Para Game added successfully.');
}




    public function paraEdit(Sport $sport)
    {
        return view('sports.para.edit', compact('sport'));
    }

   // PARA GAMES UPDATE
public function paraUpdate(Request $request, Sport $sport)
{
    $sport->load('types');

    $request->validate([
        'name' => [
    'required',
    'string',
],
        'category' => 'required|in:Boys,Girls,Mixed',
        'type_of_game.*' => 'nullable|string',
    ]);

    $oldValues = $sport->toArray();

    // ================= UPDATE =================
    $sport->update([
        'name' => $request->name,
        'category' => $request->category,
    ]);

    // ================= SAFE TYPE HANDLING =================
    $existingTypes = $sport->types()->get();

    $newTypes = collect($request->type_of_game)
        ->filter(fn($t) => !empty($t))
        ->map(fn($t) => trim($t))
        ->unique()
        ->values();

    foreach ($existingTypes as $type) {

        $isUsedInAll = \App\Models\AllAwardResult::where('type_id', $type->id)->exists();
        $isUsedInInd = \App\Models\IndividualAwardResult::where('type_id', $type->id)->exists();

        $isUsed = $isUsedInAll || $isUsedInInd;

        if (!$isUsed && !$newTypes->contains($type->type_name)) {
            $type->delete();
        }
    }

    // ADD NEW
    foreach ($newTypes as $typeName) {
        $exists = $existingTypes->firstWhere('type_name', $typeName);

        if (!$exists) {
            $sport->types()->create([
                'type_name' => $typeName
            ]);
        }
    }

    // ================= AUDIT =================
    AuditLog::create([
        'model_type' => 'Sport',
        'model_id' => $sport->id,
        'user_id' => Auth::id(),
        'old_values' => json_encode($oldValues),
        'new_values' => json_encode($sport->toArray()),
        'action' => 'Updated Para Game: ' . $sport->name,
    ]);

    return redirect()->route('para-sports.index')
        ->with('success', 'Para Game updated successfully.');
}


    public function paraDestroy(Sport $sport)
    {
        AllAwardResult::where('sport_id', $sport->id)->delete();
        IndividualAwardResult::where('sport_id', $sport->id)->delete();

        $sport->types()->delete();
        $sport->delete();

        return redirect()->route('para-sports.index')
            ->with('success', 'Para Game deleted successfully.');
    }

    // -------- BULK DELETE --------

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'sports' => 'required|array',
            'sports.*' => 'exists:sports,id'
        ]);

        $sports = Sport::whereIn('id', $request->sports)
            ->whereIn('division', ['Elementary', 'Secondary'])
            ->get();

        foreach ($sports as $sport) {
            AllAwardResult::where('sport_id', $sport->id)->delete();
            IndividualAwardResult::where('sport_id', $sport->id)->delete();
            $sport->types()->delete();
            $sport->delete();
        }

        return redirect()->route('sports.index')
            ->with('success', 'Selected sports deleted successfully!');
    }

    public function paraBulkDelete(Request $request)
    {
        $request->validate([
            'sports' => 'required|array',
            'sports.*' => 'exists:sports,id'
        ]);

        $sports = Sport::whereIn('id', $request->sports)
            ->where('division', 'Paragames')
            ->get();

        foreach ($sports as $sport) {
            AllAwardResult::where('sport_id', $sport->id)->delete();
            IndividualAwardResult::where('sport_id', $sport->id)->delete();
            $sport->types()->delete();
            $sport->delete();
        }

        return redirect()->route('para-sports.index')
            ->with('success', 'Selected para games deleted successfully!');
    }

    public function bulkUpload(Request $request)
{
    $user = Auth::user();

    // Only Super Admin
    if ($user->role->name !== 'Super Admin') {
        abort(403);
    }

    $request->validate([
        'file' => 'required|mimes:csv,txt',
    ]);

    try {
        $file = $request->file('file');

        // Keep track of counts per division & category
        $importedCounts = [
            'Elementary' => ['Boys' => 0, 'Girls' => 0],
            'Secondary'  => ['Boys' => 0, 'Girls' => 0],
            'Paragames'  => ['Boys' => 0, 'Girls' => 0],
        ];

        // Custom import that returns counts
        Excel::import(new class($importedCounts, $user) extends \App\Imports\SportsImport {
            protected $counts;
            protected $user;

            public function __construct(&$counts, $user)
            {
                $this->counts = &$counts;
                $this->user = $user;
            }

            public function collection(\Illuminate\Support\Collection $rows)
            {
                foreach ($rows as $row) {
                    if (!$row['name']) continue;

                    $name     = trim($row['name']);
                    $typeName = trim($row['type_of_sport'] ?? '');
                    $level    = trim($row['level'] ?? '');
                    $category = trim($row['category'] ?? '');

                    if (!$name || !$level) continue;

                    $isParagames = strtolower($level) === 'paragames';
                    $isParagames = strtolower($level) === 'paragames';

if ($isParagames) {
    if (strtolower($category) === 'mixed') {
        $categoriesToSave = ['Mixed'];
    } else {
        $categoriesToSave = ['Boys', 'Girls'];
    }
} else {
    $categoriesToSave = [$category];
}

                    foreach ($categoriesToSave as $catToSave) {
                        // Create or update sport
                        $sport = \App\Models\Sport::updateOrCreate(
                            ['name' => $name, 'division' => $level, 'category' => $catToSave],
                            ['level' => $level]
                        );

                        // Add sport type
                        if ($typeName) {
                            \App\Models\SportType::firstOrCreate([
                                'sport_id' => $sport->id,
                                'type_name' => $typeName
                            ]);
                        }

                        // Audit log
                        \App\Models\AuditLog::create([
                            'model_type' => 'Sport',
                            'model_id' => $sport->id,
                            'user_id' => $this->user->id,
                            'old_values' => null,
                            'new_values' => json_encode($sport->toArray()),
                            'action' => "Imported Sport via CSV: {$name} | Type: {$typeName} | Level: {$level} | Category: {$catToSave}"
                        ]);

                        // Increment count
                        if (isset($this->counts[$level][$catToSave])) {
                            $this->counts[$level][$catToSave]++;
                        }
                    }
                }
            }
        }, $file);

        // Prepare message
        $messages = [];
        foreach ($importedCounts as $division => $cats) {
            foreach ($cats as $cat => $count) {
                if ($count > 0) {
                    $messages[] = "{$count} {$division} {$cat}";
                }
            }
        }
        $messageStr = implode(', ', $messages);

        return redirect()->route('sports.index')
            ->with('success', 'Sports uploaded successfully! Created: ' . $messageStr);

    } catch (\Exception $e) {
        return back()->with('error', 'Failed to import CSV: ' . $e->getMessage());
    }
}
}
