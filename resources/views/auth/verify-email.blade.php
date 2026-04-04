@extends('layout')

@section('title', '驗證您的電子信箱')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full muji-glass rounded-[40px] p-8 sm:p-10 shadow-2xl border border-muji-edge/30 text-center transition-all duration-500">
        <div class="inline-flex p-5 bg-muji-base rounded-3xl text-muji-oak shadow-muji-sm mb-8 animate-bounce-slow">
            <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        
        <h2 class="text-3xl font-black text-muji-ink mb-4">驗證您的旅程起點</h2>
        <p class="text-muji-ash leading-relaxed mb-10">
            感謝您的註冊！在開始規劃旅程之前，請先點擊我們剛剛發送到您電子信箱的連結進行驗證。<br>
            <span class="text-xs italic mt-2 block font-medium">（如果您沒看到信件，請檢查垃圾郵件箱）</span>
        </p>

        <div class="space-y-4">
            <form method="POST" action="{{ route('verification.send') }}" onsubmit="handleResend(event, this)">
                @csrf
                <button type="submit" class="w-full h-14 bg-muji-oak text-white font-black rounded-full shadow-muji hover:opacity-90 active:scale-95 transition-all transform duration-300">
                    重新發送驗證郵件
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full h-12 bg-transparent text-muji-ash font-bold rounded-full hover:bg-muji-base hover:text-muji-oak transition-all duration-300">
                    暫時登出
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    async function handleResend(event, form) {
        event.preventDefault();
        const btn = form.querySelector('button');
        const originalText = btn.innerHTML;
        const msg = document.getElementById('resend-message');
        
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            
            msg.textContent = data.message;
            msg.classList.remove('hidden');
            msg.classList.add('animate-fade-in');
            
            showToast(data.message, 'success');
        } catch (e) {
            showToast('發送失敗，請稍後再試。', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }
</script>

<style>
    @keyframes bounce-slow {
        0%, 100% { transform: translateY(-5%); animation-timing-function: cubic-bezier(0.8,0,1,1); }
        50% { transform: none; animation-timing-function: cubic-bezier(0,0,0.2,1); }
    }
    .animate-bounce-slow { animation: bounce-slow 3s infinite; }
    
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fade-in 0.5s ease-out forwards; }
</style>
@endsection
