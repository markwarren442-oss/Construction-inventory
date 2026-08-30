<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Site;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->input('report_type', 'movement');
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $transactionsQuery = Transaction::with(['material.category', 'fromLocation.site', 'toLocation.site', 'performedByUser'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($request->filled('type') && $request->type !== 'all') {
            $transactionsQuery->where('type', $request->type);
        }

        if ($request->filled('site_id')) {
            $siteId = $request->site_id;
            $transactionsQuery->whereHas('fromLocation', function($q) use ($siteId) {
                $q->where('site_id', $siteId);
            })->orWhereHas('toLocation', function($q) use ($siteId) {
                $q->where('site_id', $siteId);
            });
        }

        $transactions = $transactionsQuery->latest()->get();

        $materials = Material::with(['category', 'location.site'])->get();
        $sites = Site::all();
        $categories = MaterialCategory::all();

        $summary = [
            'total_received' => $transactions->where('type', 'received')->sum('quantity'),
            'total_issued' => $transactions->where('type', 'issued')->sum('quantity'),
            'total_transferred' => $transactions->where('type', 'transferred')->sum('quantity'),
            'total_damaged_lost' => $transactions->whereIn('type', ['damaged', 'lost'])->sum('quantity'),
            'transaction_count' => $transactions->count(),
        ];

        return view('reports.index', compact(
            'transactions',
            'materials',
            'sites',
            'categories',
            'reportType',
            'startDate',
            'endDate',
            'summary'
        ));
    }
}
