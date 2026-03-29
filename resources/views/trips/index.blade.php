@php $isShared = $isShared ?? false; @endphp
@extends('layout')

@section('title', 'My Trips Dashboard')

@section('content')
<div class="max-w-6xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-10">
        <div>
            <h1 class="text-4xl font-extrabold text-white tracking-tight sm:text-5xl">
                旅程嘉年華 <span class="text-indigo-400">My Journeys</span>
            </h1>
            <p class="mt-2 text-xl text-gray-400">規劃下一次的冒險，或是重溫美好的回憶。</p>
        </div>
        @auth
            <button onclick="safeOpenModal('add-trip-modal')" class="inline-flex items-center px-8 py-3 bg-indigo-600 text-white font-bold rounded-2xl shadow-xl hover:bg-indigo-700 transition-all duration-300 transform hover:scale-105 active:scale-95">
                <svg class="-ml-1 mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                新增旅程 Add Trip
            </button>
        @endauth
    </div>
    @php $showArchived = request('archived') == '1'; @endphp
    @auth
    <div class="flex justify-end mb-4">
        <a href="{{ request()->fullUrlWithQuery(['archived' => $showArchived ? '0' : '1']) }}"
           class="flex items-center gap-2 text-xs font-bold px-3 py-1.5 rounded-full border transition-all {{ $showArchived ? 'bg-red-50 text-red-600 border-red-200' : 'bg-white/10 text-gray-300 border-white/20 hover:bg-white/20' }}">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12M10 12v4m4-4v4" /></svg>
            {{ $showArchived ? '隱藏封存' : '查看封存' }}
        </a>
    </div>
    @endauth

    @php
        $tripsToShow = $showArchived
            ? $trips->filter(fn($t) => $t->trashed())
            : $trips->filter(fn($t) => !$t->trashed());
    @endphp

    <div class="grid grid-cols-1 gap-y-10 gap-x-8 sm:grid-cols-2 lg:grid-cols-3 xl:gap-x-12">
        @forelse($tripsToShow as $t)
            @php $isArchived = $t->trashed(); @endphp
            <div class="relative group">
                <a href="{{ $isArchived ? '#' : route('trip.show', ['user' => auth()->user(), 'trip' => $t]) }}" 
                   class="block relative bg-white/10 backdrop-blur-md border rounded-3xl overflow-hidden transition-all duration-500 transform hover:-translate-y-2 hover:shadow-2xl {{ $isArchived ? 'border-2 border-dashed border-red-300 opacity-60 hover:opacity-80' : 'border-white/20 hover:bg-white/20 hover:shadow-indigo-500/20' }}">
                    <div class="aspect-w-3 aspect-h-2 w-full overflow-hidden">
                        <img src="{{ $t->cover_image ? asset('storage/' . $t->cover_image) : asset('bg.jpg') }}" 
                             onerror="this.src='https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?q=80&w=2070&auto=format&fit=crop'"
                             alt="{{ $t->name }}" 
                             class="h-64 w-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent opacity-60"></div>
                    </div>
                    <div class="absolute bottom-0 p-6 w-full">
                        <div class="flex items-center space-x-2 text-slate-300 text-sm font-semibold mb-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ \Carbon\Carbon::parse($t->start_date)->format('Y/m/d') }} - {{ \Carbon\Carbon::parse($t->end_date)->format('Y/m/d') }}</span>
                        </div>
                        <h3 class="text-2xl font-bold text-white group-hover:text-indigo-300 transition-colors">
                            {{ $t->name }}
                        </h3>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/10 text-gray-300">
                                {{ $isArchived ? '已封存' : $t->days->count() . ' 天行程' }}
                            </span>
                        </div>
                    </div>
                </a>

                @auth
                @if($isArchived)
                    <div class="absolute top-3 right-3 z-10 flex gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                        <form action="{{ route('trips.restore', ['user' => auth()->user(), 'tripId' => $t->id]) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="p-2 bg-green-50 text-green-600 rounded-xl hover:bg-green-100 shadow-sm transition-colors" title="還原">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            </button>
                        </form>
                        @php $forceId = 'force-trip-' . $t->id; @endphp
                        <form id="{{ $forceId }}" action="{{ route('trips.forceDelete', ['user' => auth()->user(), 'tripId' => $t->id]) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDelete('永久刪除？', '此旅程將被永久刪除，無法復原！', '{{ $forceId }}')" class="p-2 bg-red-100 text-red-600 rounded-xl hover:bg-red-200 shadow-sm transition-colors" title="永久刪除">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </form>
                    </div>
                @else
                    @php $archiveId = 'archive-trip-' . $t->id; @endphp
                    <form id="{{ $archiveId }}" action="{{ route('trips.destroy', ['user' => auth()->user(), 'trip' => $t]) }}" method="POST" class="absolute top-3 right-3 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                        @csrf @method('DELETE')
                        <button type="button" onclick="confirmDelete('封存旅程？', '此旅程將被封存，可在「查看封存」中還原。', '{{ $archiveId }}')" class="p-2 bg-red-50/90 text-red-500 rounded-xl hover:bg-red-100 hover:text-red-600 shadow-sm transition-colors backdrop-blur-sm" title="封存旅程">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12M10 12v4m4-4v4" /></svg>
                        </button>
                    </form>
                @endif
                @endauth
            </div>
        @empty
            <div class="col-span-full py-20 text-center bg-white/5 border border-dashed border-white/20 rounded-3xl">
                <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-white">{{ $showArchived ? '沒有封存的旅程' : '尚未建立旅程' }}</h3>
                <p class="mt-1 text-sm text-gray-400">{{ $showArchived ? '所有旅程都是正常狀態。' : '點擊上方按鈕開始規劃您的第一趟旅途。' }}</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('modals')
@auth
<!-- Add Trip Modal -->
<div id="add-trip-modal" class="fixed inset-0 z-[2000] hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="safeCloseModal('add-trip-modal')"></div>
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl flex flex-col max-h-[calc(100vh-160px)]">
            <div class="px-8 py-10 overflow-y-auto custom-scrollbar scroll-smooth">
                <div class="flex justify-between items-start mb-10">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-indigo-50 rounded-2xl text-indigo-600 shadow-sm">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div class="flex flex-col border-l-2 border-indigo-100 pl-4">
                            <h3 class="text-3xl font-bold text-slate-900 leading-tight">新增旅程</h3>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1">Create New Adventure</p>
                        </div>
                    </div>
                    <button onclick="safeCloseModal('add-trip-modal')" class="text-gray-300 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100 transition-all">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('trips.store', ['user' => auth()->user()]) }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">旅程名稱 Trip Name</label>
                            <input type="text" name="name" required placeholder="例如：2025 京阪神之旅" class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">開始日期</label>
                                <input type="date" name="start_date" required class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">結束日期</label>
                                <input type="date" name="end_date" required class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">匯率匯率 (換 1 {{ auth()->user()->target_currency ?? 'JPY' }})</label>
                                <input type="number" step="0.0001" name="exchange_rate" required value="0.21" class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium">
                            </div>
                        </div>
                        <div class="pt-6 mt-8 border-t border-gray-100 flex gap-4">
                            <button type="button" onclick="safeCloseModal('add-trip-modal')" class="flex-1 px-4 py-4 border border-gray-200 rounded-2xl text-gray-700 bg-gray-50 hover:bg-gray-200 transition-colors font-black shadow-sm">
                                取消 Cancel
                            </button>
                            <button type="submit" class="flex-1 px-4 py-4 border border-transparent rounded-2xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-xl hover:shadow-indigo-200 transition-all font-black active:scale-95">
                                立即建立 Create Trip
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endauth
@endpush
