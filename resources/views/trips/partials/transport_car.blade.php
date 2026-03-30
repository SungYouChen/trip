@php
    $outRoute = $flightInfo['outbound']['route'] ?? '';
    $inRoute = $flightInfo['inbound']['route'] ?? '';
@endphp

<!-- Car Rental Specialized UI -->
<div class="relative grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-dashed divide-gray-200">
    <!-- Decorative Notches -->
    <div class="absolute top-1/2 left-0 -translate-y-1/2 -translate-x-1/2 w-6 h-10 bg-gray-50 rounded-r-full hidden md:block z-10 border-r border-gray-100"></div>
    <div class="absolute top-1/2 right-0 -translate-y-1/2 translate-x-1/2 w-6 h-10 bg-gray-50 rounded-l-full hidden md:block z-10 border-l border-gray-100"></div>

    <!-- Pickup Voucher -->
    <div class="p-8 group/item hover:bg-muji-wheat/5 transition-all duration-300">
        <div class="flex justify-between items-start mb-6">
            <span class="px-3 py-1.5 bg-muji-base text-muji-oak rounded-lg border border-muji-edge text-[10px] font-black uppercase tracking-[0.2em] shadow-muji-sm">
                {{ $theme['label_out'] }}
            </span>
            <div class="text-right">
                <p class="text-[9px] font-black text-muji-ash mb-0.5 uppercase tracking-widest">預約編號 Reservation ID</p>
                <span class="text-xs font-black text-muji-ink font-mono italic">RENT-{{ substr($trip->name, 0, 3) }}-{{ $flightInfo['outbound']['date'] }}</span>
            </div>
        </div>

        <div class="flex flex-col gap-2 mb-6">
            <p class="text-[10px] font-black text-muji-ash uppercase tracking-widest leading-relaxed">取車地點 Pickup Location</p>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-muji-oak" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <p class="text-2xl font-black text-muji-ink tracking-tight">{{ $outRoute ?: 'TBA' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-muji-ash uppercase mb-0.5">預約車型 Type</span>
                <span class="text-sm font-black text-muji-ink">{{ $flightInfo['car_model'] ?: 'Standard Sedan' }}</span>
            </div>
            <div class="text-right flex flex-col">
                <span class="text-[9px] font-black text-muji-ash uppercase mb-0.5">取車時間 Time</span>
                <span class="text-lg font-black text-muji-ink font-mono">{{ $flightInfo['outbound']['time'] ?: 'Anytime' }}</span>
            </div>
        </div>
    </div>

    <!-- Drop-off Voucher -->
    <div class="p-8 group/item hover:bg-muji-wheat/5 transition-all duration-300">
        <div class="flex justify-between items-start mb-6">
            <span class="px-3 py-1.5 bg-muji-base text-muji-ash border border-muji-edge rounded-lg text-[10px] font-black uppercase tracking-[0.2em] shadow-muji-sm">
                {{ $theme['label_in'] }}
            </span>
            <div class="text-right">
                <p class="text-[9px] font-black text-muji-ash mb-0.5 uppercase tracking-widest">合約狀態 Status</p>
                <span class="text-[9px] px-2 py-0.5 bg-muji-wheat/30 text-muji-oak rounded-full font-black">已確認 CONFIRMED</span>
            </div>
        </div>

        <div class="flex flex-col gap-2 mb-6">
            <p class="text-[10px] font-black text-muji-ash uppercase tracking-widest">還車地點 Drop-off Location</p>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-muji-ash" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                <p class="text-2xl font-black text-muji-ink tracking-tight">{{ $inRoute ?: 'TBA' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-muji-ash uppercase mb-0.5">還車日期 Date</span>
                <span class="text-sm font-black text-muji-ink font-mono">{{ $flightInfo['inbound']['date'] }}</span>
            </div>
            <div class="text-right flex flex-col">
                <span class="text-[9px] font-black text-muji-ash uppercase mb-0.5">還車時間 Time</span>
                <span class="text-lg font-black text-muji-ink font-mono">{{ $flightInfo['inbound']['time'] ?: 'Anytime' }}</span>
            </div>
        </div>
    </div>
</div>
