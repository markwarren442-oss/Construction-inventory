@extends('layouts.app')
@section('title', 'Home')

@section('content')

<!-- ═══════════ WELCOME BANNER ═══════════ -->
<div class="fb-card mb-3">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div style="width: 56px; height: 56px; background: var(--fb-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #fff; flex-shrink: 0;">
                <i class="fa-solid fa-helmet-safety"></i>
            </div>
            <div>
                <div class="fw-bold text-dark" style="font-size: 20px;">Hello, {{ $user->name }}!</div>
                <div class="text-muted" style="font-size: 14px;">
                    <span style="background: #e7f3ff; color: var(--fb-blue); padding: 2px 10px; border-radius: 20px; font-weight: 600; font-size: 13px;">{{ $user->role->name ?? 'Staff' }}</span>
                    &nbsp;Bulalacao Infrastructure &amp; Logistics
                </div>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('qr.scanner') }}" class="btn btn-fb-scan">
                <i class="fa-solid fa-camera me-2"></i> Scan QR Code
            </a>
            @if(auth()->user()->hasRole('inventory-officer') || auth()->user()->isAdmin())
            <a href="{{ route('materials.create') }}" class="btn btn-fb-add">
                <i class="fa-solid fa-plus me-2"></i> Add Material
            </a>
            @endif
            <a href="{{ route('transactions.create') }}" class="btn btn-fb-action">
                <i class="fa-solid fa-right-left me-2"></i> Record Movement
            </a>
        </div>
    </div>
</div>

<!-- ═══════════ QUICK ACTION BUTTONS ═══════════ -->
<div class="fb-card mb-3">
    <div class="fw-bold text-dark mb-3" style="font-size: 17px;"><i class="fa-solid fa-bolt me-2 text-warning"></i> Quick Actions</div>
    <div class="row g-2">
        <div class="col-6 col-md-3">
            <a href="{{ route('qr.scanner') }}" class="btn btn-fb-scan w-100 py-3 d-flex flex-column align-items-center gap-1" style="font-size: 14px;">
                <i class="fa-solid fa-camera fa-2x"></i>
                <span class="fw-bold">Scan QR</span>
                <small style="font-size: 11px; opacity: .85;">Open Camera</small>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('transactions.create', ['type' => 'received']) }}" class="btn btn-fb-add w-100 py-3 d-flex flex-column align-items-center gap-1" style="font-size: 14px;">
                <i class="fa-solid fa-arrow-down fa-2x"></i>
                <span class="fw-bold">Stock-In</span>
                <small style="font-size: 11px; opacity: .85;">Receive Materials</small>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('transactions.create', ['type' => 'issued']) }}" class="btn btn-fb-action w-100 py-3 d-flex flex-column align-items-center gap-1" style="font-size: 14px;">
                <i class="fa-solid fa-arrow-up fa-2x"></i>
                <span class="fw-bold">Issue</span>
                <small style="font-size: 11px; opacity: .85;">Give to Workers</small>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('inventory.low-stock') }}" class="btn btn-fb-warn w-100 py-3 d-flex flex-column align-items-center gap-1" style="font-size: 14px;">
                <i class="fa-solid fa-triangle-exclamation fa-2x"></i>
                <span class="fw-bold">Low Stock</span>
                <small style="font-size: 11px; opacity: .85;">{{ $stats['low_stock_count'] }} Alerts</small>
            </a>
        </div>
    </div>
</div>

<!-- ═══════════ STAT SUMMARY CARDS ═══════════ -->
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="stat-block">
            <div class="stat-icon-wrap" style="background: #e7f3ff; color: var(--fb-blue);">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <div class="stat-value" style="color: var(--fb-blue);">{{ $stats['total_materials'] }}</div>
                <div class="stat-label">Total Materials</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-block">
            <div class="stat-icon-wrap" style="background: #e6f9f1; color: #219150;">
                <i class="fa-solid fa-qrcode"></i>
            </div>
            <div>
                <div class="stat-value" style="color: #219150;">{{ $stats['total_qrcodes'] }}</div>
                <div class="stat-label">Active QR Codes</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-block">
            <div class="stat-icon-wrap" style="background: #fff8e1; color: #c77700;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div class="stat-value" style="color: #c77700;">{{ $stats['low_stock_count'] }}</div>
                <div class="stat-label">Low Stock Alerts</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-block">
            <div class="stat-icon-wrap" style="background: #fdecea; color: #c62828;">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <div>
                <div class="stat-value" style="color: #c62828;">{{ $stats['damaged_count'] }}</div>
                <div class="stat-label">Damaged / Lost</div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════ RECENT MOVEMENTS (Transaction Feed) ═══════════ -->
<div class="fb-card">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="fw-bold text-dark" style="font-size: 17px;"><i class="fa-solid fa-clock-rotate-left me-2 text-muted"></i> Recent Material Movements</div>
        <a href="{{ route('transactions.index') }}" class="btn btn-fb-view btn-sm">
            <i class="fa-solid fa-list me-1"></i> See All
        </a>
    </div>

    <div class="d-flex flex-column gap-2">
        @forelse($recentTransactions as $tx)
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
        <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: var(--fb-bg);">
            <div class="tx-avatar tx-avatar-{{ $txClass }}">
                <i class="fa-solid {{ $txIcon }}"></i>
            </div>
            <div class="flex-grow-1" style="min-width: 0;">
                <div class="fw-bold text-dark" style="font-size: 14px;">{{ $tx->material->name ?? 'Material' }}</div>
                <div class="text-muted" style="font-size: 12px;">
                    <strong>{{ $tx->quantity }} {{ $tx->material->unit ?? 'pcs' }}</strong> &bull;
                    {{ $tx->fromLocation->name ?? 'External' }} &rarr; {{ $tx->toLocation->name ?? 'Site' }}
                </div>
            </div>
            <div class="text-end flex-shrink-0">
                @if($tx->type==='received') <span class="badge-received">Received</span>
                @elseif($tx->type==='issued') <span class="badge-issued">Issued</span>
                @elseif($tx->type==='transferred') <span class="badge-transferred">Transferred</span>
                @elseif(in_array($tx->type,['damaged','lost'])) <span class="badge-damaged">{{ ucfirst($tx->type) }}</span>
                @else <span class="badge-used">{{ ucfirst($tx->type) }}</span>
                @endif
                <div class="text-muted mt-1" style="font-size: 11px;">{{ $tx->created_at ? $tx->created_at->diffForHumans() : 'Just now' }}</div>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">
            <div style="font-size: 48px; margin-bottom: 8px;"><i class="fa-regular fa-clipboard" style="color: #dce1e7;"></i></div>
            <div class="fw-semibold">No movements recorded yet.</div>
            <div class="small">Use the buttons above to record your first material movement.</div>
        </div>
        @endforelse
    </div>
</div>

@endsection
