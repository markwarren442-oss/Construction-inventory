@extends('layouts.app')

@section('title', $location->name)

@section('content')

@php
    $locLat = $location->lat;
    $locLng = $location->lng;
    $siteName = $location->site->name ?? 'Bulalacao Site';
@endphp

<!-- Header -->
<div class="page-header">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="page-header-title">{{ $location->name }}</h1>
            <span class="badge-issued">
                {{ ucfirst(str_replace('_', ' ', $location->type)) }}
            </span>
        </div>
        <p class="page-header-sub">
            <i class="fa-solid fa-location-dot text-danger me-1"></i>
            {{ $siteName }} &bull; {{ $location->site->address ?? 'Bulalacao, Oriental Mindoro' }}
        </p>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <a href="{{ $location->google_maps_directions_url }}" target="_blank" class="btn btn-fb-edit btn-sm">
            <i class="fa-solid fa-diamond-turn-right me-1 text-primary"></i> Get Directions
        </a>
        <a href="{{ $location->google_maps_url }}" target="_blank" class="btn btn-fb-view btn-sm">
            <i class="fa-solid fa-map-location-dot me-1"></i> Google Maps
        </a>
        <a href="{{ route('transactions.create', ['location_id' => $location->id]) }}" class="btn btn-fb-action btn-sm">
            <i class="fa-solid fa-right-left me-1"></i> Transfer Material Here
        </a>
        <a href="{{ route('locations.index') }}" class="btn btn-fb-edit btn-sm" title="Back to Locations">
            <i class="fa-solid fa-arrow-left me-1"></i> Depots List
        </a>
    </div>
</div>

