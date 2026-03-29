@php $isShared = $isShared ?? false; @endphp
@extends('layout')

@section('content')
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl mb-2">Elk & Winnie's Honeymoon Trip to Japan</h2>
        <p class="text-lg text-gray-500">2025/12/28（日） - 2026/1/8（四）</p>
    </div>

    <!-- Flight Info -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 relative overflow-hidden group hover:shadow-md transition-shadow">
        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full -mr-10 -mt-10 blur-2xl group-hover:bg-indigo-100 transition-colors"></div>

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 relative z-10">
            <div>
                <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    航班資訊
                </h3>
                <p class="text-sm text-gray-500 mt-1">{{ $flights['airline'] }} • {{ $flights['price'] }}</p>
            </div>
            <div class="mt-2 md:mt-0">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                    {{ $flights['baggage'] }}
                </span>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8 relative z-10">
            <!-- Outbound -->
            <div class="flex items-start gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-3 h-3 bg-indigo-500 rounded-full mb-1"></div>
                    <div class="w-0.5 h-full bg-indigo-100"></div>
                </div>
                <div>
                    <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">去程 (Outbound)</span>
                    <p class="font-bold text-gray-900">{{ $flights['outbound']['route'] }}</p>
                    <p class="text-sm text-gray-600">{{ $flights['outbound']['date'] }}</p>
                    <p class="text-lg font-mono font-medium text-gray-800 mt-1">{{ $flights['outbound']['time'] }}</p>
                </div>
            </div>

            <!-- Inbound -->
            <div class="flex items-start gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-3 h-3 bg-purple-500 rounded-full mb-1"></div>
                    <div class="w-0.5 h-full bg-purple-100"></div>
                </div>
                <div>
                    <span class="text-xs font-semibold text-purple-600 uppercase tracking-wider">回程 (Inbound)</span>
                    <p class="font-bold text-gray-900">{{ $flights['inbound']['route'] }}</p>
                    <p class="text-sm text-gray-600">{{ $flights['inbound']['date'] }}</p>
                    <p class="text-lg font-mono font-medium text-gray-800 mt-1">{{ $flights['inbound']['time'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($days as $day)
            @php
                $isToday = $day['date'] == date('n/j');
            @endphp
            <a href="{{ route('day.show', ['date' => str_replace('/', '-', $day['date'])]) }}" class="group relative block rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden border {{ $isToday ? 'bg-indigo-50 border-indigo-200 ring-2 ring-indigo-200' : 'bg-white border-gray-100' }}">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>

                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-600 border border-indigo-100">
                            {{ $day['date'] }} ({{ $day['day'] }})
                        </span>
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ $day['location'] }}</span>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">
                        {{ $day['title'] }}
                    </h3>

                    <p class="text-sm text-gray-500 line-clamp-2 mb-3">
                        {{ $day['summary'] }}
                    </p>

                    @if($day['total_expense'] > 0)
                        <div class="flex items-center gap-1 text-indigo-600 font-bold text-sm bg-indigo-50 inline-block px-2 py-1 rounded-lg">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            ¥{{ number_format($day['total_expense']) }}
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between text-xs font-medium text-gray-400 group-hover:bg-indigo-50/10 transition-colors">
                    <span>View Detail</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Checklists Section -->
    <div class="grid md:grid-cols-2 gap-8 mt-12">
        <!-- Must Buy List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-pink-100 flex items-center justify-center text-pink-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </span>
                必買清單 (購物)
            </h3>
            <div class="space-y-6">
                @foreach($shoppingList as $category => $items)
                    <div>
                        <h4 class="font-bold text-gray-700 text-sm mb-2 border-l-4 border-pink-400 pl-2">{{ $category }}</h4>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($items as $item)
                                <li class="flex items-start justify-between gap-2 text-sm text-gray-600 group">
                                    <div class="flex items-start gap-2">
                                        <input type="checkbox" class="mt-1 rounded text-pink-500 focus:ring-pink-500 persist-chk disabled:opacity-50 disabled:cursor-not-allowed" data-key="chk_{{ $item->id }}" @if(!request()->cookie('admin_session')) disabled @endif>
                                        <span class="@if(!request()->cookie('admin_session')) text-gray-400 @endif">{{ $item->name }}</span>
                                    </div>
                                    @if(request()->cookie('admin_session'))
                                        <form action="{{ route('checklist.destroy', $item->id) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600 p-0.5" onclick="return confirm('確定刪除？')">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

                @if(request()->cookie('admin_session'))
                    <form action="{{ route('checklist.store') }}" method="POST" class="mt-4 pt-4 border-t border-gray-100">
                        @csrf
                        <input type="hidden" name="type" value="shopping">
                        <div class="flex gap-2">
                            <input type="text" name="category" placeholder="分類" class="w-1/3 rounded-lg border-gray-300 text-sm p-2" required list="shop_categories">
                             <datalist id="shop_categories">
                                @foreach($shoppingList->keys() as $cat)
                                    <option value="{{ $cat }}">
                                @endforeach
                            </datalist>
                            <input type="text" name="name" placeholder="項目名稱" class="w-2/3 rounded-lg border-gray-300 text-sm p-2" required>
                            <button type="submit" class="bg-pink-500 text-white p-2 rounded-lg hover:bg-pink-600">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <!-- Must Go List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </span>
                想去景點
            </h3>
            <div class="space-y-6">
                @foreach($spotList as $category => $items)
                    <div>
                        <h4 class="font-bold text-gray-700 text-sm mb-2 border-l-4 border-blue-400 pl-2">{{ $category }}</h4>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($items as $item)
                                <li class="flex items-start justify-between gap-2 text-sm text-gray-600 group">
                                    <div class="flex items-start gap-2">
                                        <input type="checkbox" class="mt-1 rounded text-blue-500 focus:ring-blue-500 persist-chk disabled:opacity-50 disabled:cursor-not-allowed" data-key="chk_{{ $item->id }}" @if(!request()->cookie('admin_session')) disabled @endif>
                                        <span class="@if(!request()->cookie('admin_session')) text-gray-400 @endif">{{ $item->name }}</span>
                                    </div>
                                    @if(request()->cookie('admin_session'))
                                        <form action="{{ route('checklist.destroy', $item->id) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600 p-0.5" onclick="return confirm('確定刪除？')">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

                @if(request()->cookie('admin_session'))
                    <form action="{{ route('checklist.store') }}" method="POST" class="mt-4 pt-4 border-t border-gray-100">
                        @csrf
                        <input type="hidden" name="type" value="spot">
                        <div class="flex gap-2">
                            <input type="text" name="category" placeholder="區域" class="w-1/3 rounded-lg border-gray-300 text-sm p-2" required list="spot_categories">
                            <datalist id="spot_categories">
                                @foreach($spotList->keys() as $cat)
                                    <option value="{{ $cat }}">
                                @endforeach
                            </datalist>
                            <input type="text" name="name" placeholder="景點名稱" class="w-2/3 rounded-lg border-gray-300 text-sm p-2" required>
                            <button type="submit" class="bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
        <div class="bg-indigo-50 rounded-xl p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-indigo-800 uppercase">Internet</p>
                    <p class="text-sm font-medium text-indigo-900">WaySim eSIM ($2,037)</p>
                </div>
            </div>
        </div>
        <div class="bg-purple-50 rounded-xl p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 rounded-lg text-purple-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-purple-800 uppercase">Payment Alert</p>
                    <p class="text-sm font-medium text-purple-900">1/2 福袋日 / 1/7 環球</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.persist-chk');

            checkboxes.forEach(chk => {
                const key = chk.getAttribute('data-key');
                const saved = localStorage.getItem(key);
                if (saved === 'true') {
                    chk.checked = true;
                }

                chk.addEventListener('change', function () {
                    localStorage.setItem(key, this.checked);
                });
            });
        });
    </script>
@endsection