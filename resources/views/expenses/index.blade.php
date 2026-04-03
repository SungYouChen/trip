@php $isShared = $isShared ?? false; @endphp
@extends('layout')
@section('title', '花費統計')
@section('content')
    @push('modals')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush

    <div class="mb-4">
        @php
            $exReturnLink = $isShared 
                ? route('trip.index_shared', ['token' => $trip->share_token])
                : route('trip.show', ['user' => $trip->user, 'trip' => $trip]);
        @endphp
        <a href="{{ $exReturnLink }}" class="inline-flex items-center text-sm font-black text-muji-oak hover:underline transition-all gap-1">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7" /></svg>
            返回
        </a>
    </div>

    <div class="mb-8 flex flex-col md:flex-row items-stretch gap-6">
        <div class="flex-1 flex flex-col justify-center">
            <h1 class="text-3xl font-black text-muji-ink tracking-tight">花費統計</h1>
            <p class="text-muji-ash mt-1 font-medium italic opacity-60">讓每一分錢都花得清清楚楚</p>
            
            <div class="mt-6 bg-muji-base/50 p-6 rounded-3xl border border-muji-edge shadow-muji-sm">
                <span class="text-[10px] font-black text-muji-ash uppercase tracking-widest block mb-2">預計總花費</span>
                <span class="text-5xl font-black text-muji-oak leading-none">{{ $trip->base_currency }} {{ number_format($totalBase) }}</span>
                <p class="text-xs font-black text-muji-ash uppercase tracking-tight mt-3">約合 {{ $trip->target_currency }} {{ number_format($totalTarget) }}</p>
            </div>
        </div>
        
        <div class="w-full md:w-72 h-72 bg-white/40 backdrop-blur-sm rounded-3xl border border-muji-edge p-6 flex items-center justify-center shadow-muji">
            <canvas id="expenseChart"></canvas>
        </div>
    </div>

    <!-- Category Breakdown -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        @foreach($byCategory as $cat => $amount)
            <div class="muji-card p-4 rounded-2xl border border-muji-edge shadow-muji-sm bg-white/60">
                <p class="text-[10px] text-muji-ash uppercase font-black tracking-widest mb-2">
                    @if($cat == 'Food') 飲食
                    @elseif($cat == 'Transport') 交通
                    @elseif($cat == 'Shopping') 購物
                    @elseif($cat == 'Accommodation') 住宿
                    @else 其他
                    @endif
                </p>
                <p class="text-xl font-black text-muji-ink">{{ $trip->base_currency }} {{ number_format($amount) }}</p>
            </div>
        @endforeach
    </div>

    @if(count($externalCosts) > 0)
    <!-- External Costs -->
    <div class="bg-muji-base/40 backdrop-blur-sm rounded-3xl p-8 mb-8 border border-muji-edge shadow-muji">
        <h2 class="text-xl font-black text-muji-ink mb-6 flex items-center gap-3">
            <svg class="w-6 h-6 text-muji-oak" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            機票與住宿 (外部預訂)
        </h2>
        <div class="space-y-4 mb-6">
            @foreach($externalCosts as $cost)
                <div class="flex justify-between items-center bg-white/60 p-4 rounded-xl border border-muji-edge shadow-muji-sm">
                    <span class="font-black text-muji-ink">{{ $cost['name'] }}</span>
                    <span class="font-mono text-muji-oak font-black text-lg">{{ $cost['raw'] }}</span>
                </div>
            @endforeach
        </div>
        <div class="border-t border-muji-edge pt-4 flex flex-wrap gap-4 mt-2">
            <span class="text-xs font-black text-muji-ash uppercase tracking-widest">各幣別加總 Total by Currency：</span>
            <div class="flex flex-wrap gap-2">
                @foreach($externalTotals as $currency => $amount)
                    <span class="text-muji-ink font-black font-mono bg-muji-wheat px-4 py-1.5 rounded-full border border-muji-edge shadow-muji-sm text-sm">
                        {{ $currency }} {{ number_format($amount) }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Add Form (Inline alternative or just rely on FAB) -->
    <!-- Let's just list recent expenses -->
    <div class="bg-white/40 backdrop-blur-md rounded-2xl shadow-sm border border-white/20 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">帳單紀錄明細</h2>
        </div>

                        <div class="grid md:grid-cols-2 gap-4 p-4 mt-2">
                            @forelse($expenses as $expense)
                                <div class="muji-card p-5 rounded-2xl border border-muji-edge shadow-muji-sm bg-white/60 hover:shadow-muji transition-all relative group">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-3">
                                            <span class="w-10 h-10 bg-muji-base rounded-xl flex items-center justify-center text-xl shadow-muji-sm">
                                                @if($expense->category == 'Food') 🍔
                                                @elseif($expense->category == 'Transport') 🚇
                                                @elseif($expense->category == 'Shopping') 🛍️
                                                @elseif($expense->category == 'Accommodation') 🏨
                                                @else 💸
                                                @endif
                                            </span>
                                            <div>
                                                <p class="font-black text-muji-ink">{{ $expense->description }}</p>
                                                <p class="text-[10px] font-black text-muji-ash uppercase tracking-widest mt-1">{{ $expense->date->format('Y/m/d') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-mono font-black text-lg {{ $expense->is_base_currency ? 'text-muji-oak' : 'text-muji-ink' }}">
                                                {{ $expense->is_base_currency ? $trip->base_currency : $trip->target_currency }} {{ number_format($expense->amount) }}
                                            </span>
                                            @if(!$expense->is_base_currency)
                                                <p class="text-[10px] text-muji-ash font-medium italic">≈ {{ $trip->base_currency }} {{ number_format($expense->amount / max($trip->exchange_rate, 0.0001)) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center pt-4 border-t border-muji-edge">
                                        <span class="text-[10px] font-black px-2.5 py-1 rounded-lg bg-muji-base/50 text-muji-oak border border-muji-edge uppercase tracking-widest">
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

    @push('modals')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('expenseChart');
            if(!ctx) return;
            
            const categoryData = @json($byCategory);
            const labels = Object.keys(categoryData).map(cat => {
                if(cat == 'Food') return '飲食 🍔';
                if(cat == 'Transport') return '交通 🚇';
                if(cat == 'Shopping') return '購物 🛍️';
                if(cat == 'Accommodation') return '住宿 🏨';
                if(cat == 'Flight') return '機票 ✈️';
                if(cat == 'Entertainment') return '娛樂 🎡';
                return '其他 💡';
            });
            const values = Object.values(categoryData);

            new Chart(ctx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: [
                            '#9c8c7c', // Muji Oak
                            '#dcd3c1', // Muji Wheat
                            '#e8e4db', // Muji Edge
                            '#757575', // Muji Ash
                            '#333333', // Muji Ink
                            '#f8f5f0', // Muji Base
                            '#c0b4a4'  // Muji Earth
                        ],
                        borderWidth: 0,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: 12
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#333333',
                            bodyColor: '#757575',
                            borderColor: '#e8e4db',
                            borderWidth: 1,
                            padding: 12,
                            cornerRadius: 12,
                            bodyFont: {
                                size: 10,
                                weight: 'bold'
                            },
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) label += ': ';
                                    const val = context.raw;
                                    label += '{{ $trip->base_currency }} ' + val.toLocaleString();
                                    return label;
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        });
    </script>
    @endpush
@endsection