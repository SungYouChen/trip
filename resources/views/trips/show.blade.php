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
        'color' => 'muji-oak',
        'gradient' => 'from-muji-wheat/5 to-muji-base/5',
        'border' => 'border-muji-edge',
        'text' => 'text-muji-oak',
        'bg_light' => 'bg-muji-base',
        'label_out' => '去程',
        'label_in' => '回程',
        'icon_mid' => '<svg class="w-5 h-5 rotate-90" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" /></svg>'
    ],
    'train' => [
        'color' => 'muji-oak',
        'gradient' => 'from-muji-wheat/5 to-muji-base/5',
        'border' => 'border-muji-edge',
        'text' => 'text-muji-oak',
        'bg_light' => 'bg-muji-base',
        'label_out' => '車次/去程',
        'label_in' => '返程/回程',
        'icon_mid' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>'
    ],
    'bus' => [
        'color' => 'muji-oak',
        'gradient' => 'from-muji-wheat/5 to-muji-base/5',
        'border' => 'border-muji-edge',
        'text' => 'text-muji-oak',
        'bg_light' => 'bg-muji-base',
        'label_out' => '發車/去程',
        'label_in' => '返程/回程',
        'icon_mid' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>'
    ],
    'car' => [
        'color' => 'muji-oak',
        'gradient' => 'from-muji-wheat/5 to-muji-base/5',
        'border' => 'border-muji-edge',
        'text' => 'text-muji-oak',
        'bg_light' => 'bg-muji-base',
        'label_out' => '取車與地點',
        'label_in' => '還車與地點',
        'icon_mid' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>'
    ],
    'ship' => [
        'color' => 'muji-oak',
        'gradient' => 'from-muji-wheat/5 to-muji-base/5',
        'border' => 'border-muji-edge',
        'text' => 'text-muji-oak',
        'bg_light' => 'bg-muji-base',
        'label_out' => '啟航與碼頭',
        'label_in' => '返航與行程',
        'icon_mid' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4M4 12l8-8m-8 8l8 8" /></svg>'
    ]
];
$theme = $themes[$transportType] ?? $themes['flight'];

// Smart default state for transport card
$startDate = \Carbon\Carbon::parse($trip->start_date)->startOfDay();
$endDate = \Carbon\Carbon::parse($trip->end_date)->startOfDay();
$today = \Carbon\Carbon::today();

// Open if today is day before start OR start day OR day before end OR end day
$isNearStart = $today->isSameDay($startDate->copy()->subDay()) || $today->isSameDay($startDate);
$isNearEnd = $today->isSameDay($endDate->copy()->subDay()) || $today->isSameDay($endDate);
$shouldOpenTransport = $isNearStart || $isNearEnd;
@endphp
@extends('layout')

@section('content')
<div class="mb-12 relative max-w-4xl mx-auto px-4 group">
    <!-- Header Content: Centered Title -->
    <div class="text-center pt-2">
        <h2 class="text-4xl font-black text-muji-ink sm:text-4xl leading-tight px-16">
            {{ $trip->name }}
        </h2>
        
        @if(!$isShared && $trip->is_public)
        <div class="mt-4 mb-2 bg-muji-base/80 backdrop-blur-sm p-2 rounded-xl flex items-center justify-between gap-3 border border-muji-edge max-w-sm mx-auto overflow-hidden shadow-muji-sm">
            <span id="shareLink" class="text-[10px] text-muji-oak font-mono truncate flex-1 font-bold">{{ route('trip.index_shared', ['token' => $trip->share_token]) }}</span>
            <button onclick="copyShareLink()" class="bg-muji-oak text-white text-[10px] px-3 py-1 rounded-lg hover:opacity-80 transition-colors font-black whitespace-nowrap">複製連結</button>
        </div>
        <script>
            function copyShareLink() {
                const link = document.getElementById('shareLink').innerText;
                navigator.clipboard.writeText(link).then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: '連結已複製！',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            }
        </script>
        @endif
        
        <p class="text-md text-muji-ash italic font-medium mt-2">
            {{ \Carbon\Carbon::parse($trip->start_date)->format('Y/m/d') }} - {{ \Carbon\Carbon::parse($trip->end_date)->format('Y/m/d') }}
        </p>
    </div>
    
    @if(!$isShared && auth()->check())
    <!-- Management Tools: Absolute Top-Right -->
    <div class="absolute -top-1 -right-1 sm:right-4 flex items-center gap-2 p-1.5 z-40">
        <!-- Settings Gear -->
        <button onclick="safeOpenModal('tripSettingsModal')" class="mt-3 p-1 text-muji-ash hover:text-muji-oak hover:bg-white rounded-xl transition-all tooltip tooltip-bottom hover:scale-105 active:scale-95 group/btn" data-tip="編輯旅程設定">
            <svg class="w-6 h-6 transition-transform group-hover/btn:rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </button>

        <!-- Privacy Toggle -->
        <form action="{{ route('trip.toggle_share', ['user' => $trip->user, 'trip' => $trip]) }}" method="POST" class="inline m-0">
            @csrf
            <button type="submit" class="mt-3 p-1 transition-all rounded-xl hover:bg-white tooltip tooltip-bottom hover:scale-105 active:scale-95 {{ $trip->is_public ? 'text-muji-oak' : 'text-muji-ash opacity-60' }}" data-tip="{{ $trip->is_public ? '已分享 (點擊隱私)' : '未分享 (點擊公開)' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                </svg>
            </button>
        </form>
    </div>
    @endif
