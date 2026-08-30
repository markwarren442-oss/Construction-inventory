@extends('layouts.app')

@section('title', 'Record Material Movement')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-lg-8 col-xl-7">
        
        <!-- Header -->
        <div class="page-header">
            <div>
                <h1 class="page-header-title">Record Material Movement</h1>
                <p class="page-header-sub">Record material stock-in, issuance, transfer, or usage</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="btn btn-fb-edit btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Movements Log
            </a>
        </div>

        <!-- Form Card -->
        <div class="fb-card p-4">
            <form method="POST" action="{{ route('transactions.store') }}">
                @csrf

                <!-- Movement Type Selection Cards -->
                <div class="mb-4">
                    <label class="form-label d-block">1. Select Movement Type <span class="text-danger">*</span></label>
                    <div class="row g-2">
                        <div class="col-6 col-sm-3">
                            <label class="p-3 text-center rounded-3 border type-card w-100 d-block cursor-pointer {{ old('type', $selectedType) === 'received' ? 'active-type' : '' }}" style="cursor: pointer; background: var(--fb-bg); transition: all .15s ease;">
                                <input type="radio" name="type" value="received" class="d-none" {{ old('type', $selectedType) === 'received' ? 'checked' : '' }} onchange="updateTypeCard()">
                                <div style="font-size: 22px; margin-bottom: 4px; color: #219150;"><i class="fa-solid fa-arrow-down"></i></div>
                                <span class="fw-bold text-dark d-block" style="font-size: 13px;">Receive</span>
                                <span class="text-muted" style="font-size: 11px;">Stock-In</span>
                            </label>
                        </div>

                        <div class="col-6 col-sm-3">
                            <label class="p-3 text-center rounded-3 border type-card w-100 d-block cursor-pointer {{ old('type', $selectedType) === 'issued' ? 'active-type' : '' }}" style="cursor: pointer; background: var(--fb-bg); transition: all .15s ease;">
                                <input type="radio" name="type" value="issued" class="d-none" {{ old('type', $selectedType) === 'issued' ? 'checked' : '' }} onchange="updateTypeCard()">
                                <div style="font-size: 22px; margin-bottom: 4px; color: var(--fb-blue);"><i class="fa-solid fa-arrow-up"></i></div>
                                <span class="fw-bold text-dark d-block" style="font-size: 13px;">Issue</span>
                                <span class="text-muted" style="font-size: 11px;">To Workers</span>
                            </label>
                        </div>

                        <div class="col-6 col-sm-3">
                            <label class="p-3 text-center rounded-3 border type-card w-100 d-block cursor-pointer {{ old('type', $selectedType) === 'transferred' ? 'active-type' : '' }}" style="cursor: pointer; background: var(--fb-bg); transition: all .15s ease;">
                                <input type="radio" name="type" value="transferred" class="d-none" {{ old('type', $selectedType) === 'transferred' ? 'checked' : '' }} onchange="updateTypeCard()">
                                <div style="font-size: 22px; margin-bottom: 4px; color: #7b4ff7;"><i class="fa-solid fa-truck"></i></div>
                                <span class="fw-bold text-dark d-block" style="font-size: 13px;">Transfer</span>
                                <span class="text-muted" style="font-size: 11px;">Move Site</span>
                            </label>
                        </div>

                        <div class="col-6 col-sm-3">
                            <label class="p-3 text-center rounded-3 border type-card w-100 d-block cursor-pointer {{ old('type', $selectedType) === 'used' ? 'active-type' : '' }}" style="cursor: pointer; background: var(--fb-bg); transition: all .15s ease;">
                                <input type="radio" name="type" value="used" class="d-none" {{ old('type', $selectedType) === 'used' ? 'checked' : '' }} onchange="updateTypeCard()">
                                <div style="font-size: 22px; margin-bottom: 4px; color: #65676b;"><i class="fa-solid fa-hammer"></i></div>
                                <span class="fw-bold text-dark d-block" style="font-size: 13px;">Used</span>
                                <span class="text-muted" style="font-size: 11px;">Consumed</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Material Selection -->
                <div class="mb-3">
                    <label for="material_id" class="form-label">2. Material <span class="text-danger">*</span></label>
                    <select class="form-select @error('material_id') is-invalid @enderror" id="material_id" name="material_id" required onchange="updateSelectedMaterialInfo(this)">
                        <option value="">Select Construction Material...</option>
                        @foreach($materials as $mat)
                            <option value="{{ $mat->id }}" data-unit="{{ $mat->unit }}" data-stock="{{ $mat->current_stock }}" {{ old('material_id', $selectedMaterialId) == $mat->id ? 'selected' : '' }}>
                                {{ $mat->name }} (Available: {{ $mat->current_stock }} {{ $mat->unit }}) &bull; {{ $mat->location->name ?? 'Depot' }}
                            </option>
                        @endforeach
                    </select>
                    @error('material_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Quantity -->
                <div class="mb-3">
                    <label for="quantity" class="form-label">3. Quantity <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" min="1" value="{{ old('quantity', 1) }}" required>
                        <span class="input-group-text bg-light text-muted" id="unitDisplay">Units</span>
                    </div>
                    @error('quantity')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Location Origin & Destination -->
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="from_location_id" class="form-label">From Location (Origin)</label>
                        <select class="form-select" id="from_location_id" name="from_location_id">
                            <option value="">External Supplier / RORO Depot</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('from_location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }} ({{ $loc->site->name ?? 'Site' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="to_location_id" class="form-label">To Location (Destination)</label>
                        <select class="form-select" id="to_location_id" name="to_location_id">
                            <option value="">Select Worksite...</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('to_location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }} ({{ $loc->site->name ?? 'Site' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Remarks -->
                <div class="mb-4">
                    <label for="remarks" class="form-label">Remarks / Operational Note</label>
                    <textarea class="form-control" id="remarks" name="remarks" rows="2" placeholder="e.g. Received via delivery truck / Issued to Barangay road project team">{{ old('remarks') }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="d-flex justify-content-end gap-2 pt-3" style="border-top: 1px solid var(--fb-border);">
                    <a href="{{ route('transactions.index') }}" class="btn btn-fb-edit">Cancel</a>
                    <button type="submit" class="btn btn-fb-view">
                        <i class="fa-solid fa-check me-1"></i> Save &amp; Log Movement
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
    .active-type {
        border-color: #1877f2 !important;
        background: #e7f3ff !important;
    }
</style>
@endpush

@push('scripts')
<script>
    function updateTypeCard() {
        const cards = document.querySelectorAll('.type-card');
        cards.forEach(card => {
            const radio = card.querySelector('input[type="radio"]');
            if (radio.checked) {
                card.classList.add('active-type');
            } else {
                card.classList.remove('active-type');
            }
        });
    }

    function updateSelectedMaterialInfo(select) {
        const selected = select.options[select.selectedIndex];
        const unit = selected.getAttribute('data-unit') || 'Units';
        document.getElementById('unitDisplay').innerText = unit;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const sel = document.getElementById('material_id');
        if (sel) updateSelectedMaterialInfo(sel);
    });
</script>
@endpush
