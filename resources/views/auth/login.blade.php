@extends('layouts.auth')
@section('title', 'Log In')

@section('form')

    <!-- ════════════════════════════════════════
         PANEL 1: SIGN UP (sits on LEFT half)
         ════════════════════════════════════════ -->
    <div class="auth-form-panel form-panel-signup">
        <div class="auth-form-wrap">

            <div class="auth-avatar-ring">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div class="auth-panel-title">SIGN UP</div>

            {{-- Signup Alerts --}}
            @if ($errors->any() && old('_form') === 'register')
                <div class="cl-alert cl-alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="hidden" name="_form" value="register">

                <div class="cl-field">
                    <i class="fa-solid fa-user field-icon"></i>
                    <input
                        type="text"
                        name="name"
                        placeholder="Full Name"
                        value="{{ old('_form') === 'register' ? old('name') : '' }}"
                        required
                    >
                </div>

                <div class="cl-field">
                    <i class="fa-solid fa-envelope field-icon"></i>
                    <input
                        type="email"
                        name="email"
                        placeholder="Email Address"
                        value="{{ old('_form') === 'register' ? old('email') : '' }}"
                        required
                    >
                </div>

                <div class="cl-field">
                    <i class="fa-solid fa-phone field-icon"></i>
                    <input
                        type="text"
                        name="phone"
                        placeholder="Phone Number (optional)"
                        value="{{ old('_form') === 'register' ? old('phone') : '' }}"
                    >
                </div>

                <div class="cl-field">
                    <i class="fa-solid fa-lock field-icon"></i>
                    <input
                        type="password"
                        name="password"
                        placeholder="Password"
                        required
                    >
                </div>

                <div class="cl-field">
                    <i class="fa-solid fa-shield-halved field-icon"></i>
                    <input
                        type="password"
                        name="password_confirmation"
                        placeholder="Confirm Password"
                        required
                    >
                </div>

                <button type="submit" class="cl-btn-primary">
                    CREATE ACCOUNT
                </button>
            </form>

            <div style="text-align: center; margin-top: 14px; font-size: 11px; color: var(--cl-muted); line-height: 1.5;">
                <i class="fa-solid fa-info-circle" style="margin-right: 3px;"></i>
                New accounts are assigned <strong>Site Personnel</strong> role.
            </div>

        </div>
    </div>

    <!-- ════════════════════════════════════════
         PANEL 2: LOGIN (sits on RIGHT half)
         ════════════════════════════════════════ -->
    <div class="auth-form-panel form-panel-login">
        <div class="auth-form-wrap">

            <div class="auth-avatar-ring">
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="auth-panel-title">LOGIN</div>

            {{-- Alerts --}}
            @if ($errors->any() && old('_form', 'login') === 'login')
                <div class="cl-alert cl-alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('success'))
                <div class="cl-alert cl-alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="standardLoginForm">
                @csrf
                <input type="hidden" name="_form" value="login">

                <div class="cl-field">
                    <i class="fa-solid fa-user field-icon"></i>
                    <input
                        type="email"
                        name="email"
                        placeholder="Email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        autofocus
                        required
                    >
                </div>

                <div class="cl-field">
                    <i class="fa-solid fa-lock field-icon"></i>
                    <input
                        type="password"
                        name="password"
                        placeholder="Password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <div class="cl-form-footer">
                    <a href="#" class="cl-forgot">Forgot Password?</a>
                </div>

                <button type="submit" class="cl-btn-primary">
                    LOGIN
                </button>
            </form>

            {{-- Quick Login iOS Glass Icons with Security Passcode Modal --}}
            <div class="cl-divider"><span>Quick Login</span></div>

            <div class="quick-login-grid">

                {{-- Hidden Quick Login Forms that submit upon passcode verification --}}
                <form id="quickLoginForm_admin" method="POST" action="{{ route('login') }}" style="display:none">
                    @csrf
                    <input type="hidden" name="email" value="admin@logistic.app">
                    <input type="hidden" name="password" value="password">
                </form>

                <form id="quickLoginForm_inventory" method="POST" action="{{ route('login') }}" style="display:none">
                    @csrf
                    <input type="hidden" name="email" value="inventory@logistic.app">
                    <input type="hidden" name="password" value="password">
                </form>

                <form id="quickLoginForm_pm" method="POST" action="{{ route('login') }}" style="display:none">
                    @csrf
                    <input type="hidden" name="email" value="pm@logistic.app">
                    <input type="hidden" name="password" value="password">
                </form>

                <form id="quickLoginForm_site" method="POST" action="{{ route('login') }}" style="display:none">
                    @csrf
                    <input type="hidden" name="email" value="site@logistic.app">
                    <input type="hidden" name="password" value="password">
                </form>

                {{-- Trigger Buttons --}}
                <button type="button" class="quick-login-btn" onclick="openPasscodeModal('admin', 'Administrator', '1101', 'fa-crown', 'icon-admin')">
                    <div class="ql-icon icon-admin"><i class="fa-solid fa-crown"></i></div>
                    <span class="ql-label">Admin</span>
                </button>

                <button type="button" class="quick-login-btn" onclick="openPasscodeModal('inventory', 'Inventory Officer', '1102', 'fa-clipboard-list', 'icon-inventory')">
                    <div class="ql-icon icon-inventory"><i class="fa-solid fa-clipboard-list"></i></div>
                    <span class="ql-label">Inventory</span>
                </button>

                <button type="button" class="quick-login-btn" onclick="openPasscodeModal('pm', 'Project Manager', '1103', 'fa-chart-gantt', 'icon-pm')">
                    <div class="ql-icon icon-pm"><i class="fa-solid fa-chart-gantt"></i></div>
                    <span class="ql-label">Manager</span>
                </button>

                <button type="button" class="quick-login-btn" onclick="openPasscodeModal('site', 'Site Personnel', '1104', 'fa-helmet-safety', 'icon-site')">
                    <div class="ql-icon icon-site"><i class="fa-solid fa-helmet-safety"></i></div>
                    <span class="ql-label">Personnel</span>
                </button>

            </div>

            <div style="text-align: center; margin-top: 14px; font-size: 11px; color: var(--cl-muted);">
                <i class="fa-solid fa-shield-halved" style="margin-right: 3px;"></i>
                Authorized personnel only • Passcode protected
            </div>

        </div>
    </div>

