@extends('layouts.app')

@section('title', 'Locations & Construction Sites')

@section('content')

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-header-title">Depots &amp; Sites</h1>
        <p class="page-header-sub">Manage warehouses, staging bays, and construction site hierarchies in Bulalacao</p>
    </div>
    <div class="d-flex gap-2">
        <a href="https://www.google.com/maps/search/?api=1&query=Bulalacao,+Oriental+Mindoro" target="_blank" class="btn btn-fb-view btn-sm">
            <i class="fa-solid fa-map-location-dot me-1"></i> Bulalacao in Google Maps
        </a>
        <button type="button" class="btn btn-fb-add btn-sm" data-bs-toggle="modal" data-bs-target="#createLocationModal">
            <i class="fa-solid fa-plus me-1"></i> Add Storage Location
        </button>
    </div>
</div>

<!-- ═══════════ INTERACTIVE BULALACAO LOGISTICS MAP ═══════════ -->
<div class="fb-card p-4 mb-3">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
        <div>
            <div class="sidebar-section-title ps-0 mb-0" style="font-size: 13px;">
                <i class="fa-solid fa-map-location-dot text-danger me-1"></i>
                Bulalacao Logistics &amp; Depots Real Map
            </div>
            <p class="text-muted small mb-0">Live interactive geolocation of all storage yards, warehouses, and active corridors</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge text-bg-light border text-muted small"><i class="fa-solid fa-layer-group me-1"></i> {{ $sites->count() }} Sites &bull; {{ $sites->sum(fn($s) => $s->locations->count()) }} Depots</span>
        </div>
    </div>

    @php
        $mapMarkers = [];
        foreach ($sites as $site) {
            foreach ($site->locations as $loc) {
                $mapMarkers[] = [
                    'lat' => $loc->lat,
                    'lng' => $loc->lng,
                    'name' => $loc->name,
                    'site_name' => $site->name,
                    'type_label' => ucfirst(str_replace('_', ' ', $loc->type)),
                    'color' => match($loc->type) {
                        'warehouse' => '#1877f2',
                        'storage_area' => '#ff7043',
                        default => '#7b4ff7',
                    },
                    'icon_class' => match($loc->type) {
                        'warehouse' => 'fa-warehouse',
                        'storage_area' => 'fa-boxes-stacked',
                        default => 'fa-road',
                    },
                    'materials_count' => $loc->materials->count(),
                    'details_url' => route('locations.show', $loc->id),
                    'gmaps_url' => $loc->google_maps_url,
                ];
            }
        }
    @endphp

    <!-- Map container -->
    <div id="allLocationsMap" data-markers="{{ json_encode($mapMarkers) }}" style="height: 360px; width: 100%; border-radius: 10px; border: 1px solid var(--fb-border); z-index: 1;"></div>

    <div class="d-flex flex-wrap align-items-center gap-3 mt-2 pt-2" style="font-size: 12px; color: var(--fb-muted);">
        <div class="d-flex align-items-center gap-1">
            <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: #1877f2;"></span> Warehouse
        </div>
        <div class="d-flex align-items-center gap-1">
            <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: #ff7043;"></span> Staging Yard
        </div>
        <div class="d-flex align-items-center gap-1">
            <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: #7b4ff7;"></span> Site Section
        </div>
        <div class="ms-auto small">
            Click any pin to inspect inventory &amp; open in Google Maps
        </div>
    </div>
</div>

<!-- Sites Hierarchy Grid -->
<div class="row g-3 g-md-4">
    @foreach($sites as $site)
        <div class="col-12 col-lg-6">
            <div class="fb-card h-100 p-4">
                
                <!-- Site Header -->
                <div class="d-flex align-items-start justify-content-between mb-3 pb-3" style="border-bottom: 1px solid var(--fb-border);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-wrap" style="background: #e7f3ff; color: var(--fb-blue); width: 44px; height: 44px; font-size: 18px; flex-shrink: 0;">
                            <i class="fa-solid fa-building"></i>
                        </div>
                        <div>
                            <h2 class="fw-bold text-dark mb-0" style="font-size: 15px;">{{ $site->name }}</h2>
                            <small class="text-muted" style="font-size: 11px;">
                                <i class="fa-solid fa-location-dot me-1 text-danger"></i>{{ $site->address }}
                            </small>
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-1">
                        <span class="badge-issued" style="white-space: nowrap;">{{ $site->locations->count() }} Depots</span>
                        <a href="{{ $site->google_maps_url }}" target="_blank" class="text-primary small text-decoration-none fw-semibold" style="font-size: 11px;">
                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Map
                        </a>
                    </div>
                </div>

                @if($site->description)
                    <p class="text-muted small mb-3" style="font-size: 12px;">{{ $site->description }}</p>
                @endif

                <!-- Location / Depots List -->
                <div class="sidebar-section-title ps-0 mb-2">Storage Yards &amp; Work Zones</div>
                <div class="d-flex flex-column gap-2">
                    @forelse($site->locations as $loc)
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between p-3 rounded-3 gap-2" style="background: var(--fb-bg); border: 1px solid var(--fb-border);">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid {{ $loc->type === 'warehouse' ? 'fa-warehouse text-primary' : ($loc->type === 'storage_area' ? 'fa-boxes-stacked text-warning' : 'fa-road text-info') }}" style="font-size: 13px;"></i>
                                    <span class="fw-bold text-dark small">{{ $loc->name }}</span>
                                    <span class="badge text-bg-light border text-muted" style="font-size: 9px;">
                                        {{ ucfirst(str_replace('_', ' ', $loc->type)) }}
                                    </span>
                                </div>
                                <small class="text-muted d-block" style="font-size: 11px; margin-top: 2px; margin-left: 20px;">
                                    {{ $loc->materials->count() }} assigned materials &bull; {{ $loc->description ?? 'Active storage' }}
                                </small>
                            </div>
                            <div class="d-flex gap-1.5 flex-shrink-0 ms-sm-auto align-items-center">
                                <a href="{{ $loc->google_maps_url }}" target="_blank" class="btn btn-fb-edit btn-sm py-1 px-2" style="font-size: 11px;" title="View on Google Maps">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                </a>
                                <a href="{{ route('locations.show', $loc->id) }}" class="btn btn-fb-view btn-sm py-1 px-2.5" style="font-size: 11px; white-space: nowrap;">
                                    <i class="fa-solid fa-arrow-right me-1"></i> View
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted small" style="background: var(--fb-bg); border-radius: 8px;">
                            <i class="fa-solid fa-warehouse me-1"></i> No locations added to this site yet.
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    @endforeach
</div>

