@extends('layouts.app')

@section('title', 'User Management')

@section('content')

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-header-title">User Management</h1>
        <p class="page-header-sub">Manage system accounts, roles, and status in Bulalacao</p>
    </div>
    <button type="button" class="btn btn-fb-add" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fa-solid fa-user-plus me-2"></i> Add New User
    </button>
</div>

<!-- Search & Filters -->
<div class="fb-card mb-3">
    <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-end">
        <div class="col-12 col-md-6">
            <label class="form-label">Search Users</label>
            <div class="input-icon-group">
                <i class="input-icon fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" class="form-control" placeholder="Search name or email..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-6 col-md-4">
            <label class="form-label">Filter by Role</label>
            <select name="role_id" class="form-select" onchange="this.form.submit()">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-fb-view w-100">Filter</button>
        </div>
    </form>
</div>

<!-- Users List / Cards -->
<div class="d-flex flex-column gap-2 mb-3">
    @forelse($users as $u)
    <div class="fb-card d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        
        <!-- Left: Avatar + Details -->
        <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
            <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--fb-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 17px; font-weight: 700; flex-shrink: 0;">
                {{ strtoupper(substr($u->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-grow-1">
                <div class="fw-bold text-dark text-truncate" style="font-size: 15px;">{{ $u->name }}</div>
                <div class="text-muted text-truncate" style="font-size: 13px;">
                    <i class="fa-regular fa-envelope me-1"></i> {{ $u->email }}
                    @if($u->phone)
                        <span class="mx-1">&bull;</span>
                        <i class="fa-solid fa-phone me-1" style="font-size: 11px;"></i> {{ $u->phone }}
                    @endif
                </div>
                <div class="text-muted small mt-1" style="font-size: 11.5px;">
                    <i class="fa-regular fa-calendar me-1"></i>
                    Joined: {{ $u->created_at ? $u->created_at->format('M d, Y') : '—' }}
                </div>
            </div>
        </div>

        <!-- Center: Role & Status Badges -->
        <div class="d-flex flex-wrap align-items-center gap-2" style="min-width: 140px;">
            @if($u->hasRole('administrator'))
                <span class="badge text-bg-danger" style="font-size: 11.5px; padding: 4px 8px;">
                    <i class="fa-solid fa-crown me-1"></i> Administrator
                </span>
            @elseif($u->hasRole('inventory-officer'))
                <span class="badge text-bg-primary" style="font-size: 11.5px; padding: 4px 8px;">
                    <i class="fa-solid fa-clipboard-list me-1"></i> Inventory Officer
                </span>
            @elseif($u->hasRole('project-manager'))
                <span class="badge text-bg-success" style="font-size: 11.5px; padding: 4px 8px;">
                    <i class="fa-solid fa-chart-bar me-1"></i> Project Manager
                </span>
            @else
                <span class="badge text-bg-warning" style="font-size: 11.5px; padding: 4px 8px;">
                    <i class="fa-solid fa-helmet-safety me-1"></i> Site Personnel
                </span>
            @endif

            @if($u->status === 'active')
                <span class="badge-available" style="font-size: 11px;">Active</span>
            @else
                <span class="badge-out" style="font-size: 11px;">Inactive</span>
            @endif
        </div>

        <!-- Right: Action Button -->
        <div class="d-flex justify-content-end w-100 w-md-auto pt-2 pt-md-0 border-top border-top-md-0 flex-shrink-0">
            <form method="POST" action="{{ route('users.toggle-status', $u->id) }}" class="w-100 w-md-auto">
                @csrf
                <button type="submit" class="btn btn-sm w-100 {{ $u->status === 'active' ? 'btn-fb-danger' : 'btn-fb-add' }}" {{ $u->id === auth()->id() ? 'disabled' : '' }}>
                    <i class="fa-solid {{ $u->status === 'active' ? 'fa-user-slash' : 'fa-user-check' }} me-1"></i>
                    {{ $u->status === 'active' ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
        </div>

    </div>
    @empty
    <div class="fb-card text-center py-5 text-muted">
        <div style="font-size: 48px; margin-bottom: 8px; color: #dce1e7;"><i class="fa-solid fa-users"></i></div>
        <div class="fw-bold">No Users Found</div>
        <div class="small">Try adjusting your search or filter criteria.</div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
    <span class="text-muted small">Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} accounts</span>
    {{ $users->links('pagination::bootstrap-5') }}
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 34px; height: 34px; background: #e6f9f1; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #219150; font-size: 15px;">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <h5 class="modal-title" id="addUserModalLabel">Create User Account</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="new_name" name="name" placeholder="e.g. Juan Perez" required>
                    </div>

                    <div class="mb-3">
                        <label for="new_email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="new_email" name="email" placeholder="name@logistic.app" required>
                    </div>

                    <div class="mb-3">
                        <label for="new_phone" class="form-label">Phone Number <span class="text-muted fw-normal" style="font-size: 12px;">(optional)</span></label>
                        <input type="tel" class="form-control" id="new_phone" name="phone" placeholder="+63 (917) 000-0000">
                    </div>

                    <div class="mb-3">
                        <label for="new_role_id" class="form-label">Assigned Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="new_role_id" name="role_id" required>
                            <option value="">Select a role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }} — {{ $role->description ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-0">
                        <label for="new_password" class="form-label">Temporary Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="new_password" name="password" placeholder="Min. 8 characters" required>
                        <div class="form-text">The user should change this after their first login.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-fb-edit btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-fb-add btn-sm">
                        <i class="fa-solid fa-user-plus me-1"></i> Enroll Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
