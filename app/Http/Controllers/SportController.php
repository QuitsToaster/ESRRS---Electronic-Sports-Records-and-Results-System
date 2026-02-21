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
            Rule::unique('sports')->where(function ($query) use ($request) {
                return $query->where('division', $request->level)
                             ->where('category', $request->category); // include category in uniqueness
            }),
        ],
        'level' => 'required|in:Elementary,Secondary',
        'category' => 'required|in:Boys,Girls',
        'type_of_game.*' => 'nullable|string',
    ]);

    // Create the sport
    $sport = Sport::create([
        'name'     => $request->name,
        'division' => $request->level,
        'level'    => $request->level,
        'category' => $request->category,
    ]);

    // Create sport types
    if ($request->type_of_game) {
        foreach ($request->type_of_game as $type) {
            if ($type) {
                $sport->types()->create(['type_name' => $type]);
            }
        }
    }

    // Audit log
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
    $sport = Sport::findOrFail($id);

    $request->validate([
        'name' => [
            'required',
            'string',
            Rule::unique('sports')->where(function ($query) use ($request, $sport) {
                return $query->where('division', $request->level)
                             ->where('category', $request->category);
            })->ignore($sport->id),
        ],
        'level' => 'required|in:Elementary,Secondary',
        'category' => 'required|in:Boys,Girls',
        'type_of_game.*' => 'nullable|string',
    ]);

    $oldValues = $sport->toArray();

    $sport->update([
        'name'     => $request->name,
        'level'    => $request->level,
        'category' => $request->category,
        'division' => $request->level,
    ]);

    $sport->types()->delete();
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

    public function paraIndex()
    {
        $sports = Sport::with('types')
            ->where('division', 'Paragames')
            ->orderBy('name')
            ->get();

        return view('sports.para.index', compact('sports'));
    }

    public function paraCreate()
    {
        return view('sports.para.create');
    }

    public function paraStore(Request $request)
{
    $request->validate([
        'name' => 'required|string|unique:sports,name',
        'category' => 'required|in:Boys,Girls', // ✅ validate category
        'type_of_game.*' => 'nullable|string',
    ]);

    // Create the para sport and save category
    $sport = Sport::create([
        'name'     => $request->name,
        'division' => 'Paragames',
        'category' => $request->category, // ✅ save category
    ]);

    // Create types if provided
    if ($request->type_of_game) {
        foreach ($request->type_of_game as $type) {
            if ($type) {
                $sport->types()->create(['type_name' => $type]);
            }
        }
    }

    return redirect()->route('para-sports.index')
        ->with('success', 'Para Game added successfully.');
}


    public function paraEdit(Sport $sport)
    {
        return view('sports.para.edit', compact('sport'));
    }

    public function paraUpdate(Request $request, Sport $sport)
{
    // Validate name and category
    $request->validate([
        'name' => 'required|string|unique:sports,name,' . $sport->id,
        'category' => 'required|in:Boys,Girls', // ✅ validate category
        'type_of_game.*' => 'nullable|string',
    ]);

    // Save old values for audit logging
    $oldValues = $sport->toArray();

    // Update name and category
    $sport->update([
        'name' => $request->name,
        'category' => $request->category, // ✅ save updated category
    ]);

    // Delete old types and create new ones
    $sport->types()->delete();
    if ($request->type_of_game) {
        foreach ($request->type_of_game as $type) {
            if ($type) {
                $sport->types()->create(['type_name' => $type]);
            }
        }
    }

    // Optional: Audit log for Para Games
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
}
