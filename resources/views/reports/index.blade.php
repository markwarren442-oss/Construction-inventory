@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-header-title">Reports &amp; Analytics</h1>
        <p class="page-header-sub">Generate and print material consumption and movement audit reports</p>
    </div>
    <button type="button" onclick="window.print()" class="btn btn-fb-view btn-sm">
        <i class="fa-solid fa-print me-1"></i> Print / Save PDF
    </button>
</div>

<!-- Filters Bar Card -->
<div class="fb-card mb-3">
    <form method="GET" action="{{ route('reports.index') }}" class="row g-2 align-items-end">
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label">Movement Type</label>
            <select name="type" class="form-select">
                <option value="all">All Transactions</option>
                <option value="received"    {{ request('type') === 'received'    ? 'selected' : '' }}>Receiving Log (Stock-In)</option>
                <option value="issued"      {{ request('type') === 'issued'      ? 'selected' : '' }}>Issuance Log (To Site)</option>
                <option value="transferred" {{ request('type') === 'transferred' ? 'selected' : '' }}>Transfer Summary</option>
                <option value="damaged"     {{ request('type') === 'damaged'     ? 'selected' : '' }}>Damaged Materials</option>
                <option value="lost"        {{ request('type') === 'lost'        ? 'selected' : '' }}>Lost Materials</option>
            </select>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label">Project Site</label>
            <select name="site_id" class="form-select">
                <option value="">All Project Sites</option>
                @foreach($sites as $s)
                    <option value="{{ $s->id }}" {{ request('site_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-6 col-md-2">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
        </div>

        <div class="col-6 col-md-2">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
        </div>

        <div class="col-12 col-md-2">
            <button type="submit" class="btn btn-fb-view w-100">
                <i class="fa-solid fa-filter me-1"></i> Generate
            </button>
        </div>
    </form>
</div>

<!-- Summary Metrics Row -->
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="stat-block">
            <div class="stat-icon-wrap" style="background: #e6f9f1; color: #219150;">
                <i class="fa-solid fa-arrow-down"></i>
            </div>
            <div>
                <div class="stat-value" style="color: #219150;">+{{ number_format($summary['total_received']) }}</div>
                <div class="stat-label">Stock-In Received</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-block">
            <div class="stat-icon-wrap" style="background: #e7f3ff; color: var(--fb-blue);">
                <i class="fa-solid fa-arrow-up"></i>
            </div>
            <div>
                <div class="stat-value" style="color: var(--fb-blue);">-{{ number_format($summary['total_issued']) }}</div>
                <div class="stat-label">Issued to Sites</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-block">
            <div class="stat-icon-wrap" style="background: #f3e8ff; color: #7b4ff7;">
                <i class="fa-solid fa-truck"></i>
            </div>
            <div>
                <div class="stat-value" style="color: #7b4ff7;">{{ number_format($summary['total_transferred']) }}</div>
                <div class="stat-label">Transferred Depots</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-block">
            <div class="stat-icon-wrap" style="background: #fdecea; color: #c62828;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div class="stat-value" style="color: #c62828;">{{ number_format($summary['total_damaged_lost']) }}</div>
                <div class="stat-label">Damaged / Lost</div>
            </div>
        </div>
    </div>
</div>

<!-- Report Feed List -->
<div class="d-flex flex-column gap-2 mb-3">
    <div class="d-flex align-items-center justify-content-between px-1 mb-1">
        <span class="fw-bold text-dark" style="font-size: 15px;">Movement Records ({{ $summary['transaction_count'] }} records)</span>
        <span class="small text-muted">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} &mdash; {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span>
    </div>

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
        <div class="d-flex align-items-start align-items-sm-center gap-3 flex-grow-1 min-w-0">
            <div class="tx-avatar tx-avatar-{{ $txClass }}" style="width: 44px; height: 44px; font-size: 18px;">
                <i class="fa-solid {{ $txIcon }}"></i>
            </div>
            <div class="min-w-0 flex-grow-1">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <span class="fw-bold text-dark text-truncate" style="font-size: 15px;">{{ $tx->material->name ?? 'Material' }}</span>
                    <span class="badge text-bg-light border text-muted font-monospace" style="font-size: 11px;">{{ $tx->reference_number }}</span>
                    @if($tx->type === 'received')
                        <span class="badge-received">Received</span>
                    @elseif($tx->type === 'issued')
                        <span class="badge-issued">Issued</span>
                    @elseif($tx->type === 'transferred')
                        <span class="badge-transferred">Transferred</span>
                    @elseif(in_array($tx->type, ['damaged', 'lost']))
                        <span class="badge-damaged">{{ ucfirst($tx->type) }}</span>
                    @else
                        <span class="badge-used">{{ ucfirst($tx->type) }}</span>
                    @endif
                </div>
                <div class="text-muted" style="font-size: 13px;">
                    <strong>{{ $tx->quantity }} {{ $tx->material->unit ?? 'pcs' }}</strong>
                    <span class="mx-1">&bull;</span>
                    {{ $tx->fromLocation->name ?? 'External' }} &rarr; {{ $tx->toLocation->name ?? 'Site' }}
                </div>
                <div class="text-muted small mt-1" style="font-size: 11.5px;">
                    <i class="fa-regular fa-user me-1"></i> Staff: <strong>{{ $tx->performedByUser->name ?? 'Staff' }}</strong>
                    <span class="mx-1">&bull;</span>
                    <i class="fa-regular fa-calendar me-1"></i> {{ $tx->created_at ? $tx->created_at->format('M d, Y H:i') : 'Today' }}
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end w-100 w-md-auto pt-2 pt-md-0 border-top border-top-md-0 flex-shrink-0">
            <a href="{{ route('transactions.show', $tx->id) }}" class="btn btn-fb-view btn-sm">
                <i class="fa-solid fa-receipt me-1"></i> Receipt
            </a>
        </div>
    </div>
    @empty
    <div class="fb-card text-center py-5 text-muted">
        <div style="font-size: 48px; margin-bottom: 8px; color: #dce1e7;"><i class="fa-regular fa-folder-open"></i></div>
        <div class="fw-bold">No Records Found for the Selected Period</div>
        <div class="small">Try expanding the date range or choosing a different project site.</div>
    </div>
    @endforelse
</div>

@endsection
