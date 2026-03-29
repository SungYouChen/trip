@php
$isShared = $isShared ?? false;
$flightInfo = $flightInfo ?? [
'transport_type' => 'flight',
'airline' => '',
'price' => '',
'baggage' => '',
'outbound' => ['date' => '', 'time' => '', 'route' => ''],
'inbound' => ['date' => '', 'time' => '', 'route' => '']
];
$hasFlight = !empty($flightInfo['airline']) || !empty($flightInfo['outbound']['route']);
$transportType = $flightInfo['transport_type'] ?? 'flight';

$themes = [
'flight' => [
'color' => 'indigo',
'gradient' => 'from-indigo-500/10 to-purple-500/10',
'border' => 'border-indigo-100',
'text' => 'text-indigo-600',
'bg_light' => 'bg-indigo-50',
'label_out' => '去程 Outbound',
'label_in' => '回程 Inbound',
'icon_mid' => '<svg class="w-5 h-5 rotate-90" fill="currentColor" viewBox="0 0 20 20">
    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
</svg>'
],
'train' => [
'color' => 'indigo',
'gradient' => 'from-indigo-500/10 to-purple-500/10',
'border' => 'border-indigo-100',
'text' => 'text-indigo-600',
'bg_light' => 'bg-indigo-50',
'label_out' => '車次/去程 Departure',
'label_in' => '返程/回程 Return',
'icon_mid' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
</svg>'
],
'bus' => [
'color' => 'indigo',
'gradient' => 'from-indigo-500/10 to-purple-500/10',
'border' => 'border-indigo-100',
'text' => 'text-indigo-600',
'bg_light' => 'bg-indigo-50',
'label_out' => '發車/去程 Departure',
'label_in' => '返程/回程 Return',
'icon_mid' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
</svg>'
],
'car' => [
'color' => 'indigo',
'gradient' => 'from-indigo-500/10 to-purple-500/10',
'border' => 'border-indigo-100',
'text' => 'text-indigo-600',
'bg_light' => 'bg-indigo-50',
'label_out' => '取車 Pick-up',
'label_in' => '還車 Drop-off',
'icon_mid' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
</svg>'
],
'ship' => [
'color' => 'indigo',
'gradient' => 'from-indigo-500/10 to-purple-500/10',
'border' => 'border-indigo-100',
'text' => 'text-indigo-600',
'bg_light' => 'bg-indigo-50',
'label_out' => '啟航 Departure',
'label_in' => '返航 Return',
'icon_mid' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4M4 12l8-8m-8 8l8 8" />
</svg>'
]
];
$theme = $themes[$transportType] ?? $themes['flight'];
@endphp
@extends('layout')

@section('content')
<div class="mb-10 text-center relative max-w-2xl mx-auto">
    <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl mb-2 inline-flex items-center gap-2 justify-center">
        {{ $trip->name }}
        @if(!$isShared)
        @auth
        <button onclick="safeOpenModal('tripSettingsModal')" class="text-slate-400 hover:text-slate-600 transition-colors tooltip tooltip-bottom" data-tip="編輯旅程設計與匯率">
            <svg class="w-6 h-6 text-slate-300 group-hover:text-slate-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </button>
        <form action="{{ route('trip.toggle_share', ['user' => $trip->user, 'trip' => $trip]) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="tooltip tooltip-bottom {{ $trip->is_public ? 'text-green-500' : 'text-slate-400' }} hover:text-slate-600 transition-colors" data-tip="{{ $trip->is_public ? '已開啟分享 (點擊隱私)' : '未開啟分享 (點擊公開)' }}">
                <svg class="w-6 h-6 text-slate-300 group-hover:text-slate-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                </svg>
            </button>
        </form>
        @endauth
        @endif
    </h2>

    @if(!$isShared && $trip->is_public)
    <div class="mt-2 mb-4 bg-indigo-50 p-2 rounded-xl flex items-center justify-between gap-3 border border-indigo-100 max-w-sm mx-auto overflow-hidden">
        <span id="shareLink" class="text-[10px] text-indigo-600 font-mono truncate flex-1">{{ route('trip.index_shared', ['token' => $trip->share_token]) }}</span>
        <button onclick="copyShareLink()" class="bg-indigo-600 text-white text-[10px] px-3 py-1 rounded-lg hover:bg-indigo-700 transition-colors font-bold whitespace-nowrap">複製連結</button>
    </div>
    <script>
        function copyShareLink() {
            const link = document.getElementById('shareLink').innerText;
            navigator.clipboard.writeText(link).then(() => {
                Toast.fire({ icon: 'success', title: '連結已複製！' });
            });
        }
    </script>
    @endif
    <p class="text-lg text-gray-500">
        {{ \Carbon\Carbon::parse($trip->start_date)->format('Y/m/d') }} - {{ \Carbon\Carbon::parse($trip->end_date)->format('Y/m/d') }}
    </p>
</div>

<style>
    .ticket-masked {
        -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='102%25' height='100%25'%3E%3Cdefs%3E%3Cmask id='m'%3E%3Crect width='100%25' height='100%25' fill='white'/%3E%3Ccircle cx='0' cy='88' r='14' fill='black'/%3E%3Ccircle cx='100%25' cy='88' r='14' fill='black'/%3E%3C/mask%3E%3C/defs%3E%3Crect width='100%25' height='100%25' mask='url(%23m)' fill='white'/%3E%3C/svg%3E");
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='102%25' height='100%25'%3E%3Cdefs%3E%3Cmask id='m'%3E%3Crect width='100%25' height='100%25' fill='white'/%3E%3Ccircle cx='0' cy='88' r='14' fill='black'/%3E%3Ccircle cx='100%25' cy='88' r='14' fill='black'/%3E%3C/mask%3E%3C/defs%3E%3Crect width='100%25' height='100%25' mask='url(%23m)' fill='white'/%3E%3C/svg%3E");
    }
</style>

