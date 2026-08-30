<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Material;
use App\Models\QrCode;
use App\Models\Site;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $roleSlug = $user->role ? $user->role->slug : 'site-personnel';

        // General Counts & Summary
        $stats = [
            'total_materials' => Material::count(),
            'total_sites' => Site::count(),
            'total_locations' => Location::count(),
            'total_qrcodes' => QrCode::where('status', 'active')->count(),
            'total_transactions' => Transaction::count(),
            'low_stock_count' => Material::whereColumn('current_stock', '<=', 'minimum_stock_level')->count(),
            'damaged_count' => Transaction::whereIn('type', ['damaged', 'lost'])->sum('quantity'),
            'today_scans' => Transaction::whereDate('created_at', today())->count(),
            'total_users' => User::count(),
        ];

        $recentTransactions = Transaction::with(['material', 'fromLocation.site', 'toLocation.site', 'performedByUser'])
            ->latest()
            ->take(6)
            ->get();

        $lowStockMaterials = Material::with(['category', 'location.site'])
            ->whereColumn('current_stock', '<=', 'minimum_stock_level')
            ->take(5)
            ->get();

        $recentLogs = ActivityLog::with('user')->latest()->take(6)->get();

        $sites = Site::withCount('locations')->with('locations.materials')->get();

        return view('dashboard', compact(
            'user',
            'roleSlug',
            'stats',
            'recentTransactions',
            'lowStockMaterials',
            'recentLogs',
            'sites'
        ));
    }
}
