@php
    $isShared = $isShared ?? false;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-transparent">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Elk 的旅程規劃</title>
    <link rel="icon" href="/icon_logo.png?v={{ time() }}" type="image/png">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Noto+Sans+TC:wght@300;400;500;700&display=swap" rel="stylesheet">

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#9c8c7c">

    <!-- iOS support -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="ElkTrip">
    <link rel="apple-touch-icon" href="/icon_logo.png">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(reg => {
                    console.log('Premium PWA Active:', reg.scope);
                }).catch(err => {
                    console.warn('PWA skipped:', err);
                });
            });
        }
    </script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        muji: {
                            base: '#f8f5f0',    // Off-white/Cream
                            paper: '#ffffff',   // Pure white for panels
                            oak: '#9c8c7c',     // Warm wood/earth tone
                            wheat: '#dcd3c1',   // Light linen
                            ink: '#333333',     // Soft black
                            ash: '#757575',     // Soft grey
                            edge: '#e8e4db'      // Subtle border
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'Noto Sans TC', 'sans-serif'],
                    },
                    boxShadow: {
                        'muji': '0 4px 20px -5px rgba(0, 0, 0, 0.05)',
                        'muji-sm': '0 2px 8px -2px rgba(0, 0, 0, 0.04)'
                    }
                }
            }
        }

        // Safe Modal Engine (Standardized Visibility Control)
        function safeOpenModal(id) {
            console.log('Safe Modal Error: Not found ->', id);
            try {
                // Force close potential overlaps if they exist
                ['loginModal', 'registerModal', 'globalProfileConfigModal', 'mapModal', 'expenseModal', 'daySummaryEditModal', 'eventDetailsModal'].forEach(mId => {
                    const otherM = document.getElementById(mId);
                    if (otherM && mId !== id) {
                        otherM.classList.add('hidden');
                        otherM.style.removeProperty('display');
                    }
                });

                const m = document.getElementById(id);
                if (!m) {
                    console.error('Safe Modal Error: Not found ->', id);
                    return;
                }

                m.classList.remove('hidden');
                m.style.removeProperty('display');
                m.style.zIndex = '3000'; // Force to top
                m.style.pointerEvents = 'auto'; // Ensure interactive

                if (typeof lockScroll === 'function') lockScroll();
            } catch (e) { console.error('Modal Open Error:', e); }
        }

        function safeCloseModal(id) {
            try {
                const m = document.getElementById(id);
                if (!m) return;
                m.classList.add('hidden');
                m.style.removeProperty('display');
                if (typeof unlockScroll === 'function') unlockScroll();
            } catch (e) { console.error('Modal Close Execution Error:', e); }
        }
    </script>
    <style>
        .tooltip {
            position: relative;
            display: inline-flex;
        }

        .tooltip::after {
            content: attr(data-tip);
            position: absolute;
            bottom: 120%;
            left: 50%;
            transform: translateX(-50%) scale(0.9);
            padding: 5px 10px;
            background: rgba(31, 41, 55, 0.9);
            color: white;
            font-size: 11px;
            font-weight: 600;
            border-radius: 6px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            pointer-events: none;
            z-index: 1000;
        }

        .tooltip:hover::after,
        .tooltip:focus::after,
        .tooltip:focus-within::after {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) scale(1);
        }

        .tooltip-bottom::after {
            bottom: auto;
            top: 120%;
        }

        /* Mobile: Tooltips usually don't work well on touch, so we can hide or adjust */
        @media (max-width: 640px) {
            .tooltip::after {
                display: none;
            }
        }
    </style>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e8e4db;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #dcd3c1;
        }

        /* MUJI Style Glass Overlay */
        .muji-glass {
            background-color: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(232, 228, 219, 0.4);
        }

        .muji-button-primary {
            background-color: #9c8c7c;
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .muji-button-primary:hover {
            background-color: #8a7b6c;
        }

        .muji-card {
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(232, 228, 219, 0.5);
            border-radius: 20px;
            box-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.05);
        }

        /* NEW: Premium Glass Inputs */
        .muji-input {
            background: rgba(255, 255, 255, 0.4) !important;
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            border: 1px solid rgba(232, 228, 219, 0.8) !important;
            border-radius: 12px !important;
            transition: all 0.3s ease !important;
            color: #333333 !important;
            font-weight: 500 !important;
        }

        .muji-input:focus {
            background: rgba(255, 255, 255, 0.8) !important;
            border-color: #9c8c7c !important;
            box-shadow: 0 0 0 4px rgba(156, 140, 124, 0.15) !important;
            outline: none !important;
            transform: translateY(-1px);
        }

        .muji-input::placeholder {
            color: rgba(117, 117, 117, 0.6) !important;
            font-size: 13px;
        }

        /* Essential Fix: Ensure Swal stays on top of all modals */
        .swal2-container {
            z-index: 10000 !important;
        }
    </style>
    <style>
        /* iOS Date/Time Input Focus Fix */
        @media screen and (max-width: 768px) {

            input[type="date"],
            input[type="time"] {
                -webkit-appearance: none !important;
                min-height: 46px !important;
                height: 46px !important;
                line-height: 46px !important;
                /* 強制文字與框同高 */
                padding-top: 0 !important;
                /* 排除任何預設偏移 */
                padding-bottom: 0 !important;
                opacity: 1 !important;
                z-index: 1 !important;
                cursor: pointer;
            }
        }

        /* Mobile Menu Drawer Styles */
        #mobileMenuDrawer {
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateX(100%);
        }

        #mobileMenuDrawer.active {
            transform: translateX(0);
        }

        #mobileMenuBackdrop {
            transition: opacity 0.3s ease;
            opacity: 0;
            pointer-events: none;
        }

        #mobileMenuBackdrop.active {
            opacity: 1;
            pointer-events: auto;
        }
    </style>
</head>