<!-- ═══════════ REAL MAP & DEPOT DETAILS ROW ═══════════ -->
<div class="row g-3 mb-3">
    <!-- Map (Col 7) -->
    <div class="col-12 col-lg-7">
        <div class="fb-card h-100 p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="sidebar-section-title ps-0 mb-0" style="font-size: 13px;">
                    <i class="fa-solid fa-map-location-dot text-danger me-1"></i>
                    Depot Real Map Geolocation
                </div>
                <span class="badge text-bg-light border font-monospace text-muted" style="font-size: 11px;">
                    {{ $locLat }}, {{ $locLng }}
                </span>
            </div>

            <!-- Leaflet Interactive Map -->
            <div id="singleLocationMap"
                 data-lat="{{ $locLat }}"
                 data-lng="{{ $locLng }}"
                 data-name="{{ $location->name }}"
                 data-site="{{ $siteName }}"
                 data-gmaps="{{ $location->google_maps_url }}"
                 style="height: 280px; width: 100%; border-radius: 10px; border: 1px solid var(--fb-border); z-index: 1;"></div>

            <div class="d-flex justify-content-between align-items-center mt-2 pt-2" style="font-size: 12px; color: var(--fb-muted);">
                <span>{{ $location->site->address ?? 'Bulalacao, Oriental Mindoro' }}</span>
                <a href="{{ $location->google_maps_url }}" target="_blank" class="text-primary fw-semibold text-decoration-none">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Fullscreen in Google Maps
                </a>
            </div>
        </div>
    </div>

    <!-- Depot Summary Specs (Col 5) -->
    <div class="col-12 col-lg-5">
        <div class="fb-card h-100 p-4 d-flex flex-column justify-content-between">
            <div>
                <div class="sidebar-section-title ps-0 mb-3" style="font-size: 13px;">
                    <i class="fa-solid fa-circle-info text-primary me-1"></i> Depot Overview
                </div>

                <div class="mb-3">
                    <span class="text-muted small d-block">Parent Construction Site</span>
                    <strong class="text-dark" style="font-size: 15px;">{{ $siteName }}</strong>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 border" style="background: var(--fb-bg);">
                            <span class="text-muted d-block" style="font-size: 11px;">STORED ITEMS</span>
                            <span class="fw-bold text-dark" style="font-size: 20px;">{{ $location->materials->count() }}</span>
                            <span class="text-muted" style="font-size: 11px;">Materials</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 border" style="background: var(--fb-bg);">
                            <span class="text-muted d-block" style="font-size: 11px;">TOTAL UNITS</span>
                            <span class="fw-bold text-primary" style="font-size: 20px;">{{ number_format($location->materials->sum('current_stock')) }}</span>
                            <span class="text-muted" style="font-size: 11px;">Units in stock</span>
                        </div>
                    </div>
                </div>

                @if($location->description)
                    <div class="mb-3">
                        <span class="text-muted small d-block mb-1">Operational Description</span>
                        <div class="p-2.5 rounded-3 text-dark small" style="background: var(--fb-bg); border: 1px solid var(--fb-border);">
                            {{ $location->description }}
                        </div>
                    </div>
                @endif
            </div>

            <div class="pt-3 border-top d-flex gap-2">
                <a href="{{ $location->google_maps_directions_url }}" target="_blank" class="btn btn-fb-view btn-sm w-100 text-center">
                    <i class="fa-solid fa-diamond-turn-right me-1"></i> Navigate via Google Maps
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Location Inventory Table Card -->
<div class="fb-card p-4 mb-3">
    <div class="sidebar-section-title ps-0 mb-3" style="font-size: 13px;">
        <i class="fa-solid fa-boxes-stacked text-primary me-1"></i> Materials Stored in {{ $location->name }}
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small" style="border-color: var(--fb-border);">
            <thead style="background: var(--fb-bg);">
                <tr>
                    <th>Material</th>
                    <th>Category</th>
                    <th>QR Tag Code</th>
                    <th>Current Quantity</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($location->materials as $mat)
                    <tr>
                        <td>
                            <a href="{{ route('materials.show', $mat->id) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $mat->name }}
                            </a>
                        </td>
                        <td>{{ $mat->category->name ?? 'Standard' }}</td>
                        <td>
                            <span class="font-monospace fw-semibold" style="font-size: 11px; color: var(--fb-blue);">
                                {{ $mat->qrCodes->first()->code ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="fw-bold text-dark">
                            {{ $mat->current_stock }} {{ $mat->unit }}
                        </td>
                        <td>
                            @if($mat->status === 'available')
                                <span class="badge-available">In Stock</span>
                            @elseif($mat->status === 'low_stock')
                                <span class="badge-low">Low Stock</span>
                            @else
                                <span class="badge-out">Out of Stock</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('transactions.create', ['material_id' => $mat->id]) }}" class="btn btn-fb-view btn-sm" style="font-size: 11px; padding: 4px 10px;">
                                <i class="fa-solid fa-right-left me-1"></i> Move
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No materials currently recorded in this storage location.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Location Movement Activity Log -->
<div class="fb-card p-4">
    <div class="sidebar-section-title ps-0 mb-3" style="font-size: 13px;">
        <i class="fa-solid fa-clock-rotate-left text-primary me-1"></i> Location Inflow &amp; Outflow History
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small" style="border-color: var(--fb-border);">
            <thead style="background: var(--fb-bg);">
                <tr>
                    <th>Ref #</th>
                    <th>Material</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Recorded By</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr>
                        <td class="font-monospace fw-semibold" style="color: var(--fb-blue);">{{ $tx->reference_number }}</td>
                        <td>{{ $tx->material->name ?? 'Material' }}</td>
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
                        <td class="fw-bold">{{ $tx->quantity }} {{ $tx->material->unit ?? 'pcs' }}</td>
                        <td>{{ $tx->performedByUser->name ?? 'Staff' }}</td>
                        <td class="text-muted">{{ $tx->created_at ? $tx->created_at->format('M d, Y H:i') : 'Recently' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No movement records logged for this location yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
        const mapEl = document.getElementById('singleLocationMap');
        if (!mapEl || !window.L) return;

        const lat = parseFloat(mapEl.dataset.lat);
        const lng = parseFloat(mapEl.dataset.lng);
        const locName = mapEl.dataset.name || '';
        const siteName = mapEl.dataset.site || '';
        const gmapsUrl = mapEl.dataset.gmaps || '#';

        const map = L.map('singleLocationMap').setView([lat, lng], 15);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://carto.com/">CARTO</a> | &copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        const pinIcon = L.divIcon({
            className: 'depot-pin-single',
            html: '<div style="width: 38px; height: 38px; background: #1877f2; color: #fff; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 3px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 16px;"><i class="fa-solid fa-warehouse"></i></div>',
            iconSize: [38, 38],
            iconAnchor: [19, 19],
            popupAnchor: [0, -19]
        });

        const popup = `
            <div style="font-family: Inter, sans-serif; min-width: 180px; padding: 4px;">
                <div style="font-weight: 700; font-size: 14px; color: #050505; margin-bottom: 2px;">${locName}</div>
                <div style="font-size: 12px; color: #65676b; margin-bottom: 8px;">${siteName}</div>
                <a href="${gmapsUrl}" target="_blank" style="display: inline-block; background: #1877f2; color: #fff; text-decoration: none; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Google Maps
                </a>
            </div>
        `;

        L.marker([lat, lng], { icon: pinIcon })
            .addTo(map)
            .bindPopup(popup)
            .openPopup();
    });
</script>
@endpush
