<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Location;
use App\Models\Material;
use App\Models\Site;
use App\Models\Transaction;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $sites = Site::with(['locations.materials.category', 'locations.inventory'])->get();
        $locations = Location::with(['site', 'materials'])->get();

        return view('locations.index', compact('sites', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:warehouse,storage_area,site_section',
            'description' => 'nullable|string',
        ]);

        $location = Location::create($validated);

        ActivityLog::log('location_created', 'Location Management', "Created location {$location->name} in site #{$location->site_id}");

        return back()->with('success', "Location '{$location->name}' added successfully.");
    }

    public function show(int $id)
    {
        $location = Location::with(['site', 'materials.category', 'materials.qrCodes', 'inventory.material'])
            ->findOrFail($id);

        $transactions = Transaction::with(['material', 'performedByUser'])
            ->where('from_location_id', $location->id)
            ->orWhere('to_location_id', $location->id)
            ->latest()
            ->take(20)
            ->get();

        return view('locations.show', compact('location', 'transactions'));
    }
}
