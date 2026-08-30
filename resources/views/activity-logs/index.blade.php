@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-header-title">Activity &amp; Security Logs</h1>
        <p class="page-header-sub">Traceable audit record of logins, changes, and QR scans in Bulalacao</p>
    </div>
    <button type="button" onclick="window.print()" class="btn btn-fb-edit btn-sm">
        <i class="fa-solid fa-print me-1"></i> Print Log
    </button>
</div>

<!-- Filters -->
<div class="fb-card mb-3">
    <form method="GET" action="{{ route('activity-logs.index') }}" class="row g-2 align-items-end">
        <div class="col-12 col-md-6">
            <label class="form-label">Search Activity</label>
            <div class="input-icon-group">
                <i class="input-icon fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" class="form-control" placeholder="Search action, description..." value="{{ request('search') }}">
            </div>
        </div>

        <div class="col-6 col-md-4">
            <label class="form-label">Module</label>
            <select name="module" class="form-select" onchange="this.form.submit()">
                <option value="">All System Modules</option>
                @foreach($modules as $mod)
                    <option value="{{ $mod }}" {{ request('module') === $mod ? 'selected' : '' }}>{{ $mod }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-6 col-md-2">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-fb-view w-100">Filter</button>
        </div>
    </form>
</div>

<!-- Logs Feed -->
<div class="d-flex flex-column gap-2 mb-3">
    @forelse($logs as $log)
    <div class="fb-card d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-start align-items-sm-center gap-3 flex-grow-1 min-w-0">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #f0f2f5; display: flex; align-items: center; justify-content: center; font-size: 15px; flex-shrink: 0; color: var(--fb-blue);">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div class="min-w-0 flex-grow-1">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <span class="badge text-bg-light border text-uppercase font-monospace" style="font-size: 11px;">{{ $log->module }}</span>
                    <span class="fw-bold text-dark text-truncate" style="font-size: 14px;">{{ $log->description ?? 'Event logged' }}</span>
                </div>
                <div class="text-muted small" style="font-size: 12px;">
                    Triggered by <strong>{{ $log->user->name ?? 'System Process' }}</strong>
                    <span class="mx-1">&bull;</span>
                    IP: {{ $log->ip_address ?? '127.0.0.1' }}
                </div>
            </div>
        </div>

        <div class="text-muted small text-start text-md-end flex-shrink-0 pt-2 pt-md-0 border-top border-top-md-0" style="font-size: 11.5px;">
            <i class="fa-regular fa-clock me-1"></i>
            {{ $log->created_at ? $log->created_at->diffForHumans() : 'Recently' }}
        </div>
    </div>
    @empty
    <div class="fb-card text-center py-5 text-muted">
        <div style="font-size: 48px; margin-bottom: 8px; color: #dce1e7;"><i class="fa-solid fa-list-check"></i></div>
        <div class="fw-bold">No Activity Logs Found</div>
        <div class="small">Try adjusting your search filters.</div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
    <span class="text-muted small">Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} events</span>
    {{ $logs->links('pagination::bootstrap-5') }}
</div>

@endsection
