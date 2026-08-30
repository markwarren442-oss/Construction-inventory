<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Location;
use App\Models\Material;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QrCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = QrCode::with(['material.category', 'material.location.site', 'generatedByUser']);

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%')
                  ->orWhere('batch_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('material', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $qrCodes = $query->latest()->paginate(12)->withQueryString();
        $materialsWithoutQr = Material::doesntHave('qrCodes')->get();

        return view('qr-codes.index', compact('qrCodes', 'materialsWithoutQr'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'batch_number' => 'nullable|string|max:100',
        ]);

        $material = Material::findOrFail($validated['material_id']);
        $batch = $validated['batch_number'] ?? ('BATCH-' . date('Ym') . '-' . rand(100, 999));

        $qrCode = QrCode::create([
            'material_id' => $material->id,
            'code' => 'MAT-BUL-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'batch_number' => $batch,
            'status' => 'active',
            'generated_by' => Auth::id(),
        ]);

        ActivityLog::log('qr_generated', 'QR Code Management', "Generated QR code {$qrCode->code} for {$material->name}");

        return back()->with('success', "QR Code {$qrCode->code} successfully created for {$material->name}.");
    }

    public function scanner()
    {
        $locations = Location::with('site')->get();
        $recentScanned = QrCode::with(['material.category', 'material.location.site'])
            ->where('status', 'active')
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('qr-codes.scanner', compact('locations', 'recentScanned'));
    }

    public function lookup(Request $request)
    {
        $code = trim($request->input('code', ''));
        
        $qrCode = QrCode::with(['material.category', 'material.supplier', 'material.location.site'])
            ->where('code', $code)
            ->first();

        if (!$qrCode) {
            // Also allow matching material id directly or name
            $material = Material::with(['category', 'supplier', 'location.site', 'qrCodes'])
                ->where('name', 'like', "%{$code}%")
                ->orWhere('id', $code)
                ->first();

            if ($material) {
                return response()->json([
                    'success' => true,
                    'qr_code' => $material->qrCodes->first()->code ?? 'N/A',
                    'material' => $material,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => "No registered material found matching QR code '{$code}' in Bulalacao Logistics database.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'qr_code' => $qrCode->code,
            'batch_number' => $qrCode->batch_number,
            'status' => $qrCode->status,
            'material' => $qrCode->material,
        ]);
    }

    public function toggleStatus(int $id)
    {
        $qrCode = QrCode::findOrFail($id);
        $qrCode->status = ($qrCode->status === 'active') ? 'inactive' : 'active';
        $qrCode->save();

        ActivityLog::log('qr_status_toggled', 'QR Code Management', "Toggled QR code {$qrCode->code} to {$qrCode->status}");

        return back()->with('success', "QR Code {$qrCode->code} is now {$qrCode->status}.");
    }
}
