@extends('layouts.app')

@section('title', 'Stock Monitoring')

@section('content')

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-header-title">Stock Monitoring</h1>
        <p class="page-header-sub">Current material balances and depot distribution in Bulalacao</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('inventory.low-stock') }}" class="btn btn-fb-warn">
            <i class="fa-solid fa-triangle-exclamation me-1"></i> Low Stock Alerts ({{ $stats['low_stock'] }})
        </a>
        <a href="{{ route('inventory.damaged-lost') }}" class="btn btn-fb-danger">
            <i class="fa-solid fa-file-circle-xmark me-1"></i> Damaged / Lost
        </a>
    </div>
</div>

<!-- 4 Stat Summary Cards -->
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="stat-block">
            <div class="stat-icon-wrap" style="background: #e7f3ff; color: var(--fb-blue);">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <div class="stat-value" style="color: var(--fb-blue);">{{ $stats['active_materials'] }}</div>
                <div class="stat-label">Material Types</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-block">
            <div class="stat-icon-wrap" style="background: #e6f9f1; color: #219150;">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div>
                <div class="stat-value" style="color: #219150;">{{ number_format($stats['total_items']) }}</div>
                <div class="stat-label">Total Units in Stock</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-block">
            <div class="stat-icon-wrap" style="background: #fff8e1; color: #c77700;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div class="stat-value" style="color: #c77700;">{{ $stats['low_stock'] }}</div>
                <div class="stat-label">Low Stock Alerts</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-block">
            <div class="stat-icon-wrap" style="background: #fdecea; color: #c62828;">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div>
                <div class="stat-value" style="color: #c62828;">{{ $stats['out_of_stock'] }}</div>
                <div class="stat-label">Out of Stock</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="fb-card mb-3">
    <form method="GET" action="{{ route('inventory.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-4">
            <label class="form-label">Location</label>
            <select name="location_id" class="form-select" onchange="this.form.submit()">
                <option value="">All Locations</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }} ({{ $loc->site->name ?? 'Site' }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-4">
            <label class="form-label">Stock Status</label>
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">All Status Levels</option>
                <option value="available"   {{ request('status') === 'available'   ? 'selected' : '' }}>Available (Healthy)</option>
                <option value="low_stock"   {{ request('status') === 'low_stock'   ? 'selected' : '' }}>Low Stock Alert</option>
                <option value="out_of_stock"{{ request('status') === 'out_of_stock'? 'selected' : '' }}>Out of Stock</option>
            </select>
        </div>
    </form>
</div>

<!-- Inventory Feed -->
<div class="d-flex flex-column gap-2 mb-3">
    @forelse($materials as $m)
    <div class="fb-card d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        
        <!-- Left: Material info -->
        <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #e7f3ff; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; color: var(--fb-blue);">
                <i class="fa-solid fa-box"></i>
            </div>
            <div class="min-w-0 flex-grow-1">
                <a href="{{ route('materials.show', $m->id) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="font-size: 15px;" title="{{ $m->name }}">
                    {{ $m->name }}
                </a>
                <div class="text-muted text-truncate" style="font-size: 12.5px;">
                    {{ $m->category->name ?? 'Standard Material' }}
                    <span class="mx-1">&bull;</span>
                    <i class="fa-solid fa-location-dot me-1 text-muted" style="font-size: 11px;"></i>
                    {{ $m->location->name ?? 'Unassigned Location' }}
                </div>
            </div>
        </div>

        <!-- Center: Stock (desktop/tablet) -->
        <div class="d-none d-md-block text-center px-3" style="min-width: 140px;">
            <div class="fw-bold text-dark" style="font-size: 22px; line-height: 1.1;">{{ $m->current_stock }} {{ $m->unit }}</div>
            <div class="text-muted" style="font-size: 12px;">Threshold: {{ $m->minimum_stock_level }} {{ $m->unit }}</div>
        </div>

        <!-- Right: Status & Action -->
        <div class="d-flex align-items-center justify-content-between justify-content-md-end gap-2 w-100 w-md-auto pt-2 pt-md-0 border-top border-top-md-0 flex-shrink-0">
            <div class="d-flex align-items-center gap-2">
                @if($m->status === 'available')
                    <span class="badge-available">In Stock</span>
                @elseif($m->status === 'low_stock')
                    <span class="badge-low">Low Stock</span>
                @else
                    <span class="badge-out">Out of Stock</span>
                @endif
                <span class="d-md-none text-muted small fw-semibold">
                    <strong class="text-dark">{{ $m->current_stock }} {{ $m->unit }}</strong>
                </span>
            </div>

            <a href="{{ route('transactions.create', ['material_id' => $m->id]) }}" class="btn btn-fb-action btn-sm">
                <i class="fa-solid fa-right-left me-1"></i> Movement
            </a>
        </div>

    </div>
    @empty
    <div class="fb-card text-center py-5 text-muted">
        <div style="font-size: 48px; margin-bottom: 8px; color: #dce1e7;"><i class="fa-solid fa-box-open"></i></div>
        <div class="fw-bold">No Inventory Records Found</div>
        <div class="small">Try changing your search filters above.</div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
    <span class="text-muted small">Showing {{ $materials->firstItem() ?? 0 }} to {{ $materials->lastItem() ?? 0 }} of {{ $materials->total() }} items</span>
    {{ $materials->links('pagination::bootstrap-5') }}
</div>

@endsection