<body class="text-muji-ink antialiased min-h-screen flex flex-col relative bg-transparent overflow-x-hidden">
    <!-- Muji Pull-to-Refresh Indicator (Mobile Optimized) -->
    <div id="ptr-indicator" class="fixed top-[-80px] left-0 w-full h-[80px] flex items-center justify-center z-[9999] pointer-events-none" style="transition: transform 0.25s cubic-bezier(0.2, 0.8, 0.2, 1); will-change: transform; backface-visibility: hidden;">
        <div class="bg-white/95 backdrop-blur-md p-3 rounded-full shadow-muji border border-muji-edge flex items-center justify-center transform-gpu">
            <svg id="ptr-icon" class="w-6 h-6 text-muji-ash will-change-transform" style="backface-visibility: hidden;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.6" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
        </div>
    </div>

    <script>
        (function () {
            let startY = 0; let startX = 0; let pullDist = 0; let active = false; let isGestureCancelled = false;
            const threshold = 85;
            const indicator = document.getElementById('ptr-indicator');
            const icon = document.getElementById('ptr-icon');

            // 輔助函式：檢查目前是否有任何 Modal 開啟
            const isModalOpen = () => {
                const modals = ['loginModal', 'registerModal', 'globalProfileConfigModal', 'mapModal', 'expenseModal', 'daySummaryEditModal', 'eventDetailsModal', 'tripTransportModal', 'tripSettingsModal', 'add-trip-modal'];
                return modals.some(id => {
                    const m = document.getElementById(id);
                    return m && !m.classList.contains('hidden') && m.style.display !== 'none';
                });
            };

            document.addEventListener('touchstart', (e) => {
                // 如果在 Modal 內，或不在頁面頂端，則不啟動重整
                if (window.scrollY === 0 && !isModalOpen()) {
                    startY = e.touches[0].pageY;
                    startX = e.touches[0].pageX;
                    active = true;
                    isGestureCancelled = false;
                    indicator.style.transition = 'none';
                } else {
                    active = false;
                }
            }, { passive: true });

            document.addEventListener('touchmove', (e) => {
                if (!active || isGestureCancelled || window.scrollY > 0 || isModalOpen()) return;

                const currentY = e.touches[0].pageY;
                const currentX = e.touches[0].pageX;
                const diffY = currentY - startY;
                const diffX = Math.abs(currentX - startX);

                if (diffX > Math.abs(diffY) && diffX > 10) {
                    isGestureCancelled = true;
                    active = false;
                    indicator.style.transform = 'translate3d(0, 0, 0)';
                    return;
                }

                if (diffY > 0) {
                    if (diffY < 15) {
                        indicator.style.transform = 'translate3d(0, 0, 0)';
                        return;
                    }

                    pullDist = (diffY - 15) * 0.45;
                    indicator.style.transform = `translate3d(0, ${Math.min(pullDist, 140)}px, 0)`;

                    const scale = 1 + (pullDist / threshold) * 0.2;
                    icon.style.transform = `rotate(${diffY * 0.6}deg) scale(${scale})`;

                    if (pullDist > threshold) {
                        if (!icon._hapt && window.navigator.vibrate) {
                            window.navigator.vibrate(12);
                            icon._hapt = true;
                        }
                    } else {
                        icon._hapt = false;
                    }
                    if (diffY > 10) e.preventDefault();
                }
            }, { passive: false });

            document.addEventListener('touchend', () => {
                indicator.style.transition = 'transform 0.4s cubic-bezier(0.2, 0.8, 0.2, 1)';
                indicator.style.transform = 'translate3d(0, 0, 0)';

                if (active && !isGestureCancelled && pullDist > threshold) {
                    icon.classList.add('animate-spin');
                    setTimeout(() => window.location.reload(), 200);
                } else {
                    icon.style.transform = 'rotate(0deg) scale(1)';
                }
                startY = 0; startX = 0; pullDist = 0; active = false; isGestureCancelled = false;
            });
        })();
    </script>

    @php
        // Background Resolution Logic (centralized)
        if (isset($user) && !empty($user->background_image)) {
            $bgOwner = $user;
        } elseif (auth()->check() && !empty(auth()->user()->background_image)) {
            $bgOwner = auth()->user();
        } else {
            $bgOwner = $user ?? auth()->user() ?? (isset($trip) ? $trip->user : null);
        }

        // 1. Pick the raw background value
        $rawBg = 'bg.jpg';
        if (isset($trip) && !empty($trip->cover_image)) {
            $rawBg = $trip->cover_image;
        } elseif ($bgOwner && !empty($bgOwner->background_image)) {
            $rawBg = $bgOwner->background_image;
        }

        // 2. Resolve URL
        $bgUrl = $rawBg;
        if (!str_starts_with($bgUrl, 'http')) {
            $cleanPath = ltrim($bgUrl, '/');
            if ($cleanPath !== 'bg.jpg' && !str_starts_with($cleanPath, 'storage/')) {
                $bgUrl = asset('storage/' . $cleanPath);
            } else {
                $bgUrl = asset($cleanPath);
            }
        }

        // 3. Resolve Visual Settings (prioritize the host/owner profile config)
        $bgOpacityValue = ($bgOwner && isset($bgOwner->bg_opacity)) ? ($bgOwner->bg_opacity / 100) : 0.4;
        $bgBlurValue = ($bgOwner && isset($bgOwner->bg_blur)) ? $bgOwner->bg_blur : 5;
        $bgWidthValue = ($bgOwner && isset($bgOwner->bg_width)) ? $bgOwner->bg_width : 45;
    @endphp

    <style id="bg-live-styles">
        :root {
            --bg-opacity: {{ $bgOpacityValue }};
            --bg-blur: {{ $bgBlurValue }}px;
            --bg-width: {{ $bgWidthValue }}%;
        }

        html, body {
            background-color: transparent !important;
        }

        @media (max-width: 768px) {
            :root {
                --bg-width: 100% !important;
            }

            #global-bg-element {
                left: 0 !important;
                transform: none !important;
            }
        }
    </style>

    <!-- Global Background Wrapper -->
    <div id="bg-wrapper" class="fixed inset-0 z-[-1] overflow-hidden pointer-events-none select-none" style="background-color: #f5f4f2;">
        <div id="global-bg-element" class="absolute inset-y-0 bg-cover bg-center bg-no-repeat transition-all duration-700 ease-in-out" style="background-image: url('{{ $bgUrl }}'); 
                    opacity: var(--bg-opacity, {{ $bgOpacityValue }}); 
                    filter: blur(var(--bg-blur, {{ $bgBlurValue }}px));
                    width: var(--bg-width, {{ $bgWidthValue }}%);
                    left: 50%;
                    transform: translateX(-50%);"></div>
    </div>

    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 z-[2000] overflow-y-auto" style="display: none;" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="safeCloseModal('loginModal')"></div>
            <div class="relative transform overflow-hidden muji-glass rounded-[40px] w-full max-w-lg p-8 shadow-2xl flex flex-col transition-all">
                <div class="flex justify-between items-center mb-8">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-muji-base rounded-xl text-muji-oak shadow-muji-sm">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <div class="flex flex-col border-l-2 border-muji-edge pl-4 text-left">
                            <h3 class="text-2xl font-black text-muji-ink m-0">登入</h3>
                        </div>
                    </div>
                    <button onclick="safeCloseModal('loginModal')" class="p-2 rounded-full text-muji-ash hover:bg-muji-base transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('login.post') }}" method="POST" onsubmit="handleAjaxSubmit(event, this, 'loginModal')">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-muji-ash text-left mb-2">電子郵件</label>
                            <input type="email" name="email" required class="block w-full px-4 py-3 muji-input" placeholder="例如：elk@example.com">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-muji-ash text-left mb-2">密碼</label>
                            <input type="password" name="password" autocomplete="current-password" required class="block w-full px-4 py-3 muji-input" placeholder="請輸入密碼">
                        </div>

                        <div class="flex gap-4 pt-6 mt-4 border-t border-muji-edge/50">
                            <button type="button" onclick="safeCloseModal('loginModal')" class="flex-1 h-[46px] flex items-center justify-center bg-muji-paper text-muji-ash border border-muji-edge font-black rounded-[24px] hover:bg-muji-base transition-all active:scale-95 text-sm">
                                取消
                            </button>
                            <button type="submit" class="flex-1 h-[46px] flex items-center justify-center bg-muji-oak text-white font-black rounded-[24px] hover:opacity-90 transition-all shadow-muji active:scale-95 text-sm">
                                登入
                            </button>
                        </div>

                        <div class="text-sm text-center mt-6 text-muji-ash pt-6 border-t border-muji-edge">
                            還沒有帳號？ <button type="button" onclick="safeCloseModal('loginModal'); setTimeout(() => safeOpenModal('registerModal'), 300)" class="text-muji-oak font-black hover:underline">立即註冊</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="registerModal" class="fixed inset-0 z-[2000] overflow-y-auto" style="display: none;" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="safeCloseModal('registerModal')"></div>
            <div class="relative transform overflow-hidden muji-glass rounded-[40px] w-full max-w-lg p-8 shadow-2xl flex flex-col transition-all">
                <div class="flex justify-between items-center mb-8">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-muji-base rounded-xl text-muji-oak shadow-muji-sm">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <div class="flex flex-col border-l-2 border-muji-edge pl-4 text-left">
                            <h3 class="text-2xl font-black text-muji-ink m-0">建立帳號</h3>
                        </div>
                    </div>
                    <button onclick="safeCloseModal('registerModal')" class="p-2 rounded-full text-muji-ash hover:bg-muji-base transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="max-h-[70vh] overflow-y-auto pr-2 custom-scrollbar">
                    <form action="{{ route('register.post') }}" method="POST" onsubmit="handleAjaxSubmit(event, this, 'registerModal')">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-muji-ash text-left mb-2">姓名</label>
                                <input type="text" name="name" required class="block w-full px-4 py-3 muji-input" placeholder="例如：王小明">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-muji-ash text-left mb-2">帳號 ID</label>
                                <input type="text" name="username" required class="block w-full px-4 py-3 muji-input" placeholder="例如：elk_trip" pattern="[a-zA-Z0-9_\-]+">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-muji-ash text-left mb-2">電子郵件</label>
                                <input type="email" name="email" required class="block w-full px-4 py-3 muji-input" placeholder="例如：elk@example.com">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <input type="password" name="password" required placeholder="請輸入密碼" class="block w-full px-4 py-3 muji-input">
                                <input type="password" name="password_confirmation" required placeholder="請再次輸入密碼" class="block w-full px-4 py-3 muji-input">
                            </div>

                            <div class="flex gap-4 pt-6 mt-4 border-t border-muji-edge/50">
                                <button type="button" onclick="safeCloseModal('registerModal')" class="flex-1 h-[46px] flex items-center justify-center bg-muji-paper text-muji-ash border border-muji-edge font-black rounded-[24px] hover:bg-muji-base transition-all active:scale-95 text-sm">
                                    取消
                                </button>
                                <button type="submit" class="flex-1 h-[46px] flex items-center justify-center bg-muji-oak text-white font-black rounded-[24px] hover:opacity-90 transition-all shadow-muji active:scale-95 text-sm">
                                    註冊
                                </button>
                            </div>

                            <div class="text-sm text-center mt-6 mb-4 text-muji-ash pt-6 border-t border-muji-edge">
                                已經有帳號了？ <button type="button" onclick="safeCloseModal('registerModal'); setTimeout(() => safeOpenModal('loginModal'), 300)" class="text-muji-oak font-black hover:underline">返回登入</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Profile Settings Modal (Global) -->
    @include('profile_modal_partial')

    <header class="bg-muji-paper/90 border-b border-muji-edge sticky top-0 z-50 backdrop-blur-md w-full overflow-hidden">
        <div class="max-w-4xl mx-auto px-2 sm:px-6 lg:px-8 h-16 flex items-center justify-between overflow-hidden">
            <a href="{{ auth()->check() ? route('home', ['user' => auth()->user()]) : '/' }}" class="flex items-center gap-2 group">
                <div class="w-10 h-10 rounded-lg bg-muji-base flex items-center justify-center shadow-muji-sm group-hover:scale-105 transition-transform overflow-hidden relative">
                    <img src="/icon_logo.png?v={{ time() }}" alt="Logo" class="w-full h-full object-contain p-1 z-10 relative">
                </div>
                <h1 class="text-sm sm:text-xl font-bold text-muji-ink truncate max-w-[120px] xs:max-w-[200px] sm:max-w-none">
                    {{ isset($trip) ? $trip->name : 'Trip Planner' }}
                </h1>
            </a>

            <nav class="flex items-center justify-end gap-1 sm:gap-4 text-muji-ash font-black">
                <!-- Desktop View (Visible on sm or larger) -->
                <div class="hidden sm:flex items-center gap-4">
                    @if(isset($trip))
                        @if($isShared)
                            <a href="{{ route('trip.index_shared', ['token' => $trip->share_token]) }}" class="flex items-center gap-2 px-3 py-1.5 rounded-xl hover:bg-muji-base transition-all {{ request()->routeIs('trip.index_shared') ? 'text-muji-ink bg-muji-base' : '' }}">
                                <span class="text-xs">總覽</span>
                            </a>
                            <a href="{{ route('expenses.index_shared', ['token' => $trip->share_token]) }}" class="flex items-center gap-2 px-3 py-1.5 rounded-xl hover:bg-muji-base transition-all {{ request()->routeIs('expenses.index_shared') ? 'text-muji-ink bg-muji-base' : '' }}">
                                <span class="text-xs">花費統計</span>
                            </a>
                        @else
                            <a href="{{ route('trip.show', ['user' => $trip->user, 'trip' => $trip]) }}" class="flex items-center gap-2 px-3 py-1.5 rounded-xl hover:bg-muji-base transition-all {{ request()->routeIs('trip.show') ? 'text-muji-ink bg-muji-base' : '' }}">
                                <span class="text-xs">旅程計劃</span>
                            </a>
                            <a href="{{ route('expenses.index', ['user' => $trip->user, 'trip' => $trip]) }}" class="flex items-center gap-2 px-3 py-1.5 rounded-xl hover:bg-muji-base transition-all {{ request()->routeIs('expenses.index') ? 'text-muji-ink bg-muji-base' : '' }}">
                                <span class="text-xs">花費統計</span>
                            </a>
                        @endif
                    @endif

                    @if(!$isShared)
                        @auth
                            <a href="{{ route('feedback.index', ['user' => auth()->user()]) }}" class="flex items-center gap-2 px-3 py-1.5 rounded-xl hover:bg-muji-base transition-all {{ request()->routeIs('feedback.index') ? 'text-muji-ink bg-muji-base' : '' }}">
                                <span class="text-xs">產品意見回饋</span>
                            </a>
                            <a href="{{ route('home', ['user' => auth()->user()]) }}" class="flex items-center gap-2 px-3 py-1.5 rounded-xl hover:bg-muji-base transition-all {{ request()->routeIs('home') ? 'text-muji-ink bg-muji-base' : '' }}">
                                <span class="text-xs">旅程足跡</span>
                            </a>
                            <button type="button" onclick="safeOpenModal('globalProfileConfigModal')" class="flex items-center gap-2 group border-0 bg-transparent cursor-pointer">
                                <div class="w-8 h-8 rounded-full overflow-hidden border border-muji-edge shadow-muji-sm group-hover:scale-110 transition-transform">
                                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=9c8c7c&color=fff' }}" class="w-full h-full object-cover">
                                </div>
                            </button>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-2 text-muji-ash hover:text-red-500 transition-colors border-0 bg-transparent cursor-pointer">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                    </svg>
                                </button>
                            </form>
                        @else
                            <button type="button" onclick="safeOpenModal('loginModal')" class="px-4 py-2 text-xs font-black text-muji-ash hover:text-muji-ink transition-all">登入</button>
                            <button type="button" onclick="safeOpenModal('registerModal')" class="px-4 py-2 text-xs font-black bg-muji-oak text-white rounded-xl shadow-muji active:scale-95 transition-all">註冊帳號</button>
                        @endauth
                    @endif
                </div>

                <!-- Mobile View Trigger (Visible only on mobile) -->
                <div class="flex sm:hidden items-center gap-2">
                    @if(isset($trip) && (request()->routeIs('expenses.index') || request()->routeIs('expenses.index_shared')))
                        <a href="{{ $isShared ? route('trip.index_shared', ['token' => $trip->share_token]) : route('trip.show', ['user' => $trip->user, 'trip' => $trip]) }}" class="p-2.5 bg-muji-base rounded-2xl text-muji-ink active:scale-90 transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </a>
                    @endif
                    <button type="button" onclick="toggleMobileMenu(true)" class="p-2.5 text-muji-ash active:scale-90 transition-transform">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                    </button>
                </div>
            </nav>
        </div>
    </header>

    <!-- Mobile Side Menu (Slider - Vanilla JS Version) -->
    <div id="mobileMenuBackdrop" class="fixed inset-0 z-[2000] sm:hidden bg-muji-ink/30 backdrop-blur-sm" onclick="toggleMobileMenu(false)"></div>

    <div id="mobileMenuDrawer" class="fixed right-0 top-0 bottom-0 z-[2001] w-[85%] max-w-sm bg-muji-paper shadow-2xl flex flex-col sm:hidden rounded-l-[32px] overflow-hidden">
        <div class="p-6 flex items-center justify-between border-b border-muji-edge bg-white/50 backdrop-blur-sm">
            <span class="text-xs font-black text-muji-ash uppercase tracking-widest">選單導覽</span>
            <button onclick="toggleMobileMenu(false)" class="p-2 text-muji-ash active:rotate-90 transition-transform">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-6">
            <!-- User Section -->
            @auth
                <div class="p-5 rounded-[2rem] bg-muji-base/30 border border-muji-edge/50">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-white shadow-muji">
                            <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=9c8c7c&color=fff' }}" class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base font-black text-muji-ink truncate">{{ auth()->user()->name }}</h3>
                            <p class="text-[10px] text-muji-ash/70 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>
            @endauth

            <!-- Navigation Links -->
            @if(auth()->check() || isset($trip))
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-muji-ash/40 uppercase tracking-[0.2em] pl-4 mb-2">計畫行程</p>
                    @auth
                        <a href="{{ route('home', ['user' => auth()->user()]) }}" class="flex items-center gap-4 p-4 rounded-2xl hover:bg-muji-base transition-all {{ request()->routeIs('home') ? 'bg-muji-wheat/30 text-muji-ink' : 'text-muji-ash' }}">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="text-sm font-bold">旅程足跡</span>
                        </a>
                    @endauth

                    @if(isset($trip))
                        <a href="{{ $isShared ? route('expenses.index_shared', ['token' => $trip->share_token]) : route('expenses.index', ['user' => $trip->user, 'trip' => $trip]) }}" class="flex items-center gap-4 p-4 rounded-2xl hover:bg-muji-base transition-all {{ request()->routeIs('expenses.index*') ? 'bg-muji-wheat/30 text-muji-ink' : 'text-muji-ash' }}">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-bold">花費統計</span>
                        </a>
                    @endif

                    @auth
                        <a href="{{ route('feedback.index', ['user' => auth()->user()]) }}" class="flex items-center gap-4 p-4 rounded-2xl hover:bg-muji-base transition-all {{ request()->routeIs('feedback.index') ? 'bg-muji-wheat/30 text-muji-ink' : 'text-muji-ash' }}">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <span class="text-sm font-bold">產品意見回饋</span>
                        </a>
                    @endauth
                </div>
            @endif

            <div class="pt-10 mt-6 space-y-2 relative">
                <p class="text-[10px] font-black text-muji-ash/40 uppercase tracking-[0.2em] pl-4 mb-2">帳戶設定</p>
                @auth
                    <button onclick="toggleMobileMenu(false); safeOpenModal('globalProfileConfigModal')" class="w-full flex items-center gap-4 p-4 rounded-2xl hover:bg-muji-base text-muji-ink transition-all text-left border-0 bg-transparent cursor-pointer">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-sm font-bold">帳號與設定</span>
                    </button>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-4 p-4 rounded-2xl hover:bg-red-50 text-red-500 transition-all text-left border-0 bg-transparent cursor-pointer">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                            <span class="text-sm font-black">登出此帳號</span>
                        </button>
                    </form>
                @else
                    <div class="flex flex-col gap-3 py-2">
                        <button onclick="toggleMobileMenu(false); safeOpenModal('loginModal')" class="w-full flex items-center gap-4 p-4 rounded-2xl bg-muji-base/50 text-muji-ink hover:bg-muji-base transition-all text-left border-0 cursor-pointer group">
                            <div class="p-2 bg-white rounded-xl shadow-muji-sm text-muji-oak group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                            </div>
                            <span class="text-sm font-black uppercase tracking-widest">登入帳號</span>
                        </button>
                        <button onclick="toggleMobileMenu(false); safeOpenModal('registerModal')" class="w-full flex items-center gap-4 p-4 rounded-2xl bg-muji-oak text-white transition-all text-left shadow-muji active:scale-[0.98] border-0 cursor-pointer group">
                            <div class="p-2 bg-white/20 rounded-xl text-white group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </div>
                            <span class="text-sm font-black uppercase tracking-widest">註冊新帳號</span>
                        </button>
                    </div>
                @endauth
            </div>
        </div>

        <div class="p-8 text-center bg-muji-base/10">
            <p class="text-[10px] text-muji-ash/40 font-mono tracking-[0.3em] uppercase">Muji Aesthetic v2.1</p>
        </div>
    </div>

    <main class="flex-grow w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-10">
        @yield('content')
    </main>

    <footer class="bg-muji-paper/70 border-t border-muji-edge py-8 mt-auto backdrop-blur-sm">
        <div class="max-w-4xl mx-auto px-4 text-center text-muji-ash text-xs font-black tracking-widest">
            <p>2026 &copy; Elk's Trip Planner</p>
        </div>
    </footer>

    <!-- Map Modal -->
    <div id="mapModal" class="fixed inset-0 z-[2000] hidden overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="safeCloseModal('mapModal')"></div>
            <div class="relative transform overflow-hidden muji-glass rounded-[40px] w-full max-w-4xl h-[80vh] shadow-2xl flex flex-col transition-all p-8">
                <div class="flex justify-between items-center mb-6 px-2">
                    <h3 id="mapTitle" class="text-2xl font-black text-muji-ink truncate pr-8">地點 (地圖)</h3>
                    <button onclick="safeCloseModal('mapModal')" class="absolute top-8 right-8 p-2 rounded-full text-muji-ash hover:bg-muji-base transition-all group z-50">
                        <svg class="w-6 h-6 group-hover:rotate-90 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex-grow w-full h-full bg-muji-base/20 rounded-2xl overflow-hidden relative border border-muji-edge">
                    <iframe id="mapFrame" class="absolute inset-0 w-full h-full" frameborder="0" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>


    <!-- Floating Action Button Speed Dial -->
    @if(isset($trip) && !$isShared)
        <div class="fixed bottom-6 right-6 flex flex-col items-end gap-3 z-[100]" id="speedDial">
            <div id="speedDialMenu" class="hidden flex flex-col items-end gap-3 mb-1 animate-in slide-in-from-bottom-4 fade-in duration-200">
                @if(request()->routeIs('day.show') || request()->routeIs('day.show_shared'))
                    @auth
                        <button onclick="toggleSpeedDial(); if(typeof openEventModal === 'function') { openEventModal(); } else { safeOpenModal('eventDetailsModal'); }" class="flex items-center gap-2 group">
                            <span class="bg-muji-ink text-white text-[10px] font-black px-2 py-1 rounded shadow-muji-sm opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap uppercase tracking-widest">新增行程</span>
                            <div class="w-12 h-12 bg-white text-muji-oak rounded-full shadow-muji border border-muji-edge flex items-center justify-center hover:bg-muji-base transition-all hover:scale-105">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </button>
                    @endauth
                @endif
                <button onclick="toggleSpeedDial(); safeOpenModal('expenseModal');" class="flex items-center gap-2 group">
                    <span class="bg-muji-ink text-white text-[10px] font-black px-2 py-1 rounded shadow-muji-sm opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap uppercase tracking-widest">記錄消費</span>
                    <div class="w-12 h-12 bg-white text-muji-oak rounded-full shadow-muji border border-muji-edge flex items-center justify-center hover:bg-muji-base transition-all hover:scale-105">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </button>
            </div>
            <button onclick="toggleSpeedDial()" id="speedDialMain" class="w-14 h-14 bg-muji-oak text-white rounded-full shadow-muji hover:bg-[#8a7b6c] transition-all hover:scale-105 flex items-center justify-center relative transform rotate-0 duration-300">
                <svg class="w-6 h-6 transition-transform duration-300" id="plusIcon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </button>
        </div>
    @endif

    <!-- Expense Modal -->
    <div id="expenseModal" class="fixed inset-0 z-[2000] hidden overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="safeCloseModal('expenseModal')"></div>
            <div class="relative transform overflow-hidden muji-glass rounded-[40px] w-full max-w-lg shadow-2xl flex flex-col transition-all max-h-[90vh]">
                <!-- 統一右上角關閉按鈕 (X) - 移出捲軸容器 -->
                <button onclick="safeCloseModal('expenseModal')" class="absolute top-6 right-6 text-muji-ash hover:text-muji-oak p-2 rounded-full hover:bg-muji-base transition-all group z-50">
                    <svg class="w-6 h-6 group-hover:rotate-90 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="px-5 sm:px-8 py-8 sm:py-10 overflow-y-auto custom-scrollbar scroll-smooth">
                    <div class="flex justify-between items-start mb-8 sm:mb-10 text-left">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-muji-base rounded-2xl text-muji-oak shadow-muji-sm">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex flex-col border-l-2 border-muji-edge pl-4">
                                <h3 class="text-3xl font-black text-muji-ink leading-tight">花費紀錄</h3>
                                <p class="text-[10px] font-bold text-muji-ash uppercase tracking-[0.2em] mt-1">管理您的旅程預算</p>
                            </div>
                        </div>
                    </div>

                    @auth
                        <form id="expenseForm" action="{{ isset($trip) ? route('expenses.store', ['user' => $trip->user, 'trip' => $trip]) : '#' }}" method="POST" onsubmit="handleAjaxSubmit(event, this, 'expenseModal')">
                            @csrf
                            <div id="methodField"></div>
                            <div class="space-y-6">
                                <div>
                                    <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">支出項目說明</label>
                                    <input type="text" id="expenseDescription" name="description" required class="w-full px-4 py-3 muji-input" placeholder="例如：午餐拉麵">
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">支出金額</label>
                                        <div class="flex relative h-[46px] rounded-xl border border-muji-edge overflow-hidden focus-within:ring-2 focus-within:ring-muji-oak muji-input group transition-all">
                                            <select id="expenseCurrency" name="is_base_currency" class="bg-muji-base h-full border-0 border-r border-muji-edge px-3 text-muji-ink font-black text-xs focus:ring-0 cursor-pointer appearance-none">
                                                <option value="0">{{ isset($trip) ? $trip->target_currency : '當地幣' }}</option>
                                                <option value="1">{{ isset($trip) ? $trip->base_currency : '本國幣' }}</option>
                                            </select>
                                            <input type="number" step="0.01" id="expenseAmount" name="amount" required class="flex-1 w-full h-full border-0 bg-transparent focus:ring-0 px-4 font-mono text-muji-ink font-black" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">支出類別</label>
                                        <select id="expenseCategory" name="category" required class="block w-full h-[46px] px-4 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak font-black appearance-none cursor-pointer">
                                            <option value="Food">飲食 🍔</option>
                                            <option value="Transport">交通 🚇</option>
                                            <option value="Shopping">購物 🛍️</option>
                                            <option value="Accommodation">住宿 🏨</option>
                                            <option value="Flight">機票 ✈️</option>
                                            <option value="Entertainment">娛樂 🎡</option>
                                            <option value="Other">其他 💡</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-muji-ash mb-2">支出日期</label>
                                    <input type="date" id="expenseDate" name="date" required value="{{ date('Y-m-d') }}" class="block w-full h-[46px] px-4 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium py-0 leading-none">
                                </div>

                                <div class="pt-8 mt-8 border-t border-muji-edge flex gap-4">
                                    <button type="button" onclick="safeCloseModal('expenseModal')" class="flex-1 h-[46px] flex items-center justify-center border border-muji-edge rounded-[24px] text-muji-ash bg-muji-paper hover:bg-muji-base transition-colors font-black text-sm">
                                        取消
                                    </button>
                                    <button type="submit" id="expenseSubmitBtn" class="flex-1 h-[46px] flex items-center justify-center border border-transparent rounded-[24px] text-white bg-muji-oak hover:opacity-90 shadow-muji transition-all font-black active:scale-95 text-sm">
                                        儲存花費
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Metro Map Modal -->
    <div id="metroModal" class="fixed inset-0 z-[2000] hidden overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center p-0 text-center">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity" onclick="safeCloseModal('metroModal')"></div>
            <div class="relative transform overflow-hidden bg-white rounded-[40px] w-full max-w-6xl h-[90vh] shadow-2xl flex flex-col transition-all border border-muji-edge">
                <div class="flex justify-between items-center px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 leading-none">大阪地鐵圖 (中文 PDF)</h3>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="/osakametro_rosenzu_20250404.pdf" target="_blank" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            全螢幕 Full Screen
                        </a>
                        <button onclick="safeCloseModal('metroModal')" class="p-2 rounded-full hover:bg-gray-200 transition-colors">
                            <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex-grow w-full bg-gray-200 relative">
                    <iframe src="/osakametro_rosenzu_20250404.pdf" class="absolute inset-0 w-full h-full" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>

    @stack('modals')

    <script>
        function previewUserAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        // Scroll Lock Helpers to prevent layout shift


        // Global Utility Redirects
        function openLoginModal() { safeOpenModal('loginModal'); }
        function closeLoginModal() { safeCloseModal('loginModal'); }
        function openRegisterModal() { safeOpenModal('registerModal'); }
        function closeRegisterModal() { safeCloseModal('registerModal'); }
        function showGlobalUserConfig() { safeOpenModal('globalProfileConfigModal'); }
        function hideGlobalUserConfig() { safeCloseModal('globalProfileConfigModal'); window.location.reload(); }

        function lockScroll() {
            try {
                const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;
                if (scrollBarWidth > 0) {
                    document.body.style.paddingRight = scrollBarWidth + 'px';
                    const speedDial = document.getElementById('speedDial');
                    if (speedDial) {
                        const currentRight = parseInt(window.getComputedStyle(speedDial).right) || 24;
                        speedDial.style.right = (currentRight + scrollBarWidth) + 'px';
                        speedDial.dataset.originalRight = currentRight;
                    }
                }
                document.body.style.overflow = 'hidden';
            } catch (e) { }
        }

        function unlockScroll() {
            try {
                document.body.style.paddingRight = '';
                document.body.style.overflow = '';
                const speedDial = document.getElementById('speedDial');
                if (speedDial && speedDial.dataset.originalRight !== undefined) {
                    speedDial.style.right = '';
                    delete speedDial.dataset.originalRight;
                }
            } catch (e) { }
        }

        // Map Modal Functions
        function openMap(query) {
            const frame = document.getElementById('mapFrame');
            const title = document.getElementById('mapTitle');
            if (!frame || !title) return;

            title.textContent = query;
            const encoded = encodeURIComponent(query);
            frame.src = `https://maps.google.com/maps?q=${encoded}&z=15&output=embed`;
            safeOpenModal('mapModal');
        }

        function openDayEditModal() {
            safeOpenModal('daySummaryEditModal');
        }

        function closeDayEditModal() {
            safeCloseModal('daySummaryEditModal');
        }

        function closeMap() {
            safeCloseModal('mapModal');
            const f = document.getElementById('mapFrame');
            if (f) f.src = '';
        }

        // Expense Modal Functions
        function openExpenseModal(data = null) {
            @auth
                                                                                const modal = document.getElementById('expenseModal');
                if (!modal) return;

                const form = document.getElementById('expenseForm');
                const methodField = document.getElementById('methodField');
                const submitBtn = document.getElementById('expenseSubmitBtn');
                const modalTitleLabel = document.querySelector('#expenseModal h3');

                if (data) {
                    form.action = `/{{ isset($trip) ? $trip->user->username : (auth()->user()->username ?? 'guest') }}/expenses/${data.id}`;
                    methodField.innerHTML = '@method("PUT")';
                    if (modalTitleLabel) modalTitleLabel.textContent = '編輯花費';
                    if (submitBtn) submitBtn.textContent = '更新';

                    document.getElementById('expenseDescription').value = data.description;
                    document.getElementById('expenseAmount').value = data.amount;
                    document.getElementById('expenseCategory').value = data.category;
                    document.getElementById('expenseCurrency').value = data.is_base_currency == 1 ? '1' : '0';
                    const dateObj = new Date(data.date);
                    document.getElementById('expenseDate').value = dateObj.toISOString().split('T')[0];
                } else {
                    form.action = "{{ isset($trip) ? route('expenses.store', ['user' => $trip->user, 'trip' => $trip]) : '#' }}";
                    methodField.innerHTML = '';
                    if (modalTitleLabel) modalTitleLabel.textContent = '新增花費';
                    if (submitBtn) submitBtn.textContent = '儲存';
                    form.reset();
                    document.getElementById('expenseDate').value = new Date().toISOString().split('T')[0];
                    document.getElementById('expenseCurrency').value = '0';
                }
                safeOpenModal('expenseModal');
            @else
                openLoginModal();
            @endauth
        }

        function openExpenseModalWithDate(date) {
            openExpenseModal();
            setTimeout(() => {
                const dateInput = document.getElementById('expenseDate');
                if (dateInput) dateInput.value = date;
            }, 50);
        }

        function closeExpenseModal() { safeCloseModal('expenseModal'); }

        function previewBackground() {
            const opacity = document.getElementById('range-bg-opacity').value;
            const blur = document.getElementById('range-bg-blur').value;
            const width = document.getElementById('range-bg-width').value;

            // Update Labels
            document.getElementById('val-bg-opacity').textContent = opacity + '%';
            document.getElementById('val-bg-blur').textContent = blur + 'px';
            document.getElementById('val-bg-width').textContent = width + '%';

            // Update CSS Variables
            document.documentElement.style.setProperty('--bg-opacity', opacity / 100);
            document.documentElement.style.setProperty('--bg-blur', blur + 'px');
            document.documentElement.style.setProperty('--bg-width', width + '%');
        }

        function resetBgDefaults() {
            document.getElementById('range-bg-opacity').value = 40;
            document.getElementById('range-bg-blur').value = 5;
            document.getElementById('range-bg-width').value = 45;
            previewBackground();
        }

        // Metro Modal Functions
        let currentZoom = 1;

        function openMetroModal() {
            const m = document.getElementById('metroModal');
            m.classList.remove('hidden');
            m.style.setProperty('display', 'block', 'important');
            lockScroll();
            resetZoom();
        }
        function closeMetroModal() {
            const m = document.getElementById('metroModal');
            m.classList.add('hidden');
            m.style.display = 'none';
            unlockScroll();
        }

        function zoomMap(delta) {
            currentZoom += delta;
            if (currentZoom < 0.2) currentZoom = 0.2;
            if (currentZoom > 3) currentZoom = 3;
            updateMapZoom();
        }
        function resetZoom() {
            currentZoom = 1;
            updateMapZoom();
        }

        function updateMapZoom() {
            const img = document.getElementById('metroMapImg');
            img.style.transform = `scale(${currentZoom})`;

            // Adjust transform origin based on scroll if needed, but top-left is usually safest for scrolling
            // Or center it? Let's stick to simple scaling for now.
            // Actually, scaling usually requires updating the container scroll or margin.
            // A simpler way for "overflow-auto" is to set width percentage or pixel width.

            // Let's change strategy: Update Width instead of Transform Scale to allow native scrolling
            if (currentZoom === 1) {
                img.style.width = 'auto';
                img.style.height = 'auto';
                img.style.transform = 'none';
            } else {
                img.style.transform = `scale(${currentZoom})`;
                img.style.transformOrigin = 'top left'; // anchors it so it grows down/right into the scroll area

                // We need to ensure the container knows the new size.
                // CSS Scale doesn't affect flow layout. 
                // Better to set width directly if possible? 
                // No, transforming is smoother. But scrolling might be weird.
                // Let's try transform-origin top left.
            }
        }

        // Speed Dial Toggle
        function toggleSpeedDial() {
            const menu = document.getElementById('speedDialMenu');
            const icon = document.getElementById('plusIcon');
            const mainBtn = document.getElementById('speedDialMain');

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                icon.classList.add('rotate-45');
                mainBtn.classList.add('bg-muji-ink');
                mainBtn.classList.remove('bg-muji-oak');
            } else {
                menu.classList.add('hidden');
                icon.classList.remove('rotate-45');
                mainBtn.classList.remove('bg-muji-ink');
                mainBtn.classList.add('bg-muji-oak');
            }
        }

        // Close on escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === "Escape") {
                closeMetroModal();
                closeMap();
                closeExpenseModal();
                closeLoginModal();
                const menu = document.getElementById('speedDialMenu');
                if (menu && !menu.classList.contains('hidden')) toggleSpeedDial();
            }
        });

        // Close Speed Dial when clicking outside
        document.addEventListener('click', function (event) {
            const dial = document.getElementById('speedDial');
            const menu = document.getElementById('speedDialMenu');
            if (dial && !dial.contains(event.target) && menu && !menu.classList.contains('hidden')) {
                toggleSpeedDial();
            }
        });
        function confirmDelete(title, text, formId) {
            Swal.fire({
                title: title || '確定要刪除嗎？',
                text: text || '此操作無法復原，請確認！',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '是的，刪除它！',
                cancelButtonText: '取消',
                background: '#ffffff',
                borderRadius: '1.5rem',
                customClass: {
                    title: 'text-2xl font-bold text-gray-900',
                    confirmButton: 'rounded-xl px-6 py-3 font-bold',
                    cancelButton: 'rounded-xl px-6 py-3 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
            return false;
        }

        function confirmAction(title, text, formId, icon = 'question', confirmText = '是的') {
            Swal.fire({
                title: title || '確認執行？',
                text: text || '請確認是否執行此動作？',
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: '#9c8c7c',
                cancelButtonColor: '#757575',
                confirmButtonText: confirmText,
                cancelButtonText: '取消',
                background: '#f8f5f0',
                borderRadius: '1.25rem',
                customClass: {
                    title: 'text-2xl font-bold text-muji-ink',
                    confirmButton: 'rounded-xl px-6 py-3 font-bold',
                    cancelButton: 'rounded-xl px-6 py-3 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
            return false;
        }

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // --- 全域手機選單控制器 (純 JS) ---
        function toggleMobileMenu(isOpen) {
            const drawer = document.getElementById('mobileMenuDrawer');
            const backdrop = document.getElementById('mobileMenuBackdrop');
            if (isOpen) {
                drawer.classList.add('active');
                backdrop.classList.add('active');
                document.body.style.overflow = 'hidden';
            } else {
                drawer.classList.remove('active');
                backdrop.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        @if(session('success'))
            Toast.fire({ icon: 'success', title: '{{ session('success') }}' });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: '糟糕！', text: '{{ session('error') }}', borderRadius: '1.5rem' });
        @endif
        @if($errors->any())
            Swal.fire({ icon: 'error', title: 'Validation Error', text: '{{ $errors->first() }}', borderRadius: '1.5rem' });
        @endif

            // --- 零依賴 Canvas 影像壓縮大招 (核心修復：解決 iPhone 照片過大上傳失敗) ---
            async function compressImage(file, maxWidth = 1600, quality = 0.85) {
                if (!file || !file.type.startsWith('image/')) return file;
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            let width = img.width; let height = img.height;
                            if (width > maxWidth) { height = (maxWidth / width) * height; width = maxWidth; }
                            canvas.width = width; canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);
                            canvas.toBlob((blob) => {
                                resolve(new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", { type: 'image/jpeg', lastModified: Date.now() }));
                            }, 'image/jpeg', quality);
                        };
                        img.src = e.target.result;
                    };
                });
            }

            // --- Global AJAX Form Handler (Swal Version + Redirect Support) ---
            async function handleAjaxSubmit(event, form, modalId) {
                event.preventDefault();
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                // Loading State
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="flex items-center gap-2 justify-center"><svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> 儲存中...</span>';

                try {
                    const formData = new FormData(form);
                    // 智慧循環：檢查並壓縮大容量照片 (超過 1.5MB 才壓縮)
                    const entries = Array.from(formData.entries());
                    for (let [key, value] of entries) {
                        if (value instanceof File && value.size > 1.5 * 1024 * 1024 && value.type.startsWith('image/')) {
                            const compressed = await compressImage(value, 1200, 0.8);
                            formData.set(key, compressed);
                        }
                    }

                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    if (response.status === 413) {
                        throw new Error('這張照片「太重了」！超過了空間上限，請壓縮後再試。');
                    }

                    let data;
                    const responseText = await response.text();
                    try {
                        data = JSON.parse(responseText);
                    } catch (jsonErr) {
                        throw new Error('伺服器無法處理此檔案(代碼:' + response.status + ')。');
                    }

                    if (response.ok) {
                        // Redirect Support (Crucial for Auth)
                        if (data.redirect) {
                            window.location.href = data.redirect;
                            return;
                        }

                        Toast.fire({ icon: 'success', title: data.message || '儲存成功！' });

                        if (modalId) {
                            safeCloseModal(modalId);
                        }
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        let errorMsg = '處理過程中發生問題。';
                        if (data.errors) {
                            errorMsg = Object.values(data.errors).flat().join('<br>'); // Change to HTML br
                        } else if (data.message) {
                            errorMsg = data.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: '輸入驗證失敗',
                            html: errorMsg, // Use html property instead of text
                            confirmButtonColor: '#9c8c7c',
                            customClass: {
                                popup: 'rounded-[32px]',
                                confirmButton: 'rounded-xl px-10'
                            }
                        });
                    }
                } catch (error) {
                    console.error('AJAX Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: '系統錯誤',
                        html: '<div class="text-sm text-muji-ash">' + (error.message || '連線異常，請稍後再試。') + '</div>',
                        confirmButtonColor: '#9c8c7c',
                        customClass: { popup: 'rounded-[32px]' }
                    });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            }
    </script>

    <script>
        // 智慧網路狀態監測
        function updateOnlineStatus() {
            const banner = document.getElementById('offline-banner');
            if (!banner) return;
            const isOnline = navigator.onLine;

            if (isOnline) {
                banner.classList.add('translate-y-[-100%]');
                banner.classList.remove('translate-y-0');
            } else {
                banner.classList.remove('translate-y-[-100%]');
                banner.classList.add('translate-y-0');
            }
        }

        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);
        window.addEventListener('load', updateOnlineStatus);
    </script>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[7000] w-10 h-10 bg-white/40 backdrop-blur-md border border-muji-edge/40 rounded-full shadow-muji-sm flex items-center justify-center text-muji-oak opacity-0 translate-y-10 transition-all duration-500 hover:bg-white/80 active:scale-95 group pointer-events-none">
        <svg class="w-5 h-5 group-hover:-translate-y-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
        </svg>
    </button>

    <!-- Offline Banner: Muji Style -->
    <div id="offline-banner" class="fixed top-0 left-0 w-full z-[9999] transform translate-y-[-100%] transition-transform duration-500 pointer-events-none">
        <div class="bg-muji-oak/90 backdrop-blur-md text-white text-[10px] font-black tracking-widest uppercase py-2 flex items-center justify-center gap-2 border-b border-muji-edge/20 shadow-lg">
            <svg class="w-3 h-3 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
            </svg>
            離線模式：目前存取的是本地快取版本
        </div>
    </div>

    <!-- PWA Install Prompt: Muji Minimalist -->
    <div id="pwa-install-prompt" class="fixed bottom-0 left-0 w-full z-[8000] transform translate-y-full transition-transform duration-700 ease-out p-4 pointer-events-none md:p-6">
        <div class="max-w-md mx-auto bg-white/95 backdrop-blur-md border border-muji-edge rounded-[2.5rem] shadow-2xl p-6 pointer-events-auto flex items-center gap-4 group">
            <div class="w-12 h-12 bg-muji-base rounded-2xl flex-shrink-0 flex items-center justify-center text-muji-oak shadow-muji-sm group-hover:scale-105 transition-transform">
                <img src="/icon_logo.png" class="w-8 h-8 object-contain">
            </div>
            <div class="flex-grow">
                <h4 class="text-sm font-black text-muji-ink">將旅程規劃安裝至桌面</h4>
                <p class="text-[10px] text-muji-ash font-medium mt-1">享受更快速、穩定的無邊框體驗</p>
            </div>
            <div class="flex flex-col gap-2">
                <button id="pwa-install-btn" class="px-4 py-2 bg-muji-oak text-white text-[10px] font-black rounded-full shadow-muji hover:opacity-90 active:scale-95 transition-all">立即安裝</button>
                <button onclick="dismissPWA()" class="text-[10px] text-muji-ash font-bold hover:text-muji-ink">下次再說</button>
            </div>
        </div>
    </div>

    <!-- iOS Install Hint (For Safari) -->
    <div id="ios-install-hint" class="fixed bottom-0 left-0 w-full z-[8000] transform translate-y-full transition-transform duration-700 ease-out p-4 pointer-events-none">
        <div class="max-w-md mx-auto bg-white/95 backdrop-blur-md border border-muji-edge rounded-[2.5rem] shadow-2xl p-6 pointer-events-auto text-center">
            <div class="flex flex-col items-center gap-4">
                <div class="w-14 h-14 bg-muji-base rounded-2xl flex items-center justify-center text-muji-oak shadow-muji-sm">
                    <img src="/icon_logo.png" class="w-10 h-10 object-contain">
                </div>
                <div>
                    <h4 class="text-sm font-black text-muji-ink">在 iPhone 上安裝 ElkTrip</h4>
                    <p class="text-[10px] text-muji-ash font-medium mt-2 leading-relaxed">
                        點擊下方導覽列的 <span class="inline-block p-1 bg-muji-base rounded"><svg class="w-3 h-3 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg></span>
                        接著向上捲動並點擊「<span class="font-black text-muji-oak">加入主畫面</span>」
                    </p>
                </div>
                <button onclick="dismissIOS()" class="mt-2 text-[10px] text-muji-ash font-bold hover:text-muji-ink">我明白了</button>
            </div>
        </div>
    </div>

    <!-- 協作者通知系統 (Collaborator Notifications) -->
    @auth
        @php
            $newInvs = auth()->user()->collaboratingTrips()->wherePivot('is_notified', false)->with(['user'])->get();
        @endphp
        @if($newInvs->count() > 0)
            <div id="collaboration-notifications" class="fixed bottom-6 right-6 z-[8500] flex flex-col gap-4 pointer-events-none">
                @foreach($newInvs as $inv)
                <div id="inv-{{ $inv->id }}" class="max-w-[320px] muji-glass rounded-[32px] p-6 shadow-2xl border border-muji-edge/40 pointer-events-auto transform translate-y-20 opacity-0 transition-all duration-700 notification-card">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full border-2 border-muji-wheat/50 overflow-hidden shadow-muji-sm">
                            <img src="{{ $inv->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($inv->user->name).'&color=9c8c7c&background=f0eae0' }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-grow">
                            <h4 class="text-xs font-black text-muji-ink">新的旅程邀請！</h4>
                            <p class="text-[10px] text-muji-ash font-medium mt-1 leading-relaxed">
                                <span class="text-muji-oak font-black">{{ $inv->user->name }}</span> 邀請您參加：
                                <br>
                                <span class="bg-muji-base/50 px-2 py-0.5 rounded text-muji-ink italic">「{{ $inv->name }}」</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-5">
                        <a href="{{ route('trip.show', ['user' => auth()->user(), 'trip' => $inv]) }}" 
                           onclick="markInvitationNotified('{{ $inv->id }}', this)"
                           class="flex-1 h-9 flex items-center justify-center bg-muji-oak text-white text-[10px] font-black rounded-full shadow-muji hover:opacity-90 active:scale-95 transition-all">
                            查看旅程
                        </a>
                        <button onclick="markInvitationNotified('{{ $inv->id }}', this)" 
                                class="w-20 h-9 flex items-center justify-center bg-muji-paper text-muji-ash border border-muji-edge text-[10px] font-black rounded-full hover:bg-muji-base transition-all active:scale-95">
                            我知道了
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    setTimeout(() => {
                        document.querySelectorAll('.notification-card').forEach((card, i) => {
                            setTimeout(() => {
                                card.classList.remove('translate-y-20', 'opacity-0');
                                card.classList.add('translate-y-0', 'opacity-100');
                            }, i * 200);
                        });
                    }, 1000);
                });

                async function markInvitationNotified(tripId, el) {
                    const card = el.closest('.notification-card');
                    try {
                        const response = await fetch(`{{ url('/') }}/{{ auth()->user()->username }}/collaborations/${tripId}/mark-as-notified`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (response.ok) {
                            card.classList.add('translate-x-20', 'opacity-0');
                            setTimeout(() => card.remove(), 700);
                        }
                    } catch (err) {
                        console.error('Failed to mark notification:', err);
                    }
                }
            </script>
        @endif
    @endauth

    <script>
        let deferredPrompt;
        const pwaPrompt = document.getElementById('pwa-install-prompt');
        const iosPrompt = document.getElementById('ios-install-hint');

        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later.
            deferredPrompt = e;

            // Show the custom prompt if not dismissed before
            if (!localStorage.getItem('pwa_dismissed')) {
                setTimeout(() => {
                    pwaPrompt.classList.remove('translate-y-full');
                }, 3000);
            }
        });

        document.getElementById('pwa-install-btn')?.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            pwaPrompt.classList.add('translate-y-full');
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            console.log(`User responded to the install prompt: ${outcome}`);
            deferredPrompt = null;
        });

        function dismissPWA() {
            pwaPrompt.classList.add('translate-y-full');
            localStorage.setItem('pwa_dismissed', 'true');
        }

        function dismissIOS() {
            iosPrompt.classList.add('translate-y-full');
            localStorage.setItem('ios_pwa_dismissed', 'true');
        }

        // Logic for iOS custom prompt
        const isIos = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;

        if (isIos && !isStandalone && !localStorage.getItem('ios_pwa_dismissed')) {
            setTimeout(() => {
                iosPrompt.classList.remove('translate-y-full');
            }, 5000);
        }

        // Back to Top Logic
        const backToTop = document.getElementById('back-to-top');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 400) {
                backToTop.classList.remove('opacity-0', 'translate-y-10', 'pointer-events-none');
            } else {
                backToTop.classList.add('opacity-0', 'translate-y-10', 'pointer-events-none');
            }
        });
        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>


</html>