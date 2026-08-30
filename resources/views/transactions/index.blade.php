@extends('layouts.app')

@section('title', 'Material Movements')

@section('content')

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-header-title">Material Movements</h1>
        <p class="page-header-sub">Traceable log of all stock-in, issuance, transfers, and consumption</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('transactions.create') }}" class="btn btn-fb-action">
            <i class="fa-solid fa-plus me-1"></i> Record Movement
        </a>
        <a href="{{ route('qr.scanner') }}" class="btn btn-fb-scan">
            <i class="fa-solid fa-camera me-1"></i> Scan QR
        </a>
    </div>
</div>

<!-- Search & Filters -->
<div class="fb-card mb-3">
    <form method="GET" action="{{ route('transactions.index') }}" class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
            <label class="form-label">Search Movements</label>
            <div class="input-icon-group">
                <i class="input-icon fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" class="form-control" placeholder="Search reference #, material..." value="{{ request('search') }}">
            </div>
        </div>

        <div class="col-6 col-md-3">
            <label class="form-label">Movement Type</label>
            <select name="type" class="form-select" onchange="this.form.submit()">
                <option value="">All Types</option>
                <option value="received"    {{ request('type') === 'received'    ? 'selected' : '' }}>Received (Stock-In)</option>
                <option value="issued"      {{ request('type') === 'issued'      ? 'selected' : '' }}>Issued (To Site)</option>
                <option value="transferred" {{ request('type') === 'transferred' ? 'selected' : '' }}>Transferred</option>
                <option value="used"        {{ request('type') === 'used'        ? 'selected' : '' }}>Used / Consumed</option>
                <option value="returned"    {{ request('type') === 'returned'    ? 'selected' : '' }}>Returned</option>
                <option value="damaged"     {{ request('type') === 'damaged'     ? 'selected' : '' }}>Damaged</option>
                <option value="lost"        {{ request('type') === 'lost'        ? 'selected' : '' }}>Lost</option>
            </select>
        </div>

        <div class="col-6 col-md-3">
            <label class="form-label">Location</label>
            <select name="location_id" class="form-select" onchange="this.form.submit()">
                <option value="">All Locations</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                        {{ $loc->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-fb-view w-100">Filter</button>
            @if(request()->anyFilled(['search', 'type', 'location_id']))
                <a href="{{ route('transactions.index') }}" class="btn btn-fb-edit" title="Clear">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Transactions Feed -->
<div class="d-flex flex-column gap-2 mb-3">
    @forelse($transactions as $tx)
    @php
        $txClass = match($tx->type) {
            'received'    => 'received',
            'issued'      => 'issued',
            'transferred' => 'transferred',
            'damaged','lost' => 'damaged',
            default       => 'default',
        };
        $txIcon = match($tx->type) {
            'received'    => 'fa-arrow-down',
            'issued'      => 'fa-arrow-up',
            'transferred' => 'fa-truck',
            'damaged'     => 'fa-bomb',
            'lost'        => 'fa-circle-question',
            'used'        => 'fa-hammer',
            default       => 'fa-rotate-left',
        };
    @endphp
    <div class="fb-card d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        
        <!-- Left: Icon + Material + Details -->
        <div class="d-flex align-items-start align-items-sm-center gap-3 flex-grow-1 min-w-0">
            <div class="tx-avatar tx-avatar-{{ $txClass }}" style="width: 44px; height: 44px; font-size: 18px;">
                <i class="fa-solid {{ $txIcon }}"></i>
            </div>
            <div class="min-w-0 flex-grow-1">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <span class="fw-bold text-dark text-truncate" style="font-size: 15px;">{{ $tx->material->name ?? 'Material' }}</span>
                    <span class="badge text-bg-light border text-muted font-monospace" style="font-size: 11px;">{{ $tx->reference_number }}</span>
                </div>
                <div class="text-muted" style="font-size: 13px;">
                    <strong>{{ $tx->quantity }} {{ $tx->material->unit ?? 'pcs' }}</strong>
                    <span class="mx-1">&bull;</span>
                    {{ $tx->fromLocation->name ?? 'External Vendor' }} &rarr; {{ $tx->toLocation->name ?? 'Site Staging' }}
                </div>
                <div class="text-muted small mt-1" style="font-size: 11.5px;">
                    <i class="fa-regular fa-user me-1"></i> {{ $tx->performedByUser->name ?? 'Staff' }}
                    <span class="mx-1">&bull;</span>
                    <i class="fa-regular fa-clock me-1"></i> {{ $tx->created_at ? $tx->created_at->diffForHumans() : 'Just now' }}
                </div>
            </div>
        </div>

        <!-- Right: Status Badge & View Button -->
        <div class="d-flex align-items-center justify-content-between justify-content-md-end gap-2 w-100 w-md-auto pt-2 pt-md-0 border-top border-top-md-0 flex-shrink-0">
            <div>
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
            </div>

            <a href="{{ route('transactions.show', $tx->id) }}" class="btn btn-fb-view btn-sm">
                <i class="fa-solid fa-receipt me-1"></i> Details
            </a>
        </div>

    </div>
    @empty
    <div class="fb-card text-center py-5 text-muted">
        <div style="font-size: 48px; margin-bottom: 8px; color: #dce1e7;"><i class="fa-regular fa-clipboard"></i></div>
        <div class="fw-bold" style="font-size: 16px;">No Material Movements Found</div>
        <div class="small mb-3">No records match your filter criteria.</div>
        <a href="{{ route('transactions.create') }}" class="btn btn-fb-action btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Record Movement
        </a>
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
    <span class="text-muted small">Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} records</span>
    {{ $transactions->links('pagination::bootstrap-5') }}
</div>

@endsection
