<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Location;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Transaction;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::with(['category', 'location.site', 'qrCodes', 'inventories.location']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $materials = $query->latest()->paginate(12)->withQueryString();
        $categories = MaterialCategory::all();
        $locations = Location::with('site')->get();

        $stats = [
            'total_items' => Material::sum('current_stock'),
            'low_stock' => Material::whereColumn('current_stock', '<=', 'minimum_stock_level')->count(),
            'out_of_stock' => Material::where('current_stock', '<=', 0)->count(),
            'active_materials' => Material::count(),
        ];

        return view('inventory.index', compact('materials', 'categories', 'locations', 'stats'));
    }

    public function lowStock()
    {
        $lowStockMaterials = Material::with(['category', 'supplier', 'location.site', 'qrCodes'])
            ->whereColumn('current_stock', '<=', 'minimum_stock_level')
            ->orderBy('current_stock', 'asc')
            ->paginate(15);

        return view('inventory.low-stock', compact('lowStockMaterials'));
    }

    public function damagedLost()
    {
        $incidents = Transaction::with(['material.category', 'fromLocation.site', 'performedByUser'])
            ->whereIn('type', ['damaged', 'lost'])
            ->latest()
            ->paginate(15);

        $totalDamaged = Transaction::where('type', 'damaged')->sum('quantity');
        $totalLost = Transaction::where('type', 'lost')->sum('quantity');

        return view('inventory.damaged-lost', compact('incidents', 'totalDamaged', 'totalLost'));
    }
}
