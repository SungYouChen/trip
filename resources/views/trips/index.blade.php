@php $isShared = $isShared ?? false; @endphp
@extends('layout')

@section('title', '旅程足跡')
@section('header_title', '旅程足跡')

@section('content')
    <div class="max-w-6xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-10">
            <div>
                <h1 class="text-4xl font-black text-muji-ink tracking-tight sm:text-5xl">
                    旅程 <span class="text-muji-oak underline decoration-muji-wheat/50 underline-offset-8">足跡</span>
                </h1>
                <p class="mt-4 text-lg text-muji-ash">規劃下一次的冒險，或是重溫美好的回憶。</p>
            </div>
            @auth
                <button onclick="safeOpenModal('add-trip-modal')" class="inline-flex items-center px-8 py-3 bg-muji-oak text-white font-black rounded-2xl shadow-muji hover:opacity-90 transition-all duration-300 transform active:scale-95 whitespace-nowrap">
                    <svg class="-ml-1 mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    新增旅程
                </button>
            @endauth
        </div>
        @php $showArchived = request('archived') == '1'; @endphp
        @auth
            <div class="flex justify-end mb-4">
                <a href="{{ request()->fullUrlWithQuery(['archived' => $showArchived ? '0' : '1']) }}" class="flex items-center gap-2 text-[10px] font-black tracking-widest uppercase px-3 py-1.5 rounded-full border border-muji-edge transition-all {{ $showArchived ? 'bg-muji-base text-muji-oak' : 'bg-muji-base/50 text-muji-ash hover:bg-muji-wheat/20' }}">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12M10 12v4m4-4v4" />
                    </svg>
                    {{ $showArchived ? '隱藏封存' : '查看封存' }}
                </a>
            </div>
        @endauth

        @php
            $tripsToShow = $showArchived
                ? $trips->filter(fn ($t) => $t->trashed())
                : $trips->filter(fn ($t) => !$t->trashed());
        @endphp

        <div class="grid grid-cols-1 gap-y-10 gap-x-8 sm:grid-cols-2 lg:grid-cols-3 xl:gap-x-12">
            @forelse($tripsToShow as $t)
                @php $isArchived = $t->trashed(); @endphp
                <div class="relative group hover:-translate-y-2 transition-all duration-500">
                    <a href="{{ $isArchived ? '#' : route('trip.show', ['user' => auth()->user(), 'trip' => $t]) }}" class="block relative muji-card overflow-hidden transition-all duration-500 hover:shadow-muji {{ $isArchived ? 'border-2 border-dashed border-muji-ash grayscale opacity-60' : '' }}">
                        <div class="aspect-w-3 aspect-h-2 w-full overflow-hidden">
                            <img src="{{ $t->cover_image ? asset('storage/' . $t->cover_image) : asset('bg.jpg') }}" onerror="this.src='https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?q=80&w=2070&auto=format&fit=crop'" alt="{{ $t->name }}" class="h-64 w-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-muji-ink/80 via-muji-ink/30 to-transparent"></div>
                        </div>
                        <div class="absolute bottom-0 p-6 w-full">
                            <div class="flex items-center space-x-2 text-muji-base/80 text-[10px] font-bold mb-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>
                                    @if($t->start_date && $t->end_date)
                                        {{ \Carbon\Carbon::parse($t->start_date)->format('Y/m/d') }} - {{ \Carbon\Carbon::parse($t->end_date)->format('Y/m/d') }}
                                    @else
                                        日期未定 (共 {{ $t->estimated_days ?? $t->days->count() }} 天)
                                    @endif
                                </span>
                            </div>
                            <h3 class="text-2xl font-black text-white group-hover:text-muji-wheat transition-colors">
                                {{ $t->name }}
                            </h3>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-white/10 text-muji-base/80 border border-white/10">
                                    {{ $isArchived ? '已封存' : $t->days->count() . ' 天行程' }}
                                </span>
                            </div>
                        </div>

                        @if(auth()->check() && $t->user_id !== auth()->id())
                            <div class="absolute top-4 left-4 z-20">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-2xl text-[10px] font-black bg-muji-oak/70 text-white border border-muji-wheat/20 backdrop-blur-md shadow-2xl">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    好友分享
                                </span>
                            </div>
                        @endif
                    </a>

                    @auth
                        @if($isArchived)
                            <div class="absolute top-3 right-3 z-10 flex gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <form action="{{ route('trips.restore', ['user' => auth()->user(), 'tripId' => $t->id]) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="p-2 bg-green-50 text-green-600 rounded-xl hover:bg-green-100 shadow-sm transition-colors" data-tooltip="還原">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>
                                </form>
                                @php $forceId = 'force-trip-' . $t->id; @endphp
                                <form id="{{ $forceId }}" action="{{ route('trips.forceDelete', ['user' => auth()->user(), 'tripId' => $t->id]) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete('永久刪除？', '此旅程將被永久刪除，無法復原！', '{{ $forceId }}')" class="p-2 bg-red-100 text-red-600 rounded-xl hover:bg-red-200 shadow-sm transition-colors" data-tooltip="永久刪除">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @else
                            @php $archiveId = 'archive-trip-' . $t->id; @endphp
                            <form id="{{ $archiveId }}" action="{{ route('trips.destroy', ['user' => auth()->user(), 'trip' => $t]) }}" method="POST" class="absolute top-3 right-3 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete('封存旅程？', '此旅程將被封存，可在「查看封存」中還原。', '{{ $archiveId }}')" class="p-2 bg-red-50/90 text-red-500 rounded-xl hover:bg-red-100 hover:text-red-600 shadow-sm transition-colors backdrop-blur-sm" data-tooltip="封存旅程">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12M10 12v4m4-4v4" />
                                    </svg>
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            @empty
                <div class="col-span-full py-20 text-center bg-muji-base/50 border border-dashed border-muji-edge rounded-3xl">
                    <svg class="mx-auto h-12 w-12 text-muji-ash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-lg font-bold text-muji-ink">{{ $showArchived ? '沒有封存的旅程' : '尚未建立旅程' }}</h3>
                    <p class="mt-1 text-sm text-muji-ash">{{ $showArchived ? '所有旅程都是正常狀態。' : '點擊上方按鈕開始規劃您的第一趟旅途。' }}</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('modals')
    @auth
        <!-- Add Trip Modal -->
        <div id="add-trip-modal" class="fixed inset-0 z-[2000] hidden overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="safeCloseModal('add-trip-modal')"></div>
                <div class="relative transform overflow-hidden rounded-[40px] muji-glass text-left shadow-2xl transition-all w-full max-w-lg flex flex-col max-h-[calc(100vh-160px)]">
                    <!-- 統一右上角關閉按鈕 (X) - 移出捲軸容器 -->
                    <button onclick="safeCloseModal('add-trip-modal')" class="absolute top-6 right-6 text-muji-ash hover:text-muji-oak p-2 rounded-full hover:bg-muji-base transition-all group z-50">
                        <svg class="w-6 h-6 group-hover:rotate-90 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div class="px-5 sm:px-8 py-8 sm:py-10 overflow-y-auto custom-scrollbar scroll-smooth">
                        <div class="flex justify-between items-start mb-8 sm:mb-10 text-left">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-muji-base rounded-2xl text-muji-oak shadow-muji-sm font-black">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <div class="flex flex-col border-l-2 border-muji-edge pl-4">
                                    <h3 class="text-3xl font-black text-muji-ink leading-tight">新增旅程</h3>
                                    <p class="text-[10px] font-bold text-muji-ash uppercase tracking-[0.2em] mt-1">開啟您的下一段全新冒險</p>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('trips.store', ['user' => auth()->user()]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-6 gap-6">
                                <!-- 旅程名稱：佔滿全寬 -->
                                <div class="col-span-full">
                                    <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">旅程名稱</label>
                                    <input type="text" name="name" required placeholder="例如：2025 京阪神之旅" class="w-full px-4 py-3 muji-input">
                                </div>

                                <!-- 未定日期切換 -->
                                <div class="col-span-full border-b border-muji-edge pb-4 mb-2">
                                    <label class="flex items-center gap-2 cursor-pointer mt-2 w-max">
                                        <input type="checkbox" id="tbd_date_toggle_add" class="muji-checkbox" onchange="toggleTbdDateAdd(this)">
                                        <span class="text-sm font-bold text-muji-ink">尚未決定具體日期</span>
                                    </label>
                                </div>

                                <!-- 確切日期區塊 -->
                                <div id="exact_dates_add" class="col-span-full grid grid-cols-1 sm:grid-cols-6 gap-6 mt-[-1rem]">
                                    <div class="col-span-full sm:col-span-3">
                                        <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">開始日期</label>
                                        <input type="date" name="start_date" id="start_date_add" required class="w-full h-[46px] px-4 muji-input">
                                    </div>
                                    <div class="col-span-full sm:col-span-3">
                                        <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">結束日期</label>
                                        <input type="date" name="end_date" id="end_date_add" required class="w-full h-[46px] px-4 muji-input">
                                    </div>
                                </div>

                                <!-- 預估天數區塊 -->
                                <div id="estimated_days_add" class="col-span-full sm:col-span-6 hidden mt-[-1rem]">
                                    <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">預計天數</label>
                                    <input type="number" name="estimated_days" id="estimated_days_input_add" min="1" placeholder="例如：5" class="w-full h-[46px] px-4 muji-input">
                                </div>

                                <script>
                                    function toggleTbdDateAdd(checkbox) {
                                        const exactDates = document.getElementById('exact_dates_add');
                                        const estimatedDays = document.getElementById('estimated_days_add');
                                        const startIn = document.getElementById('start_date_add');
                                        const endIn = document.getElementById('end_date_add');
                                        const estIn = document.getElementById('estimated_days_input_add');

                                        if (checkbox.checked) {
                                            exactDates.classList.add('hidden');
                                            estimatedDays.classList.remove('hidden');
                                            startIn.removeAttribute('required');
                                            endIn.removeAttribute('required');
                                            estIn.setAttribute('required', 'required');
                                            startIn.value = '';
                                            endIn.value = '';
                                        } else {
                                            exactDates.classList.remove('hidden');
                                            estimatedDays.classList.add('hidden');
                                            startIn.setAttribute('required', 'required');
                                            endIn.setAttribute('required', 'required');
                                            estIn.removeAttribute('required');
                                            estIn.value = '';
                                        }
                                    }
                                </script>

                                <!-- 貨幣與匯率：各佔 1/3 (2/6) -->
                                @php
                                    $allCurrencies = [
                                        'TWD' => '台幣',
                                        'JPY' => '日幣',
                                        'KRW' => '韓幣',
                                        'USD' => '美金',
                                        'EUR' => '歐元',
                                        'GBP' => '英鎊',
                                        'AUD' => '澳幣',
                                        'CAD' => '加幣',
                                        'HKD' => '港幣',
                                        'SGD' => '新幣',
                                        'CNY' => '人民幣',
                                        'THB' => '泰銖',
                                        'VND' => '越南盾',
                                        'MYR' => '馬幣',
                                    ];
                                @endphp
                                <div class="col-span-full sm:col-span-2">
                                    <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">本國貨幣</label>
                                    <select name="base_currency" required class="w-full px-4 py-3 muji-input text-center font-black">
                                        @foreach($allCurrencies as $code => $label)
                                            <option value="{{ $code }}" {{ $code == 'TWD' ? 'selected' : '' }}>{{ $code }} — {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-full sm:col-span-2">
                                    <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">當地貨幣</label>
                                    <select name="target_currency" required class="w-full px-4 py-3 muji-input text-center font-black bg-muji-base/30">
                                        @foreach($allCurrencies as $code => $label)
                                            <option value="{{ $code }}" {{ $code == 'JPY' ? 'selected' : '' }}>{{ $code }} — {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-full sm:col-span-2">
                                    <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">預估匯率</label>
                                    <div class="relative">
                                        <input type="number" step="0.0001" name="exchange_rate" required value="" class="w-full px-4 py-3 muji-input font-mono text-center font-black">
                                        <button type="button" onclick="fetchLiveRate(event)" class="mt-2 w-full py-2 bg-muji-base text-muji-oak text-[10px] font-black rounded-lg border border-muji-edge hover:bg-muji-wheat/20 transition-all flex items-center justify-center gap-1">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            獲取即時匯率
                                        </button>
                                    </div>
                                </div>

                                <script>
                                    async function fetchLiveRate(e) {
                                        const btn = e.currentTarget;
                                        const form = btn.closest('form');
                                        const base = form.querySelector('select[name="base_currency"]').value;
                                        const target = form.querySelector('select[name="target_currency"]').value;
                                        const originalHtml = btn.innerHTML;

                                        btn.disabled = true;
                                        btn.innerHTML = '...';

                                        try {
                                            const response = await fetch(`{{ route('trip.exchange_rate', ['user' => $user]) }}?base=${base}&target=${target}`);
                                            const data = await response.json();

                                            if (data.rate) {
                                                form.querySelector('input[name="exchange_rate"]').value = data.rate.toFixed(4);
                                                showToast('匯率已更新！', 'success');
                                            } else {
                                                showToast(data.error || '無法取得匯率', 'error');
                                            }
                                        } catch (e) {
                                            showToast('請求失敗', 'error');
                                        } finally {
                                            btn.disabled = false;
                                            btn.innerHTML = originalHtml;
                                        }
                                    }
                                </script>
                            </div>

                            <!-- 旅程封面圖設定 -->
                            <div class="mt-8 pt-6 border-t border-muji-edge">
                                <div class="flex justify-between items-center mb-4">
                                    <label class="block w-full text-left text-sm font-bold text-muji-ash ml-1">旅程封面圖設定</label>
                                </div>
                                <div class="bg-muji-base/30 p-4 rounded-xl border border-muji-edge">
                                    <input type="file" name="cover_image" accept="image/*" class="block w-full text-xs text-muji-ash file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-muji-paper file:text-muji-oak hover:file:bg-muji-base cursor-pointer transition-all shadow-muji-sm">
                                    <p class="text-[10px] text-muji-ash mt-2 italic shadow-muji-sm p-2 bg-muji-paper/50 rounded-lg">※ 支援 JPG、PNG，目前上限 2MB（因主機設定）。建議使用清爽的風景照。</p>
                                </div>
                            </div>
                            <div class="pt-8 mt-8 border-t border-muji-edge flex gap-4">
                                <button type="button" onclick="safeCloseModal('add-trip-modal')" class="flex-1 h-[46px] flex items-center justify-center border border-muji-edge rounded-[24px] text-muji-ash bg-muji-paper hover:bg-muji-base transition-colors font-black text-sm">
                                    取消
                                </button>
                                <button type="submit" class="flex-1 h-[46px] flex items-center justify-center border border-transparent rounded-[24px] text-white bg-muji-oak hover:opacity-90 shadow-muji transition-all font-black active:scale-95 text-sm">
                                    立即建立
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endauth
@endpush