<!-- Add Location Modal -->
<div class="modal fade" id="createLocationModal" tabindex="-1" aria-labelledby="createLocationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 34px; height: 34px; background: #e7f3ff; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--fb-blue); font-size: 15px;">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <h5 class="modal-title" id="createLocationModalLabel">Add Storage Location</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('locations.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="site_id" class="form-label">Parent Construction Site <span class="text-danger">*</span></label>
                        <select class="form-select" id="site_id" name="site_id" required>
                            <option value="">Select Construction Site...</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}">{{ $site->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="locName" class="form-label">Location / Depot Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="locName" name="name" placeholder="e.g. Storage Bay 4, Warehouse A" required>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Location Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="warehouse">Warehouse (Covered Depot)</option>
                            <option value="storage_area">Storage Area (Open Staging Yard)</option>
                            <option value="site_section">Site Section (Active Construction Corridor)</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="latitude" class="form-label">Latitude <span class="text-muted fw-normal" style="font-size: 11px;">(optional)</span></label>
                            <input type="number" step="any" class="form-control font-monospace" id="latitude" name="latitude" placeholder="e.g. 12.3340">
                        </div>
                        <div class="col-6">
                            <label for="longitude" class="form-label">Longitude <span class="text-muted fw-normal" style="font-size: 11px;">(optional)</span></label>
                            <input type="number" step="any" class="form-control font-monospace" id="longitude" name="longitude" placeholder="e.g. 121.3465">
                        </div>
                        <div class="col-12">
                            <div class="form-text" style="font-size: 11px;">Defaults to Bulalacao site coordinates if left blank.</div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label for="locDesc" class="form-label">Description / Remarks</label>
                        <textarea class="form-control" id="locDesc" name="description" rows="2" placeholder="e.g. Concrete aggregate bunker or tool cage"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-fb-edit btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-fb-add btn-sm">
                        <i class="fa-solid fa-plus me-1"></i> Save Location
                    </button>
                </div>
            </form>
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
        const mapContainer = document.getElementById('allLocationsMap');
        if (!mapContainer || !window.L) return;

        const map = L.map('allLocationsMap').setView([12.3325, 121.3400], 13);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://carto.com/">CARTO</a> | &copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        const locationMarkers = JSON.parse(mapContainer.dataset.markers || '[]');
        const bounds = [];

        locationMarkers.forEach(loc => {
            const pinIcon = L.divIcon({
                className: 'depot-pin',
                html: `<div style="width: 34px; height: 34px; background: ${loc.color}; color: #fff; border-radius: 50%; border: 2.5px solid #fff; box-shadow: 0 3px 6px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 14px;"><i class="fa-solid ${loc.icon_class}"></i></div>`,
                iconSize: [34, 34],
                iconAnchor: [17, 17],
                popupAnchor: [0, -17]
            });

            const popup = `
                <div style="font-family: Inter, sans-serif; min-width: 190px; padding: 4px;">
                    <div style="font-weight: 700; font-size: 14px; color: #050505; margin-bottom: 2px;">${loc.name}</div>
                    <div style="font-size: 12px; color: #65676b; margin-bottom: 6px;">${loc.site_name}</div>
                    <div style="font-size: 11px; margin-bottom: 8px;">
                        <span class="badge text-bg-light border text-muted">${loc.type_label}</span>
                        <span class="badge text-bg-primary-subtle text-primary border">${loc.materials_count} Materials</span>
                    </div>
                    <div style="display: flex; gap: 4px;">
                        <a href="${loc.details_url}" style="flex: 1; text-align: center; background: #e7f3ff; color: #1877f2; text-decoration: none; padding: 4px 6px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                            View Details
                        </a>
                        <a href="${loc.gmaps_url}" target="_blank" style="flex: 1; text-align: center; background: #1877f2; color: #fff; text-decoration: none; padding: 4px 6px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                            Google Maps
                        </a>
                    </div>
                </div>
            `;

            L.marker([loc.lat, loc.lng], { icon: pinIcon })
                .addTo(map)
                .bindPopup(popup);

            bounds.push([loc.lat, loc.lng]);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
        }
    });
</script>
@endpush