</div>


<style>
    .ticket-masked {
        -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='102%25' height='100%25'%3E%3Cdefs%3E%3Cmask id='m'%3E%3Crect width='100%25' height='100%25' fill='white'/%3E%3Ccircle cx='0' cy='88' r='14' fill='black'/%3E%3Ccircle cx='100%25' cy='88' r='14' fill='black'/%3E%3C/mask%3E%3C/defs%3E%3Crect width='100%25' height='100%25' mask='url(%23m)' fill='white'/%3E%3C/svg%3E");
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='102%25' height='100%25'%3E%3Cdefs%3E%3Cmask id='m'%3E%3Crect width='100%25' height='100%25' fill='white'/%3E%3Ccircle cx='0' cy='88' r='14' fill='black'/%3E%3Ccircle cx='100%25' cy='88' r='14' fill='black'/%3E%3C/mask%3E%3C/defs%3E%3Crect width='100%25' height='100%25' mask='url(%23m)' fill='white'/%3E%3C/svg%3E");
    }
</style>

@if($hasFlight)
<!-- Multi-modal Transport Card -->
<div id="transportCard" class="relative muji-card shadow-muji border-muji-edge mb-8 group/transport zoom-in-on-load ticket-masked overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br {{ $theme['gradient'] }} opacity-50 pointer-events-none"></div>

    <!-- Top Section: Header & Price -->
    <div onclick="toggleTransportDetails()" class="relative px-8 py-5 border-b border-dashed border-muji-edge flex justify-between items-center bg-muji-paper/50 cursor-pointer hover:bg-muji-wheat/10 transition-colors">
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
                    {{ $transportType == 'car' ? '租賃車資訊' : ($transportType == 'flight' ? '航班資訊' : ($transportType == 'train' ? '鐵路資訊' : '交通資訊')) }}
                </h3>
                <p class="text-xl font-black text-muji-ink tracking-tight leading-none">{{ $flightInfo['airline'] }}</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right">
                <p class="text-[10px] font-black text-muji-ash uppercase tracking-widest mb-1">預計費用</p>
                <p class="text-2xl font-mono font-black {{ $theme['text'] }} leading-none">{{ $flightInfo['price'] }}</p>
            </div>
            <div id="transportChevron" class="transition-transform duration-300 transform {{ $shouldOpenTransport ? 'rotate-180' : 'rotate-0' }}">
                <svg class="w-6 h-6 text-muji-ash" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Toggleable Content -->
    <div id="transportDetails" class="transition-[max-height] duration-500 overflow-hidden ease-in-out" style="max-height: {{ $shouldOpenTransport ? '2000px' : '0px' }};">

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
                            <p class="text-[10px] font-black text-muji-ash mb-1 uppercase tracking-widest">日期</p>
                            <span class="text-sm font-black text-muji-ink">{{ $flightInfo['outbound']['date'] }}</span>
                        </div>
                    </div>

                    @php
                    $outRoute = $flightInfo['outbound']['route'] ?? '';
                    $hasArrow = str_contains($outRoute, ' ➝ ');
                    $parts = $hasArrow ? explode(' ➝ ', $outRoute) : [$outRoute, ''];
                    @endphp

                    <div class="flex items-center justify-between gap-6">
                        <div class="flex-1">
                            <p class="text-[10px] font-black text-muji-ash uppercase tracking-widest mb-1">出發地</p>
                            <p class="text-2xl font-black text-muji-ink tracking-tighter leading-tight">{{ $parts[0] }}</p>
                        </div>
                        <div class="flex {{ $theme['text'] }} opacity-40">
                            {!! $theme['icon_mid'] !!}
                        </div>
                        <div class="flex-1 text-right">
                            <p class="text-[10px] font-black text-muji-ash uppercase tracking-widest mb-1">目的地</p>
                            <p class="text-2xl font-black text-muji-ink tracking-tighter leading-tight">{{ $parts[1] ?: '--' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-muji-edge flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-muji-ash uppercase tracking-widest mb-1">起飛時間</span>
                            <span class="text-2xl font-mono font-black text-muji-ink tracking-tighter">{{ $flightInfo['outbound']['time'] ?: '待定' }}</span>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-muji-base flex items-center justify-center shadow-muji-sm">
                            <svg class="w-5 h-5 {{ $theme['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
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
                            <p class="text-[10px] font-black text-muji-ash mb-1 uppercase tracking-widest">日期</p>
                            <span class="text-sm font-black text-muji-ink">{{ $flightInfo['inbound']['date'] }}</span>
                        </div>
                    </div>

                    @php
                    $inRoute = $flightInfo['inbound']['route'] ?? '';
                    $hasArrowIn = str_contains($inRoute, ' ➝ ');
                    $partsIn = $hasArrowIn ? explode(' ➝ ', $inRoute) : [$inRoute, ''];
                    @endphp

                    <div class="flex items-center justify-between gap-6">
                        <div class="flex-1">
                            <p class="text-[10px] font-black text-muji-ash uppercase tracking-widest mb-1">出發地</p>
                            <p class="text-2xl font-black text-muji-ink tracking-tighter leading-tight">{{ $partsIn[0] }}</p>
                        </div>
                        <div class="flex {{ $theme['text'] }} opacity-40">
                            {!! $theme['icon_mid'] !!}
                        </div>
                        <div class="flex-1 text-right">
                            <p class="text-[10px] font-black text-muji-ash uppercase tracking-widest mb-1">目的地</p>
                            <p class="text-2xl font-black text-muji-ink tracking-tighter leading-tight">{{ $partsIn[1] ?: '--' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-muji-edge flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-muji-ash uppercase tracking-widest mb-1">抵達時間</span>
                            <span class="text-2xl font-mono font-black text-muji-ink tracking-tighter">{{ $flightInfo['inbound']['time'] ?: '待定' }}</span>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-muji-base flex items-center justify-center shadow-muji-sm">
                            <svg class="w-5 h-5 {{ $theme['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                    </div>
                </div>
            @endswitch
    </div>


    <!-- Footer -->
    <div onclick="toggleTransportDetails()" class="relative px-8 py-4 bg-muji-base/30 flex flex-wrap justify-between items-center gap-4 border-t border-muji-edge cursor-pointer hover:bg-muji-wheat/10 transition-colors font-black">
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 px-3 py-1.5 bg-muji-paper rounded-xl shadow-muji-sm border border-muji-edge">
                <svg class="w-4 h-4 {{ $theme['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span class="text-xs font-black text-muji-ash truncate max-w-[200px] sm:max-w-md">
                    {{ $transportType == 'car' ? '租車備註：' : '行李/備註：' }}{{ $flightInfo['baggage'] ?: '無特別備註' }}
                </span>
            </div>
        </div>
        @if(!$isShared)
        @auth
        <button onclick="event.stopPropagation(); openFlightEditModal()" class="flex items-center gap-2 px-6 py-2.5 {{ $theme['bg_light'] }} {{ $theme['text'] }} rounded-2xl font-black text-xs hover:shadow-lg transition-all active:scale-95 border {{ $theme['border'] }}">
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
<div class="mb-8 p-8 border-2 border-dashed border-muji-edge rounded-3xl flex flex-col items-center justify-center text-muji-ash bg-muji-base/30 hover:border-muji-oak hover:text-muji-oak transition-all group" onclick="openFlightEditModal()" style="cursor: pointer;">
    <div class="w-12 h-12 rounded-full bg-muji-base group-hover:bg-muji-wheat/20 flex items-center justify-center mb-3 transition-colors">
        <svg class="w-6 h-6 text-muji-wheat group-hover:text-muji-oak transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 4v16m8-8H4" />
        </svg>
    </div>
    <span class="font-black text-xs tracking-[0.2em] uppercase">點擊新增交通資訊</span>
</div>
@endauth
@endif

@php $showArchived = request('archived') == '1'; @endphp
<div class="flex items-center justify-between mb-4">
    <p class="text-sm text-muji-ash font-medium">
        @php $activeDaysCount = $itinerary->filter(fn($d) => !$d->trashed())->count(); @endphp
        共 {{ $activeDaysCount }} 天
    </p>
    @if(!$isShared)
    @auth
    <a href="{{ request()->fullUrlWithQuery(['archived' => $showArchived ? '0' : '1']) }}" class="flex items-center gap-2 text-xs font-black px-3 py-1.5 rounded-full border border-muji-edge transition-all {{ $showArchived ? 'bg-muji-base text-muji-oak' : 'bg-muji-base text-muji-ash hover:bg-muji-wheat/20' }}">
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
        <a href="{{ $cardLink }}" class="flex flex-col h-52 muji-card shadow-muji border-muji-edge hover:shadow-muji transition-all duration-300 transform hover:-translate-y-1 overflow-hidden {{ $isArchived ? 'border-2 border-dashed border-muji-ash grayscale opacity-60' : ($isToday ? 'bg-muji-wheat/10 ring-1 ring-muji-oak' : '') }}">
            <div class="p-6 flex-1 overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[10px] font-bold bg-muji-base text-muji-oak border border-muji-edge">
                        {{ $dayDate->format('n/j') }} ({{ $dayDate->locale('zh_TW')->dayName }})
                    </span>
                </div>

                <h3 class="text-xl font-black text-muji-ink mb-1 group-hover:text-muji-oak transition-colors truncate">
                    {{ $day->title ?: 'Day ' . $loop->iteration }}
                </h3>
                @if($day->location)
                <div class="flex items-center gap-1 text-[10px] font-bold text-muji-oak mb-2 uppercase tracking-widest truncate">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="truncate">{{ $day->location }}</span>
                </div>
                @endif

                <p class="text-xs text-muji-ash line-clamp-2">
                    {{ $day->summary }}
                </p>
            </div>

            <div class="px-6 py-3 bg-muji-base border-t border-muji-edge flex items-center justify-between text-[10px] font-black text-muji-ash group-hover:bg-muji-wheat/10 transition-colors flex-shrink-0">
                <span>查看詳情</span>
                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform text-muji-oak" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
        <button type="submit" class="w-full h-full min-h-[200px] flex flex-col items-center justify-center p-6 border-2 border-dashed border-muji-edge rounded-3xl text-muji-ash hover:text-muji-oak hover:border-muji-oak hover:bg-muji-wheat/10 transition-all group">
            <div class="w-12 h-12 rounded-full bg-muji-base group-hover:bg-muji-wheat flex items-center justify-center mb-3 transition-colors">
                <svg class="w-6 h-6 text-muji-ash group-hover:text-muji-oak transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <span class="font-black text-[10px] tracking-widest uppercase">新增額外天數</span>
        </button>
    </form>
    @endauth
    @endif
</div>

<!-- Checklists Section -->
<div class="grid md:grid-cols-2 gap-8 mt-12">
    <!-- Must Buy List -->
    <div class="muji-card shadow-muji border-muji-edge p-8 bg-muji-paper/50 relative flex flex-col min-h-[400px]">
        <h3 class="text-xl font-black text-muji-ink mb-6 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-muji-base flex items-center justify-center text-muji-oak shadow-muji-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </span>
            必買清單
        </h3>
        <div class="flex-grow space-y-6">
            @php
            $shoppingCategories = $trip->checklistItems()->where('type', 'shopping')->pluck('category')->unique()->toArray();
            @endphp
            @forelse($shoppingCategories as $category)
            @php
            $allCategoryItems = $trip->checklistItems()->withTrashed()->where('type', 'shopping')->where('category', $category)->get();
            $items = $showArchived
            ? $allCategoryItems->filter(fn($i) => $i->trashed())
            : $allCategoryItems->filter(fn($i) => !$i->trashed());
            @endphp
            @if($items->count() > 0)
            <div class="animate-in fade-in slide-in-from-left-4 duration-300">
                <h4 class="font-black text-muji-ink text-sm mb-2 border-l-4 border-muji-wheat pl-2 uppercase tracking-wider">{{ $category }}</h4>
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($items as $item)
                    @php $isItemArchived = $item->trashed(); @endphp
                    <li class="flex items-start justify-between gap-2 text-sm text-muji-ash group {{ $isItemArchived ? 'border border-dashed border-red-200 rounded p-1 bg-red-50/20 grayscale opacity-60' : '' }}">
                        <div class="flex items-start gap-2">
                            @if($isItemArchived)
                            <span class="text-red-400 mt-0.5"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg></span>
                            @else
                            <input type="checkbox" class="mt-1 rounded text-muji-oak focus:ring-muji-oak persist-chk disabled:opacity-50 disabled:cursor-not-allowed" data-key="chk_{{ $item->id }}" @if(!auth()->check() || $isShared) disabled @endif>
                            @endif
                            <span class="{{ (!auth()->check() || $isShared) ? 'text-muji-ash/50' : '' }} {{ $isItemArchived ? 'text-red-800 font-bold' : '' }}">{{ $item->name }}</span>
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
@empty
                <div class="flex flex-col items-center justify-center py-10 opacity-30 select-none">
                    <svg class="w-10 h-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    <p class="text-xs font-black tracking-widest uppercase">尚無必買清單</p>
                </div>
            @endforelse

            @if(!$isShared)
            @auth
            <form action="{{ route('checklist.store', ['user' => $trip->user, 'trip' => $trip]) }}" method="POST" class="mt-4 pt-4 border-t border-muji-edge">
                @csrf
                <input type="hidden" name="type" value="shopping">
                <div class="flex gap-2">
                    <input type="text" name="category" placeholder="分類" class="w-1/3 rounded-xl border-muji-edge text-sm p-3 bg-muji-base/30 text-muji-ink focus:ring-muji-oak transition-all hover:bg-muji-base/50" required list="shop_categories" autocomplete="off">
                    <datalist id="shop_categories">
                        @foreach($shoppingCategories as $cat)
                        <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                    <input type="text" name="name" placeholder="想買什麼？" class="w-2/3 rounded-xl border-muji-edge text-sm p-3 bg-muji-base/30 text-muji-ink focus:ring-muji-oak" required>
                    <button type="submit" class="bg-muji-oak text-white p-2 rounded-lg hover:opacity-90">
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
    <div class="muji-card shadow-muji border-muji-edge p-8 bg-muji-paper/50 relative flex flex-col min-h-[400px]">
        <h3 class="text-xl font-black text-muji-ink mb-6 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-muji-base flex items-center justify-center text-muji-oak shadow-muji-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </span>
            想去景點
        </h3>
        <div class="flex-grow space-y-6">
            @php
            $spotCategories = $trip->checklistItems()->where('type', 'spot')->pluck('category')->unique()->toArray();
            @endphp
            
            @forelse($spotCategories as $category)
                @php
                $allSpotItems = $trip->checklistItems()->withTrashed()->where('type', 'spot')->where('category', $category)->get();
                $items = $showArchived
                ? $allSpotItems->filter(fn($i) => $i->trashed())
                : $allSpotItems->filter(fn($i) => !$i->trashed());
                @endphp
                @if($items->count() > 0)
                <div class="animate-in fade-in slide-in-from-right-4 duration-300">
                    <h4 class="font-black text-muji-ink text-sm mb-2 border-l-4 border-muji-wheat pl-2 uppercase tracking-wider">{{ $category }}</h4>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($items as $item)
                        @php $isItemArchived = $item->trashed(); @endphp
                        <li class="flex items-start justify-between gap-2 text-sm text-muji-ash group {{ $isItemArchived ? 'border border-dashed border-red-200 rounded p-1 bg-red-50/20 grayscale opacity-60' : '' }}">
                            <div class="flex items-start gap-2">
                                @if($isItemArchived)
                                <span class="text-red-400 mt-1"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></span>
                                @else
                                <input type="checkbox" class="mt-1 rounded text-muji-oak focus:ring-muji-oak persist-chk disabled:opacity-50 disabled:cursor-not-allowed" data-key="chk_{{ $item->id }}" @if(!auth()->check() || $isShared) disabled @endif>
                                @endif
                                <span class="{{ (!auth()->check() || $isShared) ? 'text-muji-ash/50' : '' }} {{ $isItemArchived ? 'text-red-800 font-bold' : '' }}">{{ $item->name }}</span>
                            </div>
                            @if(!$isShared)
                            @auth
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                @if($isItemArchived)
                                @php $restoreGoId = 'restore-chk-go-' . $item->id; @endphp
                                <form id="{{ $restoreGoId }}" action="{{ route('checklist.restore', ['user' => $trip->user, 'trip' => $trip, 'id' => $item->id]) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="button" class="text-green-500 hover:text-green-700 p-0.5" onclick="confirmAction('還原清單項目？', '確定要將「{{ $item->name }}」移回想去景點嗎？', '{{ $restoreGoId }}')">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                                    </button>
                                </form>
                                @else
                                @php $chkGoFormId = 'del-chk-go-' . $item->id; @endphp
                                <form id="{{ $chkGoFormId }}" action="{{ route('checklist.destroy', ['user' => $trip->user, 'trip' => $trip, 'id' => $item->id]) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" class="text-red-400 hover:text-red-600 p-0.5" onclick="confirmDelete('封存清單項目？', '確定要將「{{ $item->name }}」移至回收桶嗎？', '{{ $chkGoFormId }}')">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
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
            @empty
                <div class="flex flex-col items-center justify-center py-10 opacity-30 select-none">
                    <svg class="w-10 h-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <p class="text-xs font-black tracking-widest uppercase">尚無景點項目</p>
                </div>
            @endforelse

            @if(!$isShared)
            @auth
            <form action="{{ route('checklist.store', ['user' => $trip->user, 'trip' => $trip]) }}" method="POST" class="mt-8 pt-6 border-t border-muji-edge">
                @csrf
                <input type="hidden" name="type" value="spot">
                <div class="flex gap-2">
                    <input type="text" name="category" placeholder="區域" class="w-1/3 rounded-xl border-muji-edge text-sm p-3 bg-muji-base/30 text-muji-ink focus:ring-muji-oak transition-all hover:bg-muji-base/50" required list="spot_categories" autocomplete="off">
                    <datalist id="spot_categories">
                        @foreach($spotCategories as $cat) <option value="{{ $cat }}"> @endforeach
                    </datalist>
                    <input type="text" name="name" placeholder="想去哪裡？" class="w-2/3 rounded-xl border-muji-edge text-sm p-3 bg-muji-base/30 text-muji-ink focus:ring-muji-oak" required>
                    <button type="submit" class="bg-muji-oak text-white p-3 rounded-xl hover:opacity-90 active:scale-95 transition-all shadow-muji-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
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
                        <div class="p-3 bg-muji-base rounded-2xl text-muji-oak shadow-muji-sm">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                        </div>
                        <div class="flex flex-col border-l-2 border-muji-edge pl-4">
                            <h3 id="modal-transport-title" class="text-2xl font-black text-muji-ink leading-tight">編輯交通資訊</h3>
                            <p class="text-[10px] font-bold text-muji-ash uppercase tracking-[0.2em] mt-1">規劃您的旅程交通</p>
                        </div>
                    </div>
                    <button onclick="safeCloseModal('tripTransportModal')" class="text-muji-ash hover:text-muji-ink p-2 rounded-full hover:bg-muji-base transition-all">
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
                        <h4 class="text-[10px] font-black text-muji-oak uppercase tracking-widest flex items-center gap-2 mb-4 bg-muji-base/50 self-start px-3 py-1.5 rounded-lg border border-muji-edge">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            交通方式
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
                                <div class="w-full px-2 py-3 bg-muji-base/30 border border-muji-edge rounded-2xl text-center group-hover:bg-muji-wheat/10 peer-checked:bg-muji-oak peer-checked:border-muji-oak peer-checked:text-white transition-all shadow-muji-sm flex flex-col items-center gap-2">
                                    <svg class="w-5 h-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $data[1] }}" />
                                    </svg>
                                    <span class="text-[9px] font-black uppercase tracking-widest">{{ $data[0] }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 基本資訊 Specialized Fields -->
                    <div id="transport-basics-container" class="space-y-6">
                        <h4 id="mode-basics-title" class="text-[10px] font-black text-muji-oak uppercase tracking-widest flex items-center gap-2 mb-4 bg-muji-base/50 self-start px-3 py-1.5 rounded-lg border border-muji-edge">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            基本資訊
                        </h4>
                        
                        <!-- Flight Specific Fields -->
                        <div id="fields-flight" class="mode-fields {{ ($transportType ?? 'flight') == 'flight' ? '' : 'hidden' }} grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-muji-ash mb-2">航空公司 / 航班編號</label>
                                <input type="text" name="airline" value="{{ $flightInfo['airline'] ?? '' }}" class="block w-full px-4 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium" placeholder="例如：星宇航空 JX800">
                            </div>
                        </div>

                        <!-- Train Specific Fields -->
                        <div id="fields-train" class="mode-fields {{ ($transportType ?? '') == 'train' ? '' : 'hidden' }} grid grid-cols-1 gap-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-muji-ash mb-2">列車名稱 / 車次</label>
                                    <input type="text" name="train_no" value="{{ $flightInfo['train_no'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-muji-edge rounded-xl focus:ring-2 focus:ring-muji-oak shadow-muji-sm text-muji-ink font-medium" placeholder="例如：JR 新幹線 希望號">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-muji-ash mb-2">座位資訊 (Car/Seat)</label>
                                    <input type="text" name="train_seat" value="{{ $flightInfo['train_seat'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-muji-edge rounded-xl focus:ring-2 focus:ring-muji-oak shadow-muji-sm text-muji-ink font-medium" placeholder="例如：5車 12A">
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
                                <label class="block text-sm font-bold text-muji-ash mb-2">估計總費用</label>
                                <div class="flex relative rounded-xl border border-muji-edge overflow-hidden focus-within:ring-2 focus-within:ring-muji-oak bg-white group transition-all">
                                    <select name="flight_currency" class="bg-muji-base border-0 border-r border-muji-edge px-3 py-3 text-muji-ink font-black text-xs focus:ring-0 cursor-pointer appearance-none">
                                        @php
                                        $fPrice = $flightInfo['price'] ?? '';
                                        preg_match('/^([^\d]+)?([\d,.]+)/u', $fPrice, $fm);
                                        $fsCurrency = trim($fm[1] ?? $trip->base_currency);
                                        $fsNum = isset($fm[2]) ? str_replace(',', '', $fm[2]) : '';
                                        @endphp
                                        <option value="{{ $trip->base_currency }}" {{ $fsCurrency==$trip->base_currency ? 'selected' : '' }}>{{ $trip->base_currency }}</option>
                                        <option value="{{ $trip->target_currency }}" {{ $fsCurrency==$trip->target_currency ? 'selected' : '' }}>{{ $trip->target_currency }}</option>
                                    </select>
                                    <input type="number" step="0.01" name="flight_price_num" value="{{ $fsNum }}" class="flex-1 w-full border-0 bg-transparent focus:ring-0 px-4 py-3 font-mono text-muji-ink font-black" placeholder="25000">
                                </div>
                            </div>
                            <div>
                                <label id="mode-label-baggage" class="block text-sm font-bold text-muji-ash mb-2">行李 / 備註規定</label>
                                <input type="text" name="baggage" id="mode-input-baggage" value="{{ $flightInfo['baggage'] }}" class="block w-full px-4 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium" placeholder="例如：23kg x 2">
                            </div>
                        </div>
                    </div>

                    <!-- 去程細節 -->
                    <div class="p-6 bg-muji-base/50 rounded-2xl space-y-4 border border-muji-edge">
                        <h4 class="text-xs font-black text-muji-oak uppercase tracking-[0.2em] flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                            <span id="mode-label-outbound">去程資訊</span>
                        </h4>
                        <div class="grid grid-cols-1 gap-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-muji-ash mb-1">日期</label>
                                @php
                                    $rawOut = $flightInfo['outbound']['date'] ?? '';
                                    preg_match('/(\d{1,4}[-\/]\d{1,2}[-\/]?\d{0,4})/', $rawOut, $mOut);
                                    $valOut = '';
                                    try { if($mOut[1] ?? false) $valOut = \Carbon\Carbon::parse($mOut[1])->toDateString(); } catch(\Exception $e){}
                                @endphp
                                <input type="date" name="outbound_date" value="{{ $valOut }}" class="w-full px-4 py-3 bg-white border border-muji-edge rounded-xl shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium">
                                </div>
                                <div>
                                    <label id="mode-label-route" class="block text-xs font-bold text-muji-ash mb-1">路線</label>
                                    <input type="text" name="outbound_route" value="{{ $flightInfo['outbound']['route'] }}" class="w-full px-4 py-3 bg-white border border-muji-edge rounded-xl shadow-muji-sm text-muji-ink" placeholder="TPE ➝ UKB">
                                    <p id="mode-help-route" class="text-[9px] text-muji-ash mt-1 italic">請使用「起點 ➝ 終點」格式</p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-muji-ash mb-1">出發 / 到達時間</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[9px] font-black text-muji-ash uppercase">Dep</span>
                                        <input type="time" name="outbound_time_start" value="{{ $flightInfo['outbound']['time_start'] ?? '' }}" class="w-full pl-10 pr-4 py-3 bg-white border border-muji-edge rounded-xl shadow-muji-sm font-mono text-sm focus:ring-2 focus:ring-muji-oak text-muji-ink">
                                    </div>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[9px] font-black text-muji-ash uppercase">Arr</span>
                                        <input type="time" name="outbound_time_end" value="{{ $flightInfo['outbound']['time_end'] ?? '' }}" class="w-full pl-10 pr-4 py-3 bg-white border border-muji-edge rounded-xl shadow-muji-sm font-mono text-sm focus:ring-2 focus:ring-muji-oak text-muji-ink">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 回程細節 -->
                    <div class="p-6 bg-muji-base/50 rounded-2xl space-y-4 border border-muji-edge">
                        <h4 class="text-xs font-black text-muji-oak uppercase tracking-[0.2em] flex items-center gap-2">
                            <span class="w-2 h-2 bg-muji-wheat rounded-full"></span> <span id="mode-label-inbound">回程資訊</span>
                        </h4>
                        <div class="grid grid-cols-1 gap-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-muji-ash mb-1">日期</label>
                                @php
                                    $rawIn = $flightInfo['inbound']['date'] ?? '';
                                    preg_match('/(\d{1,4}[-\/]\d{1,2}[-\/]?\d{0,4})/', $rawIn, $mIn);
                                    $valIn = '';
                                    try { if($mIn[1] ?? false) $valIn = \Carbon\Carbon::parse($mIn[1])->toDateString(); } catch(\Exception $e){}
                                @endphp
                                <input type="date" name="inbound_date" value="{{ $valIn }}" class="w-full px-4 py-3 bg-white border border-muji-edge rounded-xl shadow-muji-sm focus:ring-2 focus:ring-muji-oak text-muji-ink">
                                </div>
                                <div>
                                    <label id="mode-label-route-in" class="block text-xs font-bold text-muji-ash mb-1">路線</label>
                                    <input type="text" name="inbound_route" value="{{ $flightInfo['inbound']['route'] }}" class="w-full px-4 py-3 bg-white border border-muji-edge rounded-xl shadow-muji-sm text-muji-ink" placeholder="KIX ➝ TPE">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-muji-ash mb-1">出發 / 到達時間</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[9px] font-black text-muji-ash uppercase">出發</span>
                                        <input type="time" name="inbound_time_start" value="{{ $flightInfo['inbound']['time_start'] ?? '' }}" class="w-full pl-10 pr-4 py-3 bg-white border border-muji-edge rounded-xl shadow-muji-sm font-mono text-sm focus:ring-2 focus:ring-muji-oak text-muji-ink">
                                    </div>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[9px] font-black text-muji-ash uppercase">到達</span>
                                        <input type="time" name="inbound_time_end" value="{{ $flightInfo['inbound']['time_end'] ?? '' }}" class="w-full pl-10 pr-4 py-3 bg-white border border-muji-edge rounded-xl shadow-muji-sm font-mono text-sm focus:ring-2 focus:ring-muji-oak text-muji-ink">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-6 mt-8 border-t border-muji-edge">
                        <button type="button" onclick="safeCloseModal('tripTransportModal')" class="flex-1 px-6 py-4 bg-muji-paper text-muji-ash border border-muji-edge font-black rounded-2xl hover:bg-muji-base transition-colors">取消</button>
                        <button type="submit" class="flex-1 px-6 py-4 bg-muji-oak text-white font-black rounded-2xl hover:opacity-90 shadow-muji transition-all active:scale-95">儲存變更</button>
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
                <div class="flex justify-between items-start mb-10">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-muji-base rounded-2xl text-muji-oak shadow-muji-sm">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="flex flex-col border-l-2 border-muji-edge pl-4">
                            <h3 class="text-2xl font-black text-muji-ink leading-tight">編輯旅程設定</h3>
                            <p class="text-[10px] font-bold text-muji-ash uppercase tracking-[0.2em] mt-1">調整您的旅行細節</p>
                        </div>
                    </div>
                    <button onclick="safeCloseModal('tripSettingsModal')" class="text-muji-ash hover:text-muji-ink p-2 rounded-full hover:bg-muji-base transition-all">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                            <label class="block text-sm font-bold text-muji-ash mb-2">旅程名稱</label>
                            <input type="text" name="name" required value="{{ $trip->name }}" class="block w-full px-4 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-muji-ash mb-2">開始日期</label>
                                <input type="date" name="start_date" required value="{{ optional($trip->start_date)->toDateString() }}" class="block w-full px-4 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-muji-ash mb-2">結束日期</label>
                                <input type="date" name="end_date" required value="{{ optional($trip->end_date)->toDateString() }}" class="block w-full px-4 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium">
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
                                <label class="block text-sm font-bold text-muji-ash uppercase mb-2 text-left">本國貨幣</label>
                                <select name="base_currency" required class="block w-full px-3 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink focus:ring-2 focus:ring-muji-oak text-center font-black">
                                    @foreach($allCurrencies as $code => $label)
                                        <option value="{{ $code }}" {{ $trip->base_currency == $code ? 'selected' : '' }}>{{ $code }} — {{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-muji-ash uppercase mb-2 text-left">當地貨幣</label>
                                <select name="target_currency" required class="block w-full px-3 py-3 bg-muji-base border border-muji-edge rounded-xl text-muji-oak focus:ring-2 focus:ring-muji-oak text-center font-black">
                                    @foreach($allCurrencies as $code => $label)
                                        <option value="{{ $code }}" {{ $trip->target_currency == $code ? 'selected' : '' }}>{{ $code }} — {{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-muji-ash uppercase mb-2 text-left">預估匯率</label>
                                <input type="number" step="0.0001" name="exchange_rate" required value="{{ $trip->exchange_rate }}" class="block w-full px-4 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink focus:ring-2 focus:ring-muji-oak font-mono text-center font-black">
                            </div>
                        </div>
                    </div>

                    <!-- 旅程封面圖設定 -->
                    <div class="mt-8 pt-6 border-t border-muji-edge">
                        <div class="flex justify-between items-center mb-4">
                            <label class="block text-sm font-bold text-muji-ash uppercase text-left">旅程封面圖設定</label>
                            @if($trip->cover_image)
                            <label class="flex items-center gap-2 text-xs text-red-400 cursor-pointer hover:text-red-600 transition-colors font-bold">
                                <input type="checkbox" name="restore_cover" value="1" class="rounded border-muji-edge text-red-500 focus:ring-red-500">
                                恢復預設
                            </label>
                            @endif
                        </div>
                        <div class="bg-muji-base/30 p-4 rounded-xl border border-muji-edge">
                            <input type="file" name="cover_image" accept="image/*" class="block w-full text-xs text-muji-ash file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-white file:text-muji-oak hover:file:bg-muji-base cursor-pointer transition-all">
                            <p class="text-[10px] text-muji-ash mt-2 italic shadow-muji-sm p-2 bg-white/50 rounded-lg">※ 支援 JPG、PNG，目前上限 2MB（因主機設定）。建議使用清爽的風景照。</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-10 pt-8 border-t border-muji-edge">
                        <button type="submit" class="px-6 py-4 bg-muji-oak text-white font-black rounded-2xl hover:opacity-90 transition-all shadow-muji active:scale-95">
                            儲存變更
                        </button>
                        @if(auth()->id() === $trip->user_id)
                        <button type="button" onclick="confirmDelete('刪除旅程？', '確定要刪除整個「{{ $trip->name }}」嗎？', 'delete-trip-form')" class="px-6 py-4 bg-white text-red-600 font-black rounded-2xl border border-red-200 hover:bg-red-50 transition-all flex items-center justify-center gap-2 active:scale-95 shadow-sm">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            刪除旅程
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
                <div class="mt-10 pt-10 border-t border-muji-edge">
                    <h4 class="text-xs font-black text-muji-oak uppercase tracking-[0.2em] flex items-center gap-2 mb-6 bg-muji-base/50 self-start px-3 py-1.5 rounded-lg border border-muji-edge">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        協作者管理
                    </h4>

                    <div class="space-y-3 mb-6">
                        @foreach($trip->collaborators as $collaborator)
                        <div class="flex justify-between items-center bg-muji-base/50 p-3 rounded-xl border border-muji-edge">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-muji-wheat/30 flex items-center justify-center text-muji-oak font-bold text-xs shadow-muji-sm">
                                    {{ strtoupper(substr($collaborator->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-muji-ink">{{ $collaborator->name }}</p>
                                    <p class="text-[10px] font-bold text-muji-ash uppercase tracking-wider">{{ $collaborator->email }}</p>
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
                        <label class="block text-sm font-bold text-muji-ash mb-2">邀請新協作者 (Email)</label>
                        <div class="flex gap-2">
                            <input type="email" name="email" required placeholder="例如：winnie@example.com" class="block w-full px-4 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium">
                            <button type="submit" class="px-8 py-3 bg-muji-oak text-white font-black rounded-2xl hover:opacity-90 transition-all shadow-muji active:scale-95 whitespace-nowrap">
                                加入
                            </button>
                        </div>
                        <p class="mt-2 text-[11px] text-muji-ash italic">※ 請確認對方已經在網站上註冊帳號。</p>
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
                basicsTitle: '航班基本資訊',
                notes: '行李規定',
                notesPlaceholder: '例如：23kg x 2',
                outbound: '去程', inbound: '回程',
                routeLabel: '航線', helpRoute: '請使用「起點機場 ➝ 終點機場」格式'
            },
            train: {
                modalTitle: '鐵路票務編輯',
                basicsTitle: '列車基本資訊',
                notes: '票位 / 備註', notesPlaceholder: '例如：自由席 / 5車12A',
                outbound: '出發 / 去程', inbound: '抵達 / 回程',
                routeLabel: '起訖站', helpRoute: '請使用「起點站 ➝ 終點站」格式'
            },
            bus: {
                modalTitle: '巴士行程編輯',
                basicsTitle: '巴士基本資訊',
                notes: '備註 / 候車處', notesPlaceholder: '例如：電子票證 / 3號月台',
                outbound: '發車 / 去程', inbound: '抵達 / 回程',
                routeLabel: '路線名稱', helpRoute: '請使用「起點站 ➝ 終點站」格式'
            },
            car: {
                modalTitle: '租車合約編輯',
                basicsTitle: '租賃基本資訊',
                notes: '租賃與停車說明', notesPlaceholder: '例如：含保險 / 飯店附停車',
                outbound: '取車地點與時間', inbound: '還車地點與時間',
                routeLabel: '地點', helpRoute: '請使用「地點」或「起點 ➝ 終點」格式'
            },
            ship: {
                modalTitle: '船期航務編輯',
                basicsTitle: '航務基本資訊',
                notes: '備註 / 艙位', notesPlaceholder: '例如：窗位 / 含餐',
                outbound: '啟航 Departure', inbound: '返航 / 回程 Return',
                routeLabel: '港口', helpRoute: '請使用「港口名稱」或「起訖港 ➝ 終點港」格式'
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
            if (elements.basicsTitle) elements.basicsTitle.innerText = config.basicsTitle || '基本資訊';
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
