@php $isShared = $isShared ?? false; @endphp
@extends('layout')

@section('content')
    <div class="mb-4">
        @php
            $exReturnLink = $isShared 
                ? route('trip.index_shared', ['token' => $trip->share_token])
                : route('trip.show', ['user' => $trip->user, 'trip' => $trip]);
        @endphp
        <a href="{{ $exReturnLink }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors gap-1">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7" /></svg>
            返回 
            <!-- {{ $trip->name }} -->
        </a>
    </div>

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900">總花費</h1>
            <!-- <p class="text-gray-500 mt-1">{{ $trip->name }}</p> -->
        </div>
        <div class="text-right">
            <span class="text-4xl font-bold text-indigo-600">{{ $trip->base_currency }} {{ number_format($totalBase) }}</span>
            <p class="text-sm font-medium text-gray-500">約合 {{ $trip->target_currency }} {{ number_format($totalTarget) }}</p>
        </div>
    </div>

    <!-- Category Breakdown -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        @foreach($byCategory as $cat => $amount)
            <div class="bg-white/40 backdrop-blur-md p-4 rounded-xl shadow-sm border border-white/20">
                <p class="text-sm text-gray-400 uppercase font-semibold">
                    @if($cat == 'Food') 飲食
                    @elseif($cat == 'Transport') 交通
                    @elseif($cat == 'Shopping') 購物
                    @elseif($cat == 'Accommodation') 住宿
                    @else 其他
                    @endif
                </p>
                <p class="text-lg font-bold text-gray-800">{{ $trip->base_currency }} {{ number_format($amount) }}</p>
            </div>
        @endforeach
    </div>

    @if(count($externalCosts) > 0)
    <!-- External Costs -->
    <div class="bg-indigo-50/40 backdrop-blur-md rounded-2xl p-6 mb-8 border border-white/20 shadow-sm">
        <h2 class="text-lg font-bold text-indigo-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            機票與住宿 (外部預訂)
        </h2>
        <div class="space-y-3 mb-4">
            @foreach($externalCosts as $cost)
                <div class="flex justify-between items-center bg-white/40 p-3 rounded-lg border border-white/10 mb-2">
                    <span class="font-medium text-indigo-800">{{ $cost['name'] }}</span>
                    <span class="font-mono text-indigo-600 font-bold">{{ $cost['raw'] }}</span>
                </div>
            @endforeach
        </div>
        <div class="border-t border-indigo-200 pt-3 flex flex-wrap gap-4 mt-2">
            <span class="text-sm font-semibold text-indigo-800">各幣別加總：</span>
            @foreach($externalTotals as $currency => $amount)
                <span class="text-indigo-900 font-bold font-mono bg-indigo-100 px-3 py-1 rounded-full">
                    {{ $currency }} {{ number_format($amount) }}
                </span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Quick Add Form (Inline alternative or just rely on FAB) -->
    <!-- Let's just list recent expenses -->
    <div class="bg-white/40 backdrop-blur-md rounded-2xl shadow-sm border border-white/20 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">帳單紀錄明細</h2>
        </div>

        <div class="grid md:grid-cols-2 gap-4 p-4">
            @forelse($expenses as $expense)
                <div class="bg-white/40 backdrop-blur-md p-4 rounded-xl border border-white/20 shadow-sm hover:shadow-md transition-shadow relative group">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">
                                @if($expense->category == 'Food') 🍔
                                @elseif($expense->category == 'Transport') 🚇
                                @elseif($expense->category == 'Shopping') 🛍️
                                @elseif($expense->category == 'Accommodation') 🏨
                                @else 💸
                                @endif
                            </span>
                            <div>
                                <p class="font-bold text-gray-900">{{ $expense->description }}</p>
                                <p class="text-xs text-gray-500">{{ $expense->date->format('Y-m-d') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-mono font-bold text-lg {{ $expense->is_base_currency ? 'text-indigo-600' : 'text-gray-900' }}">
                                {{ $expense->is_base_currency ? $trip->base_currency : $trip->target_currency }} {{ number_format($expense->amount) }}
                            </span>
                            @if(!$expense->is_base_currency)
                                <p class="text-xs text-gray-400">約 {{ $trip->base_currency }} {{ number_format($expense->amount * $trip->exchange_rate) }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100">
                        <span class="text-xs font-semibold px-2 py-1 rounded bg-gray-100 text-gray-600">
                            @if($expense->category == 'Food') 飲食
                            @elseif($expense->category == 'Transport') 交通
                            @elseif($expense->category == 'Shopping') 購物
                            @elseif($expense->category == 'Accommodation') 住宿
                            @else 其他
                            @endif
                        </span>

                        @if(!$isShared)
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                @auth
                                    @php $exTotalDelFormId = 'del-ex-total-' . $expense->id; @endphp
                                    <button onclick='openExpenseModal(@json($expense))' class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="編輯">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <form id="{{ $exTotalDelFormId }}" action="{{ route('expenses.destroy', ['user' => $trip->user, 'expense' => $expense->id]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('刪除消費紀錄？', '確定要刪除「{{ $expense->description }}」這筆花費嗎？', '{{ $exTotalDelFormId }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="刪除">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full p-8 text-center text-gray-400">
                    目前沒有紀錄。
                </div>
            @endforelse
        </div>
    </div>
@endsection