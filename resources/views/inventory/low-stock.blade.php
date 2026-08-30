@extends('layouts.app')

@section('title', 'Low Stock Alerts')

@section('content')

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-header-title">Low Stock Alerts</h1>
        <p class="page-header-sub">Materials that have dropped below minimum replenishment levels</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <button type="button" onclick="window.print()" class="btn btn-fb-edit">
            <i class="fa-solid fa-print me-1"></i> Print Alerts
        </button>
        <a href="{{ route('inventory.index') }}" class="btn btn-fb-view">
            <i class="fa-solid fa-warehouse me-1"></i> Full Inventory
        </a>
    </div>
</div>

<!-- Low Stock Feed / Cards -->
<div class="d-flex flex-column gap-2 mb-3">
    @forelse($lowStockMaterials as $mat)
        @php
            $deficit = max(0, $mat->minimum_stock_level - $mat->current_stock);
        @endphp
        <div class="fb-card d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" style="border-left: 4px solid #ffc107;">
            
            <!-- Left: Info -->
            <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: #fff8e1; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; color: #c77700;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="min-w-0 flex-grow-1">
                    <a href="{{ route('materials.show', $mat->id) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="font-size: 15px;" title="{{ $mat->name }}">
                        {{ $mat->name }}
                    </a>
                    <div class="text-muted text-truncate" style="font-size: 12.5px;">
                        {{ $mat->category->name ?? 'Standard Material' }}
                        <span class="mx-1">&bull;</span>
                        <i class="fa-solid fa-location-dot me-1 text-muted" style="font-size: 11px;"></i>
                        {{ $mat->location->name ?? 'Unassigned' }} ({{ $mat->location->site->name ?? 'Bulalacao' }})
                    </div>
                    <div class="text-muted small mt-1 text-truncate" style="font-size: 11.5px;">
                        Supplier: <strong>{{ $mat->supplier->name ?? 'Local Mindoro Supplier' }}</strong>
                        @if($mat->supplier->phone ?? false)
                            <span class="mx-1">&bull;</span>
                            <i class="fa-solid fa-phone me-1" style="font-size: 10px;"></i> {{ $mat->supplier->phone }}
                        @endif
                    </div>
                </div>
            </div>

            <!-- Center: Numbers (desktop/tablet) -->
            <div class="d-none d-md-block text-center px-3" style="min-width: 140px;">
                <div class="fw-bold text-danger" style="font-size: 20px; line-height: 1.1;">{{ $mat->current_stock }} {{ $mat->unit }}</div>
                <div class="text-muted small" style="font-size: 11.5px;">Min: {{ $mat->minimum_stock_level }} {{ $mat->unit }}</div>
                <span class="badge text-bg-danger-subtle text-danger border border-danger-subtle fw-bold mt-1" style="font-size: 10.5px;">
                    -{{ $deficit }} {{ $mat->unit }} Deficit
                </span>
            </div>

            <!-- Right: Action Button -->
            <div class="d-flex align-items-center justify-content-between justify-content-md-end gap-2 w-100 w-md-auto pt-2 pt-md-0 border-top border-top-md-0 flex-shrink-0">
                <div class="d-md-none">
                    <span class="badge text-bg-danger-subtle text-danger border border-danger-subtle fw-bold" style="font-size: 11px;">
                        Stock: {{ $mat->current_stock }} / Min: {{ $mat->minimum_stock_level }} {{ $mat->unit }}
                    </span>
                </div>
                <a href="{{ route('transactions.create', ['material_id' => $mat->id, 'type' => 'received']) }}" class="btn btn-fb-add btn-sm">
                    <i class="fa-solid fa-arrow-down me-1"></i> Stock-In
                </a>
            </div>

        </div>
    @empty
        <div class="fb-card text-center py-5 text-success">
            <div style="font-size: 48px; margin-bottom: 8px; color: #219150;"><i class="fa-solid fa-circle-check"></i></div>
            <div class="fw-bold" style="font-size: 18px;">All Stock Levels are Healthy!</div>
            <p class="text-muted small mb-0">No materials are currently below minimum reorder thresholds.</p>
        </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
    <span class="text-muted small">Showing {{ $lowStockMaterials->firstItem() ?? 0 }} to {{ $lowStockMaterials->lastItem() ?? 0 }} of {{ $lowStockMaterials->total() }} alerts</span>
    {{ $lowStockMaterials->links('pagination::bootstrap-5') }}
</div>

@endsection
