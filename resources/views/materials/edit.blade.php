@extends('layouts.app')

@section('title', 'Edit ' . $material->name)

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-lg-9 col-xl-8">
        
        <!-- Header -->
        <div class="page-header">
            <div>
                <h1 class="page-header-title">Edit Material Specifications</h1>
                <p class="page-header-sub">{{ $material->name }} (MAT-{{ str_pad($material->id, 5, '0', STR_PAD_LEFT) }})</p>
            </div>
            <a href="{{ route('materials.show', $material->id) }}" class="btn btn-fb-edit btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Details
            </a>
        </div>

        <!-- Form Card -->
        <div class="fb-card p-4">
            <form method="POST" action="{{ route('materials.update', $material->id) }}">
                @csrf
                @method('PUT')

                <div class="sidebar-section-title ps-0 mb-3" style="font-size: 13px;">
                    <i class="fa-solid fa-pen text-primary me-1"></i> Material Details
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Material Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $material->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="category_id" class="form-label">Material Category</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">Select Category...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $material->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="supplier_id" class="form-label">Supplier</label>
                        <select class="form-select" id="supplier_id" name="supplier_id">
                            <option value="">Select Supplier...</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $material->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-4">
                        <label for="unit" class="form-label">Unit of Measure <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="unit" name="unit" value="{{ old('unit', $material->unit) }}" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="minimum_stock_level" class="form-label">Min. Reorder Level <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="minimum_stock_level" name="minimum_stock_level" value="{{ old('minimum_stock_level', $material->minimum_stock_level) }}" min="0" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="available" {{ old('status', $material->status) === 'available' ? 'selected' : '' }}>Available</option>
                            <option value="low_stock" {{ old('status', $material->status) === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                            <option value="out_of_stock" {{ old('status', $material->status) === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="location_id" class="form-label">Storage Location Depot</label>
                    <select class="form-select" id="location_id" name="location_id">
                        <option value="">Select Location...</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ old('location_id', $material->location_id) == $location->id ? 'selected' : '' }}>
                                {{ $location->name }} ({{ $location->site->name ?? 'Site' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $material->description) }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3" style="border-top: 1px solid var(--fb-border);">
                    <a href="{{ route('materials.show', $material->id) }}" class="btn btn-fb-edit">Cancel</a>
                    <button type="submit" class="btn btn-fb-view">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Update Material
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection
