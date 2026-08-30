<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Material;
use App\Models\QrCode;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['material.category', 'fromLocation.site', 'toLocation.site', 'performedByUser', 'qrCode']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('material_id')) {
            $query->where('material_id', $request->material_id);
        }

        if ($request->filled('location_id')) {
            $query->where(function($q) use ($request) {
                $q->where('from_location_id', $request->location_id)
                  ->orWhere('to_location_id', $request->location_id);
            });
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('reference_number', 'like', '%' . $request->search . '%')
                  ->orWhere('remarks', 'like', '%' . $request->search . '%')
                  ->orWhereHas('material', function($mq) use ($request) {
                      $mq->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();
        $materials = Material::all();
        $locations = Location::with('site')->get();

        return view('transactions.index', compact('transactions', 'materials', 'locations'));
    }

    public function create(Request $request)
    {
        $selectedMaterialId = $request->input('material_id');
        $selectedType = $request->input('type', 'received');
        $materials = Material::with(['category', 'location.site', 'qrCodes'])->get();
        $locations = Location::with('site')->get();

        return view('transactions.create', compact('materials', 'locations', 'selectedMaterialId', 'selectedType'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'type' => 'required|in:received,issued,transferred,used,returned,damaged,lost',
            'quantity' => 'required|integer|min:1',
            'from_location_id' => 'nullable|exists:locations,id',
            'to_location_id' => 'nullable|exists:locations,id',
            'remarks' => 'nullable|string',
            'qr_code_id' => 'nullable|exists:qr_codes,id',
        ]);

        $material = Material::findOrFail($validated['material_id']);
        $type = $validated['type'];
        $qty = $validated['quantity'];

        // Auto-assign QR code if not explicitly given
        $qrCodeId = $validated['qr_code_id'] ?? ($material->qrCodes()->first()->id ?? null);

        DB::beginTransaction();
        try {
            // Calculate Stock Changes based on transaction type
            if (in_array($type, ['received', 'returned'])) {
                $material->current_stock += $qty;
                if (!empty($validated['to_location_id'])) {
                    $material->location_id = $validated['to_location_id'];
                }
            } elseif (in_array($type, ['issued', 'used', 'damaged', 'lost'])) {
                if ($material->current_stock < $qty && in_array($type, ['issued', 'used'])) {
                    return back()->withInput()->with('error', "Insufficient stock for {$material->name}. Available: {$material->current_stock} {$material->unit}");
                }
                $material->current_stock = max(0, $material->current_stock - $qty);
            } elseif ($type === 'transferred') {
                if ($material->current_stock < $qty) {
                    return back()->withInput()->with('error', "Cannot transfer {$qty} {$material->unit}. Only {$material->current_stock} available.");
                }
                if (!empty($validated['to_location_id'])) {
                    $material->location_id = $validated['to_location_id'];
                }
            }

            $material->updateStockStatus();

            // Create Transaction record
            $refNumber = Transaction::generateReferenceNumber($type);
            $transaction = Transaction::create([
                'material_id' => $material->id,
                'qr_code_id' => $qrCodeId,
                'type' => $type,
                'quantity' => $qty,
                'from_location_id' => $validated['from_location_id'] ?? $material->location_id,
                'to_location_id' => $validated['to_location_id'] ?? $material->location_id,
                'performed_by' => Auth::id(),
                'remarks' => $validated['remarks'] ?? null,
                'reference_number' => $refNumber,
            ]);

            // Update/Create Inventory Location record
            if ($transaction->to_location_id) {
                $inventory = Inventory::firstOrNew([
                    'material_id' => $material->id,
                    'location_id' => $transaction->to_location_id,
                ]);
                $inventory->quantity = $material->current_stock;
                $inventory->last_updated_by = Auth::id();
                $inventory->save();
            }

            ActivityLog::log("transaction_{$type}", 'Transactions', "Recorded {$type} of {$qty} {$material->unit} of {$material->name} [Ref: {$refNumber}]");

            DB::commit();

            return redirect()->route('transactions.show', $transaction->id)->with('success', "Transaction [{$refNumber}] recorded successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error recording transaction: ' . $e->getMessage());
        }
    }

    public function show(int $id)
    {
        $transaction = Transaction::with([
            'material.category',
            'material.supplier',
            'fromLocation.site',
            'toLocation.site',
            'performedByUser.role',
            'qrCode'
        ])->findOrFail($id);

        return view('transactions.show', compact('transaction'));
    }
}
