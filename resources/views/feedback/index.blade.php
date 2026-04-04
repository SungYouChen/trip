@extends('layout')
@section('title', '產品意見回饋')

@section('content')
<!-- Unified Page Container (p-6/space-y-6) -->
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6 pb-32">
    
    <!-- 1. Header Section -->
    <div class="flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-muji-base text-muji-oak flex items-center justify-center shadow-muji-sm">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
        </div>
        <div class="flex flex-col border-l-2 border-muji-edge pl-4">
            <h3 class="text-xl font-black text-muji-ink uppercase tracking-tight">產品意見回饋</h3>
            <p class="text-[10px] font-black text-muji-ash uppercase tracking-[0.2em] mt-0.5">社群與互動</p>
        </div>
    </div>

    <!-- 2. Posting Action Card (Symmetrical Standard) -->
    @if(!$isAdmin)
    <div class="muji-card p-8 relative overflow-hidden rounded-[28px]">
        <div class="absolute top-0 left-0 w-1.5 h-full bg-muji-oak/20"></div>
        <form action="{{ route('feedback.store', ['user' => auth()->user()]) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="space-y-6"
              onsubmit="handleAjaxSubmit(event, this, null)">
            @csrf
            <div>
                <textarea name="content" rows="3" required class="block w-full px-5 py-4 muji-input leading-relaxed text-sm" placeholder="有任何建議或問題嗎？"></textarea>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-4 justify-between">
                <label class="group flex items-center gap-2 px-4 h-[44px] bg-white border border-muji-edge rounded-xl cursor-pointer hover:bg-muji-base transition-all shadow-muji-sm">
                    <svg class="w-4 h-4 text-muji-ash group-hover:text-muji-oak" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span class="text-[10px] font-black text-muji-ash group-hover:text-muji-oak tracking-widest uppercase">附加圖片</span>
                    <input type="file" name="image" accept="image/*" class="hidden" onchange="previewMainImage(this)">
                </label>
                <div id="main-image-preview" class="hidden relative inline-flex items-center">
                    <div class="h-14 w-14 rounded-xl overflow-hidden border-2 border-white shadow-muji"><img src="" class="w-full h-full object-cover"></div>
                    <button type="button" onclick="clearMainImage()" class="absolute -top-1.5 -right-1.5 bg-muji-ink text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] shadow-lg">×</button>
                </div>
                <button type="submit" class="w-full sm:w-auto h-[44px] px-10 bg-muji-oak text-white font-black rounded-xl hover:opacity-95 shadow-muji transition-all active:scale-95 text-xs tracking-widest uppercase">發佈訊息</button>
            </div>
        </form>
    </div>
    @endif

    <!-- 3. Feedback Threads Container -->
    <div class="space-y-3 pt-2">
        <h4 class="text-[10px] font-black text-muji-ash uppercase tracking-[0.25em] pl-4 mb-3">對話列表</h4>
        @forelse($feedbacks as $feedback)
            <div id="thread-{{ $feedback->id }}" class="muji-card relative overflow-hidden transition-all duration-400 feedback-thread {{ $loop->first ? 'active' : '' }} rounded-[28px]" onclick="toggleThread('{{ $feedback->id }}', event)">
                <div class="absolute top-0 left-0 w-1 h-full bg-muji-wheat/40"></div>
                
                <div class="thread-inner transition-all duration-400 px-6 py-4">
                    <!-- Collapse Header -->
                    <div class="flex items-center justify-between cursor-pointer">
                        <div class="flex items-center gap-4">
                            <!-- MASTER AVATAR (Left Side Standard) -->
                            <div class="w-9 h-9 rounded-xl overflow-hidden border border-white shadow-sm flex-shrink-0">
                                <img src="{{ $feedback->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($feedback->user->name).'&background=9c8c7c&color=fff' }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-black text-muji-ink line-clamp-1 truncate max-w-[140px]">{{ $feedback->user->name }}</span>
                                    <span class="text-[8px] font-bold text-muji-ash/40 uppercase tracking-widest">{{ $feedback->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-[11px] text-muji-ash/60 font-medium line-clamp-1 thread-summary-text">
                                    {{ Str::limit($feedback->content, 95) }}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <!-- REPLIER PREVIEWS (Synced Style) -->
                            @php
                                $uniqueRepliers = $feedback->replies->unique('user_id')->values();
                            @endphp
                            @if($uniqueRepliers->count() > 0)
                                <div class="flex gap-1.5 items-center">
                                    @foreach($uniqueRepliers->take(3) as $r)
                                        <div class="w-9 h-9 rounded-xl border border-white overflow-hidden shadow-sm flex-shrink-0">
                                            <img src="{{ $r->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($r->user->name).'&background=333&color=fff' }}" class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                    @if($uniqueRepliers->count() > 3)
                                        <div class="w-9 h-9 rounded-xl border border-white bg-muji-base flex items-center justify-center text-[9px] font-black text-muji-ash flex-shrink-0">+{{ $uniqueRepliers->count()-3 }}</div>
                                    @endif
                                </div>
                            @endif
                            <svg class="w-5 h-5 text-muji-ash transition-transform duration-400 thread-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>

                    <!-- Collapsible Content -->
                    <div class="thread-content hidden pt-10 animate-in fade-in duration-400 overflow-hidden" onclick="event.stopPropagation()">
                        <!-- Question Main Body -->
                        <div class="px-2 mb-8 border-l-2 border-muji-edge pl-6 ml-1 text-sm">
                            <div class="text-muji-ink leading-relaxed whitespace-pre-wrap font-medium break-words">{{ $feedback->content }}</div>
                            @if($feedback->image_path)
                                <div class="rounded-xl overflow-hidden border-2 border-white shadow-muji inline-block max-w-[85%] mt-2">
                                    <img src="{{ Storage::url($feedback->image_path) }}" class="h-24 w-auto object-cover cursor-zoom-in" onclick="window.open(this.src)">
                                </div>
                            @endif
                        </div>

                        <!-- Divider decoration -->
                        <div class="h-px bg-muji-edge/40 border-dashed border-t mx-[-24px] mb-8"></div>

                        <!-- Replies Section -->
                        <div class="space-y-6 px-1 mb-8">
                            @foreach($feedback->replies as $reply)
                                <div class="flex items-start gap-4 {{ $reply->is_admin_reply ? 'flex-row-reverse' : '' }}">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-xl overflow-hidden {{ $reply->is_admin_reply ? 'bg-muji-ink' : 'bg-white border border-muji-edge shadow-sm' }}">
                                            <img src="{{ $reply->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($reply->user->name).'&background='.($reply->is_admin_reply ? '333' : '9c8c7c').'&color=fff' }}" class="w-full h-full object-cover">
                                        </div>
                                    </div>
                                    <div class="flex flex-col {{ $reply->is_admin_reply ? 'items-end' : 'items-start' }} max-w-[85%] space-y-1">
                                        <div class="flex items-center gap-2 px-1">
                                            <span class="text-[9px] font-black {{ $reply->is_admin_reply ? 'text-muji-oak' : 'text-muji-ash' }} uppercase tracking-wider">{{ $reply->user->name }}</span>
                                            <span class="text-[8px] font-bold text-muji-ash/40 uppercase">{{ $reply->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="p-4 px-6 rounded-2xl text-[13px] leading-relaxed shadow-muji-sm {{ $reply->is_admin_reply ? 'bg-muji-ink text-muji-base rounded-tr-none' : 'bg-white border border-muji-edge text-muji-ink rounded-tl-none' }}">
                                            <div class="whitespace-pre-wrap font-medium break-words text-[13px]">{{ $reply->content }}</div>
                                            @if($reply->image_path)
                                                <div class="rounded-xl overflow-hidden border-2 border-white shadow-muji inline-block max-w-full mt-2">
                                                    <img src="{{ Storage::url($reply->image_path) }}" class="h-24 w-auto object-cover cursor-zoom-in" onclick="window.open(this.src)">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Thread Reply Form -->
                        <div class="pt-6 border-t border-muji-edge/40">
                            <form action="{{ route('feedback.store', ['user' => auth()->user()]) }}" 
                                  method="POST" 
                                  enctype="multipart/form-data"
                                  onsubmit="handleAjaxSubmit(event, this, null)">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $feedback->id }}">
                                <div class="flex flex-col gap-4">
                                    <textarea name="content" rows="1" required class="block w-full px-5 py-3.5 muji-input text-[13px] resize-none overflow-hidden" placeholder="寫下你的回覆..." oninput="autoResize(this)"></textarea>
                                    <div class="flex items-center justify-between gap-3">
                                        <label class="group flex items-center gap-2 px-4 h-[44px] bg-white border border-muji-edge rounded-xl cursor-pointer hover:bg-muji-base transition-all shadow-muji-sm">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            <span class="text-[10px] font-black text-muji-ash group-hover:text-muji-oak tracking-widest uppercase">附加圖片</span>
                                            <input type="file" name="image" accept="image/*" class="hidden" onchange="previewReplyImage(this, '{{ $feedback->id }}')">
                                        </label>
                                        <div id="reply-image-preview-{{ $feedback->id }}" class="hidden relative inline-flex items-center">
                                            <div class="h-14 w-14 rounded-xl overflow-hidden border-2 border-white shadow-muji"><img src="" class="w-full h-full object-cover"></div>
                                            <button type="button" onclick="clearReplyImage('{{ $feedback->id }}')" class="absolute -top-1.5 -right-1.5 bg-muji-ink text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] shadow-lg">×</button>
                                        </div>
                                        <div class="flex-grow"></div>
                                        <button type="submit" class="h-[44px] px-10 bg-muji-oak text-white font-black rounded-xl hover:opacity-95 shadow-muji transition-all text-xs tracking-widest uppercase">回覆訊息</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="muji-card bg-muji-paper/20 border-2 border-dashed border-muji-edge/40 py-24 text-center rounded-[28px]">
                <p class="text-[10px] font-black text-muji-ash/30 uppercase tracking-widest">目前尚無對話</p>
            </div>
        @endforelse
    </div>
</div>

<script>
function toggleThread(id, event) {
    const thread = document.getElementById(`thread-${id}`);
    const inner = thread.querySelector('.thread-inner');
    const content = thread.querySelector('.thread-content');
    const chevron = thread.querySelector('.thread-chevron');
    const summary = thread.querySelector('.thread-summary-text');
    const isActive = thread.classList.contains('active');

    if (!isActive) {
        thread.classList.add('active');
        inner.classList.replace('py-4', 'p-8');
        content.classList.remove('hidden');
        chevron.classList.add('rotate-180');
        summary.classList.add('hidden');
        content.querySelectorAll('textarea').forEach(ta => autoResize(ta));
    } else {
        thread.classList.remove('active');
        inner.classList.replace('p-8', 'py-4');
        content.classList.add('hidden');
        chevron.classList.remove('rotate-180');
        summary.classList.remove('hidden');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const firstActive = document.querySelector('.feedback-thread.active');
    if (firstActive) {
        const inner = firstActive.querySelector('.thread-inner');
        inner.classList.replace('py-4', 'p-8');
        firstActive.querySelector('.thread-content').classList.remove('hidden');
        firstActive.querySelector('.thread-chevron').classList.add('rotate-180');
        firstActive.querySelector('.thread-summary-text').classList.add('hidden');
    }
});

function autoResize(el) { el.style.height = 'auto'; el.style.height = el.scrollHeight + 'px'; }
function previewMainImage(input) {
    const previewDiv = document.getElementById('main-image-preview');
    const previewImg = previewDiv.querySelector('img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) { previewImg.src = e.target.result; previewDiv.classList.remove('hidden'); }
        reader.readAsDataURL(input.files[0]);
    }
}
function clearMainImage() {
    const input = document.querySelector('input[name="image"]');
    const previewDiv = document.getElementById('main-image-preview');
    input.value = ''; previewDiv.classList.add('hidden');
}
function previewReplyImage(input, feedbackId) {
    const previewDiv = document.getElementById(`reply-image-preview-${feedbackId}`);
    const previewImg = previewDiv.querySelector('img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) { previewImg.src = e.target.result; previewDiv.classList.remove('hidden'); }
        reader.readAsDataURL(input.files[0]);
    }
}
function clearReplyImage(feedbackId) {
    const previewDiv = document.getElementById(`reply-image-preview-${feedbackId}`);
    const input = previewDiv.closest('form').querySelector('input[type="file"]');
    input.value = ''; previewDiv.classList.add('hidden');
}
</script>

<style>
/* Synchronized with global site-wide card style */
.muji-card { border-radius: 28px; }
.feedback-thread { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.feedback-thread.active { background-color: rgba(255, 255, 255, 0.9); }
.line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
</style>
@endsection
