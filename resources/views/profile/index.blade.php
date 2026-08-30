@extends('layouts.app')

@section('title', 'My Profile')

@section('content')

<!-- Header -->
<div class="fb-card mb-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <div class="fb-avatar" style="width: 52px; height: 52px; font-size: 20px;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="fw-bold text-dark mb-0" style="font-size: 18px;">{{ $user->name }}</h1>
                <div class="text-muted" style="font-size: 13px;">
                    <span class="badge text-bg-primary-subtle text-primary border border-primary-subtle me-1">{{ $user->role->name ?? 'User' }}</span>
                    <span>{{ $user->email }}</span>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-fb-danger btn-sm" onclick="openLogoutModal()">
            <i class="fa-solid fa-right-from-bracket me-1"></i> Sign Out
        </button>
    </div>
</div>

<div class="row g-3">
    
    <!-- Edit Profile & Password -->
    <div class="col-12 col-lg-6">
        <div class="fb-card h-100">
            <div class="fw-bold text-dark mb-3" style="font-size: 16px;">
                <i class="fa-solid fa-user-pen text-primary me-2"></i> Update Account Details
            </div>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Work Email Address</label>
                    <input type="email" class="form-control bg-light text-muted" id="email" value="{{ $user->email }}" readonly>
                    <small class="text-muted" style="font-size: 11px;">Contact Administrator to change email.</small>
                </div>

                <div class="mb-4">
                    <label for="phone" class="form-label">Phone / Contact Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                </div>

                <div class="fw-bold text-dark mb-3 pt-3 border-top" style="font-size: 14px;">
                    <i class="fa-solid fa-lock text-primary me-2"></i> Change Password
                </div>

                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" placeholder="Leave blank if unchanged">
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Min. 8 characters">
                    </div>
                    <div class="col-6">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Re-enter password">
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-3 border-top">
                    <button type="submit" class="btn btn-fb-view">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- User Information & Recent Activity -->
    <div class="col-12 col-lg-6">
        <div class="fb-card h-100">
            <div class="fw-bold text-dark mb-3" style="font-size: 16px;">
                <i class="fa-solid fa-shield text-primary me-2"></i> Role & Permissions
            </div>

            <div class="p-3 bg-light rounded-3 border mb-4">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="fw-bold text-dark">{{ $user->role->name ?? 'Standard Role' }}</span>
                    <span class="badge-available">Active</span>
                </div>
                <p class="text-muted small mb-0" style="font-size: 12px;">
                    {{ $user->role->description ?? 'Operational access in Bulalacao, Oriental Mindoro logistics system.' }}
                </p>
            </div>

            <div class="fw-bold text-dark mb-3" style="font-size: 14px;">
                <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> My Recent Operations
            </div>

            <div class="d-flex flex-column gap-2">
                @forelse($recentActivities as $act)
                    <div class="p-2.5 rounded-3 border d-flex align-items-center justify-content-between small" style="background: #f0f2f5;">
                        <div>
                            <span class="badge text-bg-secondary text-uppercase" style="font-size: 10px;">{{ $act->action }}</span>
                            <span class="text-dark fw-medium ms-1" style="font-size: 13px;">{{ $act->description }}</span>
                        </div>
                        <small class="text-muted" style="font-size: 11px;">{{ $act->created_at ? $act->created_at->diffForHumans() : 'Just now' }}</small>
                    </div>
                @empty
                    <p class="text-muted small py-3 text-center mb-0">No recorded activities yet.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection
