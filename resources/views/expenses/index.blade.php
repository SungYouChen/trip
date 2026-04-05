@php $isShared = $isShared ?? false; @endphp
@extends('layout')
@section('title', '花費統計')
@section('header_title', '花費統計')
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

    <div class="mb-10 muji-card p-4 sm:p-8 md:p-10 shadow-muji bg-muji-paper/80 backdrop-blur-xl relative overflow-hidden" style="border-radius: 3rem;">
        <!-- Subtle Background Accent -->
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-muji-oak/5 rounded-full blur-3xl"></div>
        
        <div class="flex flex-col lg:flex-row items-center gap-10 relative z-10">
            <!-- Chart Container - Compact Hero -->
            <div class="relative w-64 h-64 md:w-[320px] md:h-[320px] flex-shrink-0 group">
                <canvas id="expenseChart"></canvas>
            </div>
            
            <!-- Summary Area - Restructured for No-Overflow -->
            <div class="flex-1 w-full">
                <div class="mb-8 text-center lg:text-left">
                    <h1 class="text-3xl md:text-4xl font-black text-muji-ink tracking-tighter mb-1">花費統計</h1>
                    <p class="text-muji-ash font-medium italic opacity-50 tracking-widest text-[9px] uppercase">Financial Data Visualization</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-1.5">
                    @php 
                        $chartColors = [
                            '#9c8c7c', '#8a8a8a', '#a5ad94', '#b59b9b', '#dcd3c1', '#4b453d', '#757575'
                        ];
                        $colorIdx = 0;
                    @endphp
                    @foreach($byCategory as $cat => $amount)
                        <div class="expense-row flex items-center justify-between py-2.5 border-b border-muji-edge/20 transition-all hover:bg-muji-base/50 px-3 rounded-2xl group cursor-help overflow-hidden" 
                             data-index="{{ $colorIdx }}" 
                             onmouseenter="highlightChartSegment({{ $colorIdx }})" 
                             onmouseleave="resetChartHighlight()">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-2.5 h-2.5 rounded-full shadow-sm ring-2 ring-white flex-shrink-0" style="background-color: {{ $chartColors[$colorIdx % count($chartColors)] }}"></div>
                                <div class="flex items-baseline gap-2 min-w-0">
                                    <span class="text-xs font-black text-muji-ink truncate max-w-[80px]">
                                        @if($cat == 'Food') 飲食
                                        @elseif($cat == 'Transport') 交通
                                        @elseif($cat == 'Shopping') 購物
                                        @elseif($cat == 'Accommodation') 住宿
                                        @elseif($cat == 'Flight') 機票
                                        @elseif($cat == 'Entertainment') 娛樂
                                        @else 其他
                                        @endif
                                    </span>
                                    <span class="text-[9px] text-muji-ash/50 font-black tracking-tighter">{{ round(($amount / max($totalBase, 1)) * 100) }}%</span>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-[12px] font-black text-muji-oak font-mono">{{ number_format($amount) }}</span>
                            </div>
                        </div>
                        @php $colorIdx++; @endphp
                    @endforeach
                </div>

                <div class="mt-10 pt-6 border-t border-muji-edge/20 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="bg-white/90 backdrop-blur-xl px-6 py-3.5 rounded-[2rem] border border-muji-edge shadow-muji-sm flex flex-col min-w-[200px]">
                        <span class="text-[9px] font-black text-muji-ash uppercase tracking-widest block mb-0.5">ESTIMATED TOTAL {{ $trip->target_currency }}</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-xl font-black text-muji-ink font-mono tracking-tighter">{{ number_format($totalTarget) }}</span>
                            <span class="text-[10px] font-black text-muji-oak">{{ $trip->target_currency }}</span>
                        </div>
                    </div>
                    
                    <div class="flex flex-col text-right px-2">
                        <span class="text-[8px] font-black text-muji-ash uppercase tracking-[0.2em] mb-2 opacity-50">Exchange Rate System</span>
                        <div class="flex items-center gap-2 justify-end">
                            <div class="bg-muji-ink text-white px-3 py-1 rounded-full text-[9px] font-black scale-90 origin-right">
                                1 {{ $trip->base_currency }}
                            </div>
                            <svg class="w-3 h-3 text-muji-ash" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            <div class="bg-white border border-muji-edge px-3 py-1 rounded-full text-[9px] font-black text-muji-ink shadow-sm scale-90 origin-right">
                                {{ $trip->exchange_rate }} {{ $trip->target_currency }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @if(count($externalCosts) > 0)
    <!-- External Costs Section -->
    <div class="mb-10 mt-16">
        <div class="flex items-center gap-4 mb-8 pl-4">
            <div class="h-px bg-muji-edge flex-1"></div>
            <h2 class="text-xs font-black text-muji-ash uppercase tracking-[0.5em] whitespace-nowrap">大型外部支出紀錄</h2>
            <div class="h-px bg-muji-edge flex-1"></div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($externalCosts as $cost)
                <div class="muji-card p-6 bg-muji-paper/50 rounded-[2rem] border border-muji-edge/30 shadow-muji-sm group hover:scale-[1.02] transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2.5 bg-muji-base rounded-xl text-muji-oak shadow-muji-sm group-hover:bg-muji-oak group-hover:text-white transition-colors">
                            @if(\Illuminate\Support\Str::contains($cost['name'], '機票'))
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            @endif
                        </div>
                    </div>
                    <p class="text-xs font-black text-muji-ash uppercase tracking-widest mb-1">{{ \Illuminate\Support\Str::contains($cost['name'], '機票') ? 'FLIGHT' : 'STAY' }}</p>
                    <h3 class="text-sm font-black text-muji-ink mb-4">{{ $cost['name'] }}</h3>
                    <div class="pt-4 border-t border-muji-edge/30 text-right">
                        <span class="font-mono text-lg font-black text-muji-oak">{{ $cost['raw'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Transaction List Section -->
    <div class="mt-20">
        <div class="flex justify-between items-end mb-8 px-6">
            <div>
                <h2 class="text-2xl font-black text-muji-ink tracking-tight">消費明細</h2>
                <p class="text-[10px] text-muji-ash font-black uppercase tracking-[0.2em] mt-1">Detailed Transaction History</p>
            </div>
            <div class="hidden sm:block">
                <span class="text-[11px] font-black text-muji-ash/30 uppercase tracking-[0.3em]">Transactions: {{ count($expenses) }}</span>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            @forelse($expenses as $expense)
                <div class="muji-card p-6 bg-white/60 backdrop-blur rounded-[2.5rem] border border-muji-edge/30 shadow-muji-sm hover:shadow-muji transition-all relative group">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-5">
                            <div class="w-12 h-12 bg-muji-base rounded-2xl flex items-center justify-center text-2xl shadow-muji-sm group-hover:scale-110 transition-transform">
                                @if($expense->category == 'Food') 🍔
                                @elseif($expense->category == 'Transport') 🚇
                                @elseif($expense->category == 'Shopping') 🛍️
                                @elseif($expense->category == 'Accommodation') 🏨
                                @elseif($expense->category == 'Flight') ✈️
                                @elseif($expense->category == 'Entertainment') 🎡
                                @else 💸
                                @endif
                            </div>
                            <div>
                                <h4 class="text-base font-black text-muji-ink leading-tight">{{ $expense->description }}</h4>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span class="text-[9px] font-black text-muji-ash uppercase tracking-widest">{{ $expense->date->format('M d, Y') }}</span>
                                    <span class="w-1 h-1 bg-muji-edge rounded-full"></span>
                                    <span class="text-[9px] font-black text-muji-oak uppercase tracking-widest">
                                        @switch($expense->category)
                                            @case('Food') 飲食 @break
                                            @case('Transport') 交通 @break
                                            @case('Shopping') 購物 @break
                                            @case('Accommodation') 住宿 @break
                                            @case('Flight') 機票 @break
                                            @case('Entertainment') 娛樂 @break
                                            @default 其他
                                        @endswitch
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-mono font-black text-xl {{ $expense->is_base_currency ? 'text-muji-oak' : 'text-muji-ink' }}">
                                <span class="text-[10px] font-black uppercase mr-1">{{ $expense->is_base_currency ? $trip->base_currency : $trip->target_currency }}</span>{{ number_format($expense->amount) }}
                            </p>
                            @if(!$expense->is_base_currency)
                                <p class="text-[10px] text-muji-ash font-black opacity-40 mt-1">≈ {{ $trip->base_currency }} {{ number_format($expense->amount / max($trip->exchange_rate, 0.0001)) }}</p>
                            @endif
                        </div>
                    </div>

                    @if(!$isShared)
                        <div class="absolute top-4 right-4 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-all">
                            @auth
                                @php $exTotalDelFormId = 'del-ex-total-' . $expense->id; @endphp
                                <button onclick='openExpenseModal(@json($expense))' class="p-2 text-muji-ash hover:text-muji-oak hover:bg-muji-base rounded-full transition-all" title="編輯">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <form id="{{ $exTotalDelFormId }}" action="{{ route('expenses.destroy', ['user' => $trip->user, 'expense' => $expense->id]) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete('刪除紀錄', '確定要刪除這筆開銷嗎？', '{{ $exTotalDelFormId }}')" class="p-2 text-muji-ash hover:text-red-500 hover:bg-red-50 rounded-full transition-all" title="刪除">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            @endauth
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full muji-card p-20 text-center bg-muji-base/20 border-2 border-dashed border-muji-edge rounded-[3rem]">
                    <div class="w-16 h-16 bg-muji-base rounded-full flex items-center justify-center mx-auto mb-6 text-muji-edge">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <p class="text-muji-ash font-black uppercase tracking-widest text-xs">目前尚無任何花費紀錄</p>
                </div>
            @endforelse
        </div>
    </div>

    @push('modals')
    <script>
        let myChart = null;

        function highlightChartSegment(index) {
            if (!myChart) return;
            myChart.setActiveElements([{ datasetIndex: 0, index: index }]);
            myChart.update();
        }

        function resetChartHighlight() {
            if (!myChart) return;
            myChart.setActiveElements([]);
            myChart.update();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('expenseChart');
            if(!ctx) return;
            
            const categoryData = @json($byCategory);
            const labels = Object.keys(categoryData).map(cat => {
                if(cat == 'Food') return '飲食';
                if(cat == 'Transport') return '交通';
                if(cat == 'Shopping') return '購物';
                if(cat == 'Accommodation') return '住宿';
                if(cat == 'Flight') return '機票';
                if(cat == 'Entertainment') return '娛樂';
                return '其他';
            });
            const values = Object.values(categoryData);
            
            const chartColors = [
                '#9c8c7c', '#8a8a8a', '#a5ad94', '#b59b9b', '#dcd3c1', '#4b453d', '#757575'
            ];

            myChart = new Chart(ctx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: chartColors,
                        borderWidth: 0,
                        hoverOffset: 20,
                        borderRadius: 15,
                        spacing: 8,
                        cutout: '80%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: 25 },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'white',
                            titleColor: '#333',
                            bodyColor: '#666',
                            borderColor: '#e8e4db',
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.label}: {{ $trip->base_currency }} ${context.raw.toLocaleString()}`;
                                }
                            }
                        }
                    }
                },
                plugins: [{
                    id: 'centerText',
                    afterDraw: function(chart) {
                        const { ctx, chartArea: { top, bottom, left, right, width, height } } = chart;
                        ctx.save();
                        const centerX = left + width / 2;
                        const centerY = top + height / 2;
                        ctx.textAlign = 'center';
                        
                        // Label
                        ctx.font = 'bold 10px Inter';
                        ctx.fillStyle = '#757575';
                        ctx.letterSpacing = '4px';
                        ctx.fillText('TOTAL EXPENDITURE', centerX, centerY - 30);
                        
                        // Amount
                        const amount = '{{ number_format($totalBase) }}';
                        let fontSize = 38;
                        if (amount.length > 9) fontSize = 24;
                        else if (amount.length > 7) fontSize = 30;
                        
                        ctx.font = `900 ${fontSize}px Inter`;
                        ctx.fillStyle = '#333333';
                        ctx.letterSpacing = '-1px';
                        ctx.fillText(amount, centerX, centerY + 10);
                        
                        // Currency
                        ctx.font = 'bold 12px Inter';
                        ctx.fillStyle = '#9c8c7c';
                        ctx.fillText('{{ $trip->base_currency }} ESTIMATED', centerX, centerY + 35);
                        ctx.restore();
                    }
                }]
            });
        });
    </script>
    @endpush
@endsection