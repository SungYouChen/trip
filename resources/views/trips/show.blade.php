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

@section('title', $trip->name . ' | 旅程計劃')
@section('header_title', '旅程計劃')

@section('content')
<div class="mb-12 relative max-w-4xl mx-auto group">
    <!-- Decorative Elements (Clipped to container to prevent mobile horizontal scroll) -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none -z-10 rounded-[40px]">
        <div class="absolute top-[130px] right-0 translate-x-1/2 translate-y-1/2 w-48 h-48 bg-muji-oak/5 rounded-full blur-3xl group-hover:bg-muji-oak/10 transition-colors duration-1000"></div>
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-muji-wheat/10 rounded-full blur-3xl group-hover:bg-muji-wheat/20 transition-colors duration-1000 animate-pulse"></div>
        <div class="absolute top-1/2 left-0 -translate-x-full -translate-y-1/2 w-32 h-32 bg-muji-wheat/5 rounded-full blur-2xl group-hover:bg-muji-wheat/10 transition-colors duration-1000"></div>
    </div>
    <!-- Header Block: Flex Container for Perfect Alignment -->
    <div class="flex items-start justify-between min-h-[100px]">
        <!-- Left Side Spacer (for centering) -->
        <div class="hidden sm:flex w-24"></div>

        <!-- Center: Title Content -->
        <div class="flex-1 text-center min-w-0">
            <h2 class="text-3xl sm:text-4xl font-black text-muji-ink leading-tight break-words">
                {{ $trip->name }}
            </h2>
            
            <p class="text-sm sm:text-md text-muji-ash italic font-medium mt-2">
                @if($trip->start_date && $trip->end_date)
                {{ \Carbon\Carbon::parse($trip->start_date)->format('Y/m/d') }} - {{ \Carbon\Carbon::parse($trip->end_date)->format('Y/m/d') }}
                @else
                日期未定
                @endif
            </p>
        </div>

        <!-- Multi-Action Menu -->
        <div class="relative shrink-0" id="tripActionsContainer">
            @if(!$isShared && auth()->check())
            <button onclick="toggleTripActions(event)" class="w-10 h-10 flex items-center justify-center text-muji-ash hover:text-muji-oak hover:bg-muji-base rounded-xl transition-all shadow-muji-sm bg-white/50 backdrop-blur-sm border border-muji-edge/50 active:scale-95 group/more" id="tripActionsBtn">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                </svg>
            </button>
            
            <div id="tripActionsMenu" class="absolute top-full right-0 mt-3 w-56 bg-white/95 backdrop-blur-md rounded-[24px] shadow-2xl border border-muji-edge/50 py-3 hidden origin-top-right z-[100] overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <!-- 1. Settings -->
                <button onclick="toggleTripActions(event); safeOpenModal('tripSettingsModal')" class="w-full flex items-center gap-3 px-5 py-3 text-left text-xs font-black text-muji-ash hover:text-muji-ink hover:bg-muji-base transition-all border-0 bg-transparent cursor-pointer group/item">
                    <div class="p-1 text-muji-ash group-hover/item:text-muji-oak transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <span>編輯旅程設定</span>
                </button>

                <!-- 2. Map -->
                <button onclick="toggleTripActions(event); openMapViewModal()" class="w-full flex items-center gap-3 px-5 py-3 text-left text-xs font-black text-muji-ash hover:text-muji-ink hover:bg-muji-base transition-all border-0 bg-transparent cursor-pointer group/item">
                    <div class="p-1 text-muji-ash group-hover/item:text-muji-oak transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <span>查看完整地圖規劃</span>
                </button>

                <div class="mx-5 my-1 border-t border-muji-edge/30"></div>

                <!-- 3. Toggle Sharing -->
                <form action="{{ route('trip.toggle_share', ['user' => $trip->user, 'trip' => $trip]) }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-5 py-3 text-left text-xs font-black group/item transition-all border-0 bg-transparent cursor-pointer {{ $trip->is_public ? 'text-muji-oak bg-muji-wheat/5 hover:bg-muji-base' : 'text-muji-ash hover:text-muji-ink hover:bg-muji-base' }}">
                        <div class="p-1 {{ $trip->is_public ? 'text-muji-oak' : 'text-muji-ash group-hover/item:text-muji-oak' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                        </div>
                        <span>{{ $trip->is_public ? '切換為私有模式' : '開啟公開分享' }}</span>
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <!-- Share Link Box (Balanced below title) -->
    @if(!$isShared && $trip->is_public)
    <div class="bg-muji-base/80 backdrop-blur-sm p-1.5 rounded-xl flex items-center justify-between gap-3 border border-muji-edge w-full max-w-md mx-auto overflow-hidden shadow-muji-sm mt-4 sm:mt-0">
        <span id="shareLink" class="text-[10px] text-muji-oak font-mono truncate flex-1 font-black pl-3 tracking-wider">{{ route('trip.index_shared', ['token' => $trip->share_token]) }}</span>
        <button onclick="copyShareLink()" class="bg-muji-oak text-white w-[36px] h-[36px] flex items-center justify-center rounded-xl hover:opacity-80 transition-all font-black active:scale-95 shadow-muji-sm flex-shrink-0 tooltip tooltip-left" data-tooltip="複製分享連結">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
            </svg>
        </button>
    </div>
    <script>
        function copyShareLink() {
            const link = document.getElementById('shareLink').innerText;
            navigator.clipboard.writeText(link).then(() => {
                showToast('連結已複製！', 'success');
            });
        }
    </script>
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
<div id="transportCard" class="relative muji-card shadow-muji border-muji-edge mb-4 group/transport zoom-in-on-load ticket-masked overflow-hidden w-full max-w-full">
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
            @if(!$isShared)
            @auth
            <button onclick="event.stopPropagation(); openFlightEditModal()" class="w-10 h-10 flex items-center justify-center {{ $theme['bg_light'] }} {{ $theme['text'] }} rounded-xl border {{ $theme['border'] }} hover:shadow-lg transition-all active:scale-95 tooltip tooltip-left" data-tooltip="編輯交通資訊">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
            </button>
            @endauth
            @endif
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
                <div class="p-8 group/item hover:bg-muji-base/40 transition-all duration-300">
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
                <div class="p-8 group/item hover:bg-muji-paper/40 transition-all duration-300">
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
    <div onclick="toggleTransportDetails()" class="relative px-8 py-4 bg-muji-base/30 flex justify-between items-center border-t border-muji-edge cursor-pointer hover:bg-muji-wheat/10 transition-colors font-black">
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 px-4 h-[46px] bg-muji-paper rounded-xl shadow-muji-sm border border-muji-edge">
                <svg class="w-4 h-4 {{ $theme['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span class="text-xs font-black text-muji-ash truncate max-w-[200px] sm:max-w-md">
                    {{ $transportType == 'car' ? '租車備註：' : '行李/備註：' }}{{ $flightInfo['baggage'] ?: '無特別備註' }}
                </span>
            </div>
        </div>
    </div>
</div>
@elseif(!$isShared)
@auth
<!-- Placeholder for no flight info -->
<div class="mb-4 p-8 border-2 border-dashed border-muji-edge rounded-3xl flex flex-col items-center justify-center text-muji-ash bg-muji-base/30 hover:border-muji-oak hover:text-muji-oak transition-all group" onclick="openFlightEditModal()" style="cursor: pointer;">
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
<div class="flex items-center justify-between mb-4 w-full min-w-0">
    <p class="text-sm text-muji-ash font-medium">
        @php $activeDaysCount = $itinerary->filter(fn($d) => !$d->trashed())->count(); @endphp
        共 {{ $activeDaysCount }} 天
    </p>
    @if(!$isShared)
    @auth
    <a href="{{ request()->fullUrlWithQuery(['archived' => $showArchived ? '0' : '1']) }}" 
       class="flex items-center justify-center w-[36px] h-[36px] rounded-full border border-muji-edge transition-all tooltip tooltip-left {{ $showArchived ? 'bg-muji-oak text-white shadow-muji-oak/20' : 'bg-muji-base text-muji-ash hover:bg-muji-wheat/20' }}"
       data-tooltip="{{ $showArchived ? '隱藏封存項目' : '查看封存項目' }}">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12M10 12v4m4-4v4" />
        </svg>
    </a>
    @endauth
    @endif
</div>