@if($hasFlight)
<!-- Multi-modal Transport Card -->
<div id="transportCard" class="relative bg-white/40 backdrop-blur-md rounded-3xl shadow-sm border border-white/20 mb-8 group/transport zoom-in-on-load ticket-masked overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br {{ $theme['gradient'] }} opacity-50 pointer-events-none"></div>

    <!-- Top Section: Header & Price -->
    <div onclick="toggleTransportDetails()" class="relative px-8 py-5 border-b border-dashed border-gray-200 flex justify-between items-center bg-white/40 cursor-pointer hover:bg-white/60 transition-colors">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl {{ $theme['bg_light'] }} flex items-center justify-center {{ $theme['text'] }} shadow-sm rotate-3 group-hover/transport:rotate-0 transition-transform duration-500">
                @if($transportType == 'train')
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8v10a2 2 0 002 2h12a2 2 0 002-2V8a2 2 0 00-2-2H6a2 2 0 00-2 2zM9 15h6M9 11h6" />
                </svg>
                @elseif($transportType == 'bus')
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                @elseif($transportType == 'car')
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                </svg>
                @elseif($transportType == 'ship')
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13s-2 2-5 2-5-2-5-2-5 2-7 2-7-2-7-2V6l2.5-1 2.5 1 2.5-1 2.5 1 2.5-1 2.5 1 2.5-1v7z" />
                </svg>
                @else
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                @endif
            </div>
            <div>
                <h3 class="text-[10px] font-black {{ $theme['text'] }} uppercase tracking-widest leading-none mb-1">
                    {{ $transportType == 'car' ? '租賃車資訊 / Vehicle Info' : ($transportType == 'flight' ? '航班資訊 / Flight Info' : ($transportType == 'train' ? '鐵路資訊 / Train Info' : '交通資訊 / Transport Info')) }}
                </h3>
                <p class="text-xl font-black text-gray-900 tracking-tight leading-none">{{ $flightInfo['airline'] }}</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">預估費用 / Est. Cost</p>
                <p class="text-2xl font-mono font-black {{ $theme['text'] }} leading-none">{{ $flightInfo['price'] }}</p>
            </div>
            <div id="transportChevron" class="transition-transform duration-300 transform rotate-180">
                <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Toggleable Content -->
    <div id="transportDetails" class="transition-[max-height] duration-500 overflow-hidden ease-in-out" style="max-height: 2000px;">

    @switch($transportType)
        @case('train')
            <!-- Train Specialized Card -->
            @include('trips.partials.transport_train', ['flightInfo' => $flightInfo, 'theme' => $theme])
            @break
        @case('car')
            <!-- Car Specialized Card -->
            @include('trips.partials.transport_car', ['flightInfo' => $flightInfo, 'theme' => $theme])
            @break
        @case('bus')
            <!-- Bus Specialized Card -->
            @include('trips.partials.transport_bus', ['flightInfo' => $flightInfo, 'theme' => $theme])
            @break
        @case('ship')
            <!-- Ship Specialized Card -->
            @include('trips.partials.transport_ship', ['flightInfo' => $flightInfo, 'theme' => $theme])
            @break
        @default
            <!-- Default Flight Card -->
            <div class="relative grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-dashed divide-gray-200">
                

                <!-- Outbound -->
                <div class="p-8 group/item hover:bg-white/40 transition-all duration-300">
                    <div class="flex justify-between items-start mb-6">
                        <span class="px-3 py-1.5 {{ $theme['bg_light'] }} {{ $theme['text'] }} rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm">
                            {{ $theme['label_out'] }}
                        </span>
                        <div class="text-right">
                            <p class="text-xs font-black text-gray-400 mb-0.5 uppercase tracking-wide">Date</p>
                            <span class="text-sm font-black text-gray-900">{{ $flightInfo['outbound']['date'] }}</span>
                        </div>
                    </div>

                    @php
                    $outRoute = $flightInfo['outbound']['route'] ?? '';
                    $hasArrow = str_contains($outRoute, ' ➝ ');
                    $parts = $hasArrow ? explode(' ➝ ', $outRoute) : [$outRoute, ''];
                    @endphp

                    <div class="flex items-center justify-between gap-6">
                        <div class="flex-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Departure</p>
                            <p class="text-2xl font-black text-gray-900 tracking-tighter leading-tight">{{ $parts[0] }}</p>
                        </div>
                        <div class="flex transition-transform group-hover/item:scale-110 duration-500 {{ $theme['text'] }} opacity-40">
                            {!! $theme['icon_mid'] !!}
                        </div>
                        <div class="flex-1 text-right">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Arrival</p>
                            <p class="text-2xl font-black text-gray-900 tracking-tighter leading-tight">{{ $parts[1] ?: '--' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-100/50 flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Take-off Time</span>
                            <span class="text-2xl font-mono font-black text-gray-800 tracking-tighter">{{ $flightInfo['outbound']['time'] ?: 'TBA' }}</span>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                </div>

                <!-- Inbound -->
                <div class="p-8 group/item hover:bg-white/40 transition-all duration-300">
                    <div class="flex justify-between items-start mb-6">
                        <span class="px-3 py-1.5 bg-gray-100 text-gray-500 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm">
                            {{ $theme['label_in'] }}
                        </span>
                        <div class="text-right">
                            <p class="text-xs font-black text-gray-400 mb-0.5 uppercase tracking-wide">Date</p>
                            <span class="text-sm font-black text-gray-900">{{ $flightInfo['inbound']['date'] }}</span>
                        </div>
                    </div>

                    @php
                    $inRoute = $flightInfo['inbound']['route'] ?? '';
                    $hasArrowIn = str_contains($inRoute, ' ➝ ');
                    $partsIn = $hasArrowIn ? explode(' ➝ ', $inRoute) : [$inRoute, ''];
                    @endphp

                    <div class="flex items-center justify-between gap-6">
                        <div class="flex-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Departure</p>
                            <p class="text-2xl font-black text-gray-900 tracking-tighter leading-tight">{{ $partsIn[0] }}</p>
                        </div>
                        <div class="flex transition-transform group-hover/item:rotate-180 duration-700 text-gray-300 opacity-40">
                            {!! $theme['icon_mid'] !!}
                        </div>
                        <div class="flex-1 text-right">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Arrival</p>
                            <p class="text-2xl font-black text-gray-900 tracking-tighter leading-tight">{{ $partsIn[1] ?: '--' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-100/50 flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Landing Time</span>
                            <span class="text-2xl font-mono font-black text-gray-800 tracking-tighter">{{ $flightInfo['inbound']['time'] ?: 'TBA' }}</span>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                    </div>
                </div>
            @endswitch
    </div>


    <!-- Footer -->
    <div onclick="toggleTransportDetails()" class="relative px-8 py-4 bg-white/60 flex flex-wrap justify-between items-center gap-4 border-t border-gray-100 cursor-pointer hover:bg-white/80 transition-colors">
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-xl shadow-sm border border-gray-100">
                <svg class="w-4 h-4 {{ $theme['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span class="text-xs font-black text-gray-600 truncate max-w-[200px] sm:max-w-md">
                    {{ $transportType == 'car' ? '租車備註：' : '行李/備註：' }}{{ $flightInfo['baggage'] ?: '無特別備註' }}
                </span>
            </div>
        </div>
        @if(!$isShared)
        @auth
        <button onclick="openFlightEditModal()" class="flex items-center gap-2 px-6 py-2.5 {{ $theme['bg_light'] }} {{ $theme['text'] }} rounded-2xl font-black text-xs hover:shadow-lg transition-all active:scale-95 border {{ $theme['border'] }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
            編輯交通
        </button>
        @endauth
        @endif
    </div>
</div>
@elseif(!$isShared)
@auth
<!-- Placeholder for no flight info -->
<div class="mb-8 p-8 border-2 border-dashed border-white/20 rounded-3xl flex flex-col items-center justify-center text-indigo-400 bg-white/20 backdrop-blur-sm hover:border-indigo-300 hover:text-indigo-600 transition-all group" onclick="openFlightEditModal()" style="cursor: pointer;">
    <div class="w-12 h-12 rounded-full bg-indigo-50 group-hover:bg-indigo-100 flex items-center justify-center mb-3 transition-colors">
        <svg class="w-6 h-6 text-indigo-300 group-hover:text-indigo-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 4v16m8-8H4" />
        </svg>
    </div>
    <span class="font-bold text-sm tracking-wide uppercase">點擊新增交通資訊 (Add Transport)</span>
</div>
@endauth
@endif

@php $showArchived = request('archived') == '1'; @endphp
<div class="flex items-center justify-between mb-4">
    <p class="text-sm text-gray-500">
        @php $activeDaysCount = $itinerary->filter(fn($d) => !$d->trashed())->count(); @endphp
        共 {{ $activeDaysCount }} 天 ({{ $showArchived ? '顯示封存' : '正常顯示' }})
    </p>
    @if(!$isShared)
    @auth
    <a href="{{ request()->fullUrlWithQuery(['archived' => $showArchived ? '0' : '1']) }}" class="flex items-center gap-2 text-xs font-bold px-3 py-1.5 rounded-full border transition-all {{ $showArchived ? 'bg-red-50 text-red-600 border-red-200' : 'bg-gray-100 text-gray-500 border-gray-200 hover:bg-gray-200' }}">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12M10 12v4m4-4v4" />
        </svg>
        {{ $showArchived ? '隱藏封存' : '查看封存' }}
    </a>
    @endauth
    @endif
</div>

<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @php
    $daysToShow = $showArchived
    ? $itinerary->filter(fn($d) => $d->trashed())
    : $itinerary->filter(fn($d) => !$d->trashed());
    @endphp
    @foreach($daysToShow as $day)
    @php
    $dayDate = \Carbon\Carbon::parse($day->date);
    $isToday = $dayDate->isToday();
    $isArchived = $day->trashed();
    $cardLink = (!$isArchived && $isShared)
    ? route('day.show_shared', ['token' => $trip->share_token, 'date' => $dayDate->format('n-j')])
    : ((!$isArchived && !$isShared) ? route('day.show', ['user' => $trip->user, 'trip' => $trip, 'date' => $dayDate->format('n-j')]) : null);
    @endphp
    <div class="relative group">
        <a href="{{ $cardLink }}" class="flex flex-col h-52 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden border {{ $isArchived ? 'border-2 border-dashed border-red-300 bg-red-50/30 opacity-60 hover:opacity-80' : ($isToday ? 'bg-indigo-50/70 border-white/20' : 'bg-white/20 border-white/20') }} backdrop-blur-sm">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-500/5 opacity-0 hover:opacity-100 transition-opacity"></div>

            <div class="p-6 flex-1 overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-600 border border-indigo-100">
                        {{ $dayDate->format('n/j') }} ({{ $dayDate->locale('zh_TW')->dayName }})
                    </span>
                </div>

                <h3 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-indigo-600 transition-colors truncate">
                    {{ $day->title ?: 'Day ' . $loop->iteration }}
                </h3>
                @if($day->location)
                <div class="flex items-center gap-1 text-xs font-medium text-indigo-500 mb-2 uppercase tracking-wide truncate">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="truncate">{{ $day->location }}</span>
                </div>
                @endif

                <p class="text-sm text-gray-500 line-clamp-2">
                    {{ $day->summary }}
                </p>
            </div>

            <div class="px-6 py-3 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between text-xs font-medium text-gray-400 group-hover:bg-indigo-50/10 transition-colors flex-shrink-0">
                <span>View Detail</span>
                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>

        @if(!$isShared)
        @auth
        @if($isArchived)
        {{-- Archived: Show Restore + Force Delete --}}
        @php $restoreId = 'restore-day-' . $day->id; @endphp
        @php $forceId = 'force-day-' . $day->id; @endphp
        <div class="absolute top-3 right-3 z-10 flex gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
            <form id="{{ $restoreId }}" action="{{ route('day.restore', ['user' => $trip->user, 'trip' => $trip, 'dayId' => $day->id]) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="p-2 bg-green-50 text-green-600 rounded-xl hover:bg-green-100 shadow-sm transition-colors" title="還原">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </form>
            <form id="{{ $forceId }}" action="{{ route('day.forceDelete', ['user' => $trip->user, 'trip' => $trip, 'dayId' => $day->id]) }}" method="POST">
                @csrf @method('DELETE')
                <button type="button" onclick="confirmDelete('永久刪除？', '此操作無法復原！', '{{ $forceId }}')" class="p-2 bg-red-100 text-red-600 rounded-xl hover:bg-red-200 shadow-sm transition-colors" title="永久刪除">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </form>
        </div>
        @else
        {{-- Active: Soft delete (archive) --}}
        @php $formId = 'delete-day-' . $day->id; @endphp
        <form id="{{ $formId }}" action="{{ route('day.destroy', ['user' => $trip->user, 'trip' => $trip, 'date' => $dayDate->format('n-j')]) }}" method="POST" class="absolute top-4 right-4 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
            @csrf @method('DELETE')
            <button type="button" onclick="confirmDelete('封存此天行程？', '此天行程將被封存，可於「查看封存」中還原。', '{{ $formId }}')" class="p-2 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 hover:text-red-600 shadow-sm transition-colors" title="封存此天">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12M10 12v4m4-4v4" />
                </svg>
            </button>
        </form>
        @endif
        @endauth
        @endif
    </div>
    @endforeach

    @if(!$isShared)
    @auth
    <form action="{{ route('trip.add_day', ['user' => $trip->user, 'trip' => $trip]) }}" method="POST" class="h-full">
        @csrf
        <button type="submit" class="w-full h-full min-h-[200px] flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-200 rounded-3xl text-gray-400 hover:text-indigo-500 hover:border-indigo-300 hover:bg-indigo-50/10 transition-all group">
            <div class="w-12 h-12 rounded-full bg-slate-50 group-hover:bg-slate-100 flex items-center justify-center mb-3 transition-colors">
                <svg class="w-6 h-6 text-slate-300 group-hover:text-slate-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <span class="font-bold text-sm tracking-wide uppercase">Add Extra Day</span>
        </button>
    </form>
    @endauth
    @endif
</div>

<!-- Checklists Section -->
<div class="grid md:grid-cols-2 gap-8 mt-12">
    <!-- Must Buy List -->
    <div class="bg-white/30 backdrop-blur-sm rounded-3xl shadow-sm border border-white/20 p-8">
        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
            <span class="w-8 h-8 rounded-full bg-pink-100 flex items-center justify-center text-pink-500">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </span>
            必買清單 (購物)
        </h3>
        <div class="space-y-6">
            @php
            $shoppingCategories = ['藥妝', '食物', '衣物', 'Must Buy'];
            @endphp
            @foreach($shoppingCategories as $category)
            @php
            $allCategoryItems = $trip->checklistItems()->withTrashed()->where('category', $category)->get();
            $items = $showArchived
            ? $allCategoryItems->filter(fn($i) => $i->trashed())
            : $allCategoryItems->filter(fn($i) => !$i->trashed());
            @endphp
            @if($items->count() > 0 || (auth()->check() && !$showArchived))
            <div>
                <h4 class="font-bold text-gray-700 text-sm mb-2 border-l-4 border-pink-400 pl-2">{{ $category }}</h4>
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($items as $item)
                    @php $isItemArchived = $item->trashed(); @endphp
                    <li class="flex items-start justify-between gap-2 text-sm text-gray-600 group {{ $isItemArchived ? 'border border-dashed border-red-200 rounded p-1 bg-red-50/20 grayscale opacity-60' : '' }}">
                        <div class="flex items-start gap-2">
                            @if($isItemArchived)
                            <span class="text-red-400 mt-0.5"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg></span>
                            @else
                            <input type="checkbox" class="mt-1 rounded text-pink-500 focus:ring-pink-500 persist-chk disabled:opacity-50 disabled:cursor-not-allowed" data-key="chk_{{ $item->id }}" @if(!auth()->check() || $isShared) disabled @endif>
                            @endif
                            <span class="{{ (!auth()->check() || $isShared) ? 'text-gray-400' : '' }} {{ $isItemArchived ? 'text-red-800 font-medium' : '' }}">{{ $item->name }}</span>
                        </div>
                        @if(!$isShared)
                        @auth
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            @if($isItemArchived)
                            @php $restoreChkId = 'restore-chk-' . $item->id; @endphp
                            <form id="{{ $restoreChkId }}" action="{{ route('checklist.restore', ['user' => $trip->user, 'trip' => $trip, 'id' => $item->id]) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="button" class="text-green-500 hover:text-green-700 p-0.5" onclick="confirmAction('還原清單項目？', '確定要將「{{ $item->name }}」移回必買清單嗎？', '{{ $restoreChkId }}')">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                    </svg>
                                </button>
                            </form>
                            @php $forceChkId = 'force-chk-' . $item->id; @endphp
                            <form id="{{ $forceChkId }}" action="{{ route('checklist.forceDelete', ['user' => $trip->user, 'trip' => $trip, 'id' => $item->id]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="button" class="text-red-600 hover:text-red-800 p-0.5" onclick="confirmDelete('永久刪除項目？', '此動作無法復原！確定要永久刪除「{{ $item->name }}」嗎？', '{{ $forceChkId }}')">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            @else
                            @php $chkFormId = 'del-chk-' . $item->id; @endphp
                            <form id="{{ $chkFormId }}" action="{{ route('checklist.destroy', ['user' => $trip->user, 'trip' => $trip, 'id' => $item->id]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="button" class="text-red-400 hover:text-red-600 p-0.5" onclick="confirmDelete('封存清單項目？', '確定要將「{{ $item->name }}」移至回收桶嗎？', '{{ $chkFormId }}')">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                        @endauth
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
            @endforeach

            @if(!$isShared)
            @auth
            <form action="{{ route('checklist.store', ['user' => $trip->user, 'trip' => $trip]) }}" method="POST" class="mt-4 pt-4 border-t border-gray-100">
                @csrf
                <input type="hidden" name="type" value="shopping">
                <div class="flex gap-2">
                    <input type="text" name="category" placeholder="分類" class="w-1/3 rounded-lg border-gray-300 text-sm p-2" required list="shop_categories">
                    <datalist id="shop_categories">
                        @foreach($shoppingCategories as $cat)
                        <option value="{{ $cat }}">
                            @endforeach
                    </datalist>
                    <input type="text" name="name" placeholder="項目名稱" class="w-2/3 rounded-lg border-gray-300 text-sm p-2" required>
                    <button type="submit" class="bg-pink-500 text-white p-2 rounded-lg hover:bg-pink-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </button>
                </div>
            </form>
            @endauth
            @endif
        </div>
    </div>

    <!-- Must Go List -->
    <div class="bg-white/30 backdrop-blur-sm rounded-3xl shadow-sm border border-white/20 p-8 relative overflow-hidden group">
        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
            <span class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-500">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </span>
            想去景點
        </h3>
        <div class="space-y-6">
            @php
            $spotCategories = ['Kyoto', 'Osaka', 'Must Go'];
            @endphp
            @foreach($spotCategories as $category)
            @php
            $allSpotItems = $trip->checklistItems()->withTrashed()->where('category', $category)->get();
            $items = $showArchived
            ? $allSpotItems->filter(fn($i) => $i->trashed())
            : $allSpotItems->filter(fn($i) => !$i->trashed());
            @endphp
            @if($items->count() > 0 || (auth()->check() && !$showArchived))
            <div>
                <h4 class="font-bold text-gray-700 text-sm mb-2 border-l-4 border-blue-400 pl-2">{{ $category }}</h4>
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($items as $item)
                    @php $isItemArchived = $item->trashed(); @endphp
                    <li class="flex items-start justify-between gap-2 text-sm text-gray-600 group {{ $isItemArchived ? 'border border-dashed border-red-200 rounded p-1 bg-red-50/20 grayscale opacity-60' : '' }}">
                        <div class="flex items-start gap-2">
                            @if($isItemArchived)
                            <span class="text-red-400 mt-0.5"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg></span>
                            @else
                            <input type="checkbox" class="mt-1 rounded text-blue-500 focus:ring-blue-500 persist-chk disabled:opacity-50 disabled:cursor-not-allowed" data-key="chk_{{ $item->id }}" @if(!auth()->check() || $isShared) disabled @endif>
                            @endif
                            <span class="{{ (!auth()->check() || $isShared) ? 'text-gray-400' : '' }} {{ $isItemArchived ? 'text-red-800 font-medium' : '' }}">{{ $item->name }}</span>
                        </div>
                        @if(!$isShared)
                        @auth
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            @if($isItemArchived)
                            @php $restoreGoId = 'restore-chk-go-' . $item->id; @endphp
                            <form id="{{ $restoreGoId }}" action="{{ route('checklist.restore', ['user' => $trip->user, 'trip' => $trip, 'id' => $item->id]) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="button" class="text-green-500 hover:text-green-700 p-0.5" onclick="confirmAction('還原清單項目？', '確定要將「{{ $item->name }}」移回想去景點嗎？', '{{ $restoreGoId }}')">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                    </svg>
                                </button>
                            </form>
                            @php $forceGoId = 'force-chk-go-' . $item->id; @endphp
                            <form id="{{ $forceGoId }}" action="{{ route('checklist.forceDelete', ['user' => $trip->user, 'trip' => $trip, 'id' => $item->id]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="button" class="text-red-600 hover:text-red-800 p-0.5" onclick="confirmDelete('永久刪除項目？', '此動作無法復原！確定要永久刪除「{{ $item->name }}」嗎？', '{{ $forceGoId }}')">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            @else
                            @php $chkGoFormId = 'del-chk-go-' . $item->id; @endphp
                            <form id="{{ $chkGoFormId }}" action="{{ route('checklist.destroy', ['user' => $trip->user, 'trip' => $trip, 'id' => $item->id]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="button" class="text-red-400 hover:text-red-600 p-0.5" onclick="confirmDelete('封存清單項目？', '確定要將「{{ $item->name }}」移至回收桶嗎？', '{{ $chkGoFormId }}')">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                        @endauth
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
            @endforeach

            @if(!$isShared)
            @auth
            <form action="{{ route('checklist.store', ['user' => $trip->user, 'trip' => $trip]) }}" method="POST" class="mt-4 pt-4 border-t border-gray-100">
                @csrf
                <input type="hidden" name="type" value="spot">
                <div class="flex gap-2">
                    <input type="text" name="category" placeholder="區域" class="w-1/3 rounded-lg border-gray-300 text-sm p-2" required list="spot_categories">
                    <datalist id="spot_categories">
                        @foreach($spotCategories as $cat)
                        <option value="{{ $cat }}">
                            @endforeach
                    </datalist>
                    <input type="text" name="name" placeholder="景點名稱" class="w-2/3 rounded-lg border-gray-300 text-sm p-2" required>
                    <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded-xl hover:bg-indigo-600 transition-all active:scale-95 shadow-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </button>
                </div>
            </form>
            @endauth
            @endif
        </div>
    </div>
</div>


@push('modals')
@auth
<!-- Flight Edit Modal -->
<div id="tripTransportModal" class="fixed inset-0 z-[2000]" style="display: none;" role="dialog" aria-modal="true">
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity z-0" onclick="safeCloseModal('tripTransportModal')"></div>
        <div class="relative z-10 transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg flex flex-col max-h-[calc(100vh-160px)]">
            <div class="px-8 py-10 overflow-y-auto custom-scrollbar scroll-smooth">
                <div class="flex justify-between items-start mb-10">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-indigo-50 rounded-2xl text-indigo-600 shadow-sm">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                        </div>
                        <div class="flex flex-col border-l-2 border-indigo-100 pl-4">
                            <h3 id="modal-transport-title" class="text-2xl font-black text-gray-900 leading-tight">編輯交通資訊</h3>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1">Travel Logistics Design</p>
                        </div>
                    </div>
                    <button onclick="safeCloseModal('tripTransportModal')" class="text-gray-300 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100 transition-all">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('trip.flight.update', ['user' => $trip->user, 'trip' => $trip]) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <!-- 交通工具選擇 -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-black text-indigo-600 uppercase tracking-[0.2em] flex items-center gap-2 mb-4 bg-indigo-50/50 self-start px-3 py-1.5 rounded-lg border border-indigo-100">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            交通方式 / Travel Mode
                        </h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                            @foreach([
                            'flight' => ['飛機', 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8'],
                            'train' => ['鐵路', 'M13 18l1 1h-4l1-1m-4-7a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2v-4zm2-3V4m4 0v4m-5 4h6'],
                            'bus' => ['巴士', 'M8 6h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1zM6 13h12M9 18h0M15 18h0'],
                            'car' => ['自駕', 'M19 11V7a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v4a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h2a2 2 0 0 0 4 0h4a2 2 0 0 0 4 0h2a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1zM7 11h10'],
                            'ship' => ['船件', 'M21 13H3a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1zm-9-10l5 10H7l5-10z']
                            ] as $val => $data)
                            <label class="cursor-pointer group flex flex-col items-center">
                                <input type="radio" name="transport_type" value="{{ $val }}" class="hidden peer" {{ $transportType==$val ? 'checked' : '' }}>
                                <div class="w-full px-2 py-3 bg-gray-50/50 border border-gray-100 rounded-2xl text-center group-hover:bg-gray-100 peer-checked:bg-slate-600 peer-checked:border-slate-600 peer-checked:text-white transition-all shadow-sm flex flex-col items-center gap-2">
                                    <svg class="w-5 h-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $data[1] }}" />
                                    </svg>
                                    <span class="text-[9px] font-bold uppercase tracking-widest">{{ $data[0] }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 基本資訊 Specialized Fields -->
                    <div id="transport-basics-container" class="space-y-6">
                        <h4 id="mode-basics-title" class="text-xs font-black text-indigo-600 uppercase tracking-[0.2em] flex items-center gap-2 mb-4 bg-indigo-50/50 self-start px-3 py-1.5 rounded-lg border border-indigo-100">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            基本資訊 / Travel Basics
                        </h4>
                        
                        <!-- Flight Specific Fields -->
                        <div id="fields-flight" class="mode-fields {{ ($transportType ?? 'flight') == 'flight' ? '' : 'hidden' }} grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">航空公司 / 航班編號 Airline / No.</label>
                                <input type="text" name="airline" value="{{ $flightInfo['airline'] ?? '' }}" class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium" placeholder="例如：星宇航空 JX800">
                            </div>
                        </div>

                        <!-- Train Specific Fields -->
                        <div id="fields-train" class="mode-fields {{ ($transportType ?? '') == 'train' ? '' : 'hidden' }} grid grid-cols-1 gap-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">列車名稱 / 車次</label>
                                    <input type="text" name="train_no" value="{{ $flightInfo['train_no'] ?? '' }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 shadow-sm" placeholder="例如：JR 新幹線 希望號">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">座位資訊 (Car/Seat)</label>
                                    <input type="text" name="train_seat" value="{{ $flightInfo['train_seat'] ?? '' }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 shadow-sm" placeholder="例如：5車 12A">
                                </div>
                            </div>
                        </div>

                        <!-- Car Specific Fields -->
                        <div id="fields-car" class="mode-fields {{ ($transportType ?? '') == 'car' ? '' : 'hidden' }} grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">車型 / 租賃公司</label>
                                <input type="text" name="car_model" value="{{ $flightInfo['car_model'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 shadow-sm" placeholder="例如：Toyota Yaris / Times Car Rental">
                            </div>
                        </div>

                        <!-- Bus Specific Fields -->
                        <div id="fields-bus" class="mode-fields {{ ($transportType ?? '') == 'bus' ? '' : 'hidden' }} grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">客運公司 / 路線名稱</label>
                                <input type="text" name="airline" value="{{ $flightInfo['airline'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 shadow-sm" placeholder="例如：國光客運 / 京都市巴士 205路">
                            </div>
                        </div>

                        <!-- Ship Specific Fields -->
                        <div id="fields-ship" class="mode-fields {{ ($transportType ?? '') == 'ship' ? '' : 'hidden' }} grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">船名 / 航次名稱</label>
                                <input type="text" name="airline" value="{{ $flightInfo['airline'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 shadow-sm" placeholder="例如：麗星郵輪 / 櫻島渡輪">
                            </div>
                        </div>

                        <!-- Shared: Price & Notes (Always Visible but Labeled) -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">估計總費用 Total Cost</label>
                                <div class="flex relative rounded-xl border border-gray-200 overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 bg-white group transition-all">
                                    <select name="flight_currency" class="bg-gray-100 border-0 border-r border-gray-200 px-3 py-3 text-gray-700 font-bold text-xs focus:ring-0 cursor-pointer appearance-none">
                                        @php
                                        $fPrice = $flightInfo['price'] ?? '';
                                        preg_match('/^([^\d]+)?([\d,.]+)/u', $fPrice, $fm);
                                        $fsCurrency = trim($fm[1] ?? $trip->base_currency);
                                        $fsNum = isset($fm[2]) ? str_replace(',', '', $fm[2]) : '';
                                        @endphp
                                        <option value="{{ $trip->base_currency }}" {{ $fsCurrency==$trip->base_currency ? 'selected' : '' }}>{{ $trip->base_currency }}</option>
                                        <option value="{{ $trip->target_currency }}" {{ $fsCurrency==$trip->target_currency ? 'selected' : '' }}>{{ $trip->target_currency }}</option>
                                    </select>
                                    <input type="number" step="0.01" name="flight_price_num" value="{{ $fsNum }}" class="flex-1 w-full border-0 bg-transparent focus:ring-0 px-4 py-3 font-mono text-gray-900" placeholder="25000">
                                </div>
                            </div>
                            <div>
                                <label id="mode-label-baggage" class="block text-sm font-bold text-gray-700 mb-2">行李 / 備註規定 Notes</label>
                                <input type="text" name="baggage" id="mode-input-baggage" value="{{ $flightInfo['baggage'] }}" class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium" placeholder="例如：23kg x 2">
                            </div>
                        </div>
                    </div>

                    <!-- 去程細節 -->
                    <div class="p-6 bg-indigo-50/30 rounded-2xl space-y-4 border border-indigo-100/50">
                        <h4 class="text-xs font-black text-indigo-600 uppercase tracking-[0.2em] flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                            <span id="mode-label-outbound">去程 Outbound</span>
                        </h4>
                        <div class="grid grid-cols-1 gap-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">日期 (Date)</label>
                                @php
                                    $rawOut = $flightInfo['outbound']['date'] ?? '';
                                    preg_match('/(\d{1,4}[-\/]\d{1,2}[-\/]?\d{0,4})/', $rawOut, $mOut);
                                    $valOut = '';
                                    try { if($mOut[1] ?? false) $valOut = \Carbon\Carbon::parse($mOut[1])->toDateString(); } catch(\Exception $e){}
                                @endphp
                                <input type="date" name="outbound_date" value="{{ $valOut }}" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label id="mode-label-route" class="block text-xs font-bold text-gray-500 mb-1">路線 (Route)</label>
                                    <input type="text" name="outbound_route" value="{{ $flightInfo['outbound']['route'] }}" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl shadow-sm" placeholder="TPE ➝ UKB">
                                    <p id="mode-help-route" class="text-[9px] text-gray-400 mt-1 italic">請使用「起點 ➝ 終點」格式</p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">出發/到達時間 (Dep/Arr Time)</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[9px] font-black text-gray-400 uppercase">Dep</span>
                                        <input type="time" name="outbound_time_start" value="{{ $flightInfo['outbound']['time_start'] ?? '' }}" class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl shadow-sm font-mono text-sm focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[9px] font-black text-gray-400 uppercase">Arr</span>
                                        <input type="time" name="outbound_time_end" value="{{ $flightInfo['outbound']['time_end'] ?? '' }}" class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl shadow-sm font-mono text-sm focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 回程細節 -->
                    <div class="p-6 bg-purple-50/50 rounded-2xl space-y-4 border border-purple-100">
                        <h4 class="text-xs font-black text-purple-600 uppercase tracking-[0.2em] flex items-center gap-2">
                            <span class="w-2 h-2 bg-purple-500 rounded-full"></span> <span id="mode-label-inbound">回程 Inbound</span>
                        </h4>
                        <div class="grid grid-cols-1 gap-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">日期 (Date)</label>
                                @php
                                    $rawIn = $flightInfo['inbound']['date'] ?? '';
                                    preg_match('/(\d{1,4}[-\/]\d{1,2}[-\/]?\d{0,4})/', $rawIn, $mIn);
                                    $valIn = '';
                                    try { if($mIn[1] ?? false) $valIn = \Carbon\Carbon::parse($mIn[1])->toDateString(); } catch(\Exception $e){}
                                @endphp
                                <input type="date" name="inbound_date" value="{{ $valIn }}" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label id="mode-label-route-in" class="block text-xs font-bold text-gray-500 mb-1">路線 (Route)</label>
                                    <input type="text" name="inbound_route" value="{{ $flightInfo['inbound']['route'] }}" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl shadow-sm" placeholder="KIX ➝ TPE">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">出發/到達時間 (Dep/Arr Time)</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[9px] font-black text-gray-400 uppercase">Dep</span>
                                        <input type="time" name="inbound_time_start" value="{{ $flightInfo['inbound']['time_start'] ?? '' }}" class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl shadow-sm font-mono text-sm focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[9px] font-black text-gray-400 uppercase">Arr</span>
                                        <input type="time" name="inbound_time_end" value="{{ $flightInfo['inbound']['time_end'] ?? '' }}" class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl shadow-sm font-mono text-sm focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-6 mt-8 border-t border-gray-100">
                        <button type="button" onclick="safeCloseModal('tripTransportModal')" class="flex-1 px-6 py-4 bg-gray-100 text-gray-700 font-black rounded-2xl hover:bg-gray-200 transition-colors">取消 Cancel</button>
                        <button type="submit" class="flex-1 px-6 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 shadow-xl hover:shadow-indigo-200 transition-all active:scale-95">儲存變更 Save Logistics</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Trip Modal -->
<div id="tripSettingsModal" class="fixed inset-0 z-[2000]" style="display: none;" role="dialog" aria-modal="true">
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity z-0" onclick="safeCloseModal('tripSettingsModal')"></div>
        <div class="relative z-10 transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg flex flex-col max-h-[calc(100vh-160px)]">
            <div class="px-8 py-10 overflow-y-auto custom-scrollbar scroll-smooth">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">編輯旅程設定</h3>
                    <button onclick="safeCloseModal('tripSettingsModal')" class="text-gray-400 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100 transition-all">
                        <svg class="w-6 h-6 text-slate-300 group-hover:text-slate-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- 旅程設定 Form -->
                <form action="{{ route('trips.update', ['user' => $trip->user, 'trip' => $trip]) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">旅程名稱</label>
                            <input type="text" name="name" required value="{{ $trip->name }}" class="block w-full px-4 py-3 bg-white border border-gray-300 rounded-xl text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">開始日期</label>
                                <input type="date" name="start_date" required value="{{ optional($trip->start_date)->toDateString() }}" class="block w-full px-4 py-3 bg-white border border-gray-300 rounded-xl text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">結束日期</label>
                                <input type="date" name="end_date" required value="{{ optional($trip->end_date)->toDateString() }}" class="block w-full px-4 py-3 bg-white border border-gray-300 rounded-xl text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            </div>
                        </div>



                        <div class="grid grid-cols-3 gap-4 pt-2">
                            @php
                            $allCurrencies = [
                            'TWD' => '台幣', 'JPY' => '日幣', 'KRW' => '韓幣',
                            'USD' => '美金', 'EUR' => '歐元', 'GBP' => '英鎊',
                            'AUD' => '澳幣', 'CAD' => '加幣', 'HKD' => '港幣',
                            'SGD' => '新幣', 'CNY' => '人民幣', 'THB' => '泰銖',
                            'VND' => '越南盾', 'MYR' => '馬幣',
                            ];
                            @endphp
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 text-center">本國貨幣</label>
                                <select name="base_currency" required class="block w-full px-3 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-indigo-500 text-center font-bold">
                                    @foreach($allCurrencies as $code => $label)
                                    <option value="{{ $code }}" {{ $trip->base_currency == $code ? 'selected' : '' }}>{{ $code }} — {{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 text-center">當地貨幣</label>
                                <select name="target_currency" required class="block w-full px-3 py-3 bg-indigo-50 border border-indigo-200 rounded-xl text-indigo-700 focus:ring-2 focus:ring-indigo-500 text-center font-bold">
                                    @foreach($allCurrencies as $code => $label)
                                    <option value="{{ $code }}" {{ $trip->target_currency == $code ? 'selected' : '' }}>{{ $code }} — {{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 text-center">預估匯率</label>
                                <input type="number" step="0.0001" name="exchange_rate" required value="{{ $trip->exchange_rate }}" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-indigo-500 font-mono text-center">
                            </div>
                        </div>
                    </div>

                    <!-- 旅程封面圖設定 -->
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide">旅程封面圖設定</h4>
                            @if($trip->cover_image)
                            <label class="flex items-center gap-2 text-xs text-red-500 cursor-pointer hover:text-red-700 transition-colors">
                                <input type="checkbox" name="restore_cover" value="1" class="rounded border-gray-300">
                                恢復預設
                            </label>
                            @endif
                        </div>
                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-200">
                            <input type="file" name="cover_image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-white file:text-indigo-600 hover:file:bg-indigo-50 cursor-pointer transition-all">
                            <p class="text-[10px] text-gray-400 mt-2 italic">支援 JPG、PNG，最大 5MB。</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-10 pt-8 border-t border-gray-100">
                        <button type="submit" class="px-6 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-xl hover:shadow-indigo-200 active:scale-95">
                            儲存變更 Save Changes
                        </button>
                        @if(auth()->id() === $trip->user_id)
                        <button type="button" onclick="confirmDelete('刪除旅程？', '確定要刪除整個「{{ $trip->name }}」嗎？', 'delete-trip-form')" class="px-6 py-4 bg-white text-red-600 font-black rounded-2xl border border-red-200 hover:bg-red-50 transition-all flex items-center justify-center gap-2 active:scale-95 shadow-sm">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            刪除旅程 Delete
                        </button>
                        @endif
                    </div>
                </form>

                @if(auth()->id() === $trip->user_id)
                <form id="delete-trip-form" action="{{ route('trips.destroy', ['user' => $trip->user, 'trip' => $trip]) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
                @endif


                @if(auth()->id() == $trip->user_id)
                <div class="mt-10 pt-10 border-t border-gray-100">
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-[0.2em] flex items-center gap-2 mb-6 bg-indigo-50/50 self-start px-3 py-1.5 rounded-lg border border-indigo-100">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        協作者管理 Collaborators
                    </h4>

                    <div class="space-y-3 mb-6">
                        @foreach($trip->collaborators as $collaborator)
                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                    {{ strtoupper(substr($collaborator->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $collaborator->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $collaborator->email }}</p>
                                </div>
                            </div>
                            <form action="{{ route('trip.collaborators.remove', ['user' => $trip->user, 'trip' => $trip, 'collaborator' => $collaborator->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 p-1" onclick="return confirm('確定移除此協作者？')">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>

                    <form action="{{ route('trip.collaborators.add', ['user' => $trip->user, 'trip' => $trip]) }}" method="POST">
                        @csrf
                        <label class="block text-sm font-bold text-gray-700 mb-2">邀請新協作者 Invite Collaborator (Email)</label>
                        <div class="flex gap-2">
                            <input type="email" name="email" required placeholder="例如：winnie@example.com" class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-medium">
                            <button type="submit" class="px-8 py-3 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition-all shadow-md active:scale-95 whitespace-nowrap">
                                加入 Add
                            </button>
                        </div>
                        <p class="mt-2 text-[11px] text-gray-400 italic">請確認對方已經在網站上註冊帳號。</p>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endauth
@endpush

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // General Checkbox Persistence
            const checkboxes = document.querySelectorAll('.persist-chk');
            checkboxes.forEach(chk => {
                const key = chk.getAttribute('data-key');
                const saved = localStorage.getItem(key);
                if (saved === 'true') chk.checked = true;
                chk.addEventListener('change', function () { localStorage.setItem(key, this.checked); });
            });

            // Transport Mode Listeners
            document.querySelectorAll('#tripTransportModal input[name="transport_type"]').forEach(radio => {
                radio.addEventListener('change', (e) => {
                    updateTransportLabels(e.target.value);
                });
            });
            
            // Initial call
            initTransportMode();
        });

        // Transport Configurations
        const transportConfigs = {
            flight: {
                modalTitle: '航班資訊編輯',
                basicsTitle: '航班基本資訊 Flight Basics',
                notes: '行李規定 (Baggage Rules)',
                notesPlaceholder: '例如：23kg x 2',
                outbound: '去程 Outbound', inbound: '回程 Inbound',
                routeLabel: '航線 (Route)', helpRoute: '請使用「起點機場 ➝ 終點機場」格式'
            },
            train: {
                modalTitle: '鐵路票務編輯',
                basicsTitle: '列車基本資訊 Train Basics',
                notes: '票位 / 備註 (Notes)', notesPlaceholder: '例如：自由席 / 5車12A',
                outbound: '出發 / 去程 Departure', inbound: '抵達 / 回程 Return',
                routeLabel: '起訖站 (Stations)', helpRoute: '請使用「起點站 ➝ 終點站」格式'
            },
            bus: {
                modalTitle: '巴士行程編輯',
                basicsTitle: '巴士基本資訊 Bus Basics',
                notes: '備註 / 候車處 (Notes)', notesPlaceholder: '例如：電子票證 / 3號月台',
                outbound: '發車 / 去程 Departure', inbound: '抵達 / 回程 Return',
                routeLabel: '路線名稱 (Route)', helpRoute: '請使用「起點站 ➝ 終點站」格式'
            },
            car: {
                modalTitle: '租車合約編輯',
                basicsTitle: '租賃基本資訊 Rental Basics',
                notes: '租賃與停車說明 (Rental/Parking)', notesPlaceholder: '例如：含保險 / 飯店附停車',
                outbound: '取車地點與時間 Pick-up', inbound: '還車地點與時間 Drop-off',
                routeLabel: '地點 (Location)', helpRoute: '請使用「地點」或「起點 ➝ 終點」格式'
            },
            ship: {
                modalTitle: '船期航務編輯',
                basicsTitle: '航務基本資訊 Ship Basics',
                notes: '備註 / 艙位 (Notes)', notesPlaceholder: '例如：窗位 / 含餐',
                outbound: '啟航 Departure', inbound: '返航 / 回程 Return',
                routeLabel: '港口 (Ports)', helpRoute: '請使用「港口名稱」或「起訖港 ➝ 終點港」格式'
            }
        };

        function updateTransportLabels(mode) {
            const config = transportConfigs[mode] || transportConfigs.flight;
            
            // Toggle visibility of specialized field groups
            document.querySelectorAll('.mode-fields').forEach(div => div.classList.add('hidden'));
            const activeGroup = document.getElementById('fields-' + mode);
            if (activeGroup) activeGroup.classList.remove('hidden');

            const elements = {
                baggageLabel: document.getElementById('mode-label-baggage'),
                baggageInput: document.getElementById('mode-input-baggage'),
                outboundLabel: document.getElementById('mode-label-outbound'),
                inboundLabel: document.getElementById('mode-label-inbound'),
                routeLabel: document.getElementById('mode-label-route'),
                routeLabelIn: document.getElementById('mode-label-route-in'),
                helpRoute: document.getElementById('mode-help-route'),
                modalTitle: document.getElementById('modal-transport-title'),
                basicsTitle: document.getElementById('mode-basics-title')
            };

            if (elements.baggageLabel) elements.baggageLabel.innerText = config.notes;
            if (elements.baggageInput) elements.baggageInput.placeholder = config.notesPlaceholder;
            if (elements.outboundLabel) elements.outboundLabel.innerText = config.outbound;
            if (elements.inboundLabel) elements.inboundLabel.innerText = config.inbound;
            if (elements.routeLabel) elements.routeLabel.innerText = config.routeLabel;
            if (elements.routeLabelIn) elements.routeLabelIn.innerText = config.routeLabel;
            if (elements.helpRoute) elements.helpRoute.innerText = config.helpRoute;
            if (elements.modalTitle) elements.modalTitle.innerText = config.modalTitle;
            if (elements.basicsTitle) elements.basicsTitle.innerText = config.basicsTitle || '基本資訊 Basics';
        }

        function initTransportMode() {
            const selectedMode = document.querySelector('#tripTransportModal input[name="transport_type"]:checked');
            if (selectedMode) {
                updateTransportLabels(selectedMode.value);
            }
        }

        function openFlightEditModal() {
            safeOpenModal('tripTransportModal');
            initTransportMode();
        }
        function closeFlightEditModal() {
            safeCloseModal('tripTransportModal');
        }

        function openTripEditModal() {
            safeOpenModal('tripSettingsModal');
        }
        function closeTripEditModal() {
            safeCloseModal('tripSettingsModal');
        }
    </script>

    <script>
        function toggleTransportDetails() {
            const details = document.getElementById('transportDetails');
            const chevron = document.getElementById('transportChevron');
            if (details) {
                if (details.style.maxHeight === '0px') {
                    details.style.maxHeight = '2000px';
                    chevron && chevron.classList.add('rotate-180');
                } else {
                    details.style.maxHeight = '0px';
                    chevron && chevron.classList.remove('rotate-180');
                }
            }
        }
    </script>
@endsection
