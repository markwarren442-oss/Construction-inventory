@extends('layouts.app')
@section('title', 'All Materials')

@section('content')

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-header-title">Construction Materials</h1>
        <p class="page-header-sub">All materials registered in Bulalacao logistics system</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        @if(auth()->user()->hasRole('inventory-officer') || auth()->user()->isAdmin())
        <a href="{{ route('materials.create') }}" class="btn btn-fb-add">
            <i class="fa-solid fa-plus me-2"></i> Add New Material
        </a>
        @endif
        <a href="{{ route('qr.scanner') }}" class="btn btn-fb-scan">
            <i class="fa-solid fa-camera me-2"></i> Scan QR
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="fb-card mb-3">
    <form method="GET" action="{{ route('materials.index') }}" class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
            <label class="form-label">Search Material</label>
            <div class="input-icon-group">
                <i class="input-icon fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" class="form-control" placeholder="Type material name here..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label">Location</label>
            <select name="location_id" class="form-select" onchange="this.form.submit()">
                <option value="">All Locations</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                        {{ $loc->name }} ({{ $loc->site->name ?? 'Site' }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-fb-view w-100">Search</button>
            @if(request()->anyFilled(['search', 'category_id', 'location_id', 'status']))
            <a href="{{ route('materials.index') }}" class="btn btn-fb-edit" title="Clear Filters">
                <i class="fa-solid fa-xmark"></i>
            </a>
            @endif
        </div>
    </form>
</div>

<!-- Materials List as Cards -->
<div class="d-flex flex-column gap-2 mb-3">
    @forelse($materials as $material)
    <div class="fb-card d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">

        <!-- Left: Icon + Info -->
        <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
            <div style="width: 48px; height: 48px; background: #e7f3ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; color: var(--fb-blue);">
                <i class="fa-solid fa-box"></i>
            </div>
            <div class="min-w-0 flex-grow-1">
                <a href="{{ route('materials.show', $material->id) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="font-size: 15.5px;" title="{{ $material->name }}">
                    {{ $material->name }}
                </a>
                <div class="text-muted text-truncate" style="font-size: 12.5px;">
                    {{ $material->category->name ?? 'Standard Material' }}
                    <span class="mx-1">&bull;</span>
                    <i class="fa-solid fa-location-dot me-1 text-muted" style="font-size: 11px;"></i>
                    {{ $material->location->name ?? 'Unassigned Location' }}
                </div>
                <div class="mt-1 d-flex flex-wrap align-items-center gap-2">
                    @if($material->status === 'available')
                        <span class="badge-available">In Stock</span>
                    @elseif($material->status === 'low_stock')
                        <span class="badge-low">Low Stock</span>
                    @else
                        <span class="badge-out">Out of Stock</span>
                    @endif
                    <span class="d-md-none text-muted small fw-semibold">
                        &bull; Stock: <strong class="text-dark">{{ $material->current_stock }} {{ $material->unit }}</strong>
                    </span>
                </div>
            </div>
        </div>

        <!-- Center: Stock Numbers (desktop / tablet) -->
        <div class="d-none d-md-block text-center px-3" style="min-width: 130px;">
            <div class="fw-bold text-dark" style="font-size: 24px; line-height: 1.1;">{{ $material->current_stock }}</div>
            <div class="text-muted" style="font-size: 12px;">{{ $material->unit }} in stock</div>
            <div style="font-size: 11px; color: #c77700; margin-top: 2px;">Min: {{ $material->minimum_stock_level }} {{ $material->unit }}</div>
        </div>

        <!-- Right: Action Buttons -->
        <div class="d-flex flex-wrap align-items-center gap-2 w-100 w-md-auto justify-content-start justify-content-md-end pt-2 pt-md-0 border-top border-top-md-0">
            <a href="{{ route('materials.show', $material->id) }}" class="btn btn-fb-view btn-sm flex-fill flex-md-grow-0">
                <i class="fa-solid fa-eye me-1"></i> View
            </a>
            <a href="{{ route('transactions.create', ['material_id' => $material->id]) }}" class="btn btn-fb-action btn-sm flex-fill flex-md-grow-0">
                <i class="fa-solid fa-right-left me-1"></i> Movement
            </a>
            @if(auth()->user()->hasRole('inventory-officer') || auth()->user()->isAdmin())
            <a href="{{ route('materials.edit', $material->id) }}" class="btn btn-fb-edit btn-sm flex-fill flex-md-grow-0">
                <i class="fa-solid fa-pen me-1"></i> Edit
            </a>
            @endif
        </div>
    </div>
    @empty
    <div class="fb-card text-center py-5 text-muted">
        <div style="font-size: 56px; margin-bottom: 12px; color: #dce1e7;"><i class="fa-solid fa-box-open"></i></div>
        <div class="fw-bold" style="font-size: 18px;">No Materials Found</div>
        <div class="small mb-3">Try clearing your search filters or add a new material.</div>
        @if(auth()->user()->hasRole('inventory-officer') || auth()->user()->isAdmin())
        <a href="{{ route('materials.create') }}" class="btn btn-fb-add">
            <i class="fa-solid fa-plus me-1"></i> Add First Material
        </a>
        @endif
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
    <span class="text-muted small">Showing {{ $materials->firstItem() ?? 0 }} – {{ $materials->lastItem() ?? 0 }} of {{ $materials->total() }} items</span>
    {{ $materials->links('pagination::bootstrap-5') }}
</div>

@endsection