@endsection

@section('modal')

    <!-- ════════════════════════════════════════
         iOS GLASS SECURITY PASSCODE MODAL
         (Rendered OUTSIDE auth container to avoid overflow:hidden clip)
         ════════════════════════════════════════ -->
    <div id="passcodeModal" class="passcode-modal-backdrop" style="display: none;">
        <div class="passcode-modal-card" id="passcodeModalCard">
            
            {{-- Close Button --}}
            <button type="button" class="passcode-modal-close" onclick="closePasscodeModal()" title="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>

            {{-- Role Header --}}
            <div class="passcode-role-badge" id="modalRoleIconWrap">
                <div class="ql-icon" id="modalRoleIcon"><i class="fa-solid fa-shield-halved"></i></div>
            </div>
            
            <h3 class="passcode-title" id="modalRoleTitle">Role Authorization</h3>
            <p class="passcode-subtitle">Enter security passcode to verify identity</p>

            {{-- PIN Dots Indicator --}}
            <div class="passcode-dots-wrap" id="passcodeDotsWrap">
                <div class="passcode-dot" id="dot-1"></div>
                <div class="passcode-dot" id="dot-2"></div>
                <div class="passcode-dot" id="dot-3"></div>
                <div class="passcode-dot" id="dot-4"></div>
            </div>

            {{-- Status / Error text --}}
            <div class="passcode-status-msg" id="passcodeStatusMsg"></div>

            {{-- iOS Glass Numeric Keypad --}}
            <div class="passcode-keypad">
                <button type="button" class="keypad-key" onclick="enterDigit('1')">1</button>
                <button type="button" class="keypad-key" onclick="enterDigit('2')">2</button>
                <button type="button" class="keypad-key" onclick="enterDigit('3')">3</button>
                
                <button type="button" class="keypad-key" onclick="enterDigit('4')">4</button>
                <button type="button" class="keypad-key" onclick="enterDigit('5')">5</button>
                <button type="button" class="keypad-key" onclick="enterDigit('6')">6</button>
                
                <button type="button" class="keypad-key" onclick="enterDigit('7')">7</button>
                <button type="button" class="keypad-key" onclick="enterDigit('8')">8</button>
                <button type="button" class="keypad-key" onclick="enterDigit('9')">9</button>
                
                <button type="button" class="keypad-key keypad-action-key" onclick="clearPasscode()">Clear</button>
                <button type="button" class="keypad-key" onclick="enterDigit('0')">0</button>
                <button type="button" class="keypad-key keypad-action-key" onclick="deleteDigit()"><i class="fa-solid fa-delete-left"></i></button>
            </div>

            <div class="passcode-modal-footer">
                <span id="modalRoleHint" class="passcode-hint">Passcode: &bull;&bull;&bull;&bull;</span>
            </div>

        </div>
    </div>

    {{-- Passcode Modal Styles --}}
    <style>
        .passcode-modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(18, 12, 40, 0.72);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: fadeInModal .25s ease-out;
        }

        @keyframes fadeInModal {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .passcode-modal-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-radius: 28px;
            padding: 32px 28px 24px;
            width: 100%;
            max-width: 360px;
            text-align: center;
            box-shadow: 
                0 25px 70px rgba(0, 0, 0, 0.45),
                0 0 0 1px rgba(255, 255, 255, 0.6) inset;
            position: relative;
            animation: scaleUpModal .28s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes scaleUpModal {
            from { transform: scale(0.88); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .passcode-modal-card.shake {
            animation: shakePasscode .45s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        }

        @keyframes shakePasscode {
            10%, 90% { transform: translate3d(-3px, 0, 0); }
            20%, 80% { transform: translate3d(5px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-6px, 0, 0); }
            40%, 60% { transform: translate3d(6px, 0, 0); }
        }

        .passcode-modal-close {
            position: absolute;
            top: 18px;
            right: 18px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background: rgba(0, 0, 0, 0.06);
            color: #636e72;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .2s;
            font-size: 14px;
        }
        .passcode-modal-close:hover {
            background: rgba(0, 0, 0, 0.12);
            color: #2d3436;
            transform: scale(1.08);
        }

        .passcode-role-badge {
            display: flex;
            justify-content: center;
            margin-bottom: 12px;
        }

        .passcode-role-badge .ql-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            font-size: 26px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.18);
        }

        .passcode-title {
            font-size: 20px;
            font-weight: 800;
            color: #2d2640;
            margin-bottom: 4px;
            letter-spacing: -0.2px;
        }

        .passcode-subtitle {
            font-size: 13px;
            color: #7a7293;
            margin-bottom: 20px;
        }

        /* 4 Dots Indicator */
        .passcode-dots-wrap {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-bottom: 14px;
        }

        .passcode-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid #5b46b8;
            background: transparent;
            transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .passcode-dot.filled {
            background: #5b46b8;
            box-shadow: 0 0 10px rgba(91, 70, 184, 0.6);
            transform: scale(1.15);
        }

        .passcode-dot.error {
            border-color: #e74c3c;
            background: #e74c3c;
            box-shadow: 0 0 10px rgba(231, 76, 60, 0.6);
        }

        .passcode-dot.success {
            border-color: #27ae60;
            background: #27ae60;
            box-shadow: 0 0 10px rgba(39, 174, 96, 0.6);
        }

        .passcode-status-msg {
            min-height: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 14px;
            transition: all .2s;
        }
        .passcode-status-msg.error { color: #e74c3c; }
        .passcode-status-msg.success { color: #27ae60; }

        /* Keypad */
        .passcode-keypad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            max-width: 270px;
            margin: 0 auto;
        }

        .keypad-key {
            height: 56px;
            border-radius: 50%;
            width: 56px;
            margin: 0 auto;
            border: 1px solid rgba(255, 255, 255, 0.8);
            background: rgba(240, 238, 248, 0.85);
            color: #2d2640;
            font-size: 21px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .15s ease;
            box-shadow: 0 3px 10px rgba(91, 70, 184, 0.08);
            user-select: none;
            -webkit-user-select: none;
        }

        .keypad-key:hover {
            background: #5b46b8;
            color: #ffffff;
            transform: scale(1.06);
            box-shadow: 0 6px 18px rgba(91, 70, 184, 0.35);
        }

        .keypad-key:active {
            transform: scale(0.94);
        }

        .keypad-action-key {
            font-size: 13px;
            font-weight: 700;
            background: transparent;
            border-color: transparent;
            box-shadow: none;
            color: #7a7293;
        }
        .keypad-action-key:hover {
            background: rgba(0, 0, 0, 0.06);
            color: #2d2640;
            transform: scale(1.05);
            box-shadow: none;
        }

        .passcode-modal-footer {
            margin-top: 18px;
            font-size: 12px;
            color: #a49db7;
        }

        .passcode-hint {
            display: inline-block;
            background: rgba(91, 70, 184, 0.08);
            color: #5b46b8;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11.5px;
            letter-spacing: 0.5px;
        }
    </style>

    {{-- Passcode Modal JavaScript Logic --}}
    <script>
        let currentTargetRole = null;
        let currentRequiredPin = null;
        let currentEnteredPin = '';
        let isProcessingAuth = false;

        const rolePasscodes = {
            'admin': '1101',
            'inventory': '1102',
            'pm': '1103',
            'site': '1104'
        };

        function openPasscodeModal(roleKey, roleName, expectedPin, iconClass, iconBgClass) {
            currentTargetRole = roleKey;
            currentRequiredPin = expectedPin || rolePasscodes[roleKey];
            currentEnteredPin = '';
            isProcessingAuth = false;

            // Setup UI content
            document.getElementById('modalRoleTitle').textContent = roleName + ' Access';
            document.getElementById('modalRoleHint').textContent = 'Required PIN: ' + currentRequiredPin;
            
            const iconWrap = document.getElementById('modalRoleIcon');
            iconWrap.className = 'ql-icon ' + iconBgClass;
            iconWrap.innerHTML = '<i class="fa-solid ' + iconClass + '"></i>';

            // Reset dots and status
            resetDots();
            document.getElementById('passcodeStatusMsg').textContent = '';
            document.getElementById('passcodeStatusMsg').className = 'passcode-status-msg';

            // Show modal
            const modal = document.getElementById('passcodeModal');
            modal.style.display = 'flex';

            // Listen for keyboard events while modal is open
            window.addEventListener('keydown', handlePhysicalKeypad);
        }

        function closePasscodeModal() {
            const modal = document.getElementById('passcodeModal');
            modal.style.display = 'none';
            currentTargetRole = null;
            currentEnteredPin = '';
            isProcessingAuth = false;
            window.removeEventListener('keydown', handlePhysicalKeypad);
        }

        function enterDigit(digit) {
            if (isProcessingAuth || currentEnteredPin.length >= 4) return;

            currentEnteredPin += digit;
            updateDotsDisplay();

            if (currentEnteredPin.length === 4) {
                verifyPasscode();
            }
        }

        function deleteDigit() {
            if (isProcessingAuth || currentEnteredPin.length === 0) return;
            currentEnteredPin = currentEnteredPin.slice(0, -1);
            updateDotsDisplay();
            document.getElementById('passcodeStatusMsg').textContent = '';
        }

        function clearPasscode() {
            if (isProcessingAuth) return;
            currentEnteredPin = '';
            resetDots();
            document.getElementById('passcodeStatusMsg').textContent = '';
        }

        function updateDotsDisplay() {
            for (let i = 1; i <= 4; i++) {
                const dot = document.getElementById('dot-' + i);
                if (i <= currentEnteredPin.length) {
                    dot.classList.add('filled');
                } else {
                    dot.classList.remove('filled');
                }
                dot.classList.remove('error', 'success');
            }
        }

        function resetDots() {
            for (let i = 1; i <= 4; i++) {
                const dot = document.getElementById('dot-' + i);
                dot.className = 'passcode-dot';
            }
        }

        function verifyPasscode() {
            isProcessingAuth = true;
            const statusMsg = document.getElementById('passcodeStatusMsg');
            const card = document.getElementById('passcodeModalCard');

            if (currentEnteredPin === currentRequiredPin) {
                // Correct Passcode!
                for (let i = 1; i <= 4; i++) {
                    const dot = document.getElementById('dot-' + i);
                    dot.classList.add('success');
                }
                statusMsg.textContent = '✓ Passcode Verified! Logging in...';
                statusMsg.className = 'passcode-status-msg success';

                setTimeout(() => {
                    const targetForm = document.getElementById('quickLoginForm_' + currentTargetRole);
                    if (targetForm) {
                        targetForm.submit();
                    }
                }, 400);
            } else {
                // Incorrect Passcode!
                for (let i = 1; i <= 4; i++) {
                    const dot = document.getElementById('dot-' + i);
                    dot.classList.add('error');
                }
                statusMsg.textContent = '✕ Incorrect code. Please enter ' + currentRequiredPin;
                statusMsg.className = 'passcode-status-msg error';

                // Trigger card shake animation
                card.classList.add('shake');
                setTimeout(() => {
                    card.classList.remove('shake');
                }, 500);

                setTimeout(() => {
                    currentEnteredPin = '';
                    resetDots();
                    isProcessingAuth = false;
                }, 900);
            }
        }

        function handlePhysicalKeypad(e) {
            if (!currentTargetRole) return;

            if (e.key >= '0' && e.key <= '9') {
                enterDigit(e.key);
            } else if (e.key === 'Backspace') {
                deleteDigit();
            } else if (e.key === 'Escape') {
                closePasscodeModal();
            }
        }

        // Close on backdrop click outside card
        document.getElementById('passcodeModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePasscodeModal();
            }
        });
    </script>

@endsection
