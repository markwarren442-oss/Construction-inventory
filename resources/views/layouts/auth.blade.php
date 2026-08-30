<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') | ConstructLogix – Bulalacao</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --cl-primary: #5b46b8;
            --cl-primary-dark: #3d288f;
            --cl-primary-light: #7c68d0;
            --cl-accent: #9b7ff0;
            --cl-bg: #f5f3fa;
            --cl-card: #ffffff;
            --cl-text: #2d2640;
            --cl-muted: #7a7293;
            --cl-border: #e4dff2;
            --cl-danger: #e74c3c;
            --cl-success: #27ae60;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--cl-primary-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* ══════════════════════════════════════════
           MAIN AUTH CARD
           ══════════════════════════════════════════ */
        .auth-container {
            width: 100%;
            max-width: 1080px;
            min-height: 660px;
            background: var(--cl-card);
            border-radius: 28px;
            box-shadow: 0 30px 90px rgba(0,0,0,.4);
            position: relative;
            overflow: hidden;
        }

        /* ══════════════════════════════════════════
           FORM PANELS (both sit side-by-side behind overlay)
           ══════════════════════════════════════════ */
        .auth-forms-layer {
            display: flex;
            width: 100%;
            height: 100%;
            min-height: 660px;
            position: relative;
            z-index: 1;
        }

        .auth-form-panel {
            width: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 44px;
            transition: opacity .5s ease;
        }

        /* Login form is on the RIGHT half (visible by default) */
        .form-panel-login { order: 2; }
        /* Signup form is on the LEFT half (hidden by overlay by default) */
        .form-panel-signup { order: 1; }

        .auth-form-wrap {
            width: 100%;
            max-width: 380px;
        }

        /* ══════════════════════════════════════════
           PURPLE OVERLAY (slides left ↔ right)
           ══════════════════════════════════════════ */
        .auth-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            z-index: 10;
            transition: left .7s cubic-bezier(.65, 0, .35, 1);
            overflow: hidden;
            border-radius: 28px;
        }

        /* Purple gradient background */
        .auth-overlay-bg {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, var(--cl-primary) 0%, var(--cl-primary-dark) 100%);
            z-index: 0;
            pointer-events: none;
        }

        /* Geometric chevron layers */
        .chevron-1 {
            position: absolute;
            top: -20%;
            left: -45%;
            width: 110%;
            height: 140%;
            background: linear-gradient(135deg, var(--cl-primary-light) 0%, var(--cl-primary) 100%);
            transform: rotate(-12deg) skewY(8deg);
            opacity: 0.7;
            z-index: 1;
            transition: transform .7s cubic-bezier(.65, 0, .35, 1);
            pointer-events: none;
        }

        .chevron-2 {
            position: absolute;
            top: -10%;
            left: -60%;
            width: 100%;
            height: 130%;
            background: linear-gradient(135deg, var(--cl-accent) 0%, var(--cl-primary-light) 100%);
            transform: rotate(-18deg) skewY(6deg);
            opacity: 0.45;
            z-index: 2;
            transition: transform .7s cubic-bezier(.65, 0, .35, 1);
            pointer-events: none;
        }

        .chevron-3 {
            position: absolute;
            top: 10%;
            left: -30%;
            width: 90%;
            height: 120%;
            background: linear-gradient(135deg, rgba(255,255,255,.08) 0%, rgba(255,255,255,.02) 100%);
            transform: rotate(-8deg) skewY(12deg);
            z-index: 3;
            transition: transform .7s cubic-bezier(.65, 0, .35, 1);
            pointer-events: none;
        }

        /* When signup mode: overlay slides right, chevrons mirror */
        .auth-container.signup-active .auth-overlay {
            left: 50%;
        }

        .auth-container.signup-active .chevron-1 {
            transform: rotate(12deg) skewY(-8deg);
            left: auto;
            right: -45%;
        }

        .auth-container.signup-active .chevron-2 {
            transform: rotate(18deg) skewY(-6deg);
            left: auto;
            right: -60%;
        }

        .auth-container.signup-active .chevron-3 {
            transform: rotate(8deg) skewY(-12deg);
            left: auto;
            right: -30%;
        }

        /* ══════════════════════════════════════════
           OVERLAY INNER CONTENT (two sets)
           ══════════════════════════════════════════ */
        .overlay-content-wrap {
            position: absolute;
            inset: 0;
            z-index: 10;
            display: flex;
        }

        /* Each overlay content panel takes full width and height */
        .overlay-content {
            position: absolute;
            inset: 0;
            z-index: 20;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
            padding: 40px 32px;
            text-align: center;
            transition: opacity .5s ease, transform .7s cubic-bezier(.65, 0, .35, 1);
        }

        /* Content for login state (overlay on LEFT) */
        .overlay-login-state {
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
        }

        /* Content for signup state (overlay on RIGHT) – hidden by default */
        .overlay-signup-state {
            opacity: 0;
            transform: translateX(100%);
            pointer-events: none;
        }

        /* Signup mode: swap visibility */
        .auth-container.signup-active .overlay-login-state {
            opacity: 0;
            transform: translateX(-100%);
            pointer-events: none;
        }

        .auth-container.signup-active .overlay-signup-state {
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
        }

        /* Branding */
        .overlay-brand {
            font-size: 22px;
            font-weight: 900;
            color: #fff;
            letter-spacing: 3px;
        }

        .overlay-sub {
            color: rgba(255,255,255,.65);
            font-size: 14px;
            line-height: 1.6;
            max-width: 280px;
        }

        .overlay-divider {
            width: 50px;
            height: 3px;
            background: rgba(255,255,255,.3);
            border-radius: 3px;
        }

        /* Switch button on overlay */
        .overlay-switch-btn {
            position: relative;
            z-index: 30;
            pointer-events: auto;
            padding: 13px 40px;
            border: 2px solid rgba(255,255,255,.7);
            border-radius: 50px;
            background: transparent;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all .3s ease;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .overlay-switch-btn:hover {
            background: rgba(255,255,255,.15);
            border-color: #fff;
            transform: scale(1.05);
        }

        .overlay-switch-btn:active {
            transform: scale(0.97);
        }

        .overlay-icon-ring {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,.5);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #fff;
        }

        /* ══════════════════════════════════════════
           FORM AVATAR & TITLE
           ══════════════════════════════════════════ */
        .auth-avatar-ring {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--cl-primary) 0%, var(--cl-accent) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            box-shadow: 0 6px 24px rgba(91,70,184,.35);
        }

        .auth-avatar-ring i {
            font-size: 34px;
            color: #ffffff;
        }

        .auth-panel-title {
            text-align: center;
            font-size: 22px;
            font-weight: 800;
            color: var(--cl-primary);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 28px;
        }

        /* ══════════════════════════════════════════
           FORM INPUTS (underline w/ icons)
           ══════════════════════════════════════════ */
        .cl-field {
            position: relative;
            margin-bottom: 22px;
        }

        .cl-field i.field-icon {
            position: absolute;
            top: 50%;
            left: 2px;
            transform: translateY(-50%);
            color: var(--cl-muted);
            font-size: 16px;
            width: 32px;
            text-align: center;
            pointer-events: none;
            transition: color .2s;
        }

        .cl-field input {
            width: 100%;
            border: none;
            border-bottom: 2px solid var(--cl-border);
            padding: 12px 14px 12px 40px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            color: var(--cl-text);
            background: transparent;
            outline: none;
            transition: border-color .3s;
        }

        .cl-field input::placeholder {
            color: var(--cl-muted);
            font-weight: 400;
        }

        .cl-field input:focus {
            border-bottom-color: var(--cl-primary);
        }

        .cl-field input:focus ~ i.field-icon {
            color: var(--cl-primary);
        }

        /* ══════════════════════════════════════════
           BUTTONS
           ══════════════════════════════════════════ */
        .cl-btn-primary {
            width: 100%;
            background: linear-gradient(135deg, var(--cl-primary) 0%, var(--cl-primary-dark) 100%);
            border: none;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            border-radius: 10px;
            padding: 14px;
            cursor: pointer;
            transition: all .25s ease;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 4px 16px rgba(91,70,184,.35);
            margin-top: 4px;
        }

        .cl-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(91,70,184,.5);
        }

        .cl-btn-primary:active { transform: translateY(0); }

        .cl-forgot {
            display: inline-block;
            color: var(--cl-primary);
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            margin-top: 4px;
        }

        .cl-forgot:hover { text-decoration: underline; }

        .cl-form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 2px;
            margin-bottom: 6px;
        }

        /* ══════════════════════════════════════════
           ALERTS
           ══════════════════════════════════════════ */
        .cl-alert {
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cl-alert-error {
            background: #fef0f0;
            color: var(--cl-danger);
            border: 1px solid #fdd;
        }

        .cl-alert-success {
            background: #f0faf4;
            color: var(--cl-success);
            border: 1px solid #c8ecd6;
        }

        /* ══════════════════════════════════════════
           DIVIDER
           ══════════════════════════════════════════ */
        .cl-divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 22px 0 16px;
        }

        .cl-divider::before, .cl-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--cl-border);
        }

        .cl-divider span {
            font-size: 11px;
            color: var(--cl-muted);
            font-weight: 600;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        /* ══════════════════════════════════════════
           QUICK LOGIN – iOS GLASS ICONS
           ══════════════════════════════════════════ */
        .quick-login-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .quick-login-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 7px;
            padding: 12px 4px 8px;
            border: none;
            border-radius: 18px;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: all .25s cubic-bezier(.4, 0, .2, 1);
            position: relative;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.55);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow:
                0 3px 14px rgba(91, 70, 184, .08),
                inset 0 1px 0 rgba(255,255,255,.7),
                inset 0 -1px 0 rgba(0,0,0,.04);
            border: 1px solid rgba(255, 255, 255, .6);
        }

        .quick-login-btn::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 50%;
            background: linear-gradient(180deg, rgba(255,255,255,.45) 0%, transparent 100%);
            border-radius: 18px 18px 0 0;
            pointer-events: none;
        }

        .quick-login-btn:hover {
            transform: translateY(-4px) scale(1.04);
            box-shadow:
                0 12px 32px rgba(91, 70, 184, .2),
                inset 0 1px 0 rgba(255,255,255,.8);
        }

        .quick-login-btn:active { transform: scale(0.96); }

        .ql-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            position: relative;
            z-index: 1;
            box-shadow:
                0 4px 12px rgba(0,0,0,.12),
                inset 0 1px 1px rgba(255,255,255,.5);
        }

        .ql-icon.icon-admin  { background: linear-gradient(145deg, #ff6b6b, #ee5a24); color: #fff; }
        .ql-icon.icon-inventory { background: linear-gradient(145deg, #74b9ff, #0984e3); color: #fff; }
        .ql-icon.icon-pm { background: linear-gradient(145deg, #55efc4, #00b894); color: #fff; }
        .ql-icon.icon-site { background: linear-gradient(145deg, #fdcb6e, #e17055); color: #fff; }

        .ql-label {
            font-size: 10px;
            font-weight: 600;
            color: var(--cl-text);
            letter-spacing: .3px;
            position: relative;
            z-index: 1;
            text-align: center;
            line-height: 1.2;
        }

        /* ══════════════════════════════════════════
           FOOTER
           ══════════════════════════════════════════ */
        .auth-footer-note {
            text-align: center;
            font-size: 12px;
            color: rgba(255,255,255,.4);
            margin-top: 18px;
        }

        /* ══════════════════════════════════════════
           RESPONSIVE
           ══════════════════════════════════════════ */
        @media (max-width: 900px) {
            .auth-container {
                max-width: 100%;
                min-height: auto;
                flex-direction: column;
                border-radius: 20px;
            }

            .auth-forms-layer {
                flex-direction: column;
                min-height: auto;
            }

            .auth-form-panel {
                width: 100%;
                padding: 32px 24px;
                min-height: 500px;
            }

            /* Hide overlay on mobile, show both panels stacked */
            .auth-overlay { display: none; }

            .form-panel-signup {
                display: none;
            }

            .auth-container.signup-active .form-panel-signup {
                display: flex;
            }

            .auth-container.signup-active .form-panel-login {
                display: none;
            }

            /* Mobile tab switcher */
            .mobile-switcher {
                display: flex !important;
                background: linear-gradient(135deg, var(--cl-primary) 0%, var(--cl-primary-dark) 100%);
                padding: 0;
                border-radius: 20px 20px 0 0;
                overflow: hidden;
            }

            .mobile-switcher button {
                flex: 1;
                padding: 16px;
                border: none;
                background: transparent;
                color: rgba(255,255,255,.6);
                font-size: 14px;
                font-weight: 700;
                font-family: 'Inter', sans-serif;
                letter-spacing: 1px;
                text-transform: uppercase;
                cursor: pointer;
                transition: all .3s;
            }

            .mobile-switcher button.active {
                background: var(--cl-card);
                color: var(--cl-primary);
                border-radius: 16px 16px 0 0;
            }

            .quick-login-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            body { padding: 0; }
            .auth-container { border-radius: 0; }
            .auth-form-panel { padding: 24px 18px; }
            .auth-avatar-ring { width: 64px; height: 64px; }
            .auth-avatar-ring i { font-size: 28px; }
            .auth-panel-title { font-size: 18px; margin-bottom: 20px; }
        }

        /* Hide mobile switcher on desktop */
        .mobile-switcher { display: none; }
    </style>
</head>
<body data-show-signup="{{ ($errors->has('name') || $errors->has('password_confirmation') || old('_form') === 'register') ? '1' : '0' }}">

<div class="auth-container" id="authContainer">

    <!-- ══ MOBILE TAB BAR (hidden on desktop) ══ -->
    <div class="mobile-switcher">
        <button class="active" onclick="switchPanel('login')">Login</button>
        <button onclick="switchPanel('signup')">Sign Up</button>
    </div>

    <!-- ══ SLIDING PURPLE OVERLAY ══ -->
    <div class="auth-overlay">
        <div class="auth-overlay-bg"></div>
        <div class="chevron-1"></div>
        <div class="chevron-2"></div>
        <div class="chevron-3"></div>

        <!-- Content shown when overlay is on LEFT (login mode) -->
        <div class="overlay-content overlay-login-state">
            <div class="overlay-icon-ring">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
            <div class="overlay-brand">CONSTRUCTLOGIX</div>
            <div class="overlay-divider"></div>
            <div class="overlay-sub">
                Don't have an account yet?<br>
                Create one to start tracking materials and deliveries.
            </div>
            <button class="overlay-switch-btn" onclick="switchPanel('signup')">
                <i class="fa-solid fa-user-plus" style="margin-right:8px"></i> SIGN UP
            </button>
            <div style="color: rgba(255,255,255,.35); font-size: 11px; margin-top: 8px;">
                Bulalacao, Oriental Mindoro
            </div>
        </div>

        <!-- Content shown when overlay is on RIGHT (signup mode) -->
        <div class="overlay-content overlay-signup-state">
            <div class="overlay-icon-ring">
                <i class="fa-solid fa-right-to-bracket"></i>
            </div>
            <div class="overlay-brand">CONSTRUCTLOGIX</div>
            <div class="overlay-divider"></div>
            <div class="overlay-sub">
                Already have an account?<br>
                Sign in to access your dashboard and inventory.
            </div>
            <button class="overlay-switch-btn" onclick="switchPanel('login')">
                <i class="fa-solid fa-right-to-bracket" style="margin-right:8px"></i> LOGIN
            </button>
            <div style="color: rgba(255,255,255,.35); font-size: 11px; margin-top: 8px;">
                Bulalacao, Oriental Mindoro
            </div>
        </div>
    </div>

    <!-- ══ FORM PANELS LAYER ══ -->
    <div class="auth-forms-layer">

        @yield('form')

    </div>

</div>

<div class="auth-footer-note">
    <i class="fa-solid fa-infinity" style="margin-right:4px"></i>
    Bulalacao Municipal Infrastructure • Oriental Mindoro &copy; {{ date('Y') }}
</div>

@yield('modal')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function switchPanel(panel) {
        const container = document.getElementById('authContainer');
        const mobileBtns = document.querySelectorAll('.mobile-switcher button');

        if (panel === 'signup') {
            container.classList.add('signup-active');
            if (mobileBtns.length) {
                mobileBtns[0].classList.remove('active');
                mobileBtns[1].classList.add('active');
            }
        } else {
            container.classList.remove('signup-active');
            if (mobileBtns.length) {
                mobileBtns[1].classList.remove('active');
                mobileBtns[0].classList.add('active');
            }
        }
    }

    // If signup validation errors exist, show signup panel
    if (document.body.dataset.showSignup === '1') {
        document.addEventListener('DOMContentLoaded', function() { switchPanel('signup'); });
    }
</script>
@stack('scripts')
</body>
</html>
