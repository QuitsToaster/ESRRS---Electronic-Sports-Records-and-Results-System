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
                // Determine categories
                $categories = strtolower($level) === 'paragames' ? [null] : ['Boys', 'Girls'];

                foreach ($categories as $category) {
                    // Skip if category doesn't match sport's category
                    if ($category && $sport->category && strtolower($category) !== strtolower($sport->category)) {
                        continue;
                    }

                    foreach ($rolesToCreate as $roleName => $roleInitials) {
                        $role = Role::firstOrCreate(['name' => $roleName]);

                        // Create email
                        $sdoCode = strtolower(substr(str_replace('SDO - ', '', $division->name), 0, 3));
                        $email = strtolower(
                            substr($level, 0, 4) .
                            ($category ? substr($category, 0, 1) : 'P') .
                            strtolower(str_replace(' ', '', $sport->name)) .
                            '.' .
                            $roleInitials .
                            '.' . $sdoCode .
                            '@gmail.com'
                        );

                        // Display name
                        $name = $division->name . ' - ' . $level;
                        if ($category) $name .= ' ' . $category;
                        $name .= ' ' . $sport->name . ' ' . $roleName;

                        // ✅ Create user with division_name
                        User::firstOrCreate(
                            ['email' => $email],
                            [
                                'name' => $name,
                                'password' => Hash::make('ESRRS@2026'),
                                'role_id' => $role->id,
                                'division_name' => $division->name, // store actual division name
                                'access' => ['results'],
                                'level_access' => [$level],
                                'category_access' => $category ? [$category] : [],
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
