    @auth
        <div id="globalProfileConfigModal" class="fixed inset-0 z-[2000] overflow-y-auto" style="display: none;" role="dialog" aria-modal="true">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="safeCloseModal('globalProfileConfigModal')"></div>
                <div class="relative transform overflow-hidden bg-white rounded-[40px] w-full max-w-lg shadow-2xl flex flex-col transition-all">
                    <div class="px-8 py-10 overflow-y-auto custom-scrollbar scroll-smooth max-h-[85vh]">
                        <div class="flex items-center gap-4 mb-10 text-left">
                            <div class="p-3 bg-muji-base rounded-2xl text-muji-oak shadow-muji-sm">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                </svg>
                            </div>
                            <div class="flex flex-col border-l-2 border-muji-edge pl-4">
                                <h3 class="text-3xl font-black text-muji-ink">帳號與設定</h3>
                                <p class="text-[10px] font-bold text-muji-ash uppercase tracking-[0.2em] mt-1">管理個人資料與系統偏好</p>
                            </div>
                        </div>

                        <form id="profileConfigForm" 
                              action="{{ route('profile.update', ['user' => auth()->user()]) }}" 
                              method="POST" 
                              enctype="multipart/form-data" 
                              class="text-left"
                              onsubmit="handleAjaxSubmit(event, this, 'globalProfileConfigModal')">
                            @csrf
                            
                            <!-- 1. Profile Section -->
                            <div class="space-y-6 mb-10">
                                <h4 class="text-xs font-black text-muji-oak uppercase tracking-[0.2em] flex items-center gap-2 bg-muji-base/50 self-start px-3 py-1.5 rounded-lg border border-muji-edge">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    基本資料
                                </h4>

                                <div class="flex flex-col sm:flex-row items-center gap-8 bg-muji-base/20 p-6 rounded-[32px] border border-muji-edge/50">
                                    <div class="relative group">
                                        <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-white shadow-muji bg-white relative group">
                                            <img id="avatar-preview" src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=9c8c7c&color=fff' }}" class="w-full h-full object-cover">
                                            @if(auth()->user()->avatar)
                                                <button type="submit" name="remove_avatar" value="1" class="absolute top-0 right-0 bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-20 shadow-lg" title="移除頭像">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                        <label class="absolute inset-0 flex items-center justify-center bg-black/40 text-white opacity-0 group-hover:opacity-100 transition-opacity rounded-full cursor-pointer">
                                            <input type="file" name="avatar" class="hidden" onchange="previewUserAvatar(this)">
                                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        </label>
                                    </div>
                                    <div class="flex-grow space-y-4 w-full text-left">
                                        <div class="space-y-1.5">
                                            <label class="text-[10px] font-black text-muji-ash uppercase tracking-widest pl-1">顯示名稱</label>
                                            <input type="text" name="name" value="{{ auth()->user()->name }}" class="block w-full h-[46px] px-4 bg-white border border-muji-edge rounded-xl text-muji-ink font-bold focus:ring-muji-oak transition-all outline-none">
                                        </div>
                                        <div class="space-y-1.5 opacity-60">
                                            <label class="text-[10px] font-black text-muji-ash uppercase tracking-widest pl-1">電子郵件 (不可修改)</label>
                                            <input type="email" disabled value="{{ auth()->user()->email }}" class="block w-full h-[46px] px-4 bg-muji-base/30 border border-muji-edge rounded-xl text-muji-ash font-medium cursor-not-allowed outline-none">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Security Section -->
                            <div class="space-y-6 mb-10">
                                <h4 class="text-xs font-black text-muji-oak uppercase tracking-[0.2em] flex items-center gap-2 bg-muji-base/50 self-start px-3 py-1.5 rounded-lg border border-muji-edge">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    安全設定
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-black text-muji-ash uppercase tracking-widest pl-1">目前的密碼</label>
                                        <input type="password" name="current_password" placeholder="若要修改請輸入舊密碼" class="block w-full h-[46px] px-4 bg-white border border-muji-edge rounded-xl text-muji-ink font-medium focus:ring-muji-oak transition-all outline-none">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-black text-muji-ash uppercase tracking-widest pl-1">新的密碼</label>
                                        <input type="password" name="new_password" placeholder="請輸入 8 位以上新密碼" class="block w-full h-[46px] px-4 bg-white border border-muji-edge rounded-xl text-muji-ink font-medium focus:ring-muji-oak transition-all outline-none">
                                    </div>
                                    <div class="sm:col-span-2 space-y-1.5">
                                        <label class="text-[10px] font-black text-muji-ash uppercase tracking-widest pl-1">確認新密碼</label>
                                        <input type="password" name="new_password_confirmation" placeholder="請再次輸入新密碼" class="block w-full h-[46px] px-4 bg-white border border-muji-edge rounded-xl text-muji-ink font-medium focus:ring-muji-oak transition-all outline-none">
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Visual Section -->
                            <div class="space-y-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-xs font-black text-muji-oak uppercase tracking-[0.2em] flex items-center gap-2 bg-muji-base/50 self-start px-3 py-1.5 rounded-lg border border-muji-edge">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        視覺自定義
                                    </h4>
                                    @if(auth()->user()->background_image || auth()->user()->avatar)
                                        <button type="submit" name="restore_default" value="1" class="text-[10px] text-red-400 font-bold hover:underline transition-all active:scale-95">恢復預設</button>
                                    @endif
                                </div>

                                <div class="space-y-6 bg-muji-base/10 p-6 rounded-[32px] border border-muji-edge/50">
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-muji-ash uppercase tracking-widest pl-1">全站背景圖片</label>
                                        <div class="bg-white p-2 rounded-2xl border border-muji-edge h-[64px] flex items-center">
                                            <input type="file" name="background_image" accept="image/*" class="block w-full text-xs text-muji-ash file:mr-4 file:h-[40px] file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-muji-base file:text-muji-oak hover:file:bg-muji-wheat/30 cursor-pointer transition-all" id="bg-upload-input">
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center">
                                            <label class="text-[10px] font-black text-muji-ash uppercase tracking-widest pl-1">背景透明度</label>
                                            <span id="val-bg-opacity" class="text-xs font-mono text-muji-ink font-bold">{{ auth()->user()->bg_opacity }}%</span>
                                        </div>
                                        <input type="range" name="bg_opacity" id="range-bg-opacity" min="0" max="100" value="{{ auth()->user()->bg_opacity }}" class="w-full h-1.5 bg-muji-base rounded-lg appearance-none cursor-pointer accent-muji-oak" oninput="previewBackground()">
                                    </div>

                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center">
                                            <label class="text-[10px] font-black text-muji-ash uppercase tracking-widest pl-1">背景霧化</label>
                                            <span id="val-bg-blur" class="text-xs font-mono text-muji-ink font-bold">{{ auth()->user()->bg_blur }}px</span>
                                        </div>
                                        <input type="range" name="bg_blur" id="range-bg-blur" min="0" max="20" value="{{ auth()->user()->bg_blur }}" class="w-full h-1.5 bg-muji-base rounded-lg appearance-none cursor-pointer accent-muji-oak" oninput="previewBackground()">
                                    </div>

                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center">
                                            <label class="text-[10px] font-black text-muji-ash uppercase tracking-widest pl-1">內容顯示寬度 (PC)</label>
                                            <span id="val-bg-width" class="text-xs font-mono text-muji-ink font-bold">{{ auth()->user()->bg_width }}%</span>
                                        </div>
                                        <input type="range" name="bg_width" id="range-bg-width" min="5" max="100" value="{{ auth()->user()->bg_width }}" class="w-full h-1.5 bg-muji-base rounded-lg appearance-none cursor-pointer accent-muji-oak" oninput="previewBackground()">
                                    </div>
                                </div>

                                <div class="pt-10 flex gap-4">
                                    <button type="button" onclick="safeCloseModal('globalProfileConfigModal')" class="flex-1 h-[56px] flex items-center justify-center bg-white text-muji-ash font-bold rounded-[24px] hover:bg-muji-base transition-all text-sm border border-muji-edge active:scale-95">
                                        取消
                                    </button>
                                    <button type="submit" class="flex-1 h-[56px] flex items-center justify-center bg-muji-oak text-white font-black rounded-[24px] hover:opacity-95 hover:shadow-muji hover:translate-y-[-1px] transition-all active:scale-95 text-sm uppercase tracking-widest">
                                        儲存所有變更
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endauth
