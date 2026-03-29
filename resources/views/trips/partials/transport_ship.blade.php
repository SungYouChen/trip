@php
    $outRoute = $flightInfo['outbound']['route'] ?? '';
    $hasArrow = str_contains($outRoute, ' ➝ ');
    $parts = $hasArrow ? explode(' ➝ ', $outRoute) : [$outRoute, ''];
@endphp

<!-- Ship Specialized UI -->
<div class="relative grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-dashed divide-gray-200">
    <div class="p-8 group/item hover:bg-white/40 transition-all duration-300">
        <div class="flex justify-between items-start mb-6">
            <span class="px-3 py-1.5 bg-cyan-50 text-cyan-600 rounded-lg text-[10px] font-black uppercase tracking-[0.2em] shadow-sm">
                {{ $theme['label_out'] }}
            </span>
            <div class="text-right">
                <p class="text-[9px] font-black text-gray-400 mb-0.5 uppercase tracking-widest">Marine Schedule</p>
                <span class="text-sm font-black text-gray-900 font-mono">{{ $flightInfo['outbound']['date'] }}</span>
            </div>
        </div>

        <div class="flex flex-col gap-4 py-4 border-y border-cyan-100">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-black text-gray-300 uppercase shrink-0">Vessel</p>
                <p class="text-xl font-black text-gray-800 tracking-tight text-right">{{ $flightInfo['airline'] ?: 'TBA' }}</p>
            </div>
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-black text-gray-300 uppercase shrink-0">Voyage Port</p>
                <div class="flex items-center gap-2">
                   <p class="text-xl font-bold text-gray-800">{{ $parts[0] }}</p>
                   <span class="text-cyan-200">~</span>
                   <p class="text-xl font-bold text-gray-800">{{ $parts[1] ?: '--' }}</p>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between px-4 py-3 bg-cyan-50/30 rounded-2xl border border-cyan-100">
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-gray-400 uppercase mb-0.5 tracking-tight line-clamp-1">Cabin Type / Notes</span>
                <span class="text-xs font-black text-gray-800 font-mono">{{ $flightInfo['baggage'] ?: 'Standard' }}</span>
            </div>
            <div class="text-right flex flex-col">
                <span class="text-[9px] font-black text-gray-400 uppercase mb-0.5 leading-none">Dep. Time</span>
                <span class="text-2xl font-black text-gray-900 font-mono leading-none">{{ $flightInfo['outbound']['time'] ?: 'TBA' }}</span>
            </div>
        </div>
    </div>

    <!-- Placeholder for return journey -->
    <div class="p-8 group/item hover:bg-white/40 transition-all duration-300 bg-cyan-50/10">
         <div class="flex justify-between items-start mb-6">
            <span class="px-3 py-1.5 bg-gray-100 text-gray-500 rounded-lg text-[10px] font-black uppercase tracking-[0.2em] shadow-sm">
                {{ $theme['label_in'] }}
            </span>
             <span class="text-xs font-black text-gray-300">{{ $flightInfo['inbound']['date'] }}</span>
        </div>
        <div class="flex flex-col items-center justify-center h-full text-center opacity-30">
            <svg class="w-12 h-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13s-2 2-5 2-5-2-5-2-5 2-7 2-7-2-7-2V6l2.5-1 2.5 1 2.5-1 2.5 1 2.5-1 2.5 1 2.5-1v7z" /></svg>
            <p class="text-[10px] font-black uppercase tracking-widest text-cyan-900/60">No Return Leg Info Found</p>
        </div>
    </div>
</div>
