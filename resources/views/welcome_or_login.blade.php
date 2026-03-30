@extends('layout')

@section('content')
<div class="min-h-[70vh] flex flex-col items-center justify-center text-center px-4">
    <div class="mb-10 w-32 h-32 rounded-3xl bg-muji-base flex items-center justify-center shadow-muji border border-muji-edge mx-auto transform -rotate-3 hover:rotate-0 transition-transform duration-500">
        <img src="/icon_logo.png" alt="Logo" class="w-20 h-20 object-contain grayscale opacity-80">
    </div>
    
    <h1 class="text-5xl md:text-7xl font-black text-muji-ink tracking-tighter mb-8 bg-muji-base/50 px-6 py-2 rounded-2xl border border-muji-edge/30 backdrop-blur-sm">
        旅程 <span class="text-muji-oak underline decoration-muji-wheat/50 underline-offset-[12px]">足跡</span>
    </h1>
    
    <p class="text-lg md:text-xl text-muji-ash mb-12 max-w-2xl mx-auto leading-relaxed font-medium italic opacity-80">
        規劃下一次的冒險，或是重溫美好的回憶。<br>登入以紀錄您的足跡、管理預算與行程。
    </p>

    <button onclick="openLoginModal()" class="group relative px-12 py-5 bg-muji-oak text-white font-black rounded-2xl overflow-hidden shadow-muji hover:opacity-90 transition-all duration-300 transform hover:-translate-y-1 active:scale-95 border border-muji-edge">
        <span class="flex items-center gap-3 relative z-10 text-xl tracking-widest uppercase">
            立即登入紀錄足跡
            <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </span>
    </button>
</div>
@endsection