<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 w-full max-w-full">
    @php
    $daysToShow = $showArchived
    ? $itinerary->filter(fn($d) => $d->trashed())
    : $itinerary->filter(fn($d) => !$d->trashed());
    @endphp
    @php $lastLoc = $trip->name; @endphp
    @foreach($daysToShow as $day)
    @php
    $dayDate = $day->date ? \Carbon\Carbon::parse($day->date) : null;
    $isToday = $dayDate ? $dayDate->isToday() : false;
    $isArchived = $day->trashed();
    $dateParam = $dayDate ? $dayDate->format('n-j') : 'day-' . $day->day_number;
    $cardLink = (!$isArchived && $isShared)
    ? route('day.show_shared', ['token' => $trip->share_token, 'date' => $dateParam])
    : ((!$isArchived && !$isShared) ? route('day.show', ['user' => $trip->user, 'trip' => $trip, 'date' => $dateParam]) : null);
    @endphp 
        <div class="relative group min-w-0">
            <a href="{{ $cardLink }}" class="flex flex-col h-52 muji-card shadow-muji border-muji-edge hover:shadow-muji transition-all duration-300 transform hover:-translate-y-1 overflow-hidden {{ $isArchived ? 'border-2 border-dashed border-muji-ash grayscale opacity-60' : ($isToday ? 'bg-muji-wheat/10 ring-1 ring-muji-oak' : '') }}">
                <div class="p-6 flex-1 overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[10px] font-bold bg-muji-base text-muji-oak border border-muji-edge">
                            @if($dayDate)
                            {{ $dayDate->format('n/j') }} ({{ $dayDate->locale('zh_TW')->dayName }})
                            @else
                            Day {{ $day->day_number }}
                            @endif
                        </span>
                    </div>

                    <h3 class="text-xl font-black text-muji-ink mb-1 group-hover:text-muji-oak transition-colors truncate">
                        {{ $day->title ?: ($day->summary ?: 'Day ' . $loop->iteration) }}
                    </h3>
                    @php 
                        if ($day->location) { $lastLoc = $day->location; }
                        $loc = $lastLoc;
                    @endphp
                    <div class="flex items-center flex-wrap gap-2 text-[10px] font-bold text-muji-oak mb-2 uppercase tracking-widest min-w-0">
                        <div class="flex items-center gap-1 truncate max-w-[100px]">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate">{{ $loc }}</span>
                        </div>
                        @if($dayDate && $dayDate->isBetween(now()->subDays(1), now()->addDays(15)))
                        <div class="weather-indicator tooltip-bottom inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-[10px] font-black bg-muji-base border border-muji-edge shadow-muji-sm" 
                            data-date="{{ $dayDate->format('Y-m-d') }}" 
                            data-location="{{ $loc }}"
                            data-tooltip="氣象同步中..">
                            <div class="weather-icon flex items-center justify-center min-w-[12px]"><span class="animate-pulse">◌</span></div>
                            <span class="weather-temp font-black text-muji-oak">-- / --°C</span>
                        </div>
                        @endif
                    </div>

                    @if($day->title && $day->summary && $day->title !== $day->summary)
                    <p class="text-xs text-muji-ash line-clamp-2">
                        {{ $day->summary }}
                    </p>
                    @elseif(!$day->title && $day->summary)
                    {{-- Summary is already used as title above, don't repeat here --}}
                    @else
                    <p class="text-xs text-muji-ash line-clamp-2">
                        {{ $day->summary }}
                    </p>
                    @endif
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
                    <button type="submit" class="p-2 bg-green-50 text-green-600 rounded-xl hover:bg-green-100 shadow-sm transition-colors" data-tooltip="還原此天行程">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </form>
                <form id="{{ $forceId }}" action="{{ route('day.forceDelete', ['user' => $trip->user, 'trip' => $trip, 'dayId' => $day->id]) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="button" onclick="confirmDelete('永久刪除？', '此操作無法復原！', '{{ $forceId }}')" class="p-2 bg-red-100 text-red-600 rounded-xl hover:bg-red-200 shadow-sm transition-colors" data-tooltip="永久刪除此天">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            </div>
            @else
            {{-- Active: Soft delete (archive) --}}
            @php $formId = 'delete-day-' . $day->id; @endphp
            <form id="{{ $formId }}" action="{{ route('day.destroy', ['user' => $trip->user, 'trip' => $trip, 'date' => $dateParam]) }}" method="POST" class="absolute top-4 right-4 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                @csrf @method('DELETE')
                <button type="button" onclick="confirmDelete('封存此天行程？', '此天行程將被封存，可於「查看封存」中還原。', '{{ $formId }}')" class="p-2 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 hover:text-red-600 shadow-sm transition-colors" data-tooltip="封存這一天">
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
    <form action="{{ route('trip.add_day', ['user' => $trip->user, 'trip' => $trip]) }}" 
          method="POST" 
          class="h-full"
          onsubmit="handleAjaxSubmit(event, this, null)">
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

<!-- Checklists Section (Standardized space-y-8 / gap-8) -->
<div class="grid md:grid-cols-2 gap-6 mt-6">
    <!-- Must Buy List (p-8) -->
    <div class="muji-card shadow-muji p-8 relative flex flex-col">
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
            $allCategoryItems = $trip->checklistItems()->withTrashed()->where('type', 'shopping')->where('category', $category)->orderBy('sort_order')->get();
            $items = $showArchived
            ? $allCategoryItems->filter(fn($i) => $i->trashed())
            : $allCategoryItems->filter(fn($i) => !$i->trashed());
            @endphp
            @if($items->count() > 0)
            <div class="animate-in fade-in slide-in-from-left-4 duration-300">
                <h4 class="font-black text-muji-ink text-sm mb-2 border-l-4 border-muji-wheat pl-2 uppercase tracking-wider">{{ $category }}</h4>
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 sortable-list" data-type="shopping" data-category="{{ $category }}">
                    @foreach($items as $item)
                    @php $isItemArchived = $item->trashed(); @endphp
                    <li data-id="{{ $item->id }}" class="flex items-start justify-between gap-2 text-sm text-muji-ash group {{ $isItemArchived ? 'border border-dashed border-red-200 rounded p-1 bg-red-50/20 grayscale opacity-60' : '' }}">
                        <div class="flex items-start gap-2">
                            @if(!$isItemArchived)
                            <div class="drag-handle cursor-grab active:cursor-grabbing opacity-0 group-hover:opacity-100 transition-opacity mt-1 text-muji-ash/20 hover:text-muji-oak">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 8h16M4 16h16" /></svg>
                            </div>
                            @endif
                            @if($isItemArchived)
                            <span class="text-red-400 mt-0.5"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg></span>
                            @else
                            <input type="checkbox" 
                                   class="mt-1 muji-checkbox sync-chk" 
                                   onchange="toggleChecklistItem(this, '{{ route('checklist.toggle', ['user' => $trip->user, 'trip' => $trip, 'id' => $item->id]) }}')"
                                   @if($item->is_completed) checked @endif
                                   @if(!auth()->check() || $isShared) disabled @endif>
                            @endif
                            <span class="{{ (!auth()->check() || $isShared) ? 'text-muji-ash/50' : '' }} {{ $isItemArchived ? 'text-red-800 font-bold' : '' }} {{ $item->is_completed ? 'line-through opacity-40' : '' }}">{{ $item->name }}</span>
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
                                <button type="button" class="text-red-600 hover:text-red-800 p-0.5" onclick="confirmDelete('永久刪除項目？', '此動作無法復原！確定要永久刪除「{{ $item->name }}」嗎？', '{{ $forceChkId }}')" data-tooltip="刪除">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            @else
                            @php $chkFormId = 'del-chk-' . $item->id; @endphp
                            <form id="{{ $chkFormId }}" action="{{ route('checklist.destroy', ['user' => $trip->user, 'trip' => $trip, 'id' => $item->id]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="button" class="text-red-400 hover:text-red-600 p-0.5" onclick="confirmDelete('封存清單項目？', '確定要將「{{ $item->name }}」移至回收桶嗎？', '{{ $chkFormId }}')" data-tooltip="刪除">
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
            <form action="{{ route('checklist.store', ['user' => $trip->user, 'trip' => $trip]) }}" 
                  method="POST" 
                  class="mt-4 pt-4 border-t border-muji-edge"
                  onsubmit="handleAjaxSubmit(event, this, null)">
                @csrf
                <input type="hidden" name="type" value="shopping">
                <div class="flex gap-2 items-stretch h-[46px]">
                    <input type="text" name="category" placeholder="例如：藥妝" class="w-1/3 h-[46px] px-4 muji-input" required list="shop_categories" autocomplete="off">
                    <div class="hidden">
                        <datalist id="shop_categories">
                            @foreach($shoppingCategories as $cat)
                            <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                    </div>
                    <input type="text" name="name" placeholder="例如：合利他命" class="w-2/3 h-[46px] px-4 muji-input" required>
                    <button type="submit" class="bg-muji-oak text-white w-[46px] flex items-center justify-center rounded-xl hover:opacity-90 active:scale-95 transition-all shadow-muji-sm flex-shrink-0">
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
    <div class="muji-card shadow-muji p-8 relative flex flex-col">
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
                $allSpotItems = $trip->checklistItems()->withTrashed()->where('type', 'spot')->where('category', $category)->orderBy('sort_order')->get();
                $items = $showArchived
                ? $allSpotItems->filter(fn($i) => $i->trashed())
                : $allSpotItems->filter(fn($i) => !$i->trashed());
                @endphp
                @if($items->count() > 0)
                <div class="animate-in fade-in slide-in-from-right-4 duration-300">
                    <h4 class="font-black text-muji-ink text-sm mb-2 border-l-4 border-muji-wheat pl-2 uppercase tracking-wider">{{ $category }}</h4>
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 sortable-list" data-type="spot" data-category="{{ $category }}">
                    @foreach($items as $item)
                    @php $isItemArchived = $item->trashed(); @endphp
                    <li data-id="{{ $item->id }}" class="flex items-start justify-between gap-2 text-sm text-muji-ash group {{ $isItemArchived ? 'border border-dashed border-red-200 rounded p-1 bg-red-50/20 grayscale opacity-60' : '' }}">
                        <div class="flex items-start gap-2">
                            @if(!$isItemArchived)
                            <div class="drag-handle cursor-grab active:cursor-grabbing opacity-0 group-hover:opacity-100 transition-opacity mt-1 text-muji-ash/20 hover:text-muji-oak">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 8h16M4 16h16" /></svg>
                            </div>
                            @endif
                                @if($isItemArchived)
                                    <span class="text-red-400 mt-0.5"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></span>
                                @else
                                    <input type="checkbox" 
                                           class="mt-1 muji-checkbox sync-chk" 
                                           onchange="toggleChecklistItem(this, '{{ route('checklist.toggle', ['user' => $trip->user, 'trip' => $trip, 'id' => $item->id]) }}')"
                                           @if($item->is_completed) checked @endif
                                           @if(!auth()->check() || $isShared) disabled @endif>
                                @endif
                                <span class="font-bold {{ (!auth()->check() || $isShared) ? 'text-muji-ash/50' : 'text-muji-ink' }} {{ $isItemArchived ? 'text-red-800' : '' }} {{ $item->is_completed ? 'line-through opacity-40' : '' }}">{{ $item->name }}</span>
                            </div>
                            @if(!$isShared && auth()->check())
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @if($isItemArchived)
                                        <button type="button" class="text-green-500 hover:text-green-700 p-0.5" onclick="confirmAction('還原？', '將「{{ $item->name }}」移回想去景點嗎？', 'restore-chk-go-{{ $item->id }}')">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                                        </button>
                                        <form id="restore-chk-go-{{ $item->id }}" action="{{ route('checklist.restore', ['user' => $trip->user, 'trip' => $trip, 'id' => $item->id]) }}" method="POST" class="hidden">@csrf @method('PATCH')</form>
                                        
                                        <button type="button" class="text-red-600 hover:text-red-800 p-0.5" onclick="confirmDelete('永久刪除？', '此動作無法復原！確定要永久刪除「{{ $item->name }}」嗎？', 'force-chk-go-{{ $item->id }}')">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                        <form id="force-chk-go-{{ $item->id }}" action="{{ route('checklist.forceDelete', ['user' => $trip->user, 'trip' => $trip, 'id' => $item->id]) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                                    @else
                                        <button type="button" 
                                                onclick="showAssignSwal('{{ route('checklist.assign', ['user' => $trip->user, 'trip' => $trip, 'id' => $item->id]) }}', '{{ $item->name }}')"
                                                class="text-muji-oak hover:opacity-70 p-0.5"
                                                data-tooltip="指派到行程日">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                        <button type="button" class="text-red-400 hover:text-red-600 p-0.5" onclick="confirmDelete('封存？', '將「{{ $item->name }}」移至回收桶嗎？', 'del-chk-go-{{ $item->id }}')" data-tooltip="刪除">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                        <form id="del-chk-go-{{ $item->id }}" action="{{ route('checklist.destroy', ['user' => $trip->user, 'trip' => $trip, 'id' => $item->id]) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                                    @endif
                                </div>
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
            <form action="{{ route('checklist.store', ['user' => $trip->user, 'trip' => $trip]) }}" 
                  method="POST" 
                  class="mt-8 pt-6 border-t border-muji-edge"
                  onsubmit="handleAjaxSubmit(event, this, null)">
                @csrf
                <input type="hidden" name="type" value="spot">
                <div class="flex gap-2 items-stretch h-[46px]">
                    <input type="text" name="category" placeholder="例如：河口湖" class="w-1/3 h-[46px] px-4 muji-input" required list="spot_categories" autocomplete="off">
                    <div class="hidden">
                        <datalist id="spot_categories">
                            @foreach($spotCategories as $cat) <option value="{{ $cat }}"> @endforeach
                        </datalist>
                    </div>
                    <input type="text" name="name" placeholder="例如：新倉山淺間公園" class="w-2/3 h-[46px] px-4 muji-input" required>
                    <button type="submit" class="bg-muji-oak text-white w-[46px] flex items-center justify-center rounded-xl hover:opacity-90 active:scale-95 transition-all shadow-muji-sm flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                    </button>
                </div>
            </form>
            @endauth
            @endif
        </div>
    </div>

    <!-- 旅程交流牆 (Integrated into the same grid div) -->
    <div class="md:col-span-2 animate-in fade-in slide-in-from-bottom-4 duration-700 w-full">
        <div class="muji-card p-4 sm:p-6 border border-muji-edge shadow-muji shadow-muji-oak/5 rounded-[32px] w-full">
            <div class="mb-6 w-full">
                <h3 class="text-xl font-black text-muji-ink flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-muji-wheat/30 flex items-center justify-center text-muji-oak shadow-muji-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                    </span>
                    旅程交流牆
                </h3>
            </div>
            <!-- 留言列表 -->
            <div class="space-y-6 px-2">
                @forelse($globalComments as $comment)
                <div class="flex items-start gap-4 group/comment relative animate-in fade-in slide-in-from-left-2 duration-300">
                    <div class="w-10 h-10 rounded-2xl bg-muji-wheat/20 flex-shrink-0 flex items-center justify-center text-xs font-black text-muji-oak border border-muji-edge shadow-sm">
                        {{ mb_substr($comment['user_name'], 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-baseline justify-between mb-1.5">
                            <div class="flex items-baseline gap-3">
                                <span class="text-sm font-black text-muji-ink">{{ $comment['user_name'] }}</span>
                                <span class="text-[10px] text-muji-ash/40 font-bold uppercase tracking-tighter">{{ $comment['time'] }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if(auth()->check() && auth()->id() === $trip->user_id)
                                <div class="relative">
                                    <button onclick="event.stopPropagation(); this.nextElementSibling.classList.toggle('hidden')" class="text-muji-ash hover:text-muji-oak p-1 transition-all tooltip-bottom" data-tooltip="轉入清單">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                    <div class="absolute right-0 top-full hidden bg-muji-paper border border-muji-edge shadow-2xl rounded-2xl p-1 z-[200] w-32 animate-in fade-in zoom-in-95 duration-200" onclick="event.stopPropagation()">
                                        <button onclick="this.parentElement.classList.add('hidden'); convertComment('{{ addslashes($comment['content']) }}', 'spot')" class="w-full text-left px-3 py-2 text-[10px] font-black text-muji-ink hover:bg-muji-base hover:text-muji-oak rounded-xl transition-all flex items-center gap-2">
                                            📍 轉入景點
                                        </button>
                                        <button onclick="this.parentElement.classList.add('hidden'); convertComment('{{ addslashes($comment['content']) }}', 'shopping')" class="w-full text-left px-3 py-2 text-[10px] font-black text-muji-ink hover:bg-muji-base hover:text-muji-oak rounded-xl transition-all flex items-center gap-2">
                                            🛒 轉入必買
                                        </button>
                                    </div>
                                </div>
                                @endif

                                @if($comment['can_delete'])
                                    @php $commDelId = 'del-global-comm-' . $comment['id']; @endphp
                                    <form id="{{ $commDelId }}" action="{{ route('trip.comment.destroy', ['id' => $comment['id']]) }}" method="POST" class="opacity-0 group-hover/comment:opacity-100 transition-opacity">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('刪除此則留言？', '此動作無法復原！確定要永久刪除嗎？', '{{ $commDelId }}')" class="text-red-400 hover:text-red-600 transition-colors tooltip-bottom" data-tooltip="刪除留言">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div class="bg-muji-paper p-4 rounded-3xl rounded-tl-none border border-muji-edge/60 text-sm text-muji-ink leading-relaxed shadow-muji-sm hover:shadow-muji transition-all duration-300">
                            @php
                                $content = e($comment['content']);
                                // Improved regex to handle capsules better
                                $content = preg_replace_callback('/(https?:\/\/[^\s]+)/', function($m) {
                                    $url = $m[1];
                                    $isMaps = str_contains($url, 'google.com/maps') || str_contains($url, 'goo.gl/maps');
                                    $label = $isMaps ? '📍 Google Maps' : '🔗 連結';
                                    $style = $isMaps ? 'bg-muji-wheat/30 text-muji-oak border-muji-oak/30' : 'bg-muji-base text-muji-ash border-muji-edge';
                                    // Use w-fit and inline-flex to ensure it doesn't stretch
                                    return '<a href="'.$url.'" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 my-1 rounded-full border '.$style.' transition-all hover:scale-105 active:scale-95 text-[11px] font-black shadow-sm no-underline w-fit">'.$label.'</a>';
                                }, $content);
                            @endphp
                            <div class="whitespace-pre-wrap">{!! $content !!}</div>
                        </div>
                    </div>
                @empty
                <div class="py-16 flex flex-col items-center justify-center opacity-30 select-none">
                    <svg class="w-16 h-16 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="text-[10px] font-black tracking-widest uppercase">大家都很安靜呢，來聊聊細節吧！</p>
                </div>
                @endforelse
            </div>

            <!-- 留言表單 -->
            <form action="{{ $isShared ? route('trip.comment.store_shared', ['token' => $trip->share_token]) : route('trip.comment.store', ['user' => $trip->user, 'trip' => $trip]) }}" 
                  method="POST" 
                  class="relative mt-8 pt-8 border-t border-muji-edge/40"
                  onsubmit="handleAjaxSubmit(event, this, null)">
                @csrf
                @if(!auth()->check() || $isShared)
                <div class="mb-4">
                    <label class="block text-[10px] font-black text-muji-ash uppercase tracking-widest mb-1.5 ml-1">留言暱稱</label>
                    <input type="text" name="user_name" required placeholder="您的暱稱" class="w-full sm:w-1/3 bg-muji-paper border-muji-edge rounded-xl text-sm px-4 py-2.5 focus:ring-muji-oak focus:border-muji-oak shadow-sm transition-all h-[46px]">
                </div>
                @endif
                <div class="flex gap-3 items-end">
                    <div class="flex-1">
                        <label class="block text-[10px] font-black text-muji-ash uppercase tracking-widest mb-1.5 ml-1">發言內容</label>
                        <textarea name="content" required rows="3" placeholder="想對大家說什麼？或是貼上好站連結..." class="w-full bg-muji-paper border-muji-edge rounded-[24px] text-sm p-4 focus:ring-muji-oak focus:border-muji-oak transition-all resize-none shadow-sm"></textarea>
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <button type="submit" class="px-8 h-[50px] bg-muji-oak text-white rounded-full flex items-center justify-center gap-2 hover:opacity-90 active:scale-95 transition-all shadow-muji font-black text-sm tracking-widest">
                        發送留言
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('modals')
    <!-- --- CONVERSION MODAL (1:1 SweetAlert Mirror) --- -->
    <div id="convertCommentModal" class="fixed inset-0 z-[10000] hidden flex items-center justify-center bg-black/50 backdrop-blur-[6px] p-4 text-center">
        <div class="muji-card w-full max-w-[420px] rounded-[32px] p-10 shadow-2xl transform transition-all border border-muji-edge/20 text-center" style="background-color: #f8f5f0;">
            <!-- Iconic Top (Swal Style) -->
            <div class="flex justify-center mb-6">
                <div id="modalIconContainer" class="w-20 h-20 rounded-full border-4 border-muji-oak/20 flex items-center justify-center text-4xl bg-muji-oak/5">
                    <span id="modalIcon">📍</span>
                </div>
            </div>

            <h3 id="convertModalTitle" class="text-2xl font-black text-[#333333] mb-8 tracking-tighter">轉入清單</h3>
            
            <!-- Content Container -->
            <div class="space-y-6 mb-10 text-left">
                <div>
                    <label class="block text-[10px] font-black text-muji-ash uppercase tracking-widest mb-3 ml-1">選擇分類</label>
                    <input id="convertModalInput" 
                           type="text"
                           class="w-full h-[54px] px-6 muji-input text-sm border-2 border-muji-edge focus:border-muji-oak transition-all shadow-sm bg-white/50" 
                           placeholder="搜尋或輸入標籤..."
                           autocomplete="off"
                           onkeydown="if(event.key==='Enter') submitConvertModal()"
                           list="modal_all_categories">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-muji-ash uppercase tracking-widest mb-3 ml-1">內容修改</label>
                    <textarea id="convertModalContent" 
                              class="w-full min-h-[110px] p-5 muji-input text-sm border-2 border-muji-edge focus:border-muji-oak transition-all shadow-sm resize-none leading-relaxed bg-white/50" 
                              placeholder="微調一下文字..."></textarea>
                </div>
                
                <div class="hidden">
                    <datalist id="modal_spot_list">
                        @foreach($spotCategories as $sc) <option value="{{ $sc }}"> @endforeach
                    </datalist>
                    <datalist id="modal_shopping_list">
                        @foreach($shoppingCategories as $sc) <option value="{{ $sc }}"> @endforeach
                    </datalist>
                    <datalist id="modal_all_categories">
                        @foreach(array_unique(array_merge($spotCategories, $shoppingCategories)) as $sc) <option value="{{ $sc }}"> @endforeach
                    </datalist>
                </div>
            </div>

            <!-- Swal Standard Actions -->
            <div class="flex justify-center gap-4">
                <button onclick="submitConvertModal()" class="px-10 py-3.5 text-white text-[10px] font-black rounded-xl shadow-lg hover:opacity-90 active:scale-95 transition-all uppercase tracking-widest" style="background-color: #9c8c7c;">
                    確認轉入
                </button>
                <button onclick="safeCloseModal('convertCommentModal')" class="px-10 py-3.5 text-muji-ash text-[10px] font-black rounded-xl border border-muji-edge hover:bg-white transition-all active:scale-95 uppercase tracking-widest" style="background-color: #dcd3c1;">
                    取消
                </button>
            </div>
        </div>
    </div>
@if(!$isShared)
@auth
<!-- Flight Edit Modal -->
<div id="tripTransportModal" class="fixed inset-0 z-[2000]" style="display: none;" role="dialog" aria-modal="true">
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity z-0" onclick="safeCloseModal('tripTransportModal')"></div>
        <div class="relative z-10 transform overflow-hidden rounded-[40px] muji-glass text-left shadow-2xl transition-all w-full max-w-lg flex flex-col max-h-[calc(100vh-160px)]">
            <!-- 統一右上角關閉按鈕 (X) - 移出捲軸容器 -->
            <button onclick="safeCloseModal('tripTransportModal')" class="absolute top-6 right-6 text-muji-ash hover:text-muji-oak p-2 rounded-full hover:bg-muji-base transition-all group z-50">
                <svg class="w-6 h-6 group-hover:rotate-90 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="px-5 sm:px-8 py-8 sm:py-10 overflow-y-auto custom-scrollbar scroll-smooth">
                <div class="flex items-center gap-4 mb-8 sm:mb-10 text-left">
                    <div class="p-3 bg-muji-base rounded-2xl text-muji-oak shadow-muji-sm">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                    </div>
                    <div class="flex flex-col border-l-2 border-muji-edge pl-4">
                        <h3 id="modal-transport-title" class="text-2xl font-black text-muji-ink leading-tight">編輯交通資訊</h3>
                        <p class="text-[10px] font-bold text-muji-ash uppercase tracking-[0.2em] mt-1">規劃您的旅程交通</p>
                    </div>
                </div>

                <form action="{{ route('trip.flight.update', ['user' => $trip->user, 'trip' => $trip]) }}" 
                      method="POST" 
                      class="space-y-8"
                      onsubmit="handleAjaxSubmit(event, this, 'tripTransportModal')">
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
                                <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">航空公司 / 航班編號</label>
                                <input type="text" name="airline" value="{{ $flightInfo['airline'] ?? '' }}" class="w-full px-4 py-3 muji-input" placeholder="例如：星宇航空 JX800">
                            </div>
                        </div>

                        <!-- Train Specific Fields -->
                        <div id="fields-train" class="mode-fields {{ ($transportType ?? '') == 'train' ? '' : 'hidden' }} grid grid-cols-1 gap-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">列車名稱 / 車次</label>
                                    <input type="text" name="train_no" value="{{ $flightInfo['train_no'] ?? '' }}" class="w-full px-4 py-3 muji-input" placeholder="例如：JR 新幹線 希望號">
                                </div>
                                <div>
                                    <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">座位資訊</label>
                                    <input type="text" name="train_seat" value="{{ $flightInfo['train_seat'] ?? '' }}" class="w-full px-4 py-3 muji-input" placeholder="例如：5車 12A">
                                </div>
                            </div>
                        </div>

                        <!-- Car Specific Fields -->
                        <div id="fields-car" class="mode-fields {{ ($transportType ?? '') == 'car' ? '' : 'hidden' }} grid grid-cols-1 gap-6">
                            <div>
                                <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">車型 / 租賃公司</label>
                                <input type="text" name="car_model" value="{{ $flightInfo['car_model'] ?? '' }}" class="w-full h-[46px] px-4 muji-input" placeholder="例如：Toyota Yaris / Times Car Rental">
                            </div>
                        </div>

                        <!-- Bus Specific Fields -->
                        <div id="fields-bus" class="mode-fields {{ ($transportType ?? '') == 'bus' ? '' : 'hidden' }} grid grid-cols-1 gap-6">
                            <div>
                                <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">客運公司 / 路線名稱</label>
                                <input type="text" name="airline" value="{{ $flightInfo['airline'] ?? '' }}" class="w-full h-[46px] px-4 muji-input" placeholder="例如：國光客運 / 京都市巴士 205路">
                            </div>
                        </div>

                        <!-- Ship Specific Fields -->
                        <div id="fields-ship" class="mode-fields {{ ($transportType ?? '') == 'ship' ? '' : 'hidden' }} grid grid-cols-1 gap-6">
                            <div>
                                <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">船名 / 航次名稱</label>
                                <input type="text" name="airline" value="{{ $flightInfo['airline'] ?? '' }}" class="w-full h-[46px] px-4 muji-input" placeholder="例如：麗星郵輪 / 櫻島渡輪">
                            </div>
                        </div>

                        <!-- Shared: Price & Notes (Always Visible but Labeled) -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">估計總費用</label>
                                <div class="flex relative rounded-xl border border-muji-edge overflow-hidden focus-within:ring-2 focus-within:ring-muji-oak bg-muji-paper group transition-all">
                                    <select name="flight_currency" class="bg-muji-base/30 border-0 border-r border-muji-edge px-3 py-3 text-muji-ink font-black text-xs focus:ring-0 cursor-pointer appearance-none">
                                        @php
                                        $fPrice = $flightInfo['price'] ?? '';
                                        preg_match('/^([^\d]+)?([\d,.]+)/u', $fPrice, $fm);
                                        $fsCurrency = trim($fm[1] ?? $trip->base_currency);
                                        $fsNum = isset($fm[2]) ? str_replace(',', '', $fm[2]) : '';
                                        @endphp
                                        <option value="{{ $trip->base_currency }}" {{ $fsCurrency==$trip->base_currency ? 'selected' : '' }}>{{ $trip->base_currency }}</option>
                                        <option value="{{ $trip->target_currency }}" {{ $fsCurrency==$trip->target_currency ? 'selected' : '' }}>{{ $trip->target_currency }}</option>
                                    </select>
                                    <input type="number" step="0.01" name="flight_price_num" value="{{ $fsNum }}" class="flex-1 w-full h-[46px] border-0 bg-transparent focus:ring-0 px-4 font-mono text-muji-ink font-black" placeholder="例如：25000">
                                </div>
                            </div>
                            <div>
                                <label id="mode-label-baggage" class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">行李 / 備註規定</label>
                                <input type="text" name="baggage" id="mode-input-baggage" value="{{ $flightInfo['baggage'] }}" class="w-full px-4 py-3 muji-input" placeholder="例如：23kg x 2">
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
                                    <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">日期</label>
                                @php
                                    // 深度解析：防範 JSON 結構偏移
                                    $info = $flightInfo['flight_info'] ?? $flightInfo; 
                                    $valOut = $info['outbound']['date'] ?? '';
                                    if($valOut && str_contains($valOut, '-')) {
                                        try { $valOut = \Carbon\Carbon::parse($valOut)->format('Y-m-d'); } catch(\Exception $e){}
                                    }
                                @endphp
                                <input type="date" name="outbound_date" value="{{ $valOut }}" class="w-full h-[46px] px-4 muji-input text-sm py-0 leading-none">
                                </div>
                                <div>
                                    <label id="mode-label-route" class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">路線</label>
                                    <input type="text" name="outbound_route" value="{{ $flightInfo['outbound']['route'] }}" class="w-full h-[46px] px-4 muji-input" placeholder="例如：TPE ➝ UKB">
                                </div>
                            </div>
                            <div>
                                <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">出發 ／ 抵達時間</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[9px] font-black text-muji-ash uppercase">出發</span>
                                        <input type="time" name="outbound_time_start" value="{{ $info['outbound']['time_start'] ?? '' }}" class="w-full h-[46px] pl-10 pr-4 muji-input font-mono text-sm">
                                    </div>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[9px] font-black text-muji-ash uppercase">抵達</span>
                                        <input type="time" name="outbound_time_end" value="{{ $info['outbound']['time_end'] ?? '' }}" class="w-full h-[46px] pl-10 pr-4 muji-input font-mono text-sm">
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
                                    <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">日期</label>
                                @php
                                    $valIn = $flightInfo['inbound']['date'] ?? '';
                                    if($valIn && str_contains($valIn, '-')) {
                                        try { $valIn = \Carbon\Carbon::parse($valIn)->format('Y-m-d'); } catch(\Exception $e){}
                                    }
                                @endphp
                                <input type="date" name="inbound_date" value="{{ $valIn }}" class="w-full h-[46px] px-4 muji-input text-sm py-0 leading-none">
                                </div>
                                <div>
                                    <label id="mode-label-route-in" class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">路線</label>
                                    <input type="text" name="inbound_route" value="{{ $flightInfo['inbound']['route'] }}" class="w-full h-[46px] px-4 muji-input" placeholder="例如：KIX ➝ TPE">
                                </div>
                            </div>
                            <div>
                                <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">出發 ／ 抵達時間</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[9px] font-black text-muji-ash uppercase">出發</span>
                                        <input type="time" name="inbound_time_start" value="{{ $info['inbound']['time_start'] ?? '' }}" class="w-full h-[46px] pl-10 pr-4 muji-input font-mono text-sm">
                                    </div>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[9px] font-black text-muji-ash uppercase">抵達</span>
                                        <input type="time" name="inbound_time_end" value="{{ $info['inbound']['time_end'] ?? '' }}" class="w-full h-[46px] pl-10 pr-4 muji-input font-mono text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-8 mt-8 mb-4 border-t border-muji-edge">
                        <button type="button" onclick="safeCloseModal('tripTransportModal')" class="flex-1 h-[46px] flex items-center justify-center bg-muji-paper text-muji-ash border border-muji-edge font-black rounded-[24px] hover:bg-muji-base transition-all active:scale-95 text-sm">取消</button>
                        <button type="submit" class="flex-1 h-[46px] flex items-center justify-center bg-muji-oak text-white font-black rounded-[24px] hover:opacity-90 shadow-muji transition-all active:scale-95 text-sm">儲存變更</button>
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
        <div class="relative z-10 transform overflow-hidden rounded-[40px] muji-glass text-left shadow-2xl transition-all w-full max-w-lg flex flex-col max-h-[calc(100vh-160px)]">
            <!-- 統一右上角關閉按鈕 (X) - 移出捲軸容器 -->
            <button onclick="safeCloseModal('tripSettingsModal')" class="absolute top-6 right-6 text-muji-ash hover:text-muji-oak p-2 rounded-full hover:bg-muji-base transition-all group z-50">
                <svg class="w-6 h-6 group-hover:rotate-90 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="px-5 sm:px-8 py-8 sm:py-10 overflow-y-auto custom-scrollbar scroll-smooth">
                <div class="flex justify-between items-start mb-8 sm:mb-10 text-left">
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
                <form action="{{ route('trips.update', ['user' => $trip->user, 'trip' => $trip]) }}" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      class="space-y-8"
                      onsubmit="handleAjaxSubmit(event, this, 'tripSettingsModal')">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-6 gap-6">
                        <!-- 旅程名稱 -->
                        <div class="col-span-full">
                            <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">旅程名稱</label>
                            <input type="text" name="name" required value="{{ $trip->name }}" class="w-full px-4 py-3 muji-input">
                        </div>

                        <!-- 未定日期切換 -->
                        <div class="col-span-full border-b border-muji-edge pb-4 mb-2">
                            <label class="flex items-center gap-2 cursor-pointer mt-2 w-max">
                                <input type="checkbox" id="tbd_date_toggle_edit" class="muji-checkbox" onchange="toggleTbdDateEdit(this)" {{ !$trip->start_date ? 'checked' : '' }}>
                                <span class="text-sm font-bold text-muji-ink">尚未決定具體日期</span>
                            </label>
                        </div>

                        <!-- 確切日期區塊 -->
                        <div id="exact_dates_edit" class="col-span-full grid grid-cols-1 sm:grid-cols-6 gap-6 mt-[-1rem] {{ !$trip->start_date ? 'hidden' : '' }}">
                            <div class="col-span-full sm:col-span-3">
                                <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">開始日期</label>
                                <input type="date" name="start_date" id="start_date_edit" {{ $trip->start_date ? 'required' : '' }} value="{{ $trip->start_date ? $trip->start_date->format('Y-m-d') : '' }}" class="w-full h-[46px] px-4 muji-input">
                            </div>
                            <div class="col-span-full sm:col-span-3">
                                <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">結束日期</label>
                                <input type="date" name="end_date" id="end_date_edit" {{ $trip->start_date ? 'required' : '' }} value="{{ $trip->end_date ? $trip->end_date->format('Y-m-d') : '' }}" class="w-full h-[46px] px-4 muji-input">
                            </div>
                        </div>

                        <!-- 預估天數區塊 -->
                        <div id="estimated_days_edit" class="col-span-full sm:col-span-6 {{ $trip->start_date ? 'hidden' : '' }} mt-[-1rem]">
                            <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">預計天數</label>
                            <input type="number" name="estimated_days" id="estimated_days_input_edit" min="1" placeholder="例如：5" value="{{ $trip->estimated_days ?? $trip->days->count() }}" {{ !$trip->start_date ? 'required' : '' }} class="w-full h-[46px] px-4 muji-input">
                        </div>
                        <script>
                            function toggleTbdDateEdit(checkbox) {
                                const exactDates = document.getElementById('exact_dates_edit');
                                const estimatedDays = document.getElementById('estimated_days_edit');
                                const startIn = document.getElementById('start_date_edit');
                                const endIn = document.getElementById('end_date_edit');
                                const estIn = document.getElementById('estimated_days_input_edit');

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
                                }
                            }
                        </script>

                        <!-- 貨幣 -->
                        @php
                            $allCurrencies = [
                                'TWD' => '台幣', 'JPY' => '日幣', 'KRW' => '韓幣',
                                'USD' => '美金', 'EUR' => '歐元', 'GBP' => '英鎊',
                                'AUD' => '澳幣', 'CAD' => '加幣', 'HKD' => '港幣',
                                'SGD' => '新幣', 'CNY' => '人民幣', 'THB' => '泰銖',
                                'VND' => '越南盾', 'MYR' => '馬幣',
                            ];
                        @endphp
                        <div class="col-span-full sm:col-span-2">
                            <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">本國貨幣</label>
                            <select name="base_currency" required class="w-full px-4 py-3 muji-input text-center font-black">
                                @foreach($allCurrencies as $code => $label)
                                    <option value="{{ $code }}" {{ $trip->base_currency == $code ? 'selected' : '' }}>{{ $code }} — {{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-full sm:col-span-2">
                            <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">當地貨幣</label>
                            <select name="target_currency" required class="w-full px-4 py-3 muji-input text-center font-black bg-muji-base/30">
                                @foreach($allCurrencies as $code => $label)
                                    <option value="{{ $code }}" {{ $trip->target_currency == $code ? 'selected' : '' }}>{{ $code }} — {{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-full sm:col-span-2">
                            <label class="block w-full text-left text-sm font-bold text-muji-ash mb-2 ml-1">預估匯率</label>
                            <div class="relative">
                                <input type="number" step="0.0001" id="exchange_rate_input" name="exchange_rate" required value="{{ $trip->exchange_rate }}" class="w-full px-4 py-3 muji-input text-center font-mono font-black">
                                <button type="button" onclick="fetchLiveRate(event)" class="mt-2 w-full py-2 bg-muji-base text-muji-oak text-[10px] font-black rounded-lg border border-muji-edge hover:bg-muji-wheat/20 transition-all flex items-center justify-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                    獲取即時匯率
                                </button>
                            </div>
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
                                btn.innerHTML = '正在獲取...';
                                
                                try {
                                    const response = await fetch(`{{ route('trip.exchange_rate', ['user' => $trip->user, 'trip' => $trip]) }}?base=${base}&target=${target}`);
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
                    <!-- 已移除多餘的 div -->

                    <!-- 旅程封面圖設定 -->
                    <div class="mt-8 pt-6 border-t border-muji-edge">
                        <div class="flex justify-between items-center mb-4">
                            <label class="block text-sm font-bold text-muji-ash text-left mb-2">旅程封面圖設定</label>
                            @if($trip->cover_image)
                            <button type="button" onclick="performRestoreCover(this)" class="px-3 py-1.5 bg-red-50 text-red-500 text-[10px] font-black rounded-lg border border-red-100 hover:bg-red-500 hover:text-white transition-all active:scale-95 uppercase tracking-widest">恢復系統預設圖</button>
                            <input type="hidden" name="restore_cover" id="restore_cover_input" value="0">
                            <script>
                                function performRestoreCover(btn) {
                                    if(confirm('確定要將旅程封面恢復為系統預設圖嗎？')) {
                                        const form = btn.closest('form');
                                        document.getElementById('restore_cover_input').value = "1";
                                        
                                        // Trigger the same AJAX submission mechanism
                                        if (typeof handleAjaxSubmit === 'function') {
                                            const event = new Event('submit', { cancelable: true, bubbles: true });
                                            form.dispatchEvent(event);
                                        } else {
                                            form.submit();
                                        }
                                    }
                                }
                            </script>
                            @endif
                        </div>
                        <div class="bg-muji-base/30 p-4 rounded-xl border border-muji-edge">
                            <input type="file" name="cover_image" accept="image/*" class="block w-full text-xs text-muji-ash file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-muji-paper file:text-muji-oak hover:file:bg-muji-base cursor-pointer transition-all">
                            <p class="text-[10px] text-muji-ash mt-2 italic shadow-muji-sm p-2 bg-muji-paper/50 rounded-lg">※ 支援 JPG、PNG，目前上限 2MB（因主機設定）。建議使用清爽的風景照。</p>
                        </div>
                    </div>

                    <div class="flex gap-4 mt-10 pt-8 mb-6 border-t border-muji-edge">
                        <button type="button" onclick="safeCloseModal('tripSettingsModal')" class="flex-1 h-[46px] flex items-center justify-center bg-muji-paper text-muji-ash border border-muji-edge font-black rounded-[24px] hover:bg-muji-base transition-all active:scale-95 text-sm">取消</button>
                        <button type="submit" class="flex-1 h-[46px] flex items-center justify-center bg-muji-oak text-white font-black rounded-[24px] hover:opacity-90 shadow-muji transition-all active:scale-95 text-sm">儲存設定</button>
                    </div>
                </form>


                @if(auth()->id() == $trip->user_id)
                <div class="mt-10 pt-10 border-t border-muji-edge">
                    <h4 class="text-xs font-black text-muji-oak uppercase tracking-[0.2em] flex items-center gap-2 mb-6 bg-muji-base/50 self-start px-3 py-1.5 rounded-lg border border-muji-edge">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        旅伴成員與邀請
                    </h4>

                    <!-- 1. Accepted Collaborators -->
                    <div class="space-y-3 mb-8">
                        <label class="block text-[10px] font-black text-muji-ash uppercase tracking-widest pl-1 mb-2">已加入旅伴</label>
                        @forelse($trip->collaborators as $collaborator)
                        <div class="flex justify-between items-center bg-muji-base/30 p-3 rounded-xl border border-muji-edge">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full overflow-hidden border border-muji-edge shadow-muji-sm">
                                    <img src="{{ $collaborator->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($collaborator->name).'&background=9c8c7c&color=fff' }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="text-sm font-black text-muji-ink">{{ $collaborator->name }}</p>
                                    <p class="text-[10px] font-bold text-muji-ash uppercase tracking-wider">{{ $collaborator->email }}</p>
                                </div>
                            </div>
                            <form action="{{ route('trip.collaborators.remove', ['user' => $trip->user, 'trip' => $trip, 'collaborator' => $collaborator->id]) }}" 
                                  method="POST"
                                  onsubmit="handleAjaxSubmit(event, this, null)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 p-1" onclick="return confirm('確定移除此協作者？')">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                        @empty
                        <p class="text-xs text-muji-ash/50 italic py-2">目前尚無其他旅伴成員...</p>
                        @endforelse
                    </div>

                    <!-- 2. Pending Invitations -->
                    @php 
                        $pending = $trip->invitations()->where('status', 'pending')->get();
                    @endphp
                    @if($pending->count() > 0)
                    <div class="space-y-3 mb-8">
                        <label class="block text-[10px] font-black text-muji-ash uppercase tracking-widest pl-1 mb-2">等待接受中 (Email 邀請)</label>
                        @foreach($pending as $inv)
                        <div class="flex justify-between items-center bg-muji-edge/10 p-3 rounded-xl border border-dotted border-muji-edge">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-muji-paper flex items-center justify-center text-muji-ash border border-muji-edge">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-muji-ash">{{ $inv->email }}</p>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[8px] font-black bg-muji-base text-muji-oak px-1.5 py-0.5 rounded uppercase tracking-widest">Pending</span>
                                        <span class="text-[8px] font-bold text-muji-ash/40 uppercase">{{ $inv->created_at->diffForHumans() }} 邀請</span>
                                    </div>
                                </div>
                            </div>
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('trip.invitations.revoke', ['user' => $trip->user, 'trip' => $trip, 'invitation' => $inv->id]) }}" 
                                          method="POST"
                                          onsubmit="handleAjaxSubmit(event, this, null)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[10px] font-black text-red-400 hover:text-red-500 uppercase tracking-widest px-2 py-1 rounded hover:bg-red-50 transition-all" onclick="return confirm('確定撤回此邀請？')">
                                            撤回邀請
                                        </button>
                                    </form>
                                </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- 3. Add Invitation Form -->
                    <form action="{{ route('trip.invite', ['user' => $trip->user, 'trip' => $trip]) }}" 
                          method="POST"
                          onsubmit="handleAjaxSubmit(event, this, null)">
                        @csrf
                        <label class="block text-sm font-bold text-muji-ash text-left mb-2 ml-1">邀請新旅伴 (Email)</label>
                        <div class="flex gap-2">
                            <input type="email" name="email" required placeholder="例如：friend@example.com" class="block w-full h-[46px] px-4 muji-input shadow-muji-sm">
                            <button type="submit" class="px-8 h-[46px] flex items-center justify-center bg-muji-oak text-white font-black rounded-2xl hover:opacity-90 transition-all shadow-muji active:scale-95 whitespace-nowrap">
                                發送邀請
                            </button>
                        </div>
                        <p class="mt-3 text-[10px] text-muji-ash italic leading-relaxed">※ 系統將發送邀請函至對方信箱，對方點擊連結後即可加入此旅程共同編輯。</p>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endauth
@endif
@endpush


    @if(!$isShared)
    <script>
        // --- NEW: Trip Actions Dropdown Handler ---
        window.toggleTripActions = function(event) {
            event.stopPropagation();
            const menu = document.getElementById('tripActionsMenu');
            if (menu) {
                const isHidden = menu.classList.contains('hidden');
                // Close all other dropdowns first if any
                document.querySelectorAll('.action-menu-active').forEach(m => m.classList.add('hidden'));
                
                if (isHidden) {
                    menu.classList.remove('hidden');
                    menu.classList.add('action-menu-active');
                } else {
                    menu.classList.add('hidden');
                    menu.classList.remove('action-menu-active');
                }
            }
        };

        // Global click to close trip actions menu
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('tripActionsMenu');
            const btn = document.getElementById('tripActionsBtn');
            if (menu && !menu.contains(event.target) && btn && !btn.contains(event.target)) {
                menu.classList.add('hidden');
                menu.classList.remove('action-menu-active');
            }
        });

        // --- NEW: Universal AJAX Form Handler ---
        async function handleAjaxSubmit(event, form, modalId) {
            event.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            // Loading State
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    showToast(data.message || 'Saved Successfully!', 'success');
                    // OPTIONAL: Reload page after delay if data changes are too complex for partial update
                    setTimeout(() => window.location.reload(), 1000); 
                } else {
                    // English Error Handling
                    const errorMsg = data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Error occurred.');
                    showToast(errorMsg, 'error');
                }
            } catch (error) {
                showToast('Network error, please try again.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        }

        // --- NEW: Sytem-wide Toast Component ---
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-24 left-1/2 -translate-x-1/2 px-8 py-4 rounded-2xl shadow-muji border-2 font-black text-xs tracking-widest uppercase transition-all duration-500 z-[9999] animate-in fade-in slide-in-from-bottom-5`;
            
            if (type === 'success') {
                toast.classList.add('bg-muji-oak', 'text-white', 'border-white');
            } else {
                toast.classList.add('bg-red-500', 'text-white', 'border-white');
            }
            
            toast.innerText = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('opacity-0', 'scale-95');
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }

        document.addEventListener('DOMContentLoaded', function () {
            // --- NEW: Sync Checklist Item to DB ---
            window.toggleChecklistItem = async function(checkbox, url) {
                const label = checkbox.nextElementSibling;
                // Pre-update visual feedback
                if (checkbox.checked) {
                    label.classList.add('line-through', 'opacity-40');
                } else {
                    label.classList.remove('line-through', 'opacity-40');
                }

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    if (!response.ok) throw new Error('Network fail');
                    const data = await response.json();
                    
                    // Show subtle toast for feedback
                    if (typeof showToast === 'function') {
                        showToast(data.message, 'success');
                    }
                } catch (e) {
                    // Revert on error
                    checkbox.checked = !checkbox.checked;
                    if (checkbox.checked) {
                        label.classList.add('line-through', 'opacity-40');
                    } else {
                        label.classList.remove('line-through', 'opacity-40');
                    }
                    if (typeof showToast === 'function') showToast('同步失敗', 'error');
                }
            };

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
                outbound: '啟航', inbound: '返航',
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

        const dayOptions = {
            @foreach($itinerary as $index => $day)
                '{{ $day->date ? $day->date->format('Y-m-d') : "day-".$day->day_number }}': 'Day {{ $day->day_number }} {{ $day->date ? "(".$day->date->format('n/j').")" : "" }}',
            @endforeach
        };

        function showAssignSwal(url, itemName) {
            Swal.fire({
                ...getSwalConfig(),
                title: '指派景點',
                html: `<div class="mb-4 text-center">請選擇要將「<span class="text-muji-oak">${itemName}</span>」排入哪一天？</div>`,
                input: 'select',
                inputOptions: dayOptions,
                inputPlaceholder: '請選擇日期',
                showCancelButton: true,
                confirmButtonText: '確定指派',
                cancelButtonText: '取消',
                inputValidator: (value) => {
                    if (!value) return '請先選擇一個日期！';
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const selectedDate = result.value;
                    const dayLabel = dayOptions[selectedDate];
                    
                    Swal.fire({
                        ...getSwalConfig(),
                        title: '確認指派？',
                        text: `確定要將「${itemName}」加入 ${dayLabel} 嗎？`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: '確定',
                        cancelButtonText: '返回'
                    }).then((confResult) => {
                        if (confResult.isConfirmed) {
                            assignSpotToDay(url, selectedDate, itemName);
                        }
                    });
                }
            });
        }

        async function assignSpotToDay(url, date, itemName) {
            Swal.fire({
                ...getSwalConfig(),
                title: '指派中...',
                didOpen: () => { Swal.showLoading(); },
                allowOutsideClick: false,
                showConfirmButton: false
            });

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ date: date })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    Swal.fire({
                        ...getSwalConfig(),
                        icon: 'success',
                        title: '指派成功！',
                        text: `「${itemName}」已成功排入行程。`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(() => window.location.reload(), 2100);
                } else {
                    throw new Error(data.message || '指派時發生錯誤');
                }
            } catch (error) {
                console.error('Assign Error:', error);
                Swal.fire({
                    ...getSwalConfig(),
                    icon: 'error',
                    title: '指派失敗',
                    text: error.message || '連線異常，請稍後再試。'
                });
            }
        }
    </script>
    @endif

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

    <!-- Weather Forecast System (High Reliability) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const indicators = document.querySelectorAll('.weather-indicator');
            const geoMap = {
                '福岡': { lat: 33.59, lon: 130.40 }, 'Fukuoka': { lat: 33.59, lon: 130.40 },
                '東京': { lat: 35.68, lon: 139.76 }, 'Tokyo': { lat: 35.68, lon: 139.76 },
                '大阪': { lat: 34.69, lon: 135.50 }, 'Osaka': { lat: 34.69, lon: 135.50 }
            };
            indicators.forEach(async (el) => {
                const loc = el.dataset.location; const date = el.dataset.date; if (!loc) return;
                try {
                    let lat, lon;
                    if (geoMap[loc]) { lat = geoMap[loc].lat; lon = geoMap[loc].lon; }
                    else {
                        let q = loc.replace('未來', '').replace('行程', '');
                        let res = await fetch(`https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(q)}&count=1&language=en&format=json`);
                        let data = await res.json();
                        if (data.results) { lat = data.results[0].latitude; lon = data.results[0].longitude; }
                    }
                    if (lat && lon) {
                        const wR = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&daily=weather_code,temperature_2m_max,temperature_2m_min&timezone=auto&start_date=${date}&end_date=${date}`);
                        const wD = await wR.json();
                        if (wD.daily && wD.daily.weather_code[0] !== null) {
                            const code = wD.daily.weather_code[0];
                            const tMax = Math.round(wD.daily.temperature_2m_max[0]);
                            const tMin = Math.round(wD.daily.temperature_2m_min[0]);
                            let icon = '☀️'; let desc = '晴朗';
                            if (code >= 51 && code <= 67) { icon = '🌧️'; desc = '雨天'; }
                            else if (code >= 1 && code <= 3) { icon = '☁️'; desc = '多雲'; }
                            else if (code >= 45 && code <= 48) { icon = '🌫️'; desc = '有霧'; }
                            else if (code >= 71 && code <= 77) { icon = '❄️'; desc = '下雪'; }
                            else if (code >= 80 && code <= 82) { icon = '🌦️'; desc = '陣雨'; }
                            else if (code >= 95 && code <= 99) { icon = '⛈️'; desc = '雷雨'; }
                            el.querySelector('.weather-icon').innerHTML = icon;
                            el.querySelector('.weather-temp').innerText = `${tMax}°/${tMin}°C`;
                            el.setAttribute('data-tooltip', `${desc} (最高 ${tMax}°, 最低 ${tMin}°)`);
                            return;
                        }
                    }
                    el.classList.add('hidden');
                } catch (e) { el.classList.add('hidden'); }
            });
        });
    </script>

    @push('modals')
    <!-- Full Trip Map Modal -->
    <div id="mapViewModal" class="fixed inset-0 z-[3000] hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-md" onclick="safeCloseModal('mapViewModal')"></div>
        <div class="absolute inset-4 sm:inset-10 muji-glass rounded-[40px] shadow-2xl flex flex-col overflow-hidden border border-white/20">
            <div class="px-6 py-4 border-b border-muji-edge flex justify-between items-center bg-muji-paper/80">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-muji-oak text-white flex items-center justify-center font-black text-xs shadow-muji-sm">TRIP</span>
                    <h3 class="text-lg font-black text-muji-ink">{{ $trip->name }} 完整地圖規劃</h3>
                </div>
                <button onclick="safeCloseModal('mapViewModal')" class="p-2 text-muji-ash hover:text-muji-oak transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div id="tripFullMap" class="flex-grow w-full bg-muji-base"></div>
            <div class="px-6 py-4 border-t border-muji-edge bg-muji-paper/90 flex flex-wrap gap-6 items-center justify-between">
                <div class="flex gap-4 items-center">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-muji-oak"></div>
                        <span class="text-xs font-bold text-muji-ash uppercase tracking-widest">全行程景點</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endpush

    <!-- Leaflet Map Integration (Free & No Token Required) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let fullMap;
        const cartoUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        const attribution = '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>';

        function openMapViewModal() {
            safeOpenModal('mapViewModal');
            setTimeout(initFullMap, 300);
        }

        function initFullMap() {
            if (fullMap) fullMap.remove();
            
            const events = [];
            @foreach($trip->days as $day)
                @foreach($day->events as $event)
                    @if(!$event->trashed() && $event->latitude && $event->longitude)
                        events.push({
                            lat: {{ $event->latitude }},
                            lng: {{ $event->longitude }},
                            activity: "{{ addslashes($event->activity) }}",
                            date: "{{ $day->date ? $day->date->format('n/j') : 'Day '.$day->day_number }}"
                        });
                    @endif
                @endforeach
            @endforeach

            if (events.length === 0) {
                document.getElementById('tripFullMap').innerHTML = `
                    <div class="h-full flex flex-col items-center justify-center text-muji-ash opacity-50 p-10 text-center">
                        <svg class="w-16 h-16 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                        <p class="font-bold">尚未標註任何具座標的地點</p>
                    </div>
                `;
                return;
            }

            fullMap = L.map('tripFullMap').setView([events[0].lat, events[0].lng], 10);
            L.tileLayer(cartoUrl, { attribution }).addTo(fullMap);

            const markers = [];
            const coordinates = [];

            events.forEach(ev => {
                const latlng = [ev.lat, ev.lng];
                coordinates.push(latlng);
                const marker = L.marker(latlng, { icon: L.icon({
                    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41]
                })})
                    .bindPopup(`<div class="p-2 font-black text-muji-ink">${ev.date}: ${ev.activity}</div>`)
                    .addTo(fullMap);
                markers.push(marker);
            });

            if (coordinates.length > 1) {
                const group = new L.featureGroup(markers);
                fullMap.fitBounds(group.getBounds(), { padding: [50, 50] });
            }

            // Real-time User Location on Full Map
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((pos) => {
                    const userLat = pos.coords.latitude;
                    const userLng = pos.coords.longitude;
                    
                    const userIcon = L.divIcon({
                        className: 'user-location-marker',
                        html: `<div class="relative w-6 h-6"><div class="absolute inset-0 rounded-full bg-blue-500 opacity-25 animate-ping"></div><div class="relative w-4 h-4 mt-1 ml-1 rounded-full bg-blue-600 border-2 border-white shadow-lg"></div></div>`,
                        iconSize: [24, 24],
                        iconAnchor: [12, 12]
                    });

                    L.marker([userLat, userLng], { icon: userIcon })
                        .bindPopup('<div class="p-1 font-bold text-blue-600 italic">您目前的位置</div>')
                        .addTo(fullMap);

                    // If you want to include user in the view bounds:
                    // group.addLayer(userMarker);
                    // fullMap.fitBounds(group.getBounds(), { padding: [80, 80] });
                }, null, { enableHighAccuracy: true });
            }
        }

        async function convertComment(content, type, category = null) {
            if (!category) {
                openConvertModal(content, type);
                return;
            }

            try {
                const res = await fetch('{{ route("comment.convert", ["user" => $trip->user, "trip" => $trip]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ content, type, category })
                });
                const data = await res.json();

                if (res.ok) {
                    Swal.fire({
                        ...getSwalConfig(),
                        icon: 'success',
                        title: '轉換成功！',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(() => window.location.reload(), 1600);
                } else {
                    throw new Error(data.message || '轉換失敗');
                }
            } catch (err) {
                Swal.fire({
                    ...getSwalConfig(),
                    icon: 'error',
                    title: '錯誤',
                    text: err.message
                });
            }
        }

        // --- NEW: Static Conversion Modal Logic ---
        let currentConvertContent = '';
        let currentConvertType = '';

        function openConvertModal(content, type) {
            currentConvertContent = content;
            currentConvertType = type;
            
            // Update UI
            document.getElementById('modalIcon').innerText = type === 'shopping' ? '🛒' : '📍';
            document.getElementById('modalIconContainer').className = type === 'shopping' 
                ? 'w-20 h-20 rounded-full border-4 border-muji-oak/20 flex items-center justify-center text-4xl bg-muji-oak/5'
                : 'w-20 h-20 rounded-full border-4 border-muji-oak/20 flex items-center justify-center text-4xl bg-muji-oak/5';
            
            document.getElementById('convertModalTitle').innerText = type === 'shopping' ? '轉入必買' : '轉入景點';
            
            // Populate content for editing
            document.getElementById('convertModalContent').value = content;
            
            const input = document.getElementById('convertModalInput');
            // 關鍵：不要預填文字，瀏覽器才會跳出完整下拉
            input.value = ''; 
            input.placeholder = type === 'shopping' ? '預設：藥妝' : '預設：景點';
            
            // Link directly to modal-private datalists
            input.setAttribute('list', type === 'shopping' ? 'modal_shopping_list' : 'modal_spot_list');
            
            safeOpenModal('convertCommentModal');
            setTimeout(() => {
                input.focus();
            }, 100);
        }

        function submitConvertModal() {
            let category = document.getElementById('convertModalInput').value;
            const content = document.getElementById('convertModalContent').value;
            
            if (!content) {
                showToast('內容不能為空', 'error');
                return;
            }

            // 如果沒填分類，就用預設值
            if (!category) {
                category = currentConvertType === 'shopping' ? '藥妝' : '景點';
            }
            safeCloseModal('convertCommentModal');
            convertComment(content, currentConvertType, category);
        }

        let draggedCommentContent = '';
        function handleCommentDragStart(e, content) {
            draggedCommentContent = content;
            e.dataTransfer.setData('text/plain', content);
            e.dataTransfer.effectAllowed = 'copy';
            
            // Highlight zones
            document.querySelectorAll('.drop-target-zone').forEach(z => {
                z.classList.add('ring-2', 'ring-muji-oak/30', 'bg-muji-wheat/5');
            });
        }

        function handleCommentDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
            const zone = e.currentTarget;
            zone.classList.remove('ring-muji-oak/30');
            zone.classList.add('border-muji-oak', 'bg-muji-wheat/20', 'shadow-2xl', 'scale-[1.02]');
        }

        function handleCommentDragLeave(e) {
            const zone = e.currentTarget;
            zone.classList.remove('border-muji-oak', 'bg-muji-wheat/20', 'shadow-2xl', 'scale-[1.02]');
            zone.classList.add('ring-muji-oak/30');
        }

        function handleCommentDrop(e, type) {
            e.preventDefault();
            const zone = e.currentTarget;
            zone.classList.remove('border-muji-oak', 'bg-muji-wheat/20', 'shadow-2xl', 'scale-[1.02]', 'ring-2', 'ring-muji-oak/30', 'bg-muji-wheat/5');
            
            // Un-highlight all
            document.querySelectorAll('.drop-target-zone').forEach(z => {
                z.classList.remove('ring-2', 'ring-muji-oak/30', 'bg-muji-wheat/5');
            });

            const content = e.dataTransfer.getData('text/plain') || draggedCommentContent;
            if (content) {
                convertComment(content, type);
            }
        }

        // Initialize Internal Checklist Sorting
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Sortable !== 'undefined') {
                document.querySelectorAll('.sortable-list').forEach(el => {
                    new Sortable(el, {
                        group: 'checklist-' + el.dataset.type,
                        handle: '.drag-handle',
                        animation: 150,
                        ghostClass: 'muji-ghost',
                        fallbackOnBody: true,
                        swapThreshold: 0.65,
                        onEnd: function (evt) {
                            const itemId = evt.item.dataset.id;
                            const newCategory = evt.to.dataset.category;
                            
                            // Get all IDs in the new target list
                            const orderedIds = Array.from(evt.to.children).map(li => li.dataset.id);

                            fetch('{{ route("checklist.reorder", ["user" => $trip->user, "trip" => $trip]) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    item_id: itemId,
                                    category: newCategory,
                                    order: orderedIds
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                // showToast(data.message, 'success');
                            });
                        }
                    });
                });
            }
        });
    </script>
@endsection
