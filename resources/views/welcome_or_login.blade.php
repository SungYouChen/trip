@extends('layout')

@section('content')
    <div class="max-w-[1000px] mx-auto px-6 py-20">
        <!-- Brand Slogan Header -->
        <header class="border-b border-muji-edge pb-12 mb-20 flex justify-between items-baseline">
            <div class="flex flex-col">
                <span class="text-[10px] font-black tracking-[0.6em] text-muji-oak uppercase mb-2">Trip Planner System</span>
                <h2 class="text-xs font-black tracking-[1em] text-muji-ash uppercase">Journey足跡 / 質感旅程紀錄</h2>
            </div>
        </header>

        <!-- Project Hero: Multi-Destination Focus -->
        <section class="grid grid-cols-1 md:grid-cols-2 border border-muji-edge">

            <!-- Hero Information Block -->
            <div class="p-12 md:p-16 border-b md:border-b-0 md:border-r border-muji-edge flex flex-col justify-between">
                <div>
                    <h1 class="text-5xl md:text-7xl font-black text-muji-ink tracking-tight leading-[1.1] mb-12">
                        讓紀錄<br>回歸純粹
                    </h1>
                    <p class="text-sm text-muji-ash leading-[2.2] max-w-xs font-medium">
                        Journey 足跡是一款專為質感旅人打造的工具。<br>
                        摒棄繁雜的設計，<br>
                        只為安放您旅途中的每一份靈感與步伐。
                    </p>
                </div>

                <div class="mt-20">
                    <button onclick="openLoginModal()" class="group flex items-center gap-6 text-muji-ink hover:text-muji-oak transition-colors duration-500">
                        <span class="text-xs font-black tracking-[0.5em] uppercase">Start Your Trip</span>
                        <div class="w-12 h-px bg-muji-ink group-hover:w-20 transition-all duration-500"></div>
                    </button>
                </div>
            </div>

            <!-- Featured Image: Lifestyle Focus -->
            <div class="bg-muji-base flex items-center justify-center p-8 md:p-12">
                <div class="w-full aspect-[4/5] bg-muji-paper relative overflow-hidden group">
                    <img src="https://picsum.photos/seed/journey_hero/800/1000?grayscale" class="w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:grayscale-0 transition-all duration-1000">
                    <!-- Precise corner markers -->
                    <div class="absolute top-4 left-4 w-2 h-2 border-t border-l border-muji-oak/30"></div>
                    <div class="absolute bottom-4 right-4 w-2 h-2 border-b border-r border-muji-oak/30"></div>
                </div>
            </div>
        </section>

        <!-- Core Features Matrix -->
        <section class="mt-40">
            <div class="mb-16">
                <h3 class="text-xs font-black tracking-[1em] text-muji-ash uppercase border-b border-muji-edge pb-4 inline-block">核心功能 · Modules</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 border-l border-t border-muji-edge">
                <!-- Feature 1: Scheduling -->
                <div class="p-10 border-r border-b border-muji-edge hover:bg-muji-paper transition-colors duration-500 group">
                    <span class="text-[9px] font-black tracking-[0.4em] text-muji-oak block mb-6">Schedule / 精確安排</span>
                    <h4 class="text-lg font-bold text-muji-ink mb-6 group-hover:translate-x-2 transition-transform">行程。有序井然</h4>
                    <p class="text-xs text-muji-ash leading-[2] font-medium opacity-70">
                        直觀的兩欄式設計，完美整合時間軸與地圖指引。讓您在旅途中，隨時掌握下一站的節奏。
                    </p>
                </div>
                <!-- Feature 2: Expenses -->
                <div class="p-10 border-r border-b border-muji-edge hover:bg-muji-paper transition-colors duration-500 group">
                    <span class="text-[9px] font-black tracking-[0.4em] text-muji-oak block mb-6">Ledger / 記帳對帳</span>
                    <h4 class="text-lg font-bold text-muji-ink mb-6 group-hover:translate-x-2 transition-transform">消費。透明掌控</h4>
                    <p class="text-xs text-muji-ash leading-[2] font-medium opacity-70">
                        多幣種自動換算，輕鬆記錄每一筆開銷。讓您可以專注於體驗，而非繁瑣的計算。
                    </p>
                </div>
                <!-- Feature 3: Checklist -->
                <div class="p-10 border-r border-b border-muji-edge hover:bg-white transition-colors duration-500 group">
                    <span class="text-[9px] font-black tracking-[0.4em] text-muji-oak block mb-6">Checklist / 清單收納</span>
                    <h4 class="text-lg font-bold text-muji-ink mb-6 group-hover:translate-x-2 transition-transform">清單。不再遺忘</h4>
                    <p class="text-xs text-muji-ash leading-[2] font-medium opacity-70">
                        無論是必買小物還是必訪景點。將所有願望收納於一處，讓旅程不留遺憾。
                    </p>
                </div>
            </div>
        </section>

        <!-- Aesthetic Proof -->
        <section class="mt-40 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="order-2 md:order-1">
                <div class="bg-muji-paper border border-muji-edge p-0 overflow-hidden shadow-none">
                    <img src="https://picsum.photos/seed/journey_detail/800/800?grayscale" class="w-full aspect-square object-cover opacity-80 grayscale hover:grayscale-0 transition-all duration-700">
                </div>
            </div>
            <div class="p-8 md:p-12 order-1 md:order-2">
                <h3 class="text-2xl font-black text-muji-ink mb-8 tracking-tight italic">Design for Travellers.</h3>
                <div class="space-y-6 text-sm text-muji-ash leading-[2] font-medium">
                    <p>我們相信，好的工具不應該搶走主角的風采。Journey 用最溫潤的方式，守護您的每一段冒險。</p>
                    <ul class="space-y-4 pt-4 border-t border-muji-edge">
                        <li class="flex items-center gap-4">
                            <div class="w-1 h-1 bg-muji-oak rounded-full"></div>
                            <span>離線優先的存取體驗</span>
                        </li>
                        <li class="flex items-center gap-4">
                            <div class="w-1 h-1 bg-muji-oak rounded-full"></div>
                            <span>多人實時協作與共享</span>
                        </li>
                        <li class="flex items-center gap-4">
                            <div class="w-1 h-1 bg-muji-oak rounded-full"></div>
                            <span>支援地表多數主流幣種</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Global CTA -->
        <footer class="mt-60 pt-20 border-t border-muji-edge text-center">
            <div class="mb-16 space-y-4">
                <h2 class="text-3xl font-black text-muji-ink tracking-tight italic opacity-90">準備好開啟下一段旅程了嗎？</h2>
                <p class="text-[10px] font-black tracking-[0.5em] text-muji-ash">AUTHENTICATE TO COMMENCE RECORDING</p>
            </div>

            <div class="flex justify-center">
                <button onclick="openLoginModal()" class="px-20 py-6 border border-muji-ink text-muji-ink font-black hover:bg-muji-ink hover:text-muji-paper transition-all duration-700 tracking-[0.3em] uppercase text-xs">
                    AUTHENTICATE / SIGN IN
                </button>
            </div>

            <div class="mt-40 pb-12 flex flex-col md:flex-row justify-between items-center md:items-end border-t border-muji-edge pt-12 gap-8 md:gap-0 text-center md:text-left">
                <div class="text-[9px] font-black tracking-[0.4em] text-muji-ash uppercase">
                    &copy; 2026 JOURNEY APP PROJECT
                </div>
                <div class="flex flex-wrap justify-center md:justify-end gap-x-12 gap-y-4 text-[9px] font-black tracking-[0.4em] text-muji-ash uppercase opacity-50">
                    <span>Personal Note</span>
                    <span>System Status</span>
                </div>
            </div>
        </footer>
    </div>

    <style>
        /* Allow global background to show through */
        body {
            background-color: transparent !important;
        }

        html {
            scroll-behavior: smooth;
        }

        ::selection {
            background: #333;
            color: #fff;
        }
    </style>
@endsection