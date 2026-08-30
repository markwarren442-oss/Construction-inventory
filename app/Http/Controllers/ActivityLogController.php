<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('action', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('module', 'like', '%' . $request->search . '%')
                  ->orWhere('ip_address', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        $logs = $query->latest()->paginate(20)->withQueryString();
        $modules = ActivityLog::distinct()->pluck('module')->filter();

        return view('activity-logs.index', compact('logs', 'modules'));
    }
}
