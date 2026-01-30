<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Division;
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
            'logo' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:102400', // 100MB
        ]);

        $data = $request->only('name');

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->storeLargeImage($request->file('logo'));
        }

        Division::create($data);

        return redirect()->route('divisions.index')->with('success', 'Division created successfully.');
    }

    public function edit(Division $division)
    {
        return view('divisions.edit', compact('division'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'name' => 'required|string|unique:divisions,name,' . $division->id,
            'logo' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:102400', // 100MB
        ]);

        $data = $request->only('name');

        if ($request->hasFile('logo')) {
            if ($division->logo && Storage::disk('public')->exists($division->logo)) {
                Storage::disk('public')->delete($division->logo);
            }

            $data['logo'] = $this->storeLargeImage($request->file('logo'));
        }

        $division->update($data);

        return redirect()->route('divisions.index')->with('success', 'Division updated successfully.');
    }

    public function destroy(Division $division)
    {
        if ($division->logo && Storage::disk('public')->exists($division->logo)) {
            Storage::disk('public')->delete($division->logo);
        }

        $division->delete();

        return redirect()->route('divisions.index')->with('success', 'Division deleted successfully.');
    }

    /**
     * Store a large uploaded image directly without resizing
     */
    private function storeLargeImage($image)
    {
        $filename = 'divisions/' . uniqid() . '.' . $image->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('divisions', $image, basename($filename));
        return $filename;
    }
}
