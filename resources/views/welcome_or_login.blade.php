@extends('layout')

@section('content')
<div class="min-h-[70vh] flex flex-col items-center justify-center text-center px-4">
    <div class="mb-8 w-24 h-24 rounded-full bg-gradient-to-br from-indigo-400 to-purple-300 flex items-center justify-center shadow-lg mx-auto">
        <img src="/icon_logo.png" alt="Logo" class="w-16 h-16 object-contain">
    </div>
    
    <h1 class="text-4xl md:text-6xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 mb-6 drop-shadow-sm">
        歡迎使用 Trip Planner
    </h1>
    
    <p class="text-lg md:text-xl text-gray-600 mb-10 max-w-2xl mx-auto leading-relaxed">
        開始規劃您的下一趟旅程！登入以管理您的行程表、花費紀錄與代辦事項。
    </p>

    <button onclick="openLoginModal()" class="group relative px-8 py-4 bg-indigo-600 text-white font-bold rounded-full overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute inset-0 bg-white/20 group-hover:translate-x-full -translate-x-full transition-transform duration-500 ease-in-out skew-x-12"></div>
        <span class="flex items-center gap-2 relative z-10 text-lg">
            立即登入開始使用
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </span>
    </button>
</div>
@endsection
