@extends('layouts.app')

@section('title', 'Transaction ' . $transaction->reference_number)

@section('content')

@php
    $fromLoc = $transaction->fromLocation;
    $toLoc = $transaction->toLocation;

    $fromLat = $fromLoc ? $fromLoc->lat : 12.3340;
    $fromLng = $fromLoc ? $fromLoc->lng : 121.3465;
    $toLat = $toLoc ? $toLoc->lat : 12.3295;
    $toLng = $toLoc ? $toLoc->lng : 121.3390;

    $googleRouteUrl = "https://www.google.com/maps/dir/?api=1&origin=" . urlencode("{$fromLat},{$fromLng}") . "&destination=" . urlencode("{$toLat},{$toLng}");
@endphp

<div class="row justify-content-center">
    <div class="col-12 col-lg-8 col-xl-7">
        
        <!-- Header -->
        <div class="page-header">
            <div>
                <h1 class="page-header-title">Transaction Receipt</h1>
                <p class="page-header-sub">Ref # {{ $transaction->reference_number }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ $googleRouteUrl }}" target="_blank" class="btn btn-fb-view btn-sm">
                    <i class="fa-solid fa-route me-1"></i> Google Maps Route
                </a>
                <button type="button" onclick="window.print()" class="btn btn-fb-edit btn-sm">
                    <i class="fa-solid fa-print me-1"></i> Print
                </button>
                <a href="{{ route('transactions.index') }}" class="btn btn-fb-edit btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <!-- Receipt Card -->
        <div class="fb-card p-4 p-md-5">
            
            <!-- Receipt Header Branding -->
            <div class="text-center pb-4 mb-4" style="border-bottom: 1px solid var(--fb-border);">
                <div style="width: 50px; height: 50px; background: var(--fb-blue); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: #fff; font-size: 22px; margin-bottom: 10px;">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </div>
                <h4 class="fw-bold text-dark mb-0">ConstructLogix Logistics System</h4>
                <p class="text-muted small mb-1">Bulalacao, Oriental Mindoro Infrastructure Project</p>
                <div class="font-monospace fw-bold small" style="color: var(--fb-blue);">REF: {{ $transaction->reference_number }}</div>
            </div>

            <!-- Transaction Status Banner -->
            <div class="p-3 rounded-3 d-flex align-items-center justify-content-between mb-4" style="background: var(--fb-bg); border: 1px solid var(--fb-border);">
                <div>
                    <span class="text-muted d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Movement Type</span>
                    <span class="fw-bold text-dark text-uppercase" style="font-size: 16px;">{{ $transaction->type }}</span>
                </div>
                <div>
                    @if($transaction->type === 'received')
                        <span class="badge-received" style="font-size: 13px; padding: 6px 14px;">Stock-In Confirmed</span>
                    @elseif($transaction->type === 'issued')
                        <span class="badge-issued" style="font-size: 13px; padding: 6px 14px;">Issued to Site</span>
                    @elseif($transaction->type === 'transferred')
                        <span class="badge-transferred" style="font-size: 13px; padding: 6px 14px;">Transferred</span>
                    @elseif(in_array($transaction->type, ['damaged', 'lost']))
                        <span class="badge-damaged" style="font-size: 13px; padding: 6px 14px;">{{ ucfirst($transaction->type) }}</span>
                    @else
                        <span class="badge-used" style="font-size: 13px; padding: 6px 14px;">{{ ucfirst($transaction->type) }}</span>
                    @endif
                </div>
            </div>

            <!-- Material & Quantity Details -->
            <div class="sidebar-section-title ps-0 mb-2">Material Details</div>
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle small mb-0" style="border-color: var(--fb-border);">
                    <thead style="background: var(--fb-bg);">
                        <tr>
                            <th class="fw-600">Material Name</th>
                            <th class="fw-600">Category</th>
                            <th class="text-center fw-600">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <a href="{{ route('materials.show', $transaction->material_id) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $transaction->material->name ?? 'Material' }}
                                </a>
                            </td>
                            <td>{{ $transaction->material->category->name ?? 'Standard' }}</td>
                            <td class="text-center fw-bold" style="color: var(--fb-blue); font-size: 16px;">
                                {{ $transaction->quantity }} {{ $transaction->material->unit ?? 'pcs' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Logistics Flow -->
            <div class="sidebar-section-title ps-0 mb-2">Location Chain &amp; Verification</div>
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="p-3 rounded-3" style="background: var(--fb-bg); border: 1px solid var(--fb-border);">
                        <small class="text-muted d-block mb-1" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-circle-dot text-success me-1"></i> From (Origin)
                        </small>
                        <div class="fw-bold text-dark">{{ $fromLoc ? $fromLoc->name : 'External Supplier' }}</div>
                        <small class="text-muted d-block mb-2">{{ $fromLoc && $fromLoc->site ? $fromLoc->site->name : 'Supplier Delivery' }}</small>
                        @if($fromLoc)
                            <a href="{{ $fromLoc->google_maps_url }}" target="_blank" class="text-primary small text-decoration-none fw-semibold" style="font-size: 11px;">
                                <i class="fa-solid fa-map-location-dot me-1"></i> View in Google Maps
                            </a>
                        @endif
                    </div>
                </div>

                <div class="col-6">
                    <div class="p-3 rounded-3" style="background: var(--fb-bg); border: 1px solid var(--fb-border);">
                        <small class="text-muted d-block mb-1" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-location-dot text-danger me-1"></i> To (Destination)
                        </small>
                        <div class="fw-bold text-dark">{{ $toLoc ? $toLoc->name : 'Worksite Section' }}</div>
                        <small class="text-muted d-block mb-2">{{ $toLoc && $toLoc->site ? $toLoc->site->name : 'Bulalacao Site' }}</small>
                        @if($toLoc)
                            <a href="{{ $toLoc->google_maps_url }}" target="_blank" class="text-primary small text-decoration-none fw-semibold" style="font-size: 11px;">
                                <i class="fa-solid fa-map-location-dot me-1"></i> View in Google Maps
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ═══════════ TRANSIT ROUTE REAL MAP ═══════════ -->
            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="sidebar-section-title ps-0 mb-0">
                        <i class="fa-solid fa-route text-primary me-1"></i> Movement Route &amp; GPS Tracking Map
                    </div>
                    <a href="{{ $googleRouteUrl }}" target="_blank" class="text-primary small fw-semibold text-decoration-none" style="font-size: 11px;">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Live Navigation
                    </a>
                </div>
                <div id="txRouteMap"
                     data-from-lat="{{ $fromLat }}"
                     data-from-lng="{{ $fromLng }}"
                     data-from-name="{{ $fromLoc ? $fromLoc->name : 'Origin' }}"
                     data-to-lat="{{ $toLat }}"
                     data-to-lng="{{ $toLng }}"
                     data-to-name="{{ $toLoc ? $toLoc->name : 'Destination' }}"
                     style="height: 250px; width: 100%; border-radius: 10px; border: 1px solid var(--fb-border); z-index: 1;"></div>
            </div>

            <!-- Operator & Timestamp -->
            <div class="p-3 rounded-3 mb-4" style="background: var(--fb-bg); border: 1px solid var(--fb-border);">
                <div class="row g-2 small">
                    <div class="col-6">
                        <span class="text-muted d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-user me-1"></i> Operator
                        </span>
                        <strong class="text-dark">{{ $transaction->performedByUser->name ?? 'Staff' }}</strong>
                        <span class="text-muted"> ({{ $transaction->performedByUser->role->name ?? 'User' }})</span>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fa-regular fa-clock me-1"></i> Timestamp
                        </span>
                        <strong class="text-dark">{{ $transaction->created_at ? $transaction->created_at->format('F d, Y - h:i A') : 'Today' }}</strong>
                    </div>
                </div>
            </div>

            @if($transaction->remarks)
                <div class="mb-4">
                    <div class="sidebar-section-title ps-0 mb-2">Remarks &amp; Notes</div>
                    <div class="p-3 rounded-3 text-dark small" style="background: var(--fb-bg); border: 1px solid var(--fb-border); font-style: italic;">
                        "{{ $transaction->remarks }}"
                    </div>
                </div>
            @endif

            <!-- QR Verification Footer -->
            <div class="d-flex justify-content-between align-items-center pt-4 text-muted small" style="border-top: 1px solid var(--fb-border);">
                <div>
                    <span class="d-block" style="font-size: 11px;">Verified via QR Material Tagging</span>
                    <span class="font-monospace text-dark fw-bold" style="font-size: 10px;">ID: TX-{{ $transaction->id }}-SECURE</span>
                </div>
                <div class="text-end">
                    <span class="d-block fw-bold" style="color: #219150;"><i class="fa-solid fa-circle-check me-1"></i> Database Committed</span>
                </div>
            </div>

        </div>

    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const mapEl = document.getElementById('txRouteMap');
        if (!mapEl || !window.L) return;

        const fromLat = parseFloat(mapEl.dataset.fromLat) || 12.3340;
        const fromLng = parseFloat(mapEl.dataset.fromLng) || 121.3465;
        const toLat = parseFloat(mapEl.dataset.toLat) || 12.3295;
        const toLng = parseFloat(mapEl.dataset.toLng) || 121.3390;
        const fromName = mapEl.dataset.fromName || 'Origin';
        const toName = mapEl.dataset.toName || 'Destination';

        const map = L.map('txRouteMap').setView([fromLat, fromLng], 14);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://carto.com/">CARTO</a> | &copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        const fromIcon = L.divIcon({
            className: 'route-from-pin',
            html: '<div style="width: 32px; height: 32px; background: #219150; color: #fff; border-radius: 50%; border: 2.5px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 13px;"><i class="fa-solid fa-arrow-up-from-bracket"></i></div>',
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });

        const toIcon = L.divIcon({
            className: 'route-to-pin',
            html: '<div style="width: 32px; height: 32px; background: #1877f2; color: #fff; border-radius: 50%; border: 2.5px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 13px;"><i class="fa-solid fa-location-dot"></i></div>',
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });

        L.marker([fromLat, fromLng], { icon: fromIcon })
            .addTo(map)
            .bindPopup(`<strong>Origin:</strong> ${fromName}`);

        L.marker([toLat, toLng], { icon: toIcon })
            .addTo(map)
            .bindPopup(`<strong>Destination:</strong> ${toName}`);

        // Draw transit route polyline
        L.polyline([
            [fromLat, fromLng],
            [toLat, toLng]
        ], {
            color: '#1877f2',
            weight: 4,
            dashArray: '8, 8',
            opacity: 0.85
        }).addTo(map);

        map.fitBounds([
            [fromLat, fromLng],
            [toLat, toLng]
        ], { padding: [40, 40], maxZoom: 15 });
    });
</script>
@endpush
