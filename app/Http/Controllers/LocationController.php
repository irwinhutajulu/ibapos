<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    /**
     * Display a listing of locations
     */
    public function index()
    {
        $locations = Location::with('users:id,name,email')
            ->orderBy('name')
            ->paginate(10);

        return view('locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new location
     */
    public function create()
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        return view('locations.create', compact('users'));
    }

    /**
     * Store a newly created location
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:locations,name',
            'address' => 'nullable|string|max:255',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $location = Location::create([
            'name' => $validated['name'],
            'address' => $validated['address']
        ]);

        // Sync users
        if (!empty($validated['user_ids'])) {
            $location->users()->sync($validated['user_ids']);
        }

        return redirect()->route('locations.index')
            ->with('success', 'Location created successfully.');
    }

    /**
     * Display the specified location
     */
    public function show(Location $location)
    {
        $location->load(['users:id,name,email']);
        return view('locations.show', compact('location'));
    }

    /**
     * Show the form for editing the specified location
     */
    public function edit(Location $location)
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $location->load('users');
        return view('locations.edit', compact('location', 'users'));
    }

    /**
     * Update the specified location
     */
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('locations', 'name')->ignore($location->id)
            ],
            'address' => 'nullable|string|max:255',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $location->update([
            'name' => $validated['name'],
            'address' => $validated['address']
        ]);

        // Sync users
        $location->users()->sync($validated['user_ids'] ?? []);

        return redirect()->route('locations.index')
            ->with('success', 'Location updated successfully.');
    }

    /**
     * Remove the specified location
     */
    public function destroy(Location $location)
    {
        try {
            // Check if location has any related data
            if ($location->users()->count() > 0) {
                return redirect()->route('locations.index')
                    ->with('error', 'Cannot delete location that has assigned users. Please remove users first.');
            }

            $location->delete();

            return redirect()->route('locations.index')
                ->with('success', 'Location deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('locations.index')
                ->with('error', 'Cannot delete location. It may be referenced by other records.');
        }
    }

    /**
     * API endpoint for locations list
     */
    public function api()
    {
        $locations = Location::select('id', 'name', 'address')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }
}
