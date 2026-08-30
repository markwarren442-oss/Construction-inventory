@extends('layouts.app')

@section('title', $material->name)

@section('content')

<!-- Header with Quick Action Buttons -->
<div class="page-header">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="page-header-title">{{ $material->name }}</h1>
            @if($material->status === 'available')
                <span class="badge-available">In Stock</span>
            @elseif($material->status === 'low_stock')
                <span class="badge-low">Low Stock</span>
            @else
                <span class="badge-out">Out of Stock</span>
            @endif
        </div>
        <p class="page-header-sub">Item ID #MAT-{{ str_pad($material->id, 5, '0', STR_PAD_LEFT) }} &bull; {{ $material->category->name ?? 'General Material' }}</p>
    </div>

    <div class="d-flex flex-wrap gap-2">
        @if($material->location)
            <a href="{{ $material->location->google_maps_url }}" target="_blank" class="btn btn-fb-view btn-sm" title="View depot on Google Maps">
                <i class="fa-solid fa-map-location-dot me-1"></i> Google Maps
            </a>
        @endif
        <a href="{{ route('transactions.create', ['material_id' => $material->id]) }}" class="btn btn-fb-action btn-sm">
            <i class="fa-solid fa-right-left me-1"></i> Record Movement
        </a>
        @if(auth()->user()->hasRole('inventory-officer') || auth()->user()->isAdmin())
            <a href="{{ route('materials.edit', $material->id) }}" class="btn btn-fb-edit btn-sm">
                <i class="fa-solid fa-pen me-1"></i> Edit Info
            </a>
        @endif
        <button type="button" onclick="window.print()" class="btn btn-fb-edit btn-sm">
            <i class="fa-solid fa-print me-1"></i> Print Tag
        </button>
        <a href="{{ route('materials.index') }}" class="btn btn-fb-edit btn-sm" title="Back to Catalog">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>
</div>

<div class="row g-3 mb-3">
    
    <!-- Material Info Card -->
    <div class="col-12 col-lg-8">
        <div class="fb-card h-100 p-4">
            <div class="sidebar-section-title ps-0 mb-3" style="font-size: 13px;">
                <i class="fa-solid fa-circle-info text-primary me-1"></i> Material Specifications
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6 col-sm-4">
                    <span class="text-muted small d-block">Current Stock Level</span>
                    <span class="fs-4 fw-bold text-dark">{{ $material->current_stock }}</span>
                    <span class="text-muted small">{{ $material->unit }}</span>
                </div>
                <div class="col-6 col-sm-4">
                    <span class="text-muted small d-block">Minimum Threshold</span>
                    <span class="fs-4 fw-bold text-secondary">{{ $material->minimum_stock_level }}</span>
                    <span class="text-muted small">{{ $material->unit }}</span>
                </div>
                <div class="col-12 col-sm-4">
                    <span class="text-muted small d-block">Assigned Location</span>
                    <span class="fw-bold text-dark d-block">{{ $material->location->name ?? 'Unassigned Depot' }}</span>
                    <small class="text-muted">{{ $material->location->site->name ?? 'Bulalacao Infrastructure' }}</small>
                </div>
            </div>

            <div class="row g-3 mb-3 pt-3" style="border-top: 1px solid var(--fb-border);">
                <div class="col-12 col-sm-6">
                    <span class="text-muted small d-block">Supplier / Source</span>
                    <span class="fw-semibold text-dark">{{ $material->supplier->name ?? 'Local Mindoro Supplier' }}</span>
                    @if($material->supplier->phone ?? false)
                        <small class="text-muted d-block"><i class="fa-solid fa-phone me-1" style="font-size: 10px;"></i> {{ $material->supplier->phone }}</small>
                    @endif
                </div>
                <div class="col-12 col-sm-6">
                    <span class="text-muted small d-block">Category</span>
                    <span class="fw-semibold text-dark">{{ $material->category->name ?? 'Standard Material' }}</span>
                </div>
            </div>

            @if($material->description)
                <div class="pt-3" style="border-top: 1px solid var(--fb-border);">
                    <span class="text-muted small d-block mb-1">Notes &amp; Description</span>
                    <div class="p-3 rounded-3 text-dark small" style="background: var(--fb-bg); border: 1px solid var(--fb-border);">
                        {{ $material->description }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- QR Code Card -->
    <div class="col-12 col-lg-4">
        <div class="fb-card h-100 p-4 text-center d-flex flex-column justify-content-between">
            <div>
                <div class="sidebar-section-title ps-0 mb-1" style="font-size: 13px;">
                    <i class="fa-solid fa-qrcode text-primary me-1"></i> Material QR Tag
                </div>
                <p class="text-muted small mb-3">Scan with camera or reader</p>

                <div class="p-3 rounded-3 d-inline-block mb-3" style="background: var(--fb-bg); border: 1px solid var(--fb-border);">
                    <div class="p-2 rounded d-inline-block mb-2" style="background: #fff; border: 1px solid var(--fb-border);">
                        <i class="fa-solid fa-qrcode text-dark" style="font-size: 5rem;"></i>
                    </div>
                    <div class="font-monospace fw-bold small" style="color: var(--fb-blue);">
                        {{ $material->qrCodes->first()->code ?? 'MAT-BUL-' . str_pad($material->id, 6, '0', STR_PAD_LEFT) }}
                    </div>
                    <span class="badge text-bg-light border text-muted" style="font-size: 10px;">
                        Batch: {{ $material->qrCodes->first()->batch_number ?? 'BATCH-2026-A' }}
                    </span>
                </div>
            </div>

            <a href="{{ route('qr.scanner') }}" class="btn btn-fb-scan btn-sm">
                <i class="fa-solid fa-camera me-1"></i> Open in Scanner
            </a>
        </div>
    </div>

</div>

<!-- ═══════════ REAL MAP / DEPOT GPS TRACKING ═══════════ -->
@if($material->location)
@php
    $loc = $material->location;
    $locLat = $loc->lat;
    $locLng = $loc->lng;
    $siteName = $loc->site->name ?? 'Bulalacao Site';
@endphp
<div class="fb-card p-4 mb-3">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
        <div>
            <div class="sidebar-section-title ps-0 mb-0" style="font-size: 13px;">
                <i class="fa-solid fa-location-dot text-danger me-1"></i>
                Depot &amp; Worksite Location Map
            </div>
            <p class="text-muted small mb-0">
                Stored at <strong>{{ $loc->name }}</strong> &bull; {{ $siteName }} (GPS: {{ $locLat }}, {{ $locLng }})
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ $loc->google_maps_directions_url }}" target="_blank" class="btn btn-fb-edit btn-sm">
                <i class="fa-solid fa-diamond-turn-right me-1 text-primary"></i> Get Directions
            </a>
            <a href="{{ $loc->google_maps_url }}" target="_blank" class="btn btn-fb-view btn-sm">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open in Google Maps
            </a>
        </div>
    </div>

    <!-- Interactive Leaflet Map Container -->
    <div id="materialDepotMap"
         data-lat="{{ $locLat }}"
         data-lng="{{ $locLng }}"
         data-name="{{ $loc->name }}"
         data-site="{{ $siteName }}"
         data-stock="{{ $material->current_stock }} {{ $material->unit }}"
         data-gmaps="{{ $loc->google_maps_url }}"
         style="height: 320px; width: 100%; border-radius: 10px; border: 1px solid var(--fb-border); z-index: 1;"></div>
    
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2 pt-2" style="font-size: 12px; color: var(--fb-muted);">
        <div>
            <i class="fa-solid fa-map-pin text-danger me-1"></i>
            <strong>{{ $loc->name }}</strong> &mdash; {{ $loc->site->address ?? 'Bulalacao, Oriental Mindoro' }}
        </div>
        <div>
            <span class="badge text-bg-light border font-monospace text-muted">Lat: {{ $locLat }}</span>
            <span class="badge text-bg-light border font-monospace text-muted">Lng: {{ $locLng }}</span>
        </div>
    </div>
</div>
@endif

<!-- Movement History Table Card -->
<div class="fb-card p-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <div class="sidebar-section-title ps-0 mb-0" style="font-size: 13px;">
                <i class="fa-solid fa-clock-rotate-left text-primary me-1"></i> Movement &amp; Audit History
            </div>
            <p class="text-muted small mb-0">Traceable ledger of all issuances, receipts, transfers, and adjustments</p>
        </div>
        <a href="{{ route('transactions.create', ['material_id' => $material->id]) }}" class="btn btn-fb-action btn-sm">
            <i class="fa-solid fa-plus me-1"></i> New Movement
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small" style="border-color: var(--fb-border);">
            <thead style="background: var(--fb-bg);">
                <tr>
                    <th>Ref #</th>
                    <th>Action Type</th>
                    <th>Quantity</th>
                    <th>From Location</th>
                    <th>To Location</th>
                    <th>Recorded By</th>
                    <th>Remarks</th>
                    <th>Date &amp; Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    @php
                        $txClass = match($tx->type) {
                            'received'    => 'received',
                            'issued'      => 'issued',
                            'transferred' => 'transferred',
                            'damaged','lost' => 'damaged',
                            default       => 'default',
                        };
                    @endphp
                    <tr>
                        <td class="font-monospace fw-semibold" style="color: var(--fb-blue);">{{ $tx->reference_number }}</td>
                        <td>
                            @if($tx->type === 'received')
                                <span class="badge-received">Received</span>
                            @elseif($tx->type === 'issued')
                                <span class="badge-issued">Issued</span>
                            @elseif($tx->type === 'transferred')
                                <span class="badge-transferred">Transferred</span>
                            @elseif($tx->type === 'used')
                                <span class="badge-used">Used</span>
                            @elseif(in_array($tx->type, ['damaged', 'lost']))
                                <span class="badge-damaged">{{ ucfirst($tx->type) }}</span>
                            @else
                                <span class="badge-used">{{ ucfirst($tx->type) }}</span>
                            @endif
                        </td>
                        <td class="fw-bold text-dark">
                            {{ $tx->quantity }} {{ $material->unit }}
                        </td>
                        <td class="text-muted">{{ $tx->fromLocation->name ?? 'External Supplier' }}</td>
                        <td class="fw-semibold text-dark">{{ $tx->toLocation->name ?? 'Worksite' }}</td>
                        <td>{{ $tx->performedByUser->name ?? 'Staff' }}</td>
                        <td class="text-muted small" style="max-width: 200px;">{{ $tx->remarks ?? '—' }}</td>
                        <td class="text-muted">{{ $tx->created_at ? $tx->created_at->format('M d, Y H:i') : 'Recently' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            No movement records registered for this material yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@if($material->location)
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const mapEl = document.getElementById('materialDepotMap');
        if (!mapEl || !window.L) return;

        const lat = parseFloat(mapEl.dataset.lat);
        const lng = parseFloat(mapEl.dataset.lng);
        const locName = mapEl.dataset.name || '';
        const siteName = mapEl.dataset.site || '';
        const stock = mapEl.dataset.stock || '';
        const gmapsUrl = mapEl.dataset.gmaps || '#';

        const map = L.map('materialDepotMap').setView([lat, lng], 14);

        // Clean modern CartoDB / OSM tiles
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://carto.com/">CARTO</a> | &copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Custom branded marker icon
        const customIcon = L.divIcon({
            className: 'custom-map-pin',
            html: '<div style="width: 36px; height: 36px; background: #1877f2; color: #fff; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 3px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 16px;"><i class="fa-solid fa-boxes-stacked"></i></div>',
            iconSize: [36, 36],
            iconAnchor: [18, 18],
            popupAnchor: [0, -18]
        });

        const popupContent = `
            <div style="font-family: Inter, sans-serif; min-width: 180px; padding: 4px;">
                <div style="font-weight: 700; font-size: 14px; color: #050505; margin-bottom: 2px;">${locName}</div>
                <div style="font-size: 12px; color: #65676b; margin-bottom: 8px;">${siteName}</div>
                <div style="font-size: 12px; font-weight: 600; color: #219150; margin-bottom: 8px;">
                    <i class="fa-solid fa-box me-1"></i> Stock: ${stock}
                </div>
                <a href="${gmapsUrl}" target="_blank" style="display: inline-block; background: #1877f2; color: #fff; text-decoration: none; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Google Maps
                </a>
            </div>
        `;

        L.marker([lat, lng], { icon: customIcon })
            .addTo(map)
            .bindPopup(popupContent)
            .openPopup();
    });
</script>
@endif
@endpush
