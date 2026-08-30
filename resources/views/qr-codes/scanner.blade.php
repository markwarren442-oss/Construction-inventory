@extends('layouts.app')

@section('title', 'Scan QR Code')

@section('content')

<!-- Header Card -->
<div class="fb-card mb-3">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div style="width: 48px; height: 48px; background: #f3e8ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px; color: #7b4ff7; flex-shrink: 0;">
                📷
            </div>
            <div>
                <h1 class="fw-bold text-dark mb-0" style="font-size: 20px;">QR Code Scanner</h1>
                <p class="text-muted small mb-0">Point your camera at a material QR tag or enter the code to record movements</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('materials.index') }}" class="btn btn-fb-edit btn-sm">
                <i class="fa-solid fa-boxes-stacked me-1"></i> All Materials
            </a>
            <a href="{{ route('transactions.index') }}" class="btn btn-fb-view btn-sm">
                <i class="fa-solid fa-clock-rotate-left me-1"></i> Movements
            </a>
        </div>
    </div>
</div>

<div class="row g-3">
    
    <!-- Left Column: Camera Scanner & Input -->
    <div class="col-12 col-lg-6">
        <div class="fb-card h-100">
            <div class="fw-bold text-dark mb-3" style="font-size: 16px;">
                <i class="fa-solid fa-camera text-primary me-2"></i> Camera Viewport
            </div>

            <!-- Video Viewport / Scanner Area -->
            <div class="position-relative rounded-3 overflow-hidden text-center d-flex flex-column align-items-center justify-content-center mb-3" style="min-height: 260px; max-height: 300px; background: #1c1e21;">
                <video id="qrVideo" class="w-100 h-100 position-absolute top-0 start-0 object-fit-cover d-none" playsinline></video>
                <canvas id="qrCanvas" class="d-none"></canvas>

                <!-- Scanner Overlay Graphic -->
                <div id="scannerOverlay" class="position-relative text-white p-4">
                    <div class="border border-primary border-3 rounded-4 p-4 d-inline-block position-relative mb-2" style="width: 160px; height: 160px; border-style: dashed !important; background: rgba(24, 119, 242, 0.12);">
                        <i class="fa-solid fa-qrcode fa-3x text-white opacity-75"></i>
                        <div class="position-absolute top-50 start-0 end-0 border-top border-danger border-2 shadow" style="animation: scanLaser 2s infinite ease-in-out;"></div>
                    </div>
                    <div class="small fw-semibold mt-1">Align QR Code within the box</div>
                </div>
            </div>

            <!-- Camera Controls -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button type="button" id="startCamBtn" class="btn btn-fb-scan" onclick="startCamera()">
                    <i class="fa-solid fa-video me-1"></i> Start Camera
                </button>
                <button type="button" id="stopCamBtn" class="btn btn-fb-danger d-none" onclick="stopCamera()">
                    <i class="fa-solid fa-video-slash me-1"></i> Stop Camera
                </button>
                <small id="cameraStatus" class="text-muted" style="font-size: 12px;">Camera standby</small>
            </div>

            <!-- Manual QR Input -->
            <div class="pt-3 border-top">
                <label for="manualQrInput" class="form-label">⌨️ Manual QR Code Entry</label>
                <div class="input-group">
                    <span class="input-group-text bg-white" style="border-color: var(--fb-border);"><i class="fa-solid fa-keyboard text-muted"></i></span>
                    <input type="text" id="manualQrInput" class="form-control" placeholder="e.g. MAT-BUL-CEM-001 or Material Name" onkeydown="if(event.key==='Enter') lookupCode(this.value)">
                    <button class="btn btn-fb-view" type="button" onclick="lookupCode(document.getElementById('manualQrInput').value)">
                        Search
                    </button>
                </div>
            </div>

            <!-- Sample Quick-Test Buttons -->
            <div class="mt-3 p-3 rounded-3" style="background: #f0f2f5;">
                <span class="small fw-bold text-muted d-block mb-2" style="font-size: 12px;">🧪 Quick Test (Click to simulate scan):</span>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" onclick="lookupCode('MAT-BUL-CEM-001')" class="btn btn-fb-edit btn-sm py-1 px-2" style="font-size: 12px;">
                        📦 Portland Cement
                    </button>
                    <button type="button" onclick="lookupCode('MAT-BUL-STL-016')" class="btn btn-fb-edit btn-sm py-1 px-2" style="font-size: 12px;">
                        🔩 16mm Rebars
                    </button>
                    <button type="button" onclick="lookupCode('MAT-BUL-SND-001')" class="btn btn-fb-edit btn-sm py-1 px-2" style="font-size: 12px;">
                        🏖️ River Sand
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Scanned Result -->
    <div class="col-12 col-lg-6">
        <div class="fb-card h-100">
            <div class="fw-bold text-dark mb-3" style="font-size: 16px;">
                <i class="fa-solid fa-clipboard-check text-primary me-2"></i> Scanned Material Result
            </div>

            <!-- Placeholder State -->
            <div id="noScanState" class="text-center py-5 text-muted">
                <div class="rounded-circle p-3 d-inline-flex mb-3" style="background: #f0f2f5; font-size: 40px;">
                    🏷️
                </div>
                <div class="fw-bold text-dark mb-1" style="font-size: 16px;">Awaiting QR Scan</div>
                <p class="small text-muted mb-0" style="max-width: 320px; margin: 0 auto;">
                    Scan a tag using your camera or enter a code on the left to record stock actions.
                </p>
            </div>

            <!-- Active Scanned Material Card -->
            <div id="materialResultCard" class="d-none">
                
                <div class="p-3 rounded-3 border mb-3" style="background: #f8f9fa;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span id="resCategory" class="badge text-bg-primary-subtle text-primary border border-primary-subtle">Category</span>
                        <span id="resStatus" class="badge-available">In Stock</span>
                    </div>
                    <div id="resName" class="fw-bold text-dark mb-1" style="font-size: 18px;">Material Name</div>
                    <div class="font-monospace text-primary fw-semibold small mb-3" id="resQrCode">MAT-BUL-XXXXX</div>

                    <div class="row g-2 pt-2 border-top">
                        <div class="col-6">
                            <small class="text-muted d-block">Current Stock:</small>
                            <span class="fs-4 fw-bold text-dark" id="resStock">0</span>
                            <span class="small text-muted" id="resUnit">pcs</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Depot Location:</small>
                            <span class="fw-bold text-dark small d-block" id="resLocation">Main Depot</span>
                        </div>
                    </div>
                </div>

                <!-- Transaction Selection Actions Grid -->
                <div class="fw-bold text-dark mb-2" style="font-size: 14px;">
                    <i class="fa-solid fa-bolt text-warning me-1"></i> Select Action to Record:
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6 col-sm-4">
                        <button type="button" onclick="prepareTransaction('received')" class="btn btn-fb-add w-100 py-2 d-flex flex-column align-items-center gap-1">
                            <i class="fa-solid fa-arrow-down fa-lg"></i>
                            <span class="fw-bold" style="font-size: 13px;">Receive</span>
                            <small style="font-size: 10px; opacity: .9;">Stock-In</small>
                        </button>
                    </div>
                    <div class="col-6 col-sm-4">
                        <button type="button" onclick="prepareTransaction('issued')" class="btn btn-fb-action w-100 py-2 d-flex flex-column align-items-center gap-1">
                            <i class="fa-solid fa-arrow-up fa-lg"></i>
                            <span class="fw-bold" style="font-size: 13px;">Issue</span>
                            <small style="font-size: 10px; opacity: .9;">Give to Crew</small>
                        </button>
                    </div>
                    <div class="col-6 col-sm-4">
                        <button type="button" onclick="prepareTransaction('transferred')" class="btn btn-fb-view w-100 py-2 d-flex flex-column align-items-center gap-1">
                            <i class="fa-solid fa-truck fa-lg"></i>
                            <span class="fw-bold" style="font-size: 13px;">Transfer</span>
                            <small style="font-size: 10px; opacity: .9;">Move Site</small>
                        </button>
                    </div>
                    <div class="col-6 col-sm-4">
                        <button type="button" onclick="prepareTransaction('used')" class="btn btn-fb-edit w-100 py-2 d-flex flex-column align-items-center gap-1">
                            <i class="fa-solid fa-hammer fa-lg"></i>
                            <span class="fw-bold" style="font-size: 13px;">Used</span>
                            <small style="font-size: 10px; opacity: .9;">Installed</small>
                        </button>
                    </div>
                    <div class="col-6 col-sm-4">
                        <button type="button" onclick="prepareTransaction('damaged')" class="btn btn-fb-danger w-100 py-2 d-flex flex-column align-items-center gap-1">
                            <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
                            <span class="fw-bold" style="font-size: 13px;">Damaged</span>
                            <small style="font-size: 10px; opacity: .9;">Broken</small>
                        </button>
                    </div>
                    <div class="col-6 col-sm-4">
                        <button type="button" onclick="prepareTransaction('lost')" class="btn btn-fb-danger w-100 py-2 d-flex flex-column align-items-center gap-1">
                            <i class="fa-solid fa-circle-question fa-lg"></i>
                            <span class="fw-bold" style="font-size: 13px;">Missing</span>
                            <small style="font-size: 10px; opacity: .9;">Lost</small>
                        </button>
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-2 border-top">
                    <div class="d-flex gap-2">
                        <a id="resMapLink" href="#" target="_blank" class="btn btn-fb-view btn-sm">
                            <i class="fa-solid fa-map-location-dot me-1"></i> Google Maps
                        </a>
                        <a id="resDetailsLink" href="#" class="btn btn-fb-edit btn-sm">
                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Details
                        </a>
                    </div>
                    <button type="button" onclick="resetScannerResult()" class="btn btn-light border btn-sm">
                        <i class="fa-solid fa-xmark me-1"></i> Clear
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- Transaction Action Modal -->
<div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 border">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold fs-6" id="actionModalLabel">Record Material Movement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('transactions.store') }}">
                @csrf
                <input type="hidden" name="material_id" id="modalMaterialId">
                <input type="hidden" name="type" id="modalTxType">

                <div class="modal-body p-3 p-md-4">
                    <div class="p-3 rounded-3 bg-light border mb-3">
                        <div class="fw-bold text-dark" id="modalMaterialName" style="font-size: 16px;">Selected Material</div>
                        <div class="text-muted small mt-1">Action: <span class="badge text-bg-primary text-uppercase" id="modalActionBadge">Action</span></div>
                    </div>

                    <!-- Quantity -->
                    <div class="mb-3">
                        <label for="modalQuantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="modalQuantity" name="quantity" min="1" value="1" required>
                            <span class="input-group-text bg-light text-muted" id="modalUnitLabel">pcs</span>
                        </div>
                    </div>

                    <!-- Target Location -->
                    <div class="mb-3" id="locationSelectGroup">
                        <label for="to_location_id" class="form-label">Worksite / Depot Location</label>
                        <select class="form-select" id="to_location_id" name="to_location_id">
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->site->name ?? 'Bulalacao' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Remarks -->
                    <div class="mb-3">
                        <label for="modalRemarks" class="form-label">Remarks / Purpose</label>
                        <textarea class="form-control" id="modalRemarks" name="remarks" rows="2" placeholder="e.g. Received via RORO delivery / Issued to Team B"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-fb-edit btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-fb-view btn-sm">
                        <i class="fa-solid fa-check me-1"></i> Save Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    @keyframes scanLaser {
        0%, 100% { top: 15%; opacity: 0.8; }
        50% { top: 85%; opacity: 1; }
    }
</style>
@endpush

@push('scripts')
<script>
    let currentScannedMaterial = null;
    let videoStream = null;

    function lookupCode(code) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        fetch('/qr-codes/lookup', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ code: code.trim() })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.material) {
                currentScannedMaterial = data.material;
                renderMaterialResult(data.material, data.qr_code);
            } else {
                alert(data.message || 'QR Code not found in logistics database.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Lookup failed. Please check network connection.');
        });
    }

    function renderMaterialResult(mat, qrCode) {
        document.getElementById('noScanState').classList.add('d-none');
        document.getElementById('materialResultCard').classList.remove('d-none');

        document.getElementById('resName').innerText = mat.name;
        document.getElementById('resCategory').innerText = mat.category ? mat.category.name : 'Material';
        document.getElementById('resQrCode').innerText = qrCode || ('MAT-BUL-' + mat.id);
        document.getElementById('resStock').innerText = mat.current_stock;
        document.getElementById('resUnit').innerText = mat.unit;
        document.getElementById('resLocation').innerText = mat.location ? (mat.location.name + ' (' + (mat.location.site ? mat.location.site.name : 'Site') + ')') : 'Unassigned';
        document.getElementById('resDetailsLink').href = '/materials/' + mat.id;

        const mapBtn = document.getElementById('resMapLink');
        if (mapBtn) {
            if (mat.location && mat.location.google_maps_url) {
                mapBtn.href = mat.location.google_maps_url;
            } else if (mat.location && mat.location.latitude && mat.location.longitude) {
                mapBtn.href = `https://www.google.com/maps/search/?api=1&query=${mat.location.latitude},${mat.location.longitude}`;
            } else {
                mapBtn.href = 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent((mat.location ? mat.location.name : 'Bulalacao') + ', Oriental Mindoro');
            }
        }

        const statusBadge = document.getElementById('resStatus');
        if (mat.status === 'available') {
            statusBadge.className = 'badge-available';
            statusBadge.innerText = 'In Stock';
        } else if (mat.status === 'low_stock') {
            statusBadge.className = 'badge-low';
            statusBadge.innerText = 'Low Stock';
        } else {
            statusBadge.className = 'badge-out';
            statusBadge.innerText = 'Out of Stock';
        }
    }

    function resetScannerResult() {
        document.getElementById('noScanState').classList.remove('d-none');
        document.getElementById('materialResultCard').classList.add('d-none');
        document.getElementById('manualQrInput').value = '';
        currentScannedMaterial = null;
    }

    function prepareTransaction(type) {
        if (!currentScannedMaterial) return;

        document.getElementById('modalMaterialId').value = currentScannedMaterial.id;
        document.getElementById('modalTxType').value = type;
        document.getElementById('modalMaterialName').innerText = currentScannedMaterial.name;
        document.getElementById('modalUnitLabel').innerText = currentScannedMaterial.unit;
        
        const badge = document.getElementById('modalActionBadge');
        badge.innerText = type;

        const modal = new bootstrap.Modal(document.getElementById('actionModal'));
        modal.show();
    }

    async function startCamera() {
        const video = document.getElementById('qrVideo');
        const overlay = document.getElementById('scannerOverlay');
        const status = document.getElementById('cameraStatus');

        try {
            videoStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            video.srcObject = videoStream;
            video.classList.remove('d-none');
            overlay.classList.add('d-none');
            await video.play();

            document.getElementById('startCamBtn').classList.add('d-none');
            document.getElementById('stopCamBtn').classList.remove('d-none');
            status.innerText = 'Camera scanning active...';
        } catch (err) {
            console.warn('Camera access error:', err);
            status.innerText = 'Camera unavailable / permission denied. Use sample tags or manual input.';
        }
    }

    function stopCamera() {
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
        document.getElementById('qrVideo').classList.add('d-none');
        document.getElementById('scannerOverlay').classList.remove('d-none');
        document.getElementById('startCamBtn').classList.remove('d-none');
        document.getElementById('stopCamBtn').classList.add('d-none');
        document.getElementById('cameraStatus').innerText = 'Camera stopped';
    }
</script>
@endpush
