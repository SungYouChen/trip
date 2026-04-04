@extends('layout')

@section('title', '重設密碼')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full muji-glass rounded-[40px] p-8 sm:p-10 shadow-2xl border border-muji-edge/30 transition-all duration-500">
        <div class="text-center mb-10">
            <div class="inline-flex p-4 bg-muji-base rounded-3xl text-muji-oak shadow-muji-sm mb-6">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <h2 class="text-3xl font-black text-muji-ink">重設您的密碼</h2>
            <p class="mt-2 text-sm text-muji-ash">請輸入您的電子信箱與新密碼</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50/50 backdrop-blur-sm border border-red-200 rounded-2xl">
                <ul class="list-disc list-inside text-xs text-red-600 font-bold space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label class="block text-sm font-bold text-muji-ash mb-2 ml-1">電子信箱</label>
                <input type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus
                       class="w-full px-5 py-4 muji-input font-medium" placeholder="your@email.com">
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-bold text-muji-ash mb-2 ml-1">新密碼</label>
                    <input type="password" name="password" required
                           class="w-full px-5 py-4 muji-input font-medium" placeholder="至少 8 個字元">
                </div>
                <div>
                    <label class="block text-sm font-bold text-muji-ash mb-2 ml-1">確認新密碼</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-5 py-4 muji-input font-medium" placeholder="再次輸入新密碼">
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full h-14 bg-muji-oak text-white font-black rounded-full shadow-muji hover:opacity-90 active:scale-95 transition-all transform duration-300">
                    立即重設密碼
                </button>
            </div>
        </form>
        
        <div class="mt-8 text-center pt-6 border-t border-muji-edge/30">
            <a href="/" class="text-xs font-bold text-muji-ash hover:text-muji-oak transition-colors">
                ← 返回登入頁面
            </a>
        </div>
    </div>
</div>
@endsection
