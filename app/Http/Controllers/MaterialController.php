<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\QrCode;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::with(['category', 'supplier', 'location.site']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $materials = $query->latest()->paginate(10)->withQueryString();
        $categories = MaterialCategory::all();
        $locations = Location::with('site')->get();

        return view('materials.index', compact('materials', 'categories', 'locations'));
    }

    public function create()
    {
        $categories = MaterialCategory::all();
        $suppliers = Supplier::all();
        $locations = Location::with('site')->get();

        return view('materials.create', compact('categories', 'suppliers', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:material_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit' => 'required|string|max:50',
            'description' => 'nullable|string',
            'minimum_stock_level' => 'required|integer|min:0',
            'current_stock' => 'required|integer|min:0',
            'location_id' => 'nullable|exists:locations,id',
            'action_type' => 'nullable|string',
        ]);

        $status = 'available';
        if ($validated['current_stock'] <= 0) {
            $status = 'out_of_stock';
        } elseif ($validated['current_stock'] <= $validated['minimum_stock_level']) {
            $status = 'low_stock';
        }

        $material = Material::create([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'] ?? null,
            'supplier_id' => $validated['supplier_id'] ?? null,
            'unit' => $validated['unit'],
            'description' => $validated['description'] ?? null,
            'minimum_stock_level' => $validated['minimum_stock_level'],
            'current_stock' => $validated['current_stock'],
            'location_id' => $validated['location_id'] ?? null,
            'status' => $status,
        ]);

        // Automatically Generate QR Code
        $qrCode = QrCode::create([
            'material_id' => $material->id,
            'code' => 'MAT-BUL-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'batch_number' => 'BATCH-' . date('Ym') . '-' . rand(10, 99),
            'status' => 'active',
            'generated_by' => Auth::id(),
        ]);

        // Create inventory location record
        if ($material->location_id) {
            Inventory::create([
                'material_id' => $material->id,
                'location_id' => $material->location_id,
                'quantity' => $material->current_stock,
                'last_updated_by' => Auth::id(),
            ]);
        }

        ActivityLog::log('material_created', 'Material Management', "Created material {$material->name} with QR Code {$qrCode->code}");

        if ($request->input('action_type') === 'save_and_qr') {
            return redirect()->route('materials.show', $material->id)->with('success', "Material registered and QR Code ({$qrCode->code}) generated successfully!");
        }

        return redirect()->route('materials.index')->with('success', "Material '{$material->name}' registered successfully.");
    }

    public function show(int $id)
    {
        $material = Material::with(['category', 'supplier', 'location.site', 'qrCodes', 'inventories.location.site'])
            ->findOrFail($id);

        $transactions = $material->transactions()
            ->with(['fromLocation.site', 'toLocation.site', 'performedByUser'])
            ->latest()
            ->take(15)
            ->get();

        return view('materials.show', compact('material', 'transactions'));
    }

    public function edit(int $id)
    {
        $material = Material::findOrFail($id);
        $categories = MaterialCategory::all();
        $suppliers = Supplier::all();
        $locations = Location::with('site')->get();

        return view('materials.edit', compact('material', 'categories', 'suppliers', 'locations'));
    }

    public function update(Request $request, int $id)
    {
        $material = Material::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:material_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit' => 'required|string|max:50',
            'description' => 'nullable|string',
            'minimum_stock_level' => 'required|integer|min:0',
            'location_id' => 'nullable|exists:locations,id',
            'status' => 'required|in:available,low_stock,out_of_stock',
        ]);

        $material->update($validated);
        $material->updateStockStatus();

        ActivityLog::log('material_updated', 'Material Management', "Updated material specifications for {$material->name}");

        return redirect()->route('materials.show', $material->id)->with('success', 'Material details updated successfully.');
    }

    public function destroy(int $id)
    {
        $material = Material::findOrFail($id);
        $name = $material->name;
        $material->update(['status' => 'out_of_stock']);

        ActivityLog::log('material_archived', 'Material Management', "Archived material {$name}");

        return redirect()->route('materials.index')->with('success', "Material '{$name}' archived.");
    }
}
