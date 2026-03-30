@php
    $outRoute = $flightInfo['outbound']['route'] ?? '';
    $hasArrow = str_contains($outRoute, ' ➝ ');
    $parts = $hasArrow ? explode(' ➝ ', $outRoute) : [$outRoute, ''];
@endphp

<!-- Ship Specialized UI -->
<div class="relative grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-dashed divide-gray-200">
    <div class="p-8 group/item hover:bg-muji-wheat/5 transition-all duration-300">
        <div class="flex justify-between items-start mb-6">
            <span class="px-3 py-1.5 bg-muji-base text-muji-oak rounded-lg border border-muji-edge text-[10px] font-black uppercase tracking-[0.2em] shadow-muji-sm">
                {{ $theme['label_out'] }}
            </span>
            <div class="text-right">
                <p class="text-[9px] font-black text-muji-ash mb-0.5 uppercase tracking-widest">船期班次 Schedule</p>
                <span class="text-sm font-black text-muji-ink font-mono">{{ $flightInfo['outbound']['date'] }}</span>
            </div>
        </div>

        <div class="flex flex-col gap-4 py-4 border-y border-dashed border-muji-edge">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-black text-muji-ash uppercase shrink-0">船名 / 航次 Vessel</p>
                <p class="text-xl font-black text-muji-ink tracking-tight text-right">{{ $flightInfo['airline'] ?: 'TBA' }}</p>
            </div>
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-black text-muji-ash uppercase shrink-0">出發與抵達 Port</p>
                <div class="flex items-center gap-2">
                   <p class="text-xl font-black text-muji-ink tracking-tight">{{ $parts[0] }}</p>
                   <span class="text-muji-ash opacity-40">~</span>
                   <p class="text-xl font-black text-muji-ink tracking-tight">{{ $parts[1] ?: '--' }}</p>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between px-4 py-3 bg-muji-base/50 rounded-2xl border border-muji-edge">
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-muji-ash uppercase mb-0.5 tracking-tight line-clamp-1">艙位類型 / 備註 Notes</span>
                <span class="text-xs font-black text-muji-oak font-mono">{{ $flightInfo['baggage'] ?: 'Standard' }}</span>
            </div>
            <div class="text-right flex flex-col">
                <span class="text-[9px] font-black text-muji-ash uppercase mb-0.5 leading-none">啟航時間 Time</span>
                <span class="text-2xl font-black text-muji-ink font-mono leading-none">{{ $flightInfo['outbound']['time'] ?: 'TBA' }}</span>
            </div>
        </div>
    </div>

    <!-- Placeholder for return journey -->
    <div class="p-8 group/item hover:bg-muji-wheat/5 transition-all duration-300 bg-muji-base/30">
         <div class="flex justify-between items-start mb-6">
            <span class="px-3 py-1.5 bg-muji-base text-muji-ash border border-muji-edge rounded-lg text-[10px] font-black uppercase tracking-[0.2em] shadow-muji-sm">
                {{ $theme['label_in'] }}
            </span>
             <span class="text-xs font-black text-muji-ash/50">{{ $flightInfo['inbound']['date'] }}</span>
        </div>
        <div class="flex flex-col items-center justify-center h-full text-center opacity-40">
            <svg class="w-12 h-12 mb-2 text-muji-wheat" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13s-2 2-5 2-5-2-5-2-5 2-7 2-7-2-7-2V6l2.5-1 2.5 1 2.5-1 2.5 1 2.5-1 2.5 1 2.5-1v7z" /></svg>
            <p class="text-[10px] font-black uppercase tracking-widest text-muji-ink">回程航班資訊 Details</p>
        </div>
    </div>
</div>
