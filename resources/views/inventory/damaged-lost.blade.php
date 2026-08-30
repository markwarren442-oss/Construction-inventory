@extends('layouts.app')

@section('title', 'Damaged & Lost Materials')

@section('content')

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-header-title">Damaged &amp; Lost Materials</h1>
        <p class="page-header-sub">Log of damaged goods, weather losses, or missing site inventory</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('transactions.create', ['type' => 'damaged']) }}" class="btn btn-fb-danger">
            <i class="fa-solid fa-triangle-exclamation me-1"></i> Report Damaged
        </a>
        <a href="{{ route('transactions.create', ['type' => 'lost']) }}" class="btn btn-fb-warn">
            <i class="fa-solid fa-circle-question me-1"></i> Report Missing
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-3">
    <div class="col-12 col-md-6">
        <div class="stat-block">
            <div class="stat-icon-wrap" style="background: #fdecea; color: #c62828;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div class="stat-value" style="color: #c62828;">{{ $totalDamaged }} <span style="font-size: 14px; font-weight: normal; color: #65676b;">units</span></div>
                <div class="stat-label">Total Damaged Materials</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="stat-block">
            <div class="stat-icon-wrap" style="background: #fff8e1; color: #c77700;">
                <i class="fa-solid fa-circle-question"></i>
            </div>
            <div>
                <div class="stat-value" style="color: #c77700;">{{ $totalLost }} <span style="font-size: 14px; font-weight: normal; color: #65676b;">units</span></div>
                <div class="stat-label">Total Missing / Lost Materials</div>
            </div>
        </div>
    </div>
</div>

<!-- Incident Feed -->
<div class="d-flex flex-column gap-2 mb-3">
    @forelse($incidents as $inc)
    <div class="fb-card d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" style="border-left: 4px solid {{ $inc->type === 'damaged' ? '#c62828' : '#c77700' }};">
        
        <!-- Left: Incident info -->
        <div class="d-flex align-items-start align-items-sm-center gap-3 flex-grow-1 min-w-0">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: {{ $inc->type === 'damaged' ? '#fdecea' : '#fff8e1' }}; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; color: {{ $inc->type === 'damaged' ? '#c62828' : '#c77700' }};">
                <i class="fa-solid {{ $inc->type === 'damaged' ? 'fa-bomb' : 'fa-circle-question' }}"></i>
            </div>
            <div class="min-w-0 flex-grow-1">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <a href="{{ route('materials.show', $inc->material_id) }}" class="fw-bold text-dark text-decoration-none text-truncate" style="font-size: 15px;" title="{{ $inc->material->name ?? 'Material' }}">
                        {{ $inc->material->name ?? 'Material' }}
                    </a>
                    <span class="badge text-bg-light border text-muted font-monospace" style="font-size: 11px;">{{ $inc->reference_number }}</span>
                </div>
                <div class="text-muted" style="font-size: 13px;">
                    <strong>{{ $inc->quantity }} {{ $inc->material->unit ?? 'pcs' }}</strong>
                    <span class="mx-1">&bull;</span>
                    {{ $inc->fromLocation->name ?? 'Site' }} ({{ $inc->fromLocation->site->name ?? 'Bulalacao' }})
                </div>
                @if($inc->remarks)
                <div class="text-muted small mt-1" style="font-style: italic;">
                    "{{ $inc->remarks }}"
                </div>
                @endif
                <div class="text-muted small mt-1" style="font-size: 11.5px;">
                    <i class="fa-regular fa-user me-1"></i> {{ $inc->performedByUser->name ?? 'Staff' }}
                    <span class="mx-1">&bull;</span>
                    <i class="fa-regular fa-clock me-1"></i> {{ $inc->created_at ? $inc->created_at->diffForHumans() : 'Recently' }}
                </div>
            </div>
        </div>

        <!-- Right: Status -->
        <div class="d-flex justify-content-end w-100 w-md-auto pt-2 pt-md-0 border-top border-top-md-0 flex-shrink-0">
            @if($inc->type === 'damaged')
                <span class="badge-damaged">Damaged</span>
            @else
                <span class="badge-low">Missing / Lost</span>
            @endif
        </div>

    </div>
    @empty
    <div class="fb-card text-center py-5">
        <div style="font-size: 48px; margin-bottom: 8px; color: #219150;"><i class="fa-solid fa-shield-halved"></i></div>
        <div class="fw-bold text-success" style="font-size: 18px;">No Damaged or Lost Materials Reported</div>
        <p class="text-muted small mb-0">All logistics material counts are intact and verified.</p>
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
    <span class="text-muted small">Showing {{ $incidents->firstItem() ?? 0 }} to {{ $incidents->lastItem() ?? 0 }} of {{ $incidents->total() }} records</span>
    {{ $incidents->links('pagination::bootstrap-5') }}
</div>

@endsection
