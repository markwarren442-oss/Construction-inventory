@extends('layouts.app')

@section('title', 'Register New Material')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-lg-9 col-xl-8">
        
        <!-- Header -->
        <div class="page-header">
            <div>
                <h1 class="page-header-title">Add Construction Material</h1>
                <p class="page-header-sub">Register item into Bulalacao Logistics inventory and auto-generate QR tag</p>
            </div>
            <a href="{{ route('materials.index') }}" class="btn btn-fb-edit btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Catalog
            </a>
        </div>

        <!-- Form Card -->
        <div class="fb-card p-4">
            <form method="POST" action="{{ route('materials.store') }}" id="materialForm">
                @csrf

                <!-- Basic Info -->
                <div class="sidebar-section-title ps-0 mb-3" style="font-size: 13px;">
                    <i class="fa-solid fa-circle-info text-primary me-1"></i> Material Specifications
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Material Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Portland Cement Type 1 (40kg)" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="category_id" class="form-label">Material Category <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">Select Category...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="supplier_id" class="form-label">Material Supplier</label>
                        <select class="form-select" id="supplier_id" name="supplier_id">
                            <option value="">Select Supplier...</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Detailed Description &amp; Notes</label>
                    <textarea class="form-control" id="description" name="description" rows="2" placeholder="e.g. Batch specs, standard compliance (ASTM / PNS), rebar grade, etc.">{{ old('description') }}</textarea>
                </div>

                <!-- Stock & Location -->
                <div class="sidebar-section-title ps-0 mb-3 mt-4" style="font-size: 13px;">
                    <i class="fa-solid fa-warehouse text-primary me-1"></i> Inventory Levels &amp; Storage Location
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-4">
                        <label for="unit" class="form-label">Unit of Measurement <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="unit" name="unit" value="{{ old('unit', 'Pcs') }}" placeholder="e.g. Bags, Pcs, Cu.m, Bd.ft, Sheets" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="current_stock" class="form-label">Initial Quantity in Stock <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="current_stock" name="current_stock" value="{{ old('current_stock', 0) }}" min="0" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="minimum_stock_level" class="form-label">Minimum Reorder Level <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="minimum_stock_level" name="minimum_stock_level" value="{{ old('minimum_stock_level', 10) }}" min="0" required>
                        <div class="form-text">Alerts when stock drops to or below this amount.</div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="location_id" class="form-label">Primary Storage Location / Site <span class="text-danger">*</span></label>
                    <select class="form-select @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required>
                        <option value="">Select Depot / Staging Yard...</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }} &mdash; [{{ $location->site->name ?? 'Bulalacao Site' }}]
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Hidden action type -->
                <input type="hidden" name="action_type" id="action_type" value="save">

                <!-- Submit Action Buttons -->
                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 pt-3" style="border-top: 1px solid var(--fb-border);">
                    <a href="{{ route('materials.index') }}" class="btn btn-fb-edit">Cancel</a>
                    <button type="submit" onclick="document.getElementById('action_type').value='save'" class="btn btn-fb-view">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Material
                    </button>
                    <button type="submit" onclick="document.getElementById('action_type').value='save_and_qr'" class="btn btn-fb-scan">
                        <i class="fa-solid fa-qrcode me-1"></i> Save &amp; Generate QR
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection
