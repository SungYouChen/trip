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
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            border: 1px solid #e8e4db;
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
            background: #ffffff;
            border: 1px solid #e8e4db;
            border-radius: 12px;
            box-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.05);
        }

        /* Essential Fix: Ensure Swal stays on top of all modals */
        .swal2-container {
            z-index: 10000 !important;
        }
    </style>
</head>

<body class="text-muji-ink antialiased min-h-screen flex flex-col relative bg-transparent">
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
        $bgOpacityValue = (($bgOwner->bg_opacity ?? 40)) / 100;
        $bgBlurValue = $bgOwner->bg_blur ?? 5;
        $bgWidthValue = $bgOwner->bg_width ?? 45;
    @endphp

    <style id="bg-live-styles">
        :root {
            --bg-opacity: {{ $bgOpacityValue }};
            --bg-blur: {{ $bgBlurValue }}px;
            --bg-width: {{ $bgWidthValue }}%;
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
    <div id="bg-wrapper" class="fixed inset-0 z-[-10] overflow-hidden pointer-events-none select-none" style="background-color: #f5f4f2;">
        <div id="global-bg-element" class="absolute inset-y-0 bg-cover bg-center bg-no-repeat transition-all duration-700 ease-in-out" style="background-image: url('{{ $bgUrl }}'); 
                    opacity: var(--bg-opacity); 
                    filter: blur(var(--bg-blur));
                    width: var(--bg-width);
                    left: 50%;
                    transform: translateX(-50%);"></div>

    </div>

    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 z-[2000] overflow-y-auto" style="display: none;" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="safeCloseModal('loginModal')"></div>
            <div class="relative transform overflow-hidden bg-white rounded-3xl w-full max-w-lg p-8 shadow-2xl flex flex-col transition-all">
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
                            <input type="email" name="email" required class="block w-full px-4 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium" placeholder="例如：elk@example.com">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-muji-ash text-left mb-2">密碼</label>
                            <input type="password" name="password" autocomplete="current-password" required class="block w-full px-4 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium" placeholder="請輸入密碼">
                        </div>

                        <div class="flex gap-4 pt-4 mt-2 border-t border-muji-edge/50">
                            <button type="button" onclick="safeCloseModal('loginModal')" class="flex-1 h-[46px] flex items-center justify-center bg-muji-paper text-muji-ash border border-muji-edge font-black rounded-2xl hover:bg-muji-base transition-all active:scale-95 text-sm">
                                取消
                            </button>
                            <button type="submit" class="flex-1 h-[46px] flex items-center justify-center bg-muji-oak text-white font-black rounded-2xl hover:opacity-90 transition-all shadow-muji active:scale-95 text-sm">
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
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="safeCloseModal('registerModal')"></div>
            <div class="relative transform overflow-hidden bg-white rounded-3xl w-full max-w-lg p-8 shadow-2xl flex flex-col transition-all">
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
                                <input type="text" name="name" required class="block w-full px-4 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium" placeholder="例如：王小明">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-muji-ash text-left mb-2">帳號 ID</label>
                                <input type="text" name="username" required class="block w-full px-4 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium" placeholder="例如：elk_trip" pattern="[a-zA-Z0-9_\-]+">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-muji-ash text-left mb-2">電子郵件</label>
                                <input type="email" name="email" required class="block w-full px-4 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium" placeholder="例如：elk@example.com">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <input type="password" name="password" required placeholder="請輸入密碼" class="block w-full px-4 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium">
                                <input type="password" name="password_confirmation" required placeholder="請再次輸入密碼" class="block w-full px-4 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium">
                            </div>

                            <div class="flex gap-4 pt-4 mt-2 border-t border-muji-edge/50">
                                <button type="button" onclick="safeCloseModal('registerModal')" class="flex-1 h-[46px] flex items-center justify-center bg-muji-paper text-muji-ash border border-muji-edge font-black rounded-2xl hover:bg-muji-base transition-all active:scale-95 text-sm">
                                    取消
                                </button>
                                <button type="submit" class="flex-1 h-[46px] flex items-center justify-center bg-muji-oak text-white font-black rounded-2xl hover:opacity-90 transition-all shadow-muji active:scale-95 text-sm">
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

    <header class="bg-muji-paper/90 border-b border-muji-edge sticky top-0 z-50 backdrop-blur-md">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ auth()->check() ? route('home', ['user' => auth()->user()]) : '/' }}" class="flex items-center gap-2 group">
                <div class="w-10 h-10 rounded-lg bg-muji-base flex items-center justify-center shadow-muji-sm group-hover:scale-105 transition-transform overflow-hidden relative">
                    <img src="/icon_logo.png?v={{ time() }}" alt="Logo" class="w-full h-full object-contain p-1 z-10 relative">
                </div>
                <h1 class="text-lg sm:text-xl font-bold text-muji-ink truncate max-w-[140px] xs:max-w-[200px] sm:max-w-none">
                    {{ isset($trip) ? $trip->name : 'Trip Planner' }}
                </h1>
            </a>

            <nav class="flex items-center justify-end gap-1 sm:gap-4 text-muji-ash font-black">
                @if(isset($trip))
                    @if($isShared)
                        <a href="{{ route('trip.index_shared', ['token' => $trip->share_token]) }}" class="flex flex-col sm:flex-row items-center gap-0.5 sm:gap-2 px-2 py-1 rounded-lg hover:bg-slate-50 transition-all {{ request()->routeIs('trip.index_shared') ? 'text-slate-900 bg-slate-100' : '' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            <span class="text-[10px] sm:text-xs">總覽</span>
                        </a>
                        <a href="{{ route('expenses.index_shared', ['token' => $trip->share_token]) }}" class="flex flex-col sm:flex-row items-center gap-0.5 sm:gap-2 px-2 py-1 rounded-lg hover:bg-slate-50 transition-all {{ request()->routeIs('expenses.index_shared') ? 'text-slate-900 bg-slate-100' : '' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-[10px] sm:text-xs">消費</span>
                        </a>
                    @else
                        <a href="{{ route('trip.show', ['user' => $trip->user, 'trip' => $trip]) }}" class="flex flex-col sm:flex-row items-center gap-0.5 sm:gap-2 px-2 py-1 rounded-lg hover:bg-slate-50 transition-all {{ request()->routeIs('trip.show') ? 'text-slate-900 bg-slate-100' : '' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            <span class="text-[10px] sm:text-xs">總覽</span>
                        </a>
                        <a href="{{ route('expenses.index', ['user' => $trip->user, 'trip' => $trip]) }}" class="flex flex-col sm:flex-row items-center gap-0.5 sm:gap-2 px-2 py-1 rounded-lg hover:bg-muji-base transition-all {{ request()->routeIs('expenses.index') ? 'text-muji-ink bg-muji-wheat/50' : '' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-[10px] sm:text-xs">消費</span>
                        </a>
                    @endif
                @endif

                @if(!$isShared)
                    @auth
                        <a href="{{ route('feedback.index', ['user' => auth()->user()]) }}" class="flex flex-col sm:flex-row items-center gap-0.5 sm:gap-2 px-2 py-1 rounded-lg hover:bg-slate-50 transition-all {{ request()->routeIs('feedback.index') ? 'text-slate-900 bg-slate-100' : '' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <span class="text-[10px] sm:text-xs">回饋</span>
                        </a>
                        <a href="{{ route('home', ['user' => auth()->user()]) }}" class="flex flex-col sm:flex-row items-center gap-0.5 sm:gap-2 px-2 py-1 rounded-lg hover:bg-slate-50 transition-all {{ request()->routeIs('home') ? 'text-slate-900 bg-slate-100' : '' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="text-[10px] sm:text-xs">旅程</span>
                        </a>
                    @else
                        <button type="button" onclick="safeOpenModal('loginModal')" class="flex flex-col sm:flex-row items-center gap-0.5 sm:gap-2 px-2 py-1 rounded-lg hover:bg-muji-base transition-all border-0 bg-transparent text-muji-ash cursor-pointer">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span class="text-[10px] sm:text-xs whitespace-nowrap">登入</span>
                        </button>
                        <button type="button" onclick="safeOpenModal('registerModal')" class="flex flex-col sm:flex-row items-center gap-0.5 sm:gap-2 px-2 py-1 rounded-lg hover:bg-muji-base transition-all border-0 bg-transparent text-muji-ash cursor-pointer">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <span class="text-[10px] sm:text-xs whitespace-nowrap">註冊</span>
                        </button>
                    @endauth

                    @auth
                        <button type="button" onclick="safeOpenModal('globalProfileConfigModal')" class="flex flex-col sm:flex-row items-center gap-0.5 sm:gap-2 px-2 py-1 rounded-lg hover:bg-muji-base transition-all text-muji-ash hover:text-muji-ink border-0 bg-transparent cursor-pointer group">
                            <div class="w-6 h-6 rounded-full overflow-hidden border border-muji-edge shadow-muji-sm group-hover:scale-110 transition-transform">
                                <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=9c8c7c&color=fff' }}" class="w-full h-full object-cover">
                            </div>
                            <span class="text-[10px] sm:text-xs font-black">設定</span>
                        </button>

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="flex flex-col items-center gap-0.5 px-2 py-1 text-slate-300 hover:text-red-400 transition-colors border-0 bg-transparent cursor-pointer">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span class="text-[10px] sm:hidden text-center truncate">登出</span>
                            </button>
                        </form>
                    @endauth
                @endif
            </nav>
        </div>
    </header>

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
            <div class="relative transform overflow-hidden bg-white rounded-3xl w-full max-w-4xl h-[80vh] shadow-2xl flex flex-col transition-all p-4">
                <div class="flex justify-between items-center mb-4 px-2">
                    <h3 id="mapTitle" class="text-xl font-bold text-gray-900 truncate pr-4">地點 (地圖)</h3>
                    <button onclick="safeCloseModal('mapModal')" class="p-2 rounded-full hover:bg-gray-100 transition-colors">
                        <svg class="w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex-grow w-full h-full bg-gray-100 rounded-xl overflow-hidden relative">
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
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl flex flex-col max-h-[85vh]">
                <div class="px-8 py-10 overflow-y-auto custom-scrollbar scroll-smooth">
                    <div class="flex justify-between items-start mb-10">
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
                        <button onclick="safeCloseModal('expenseModal')" class="text-muji-ash hover:text-muji-ink p-2 rounded-full hover:bg-muji-base transition-all">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    @auth
                        <form id="expenseForm" 
                              action="{{ isset($trip) ? route('expenses.store', ['user' => $trip->user, 'trip' => $trip]) : '#' }}" 
                              method="POST"
                              onsubmit="handleAjaxSubmit(event, this, 'expenseModal')">
                            @csrf
                            <div id="methodField"></div>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-bold text-muji-ash mb-2">支出項目說明</label>
                                    <input type="text" id="expenseDescription" name="description" required class="block w-full h-[46px] px-4 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium" placeholder="例如：午餐拉麵">
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-muji-ash mb-2">支出金額</label>
                                        <div class="flex relative h-[46px] rounded-xl border border-muji-edge overflow-hidden focus-within:ring-2 focus-within:ring-muji-oak bg-white group transition-all">
                                            <select id="expenseCurrency" name="is_base_currency" class="bg-muji-base h-full border-0 border-r border-muji-edge px-3 text-muji-ink font-black text-xs focus:ring-0 cursor-pointer appearance-none">
                                                <option value="0">{{ isset($trip) ? $trip->target_currency : '當地幣' }}</option>
                                                <option value="1">{{ isset($trip) ? $trip->base_currency : '本國幣' }}</option>
                                            </select>
                                            <input type="number" step="0.01" id="expenseAmount" name="amount" required class="flex-1 w-full h-full border-0 bg-transparent focus:ring-0 px-4 font-mono text-muji-ink font-black" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-muji-ash mb-2">支出類別</label>
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
                                    <input type="date" id="expenseDate" name="date" required value="{{ date('Y-m-d') }}" class="block w-full h-[46px] px-4 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium">
                                </div>

                                <div class="pt-6 mt-8 border-t border-muji-edge flex gap-4">
                                    <button type="button" onclick="safeCloseModal('expenseModal')" class="flex-1 h-[46px] flex items-center justify-center border border-muji-edge rounded-2xl text-muji-ash bg-muji-paper hover:bg-muji-base transition-colors font-black text-sm">
                                        取消
                                    </button>
                                    <button type="submit" id="expenseSubmitBtn" class="flex-1 h-[46px] flex items-center justify-center border border-transparent rounded-2xl text-white bg-muji-oak hover:opacity-90 shadow-muji transition-all font-black active:scale-95 text-sm">
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
            <div class="relative transform overflow-hidden bg-white w-full max-w-6xl h-screen shadow-2xl flex flex-col transition-all">
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
                reader.onload = function(e) {
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

        @if(session('success'))
            Toast.fire({ icon: 'success', title: '{{ session('success') }}' });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: '糟糕！', text: '{{ session('error') }}', borderRadius: '1.5rem' });
        @endif
        @if($errors->any())
            Swal.fire({ icon: 'error', title: 'Validation Error', text: '{{ $errors->first() }}', borderRadius: '1.5rem' });
        @endif

        // --- Global AJAX Form Handler (Swal Version + Redirect Support) ---
        async function handleAjaxSubmit(event, form, modalId) {
            event.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            // Loading State
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="flex items-center gap-2 justify-center"><svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> 處理中...</span>';

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

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
                    text: '網路連線異常，請稍後再試。',
                    confirmButtonColor: '#9c8c7c',
                    customClass: { popup: 'rounded-[32px]' }
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        }
    </script>

</body>

</html>