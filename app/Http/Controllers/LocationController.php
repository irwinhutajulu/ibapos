<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

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
            'phone' => 'nullable|string|max:20',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $location = Location::create([
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null
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
            'phone' => 'nullable|string|max:20',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $location->update([
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null
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
        // Defensive check across common dependent tables to prevent accidental destructive deletes.
        $locationId = $location->id;

        $counts = [];

        // users relation (pivot)
        try {
            $counts['users'] = $location->users()->count();
        } catch (\Throwable $e) {
            $counts['users'] = null;
        }

        // Common tables that may reference locations. If a table/column doesn't exist, we set null.
        $checks = [
            ['table' => 'stocks', 'column' => 'location_id', 'label' => 'stocks'],
            ['table' => 'sale_items', 'column' => 'source_location_id', 'label' => 'sale_items'],
            ['table' => 'sales', 'column' => 'location_id', 'label' => 'sales'],
            ['table' => 'purchases', 'column' => 'location_id', 'label' => 'purchases'],
            ['table' => 'stock_mutations', 'column' => 'from_location_id', 'label' => 'stock_mutations_from'],
            ['table' => 'stock_mutations', 'column' => 'to_location_id', 'label' => 'stock_mutations_to'],
            ['table' => 'adjustments', 'column' => 'location_id', 'label' => 'adjustments'],
            ['table' => 'reservations', 'column' => 'location_id', 'label' => 'reservations'],
        ];

        foreach ($checks as $c) {
            try {
                $counts[$c['label']] = DB::table($c['table'])->where($c['column'], $locationId)->count();
            } catch (\Throwable $e) {
                $counts[$c['label']] = null; // table/column missing or other error
            }
        }

        $blocking = array_filter($counts, function ($v) {
            return is_numeric($v) && $v > 0;
        });

        if (!empty($blocking)) {
            // Build human-friendly message
            $parts = [];
            foreach ($blocking as $k => $v) {
                $parts[] = sprintf('%s: %d', $k, $v);
            }

            $message = 'Cannot delete location because it is referenced by other records: ' . implode(', ', $parts);

            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message, 'details' => $blocking], 422);
            }

            return redirect()->route('locations.index')->with('error', $message);
        }

        // Safe to delete
        try {
            $location->delete();

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Location deleted successfully.']);
            }

            return redirect()->route('locations.index')->with('success', 'Location deleted successfully.');
        } catch (\Throwable $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unable to delete location: ' . $e->getMessage()], 500);
            }

            return redirect()->route('locations.index')->with('error', 'Unable to delete location.');
        }
    }

    /**
     * API endpoint for locations list
     */
    public function api()
    {
        $locations = Location::select('id', 'name', 'address', 'phone')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }
}
