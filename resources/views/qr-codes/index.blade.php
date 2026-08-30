@extends('layouts.app')

@section('title', 'QR Code Management')

@section('content')

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-header-title">QR Code Management</h1>
        <p class="page-header-sub">Generate, assign, print, and validate QR codes for Bulalacao construction logistics</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('qr.scanner') }}" class="btn btn-fb-scan">
            <i class="fa-solid fa-camera me-2"></i> Open Scanner
        </a>
        <button type="button" class="btn btn-fb-view" data-bs-toggle="modal" data-bs-target="#generateQrModal">
            <i class="fa-solid fa-qrcode me-2"></i> Generate QR Tag
        </button>
    </div>
</div>

<!-- Search & Filter -->
<div class="fb-card mb-3">
    <form method="GET" action="{{ route('qr.index') }}" class="row g-2 align-items-end">
        <div class="col-12 col-md-6">
            <label class="form-label">Search QR Codes</label>
            <div class="input-icon-group">
                <i class="input-icon fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" class="form-control" placeholder="Search QR code, batch number, or material name..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">All QR Status</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-fb-view w-100">Filter</button>
        </div>
    </form>
</div>

<!-- QR Codes Grid -->
<div class="row g-3 mb-3">
    @forelse($qrCodes as $qr)
        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <div class="fb-card h-100 text-center d-flex flex-column justify-content-between" style="padding: 16px;">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge text-bg-light border font-monospace text-muted" style="font-size: 10px;">
                            {{ $qr->batch_number ?? 'BATCH-A' }}
                        </span>
                        @if($qr->status === 'active')
                            <span class="badge-available" style="font-size: 10px;">Active</span>
                        @else
                            <span class="badge-used" style="font-size: 10px;">Inactive</span>
                        @endif
                    </div>

                    <!-- QR Icon -->
                    <div class="d-flex justify-content-center mb-3">
                        <div style="width: 80px; height: 80px; background: #f0f2f5; border-radius: 12px; border: 1px solid var(--fb-border); display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-qrcode" style="font-size: 3rem; color: #050505;"></i>
                        </div>
                    </div>

                    <div class="font-monospace fw-bold mb-1" style="color: var(--fb-blue); font-size: 13px;">{{ $qr->code }}</div>
                    <a href="{{ route('materials.show', $qr->material_id) }}" class="fw-bold text-dark text-decoration-none small d-block mb-1 text-truncate" title="{{ $qr->material->name ?? 'Material' }}">
                        {{ $qr->material->name ?? 'Material' }}
                    </a>
                    <span class="text-muted d-block" style="font-size: 11px;">
                        Stock: <strong>{{ $qr->material->current_stock ?? 0 }} {{ $qr->material->unit ?? 'pcs' }}</strong>
                        &bull; {{ $qr->material->location->name ?? 'Depot' }}
                    </span>
                </div>

                <div class="d-flex gap-2 mt-3 pt-3" style="border-top: 1px solid var(--fb-border);">
                    <a href="{{ route('materials.show', $qr->material_id) }}" class="btn btn-fb-edit btn-sm w-50" style="font-size: 12px;">
                        <i class="fa-solid fa-eye me-1"></i> View
                    </a>
                    <form method="POST" action="{{ route('qr.toggle-status', $qr->id) }}" class="w-50">
                        @csrf
                        <button type="submit" class="btn btn-sm w-100 {{ $qr->status === 'active' ? 'btn-fb-danger' : 'btn-fb-add' }}" style="font-size: 12px;">
                            {{ $qr->status === 'active' ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="fb-card text-center py-5 text-muted">
                <div style="font-size: 56px; margin-bottom: 12px; color: #dce1e7;"><i class="fa-solid fa-qrcode"></i></div>
                <div class="fw-bold" style="font-size: 18px;">No QR Codes Found</div>
                <div class="small mb-3">Generate a new QR code or register a material to get started.</div>
                <button type="button" class="btn btn-fb-view" data-bs-toggle="modal" data-bs-target="#generateQrModal">
                    <i class="fa-solid fa-qrcode me-1"></i> Generate QR Tag
                </button>
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
    <span class="text-muted small">Showing {{ $qrCodes->firstItem() ?? 0 }} to {{ $qrCodes->lastItem() ?? 0 }} of {{ $qrCodes->total() }} QR codes</span>
    {{ $qrCodes->links('pagination::bootstrap-5') }}
</div>

<!-- Generate QR Modal -->
<div class="modal fade" id="generateQrModal" tabindex="-1" aria-labelledby="generateQrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 34px; height: 34px; background: #f3e8ff; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #7b4ff7; font-size: 15px;">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <h5 class="modal-title" id="generateQrModalLabel">Generate Material QR Code</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('qr.generate') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="material_id" class="form-label">Target Material <span class="text-danger">*</span></label>
                        <select class="form-select" id="material_id" name="material_id" required>
                            <option value="">Select Material...</option>
                            @foreach(\App\Models\Material::all() as $m)
                                <option value="{{ $m->id }}">
                                    {{ $m->name }} (Available: {{ $m->current_stock }} {{ $m->unit }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-0">
                        <label for="batch_number" class="form-label">Batch Identification Tag</label>
                        <input type="text" class="form-control" id="batch_number" name="batch_number" value="BATCH-{{ date('Ym') }}-{{ rand(100, 999) }}" placeholder="e.g. BATCH-202608-01">
                        <div class="form-text">Optional batch tag for shipment lot tracking.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-fb-edit btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-fb-scan btn-sm">
                        <i class="fa-solid fa-qrcode me-1"></i> Generate &amp; Assign QR
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
