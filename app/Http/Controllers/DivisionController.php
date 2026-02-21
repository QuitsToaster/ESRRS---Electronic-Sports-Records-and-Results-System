<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Division;
use App\Models\User;
use App\Models\Role;
use App\Models\Sport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DivisionController extends Controller
{
    public function index(Request $request)
    {
        $query = Division::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $divisions = $query->orderBy('name')->get();

        return view('divisions.index', compact('divisions'));
    }

    public function create()
    {
        return view('divisions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:divisions,name',
            'logo' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:102400',
        ]);

        $data = $request->only('name');

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->storeLargeImage($request->file('logo'));
        }

        // Create division
        $division = Division::create($data);

        // Automatically create accounts for this division
        $this->createDivisionAccounts($division);

        return redirect()->route('divisions.index')
            ->with('success', 'Division created successfully, and user accounts generated.');
    }

    private function createDivisionAccounts(Division $division)
{
    $rolesToCreate = [
        'Tournament Secretary' => 'ts',
        'Tournament Manager' => 'tm'
    ];

    $levels = ['Elementary', 'Secondary', 'Paragames'];

    foreach ($levels as $level) {
        // Get sports for this level
        $sports = Sport::where('division', $level)->get();

        foreach ($sports as $sport) {
            // For Paragames, assign default category same as sport's category if exists
            $categories = strtolower($level) === 'paragames'
                ? [$sport->category ?? 'Boys']  // default to sport's category or 'Boys'
                : ['Boys', 'Girls'];

            foreach ($categories as $category) {
                // Skip if category doesn't match sport's category (for Elementary/Secondary)
                if ($category && $sport->category && strtolower($category) !== strtolower($sport->category)) {
                    continue;
                }

                foreach ($rolesToCreate as $roleName => $roleInitials) {
                    $role = Role::firstOrCreate(['name' => $roleName]);

                    // ---------------- SANITIZE SPORT NAME FOR EMAIL ----------------
                    $cleanSportName = preg_replace('/[^a-z0-9]/i', '', strtolower($sport->name));

                    // SDO code (3 letters, lowercase)
                    $sdoCode = strtolower(substr(str_replace('SDO - ', '', $division->name), 0, 3));

                    // Category initial (B/G/P)
                    $categoryInitial = $category ? substr($category, 0, 1) : 'P';

                    // Level code (first 4 letters)
                    $levelCode = substr(strtolower($level), 0, 4);

                    // Build email safely
                    $email = "{$levelCode}{$categoryInitial}{$cleanSportName}.{$roleInitials}.{$sdoCode}@gmail.com";

                    // Display name
                    $name = $division->name . ' - ' . $level;
                    if ($category) $name .= ' ' . $category;
                    $name .= ' ' . $sport->name . ' ' . $roleName;

                    // Create or fetch user
                    User::firstOrCreate(
                        ['email' => $email],
                        [
                            'name' => $name,
                            'password' => Hash::make('ESRRS@2026'),
                            'role_id' => $role->id,
                            'division_name' => $division->name,
                            'access' => ['results'],
                            'level_access' => [$level],
                            'category_access' => [$category], // now not empty
                            'sports_access' => [$sport->id],
                        ]
                    );
                }
            }
        }
    }
}



    private function storeLargeImage($image)
    {
        $filename = 'divisions/' . uniqid() . '.' . $image->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('divisions', $image, basename($filename));
        return $filename;
    }

    public function edit(Division $division)
    {
        return view('divisions.edit', compact('division'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'name' => 'required|string|unique:divisions,name,' . $division->id,
            'logo' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:102400',
        ]);

        $data = $request->only('name');

        if ($request->hasFile('logo')) {
            if ($division->logo && Storage::disk('public')->exists($division->logo)) {
                Storage::disk('public')->delete($division->logo);
            }

            $data['logo'] = $this->storeLargeImage($request->file('logo'));
        }

        $division->update($data);

        return redirect()->route('divisions.index')
            ->with('success', 'Division updated successfully.');
    }

    public function destroy(Division $division)
    {
        if ($division->logo && Storage::disk('public')->exists($division->logo)) {
            Storage::disk('public')->delete($division->logo);
        }

        $division->delete();

        return redirect()->route('divisions.index')
            ->with('success', 'Division deleted successfully.');
    }
}
