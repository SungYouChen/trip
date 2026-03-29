@php
    $outRoute = $flightInfo['outbound']['route'] ?? '';
    $hasArrow = str_contains($outRoute, ' ➝ ');
    $parts = $hasArrow ? explode(' ➝ ', $outRoute) : [$outRoute, ''];
@endphp

<!-- Bus Specialized UI -->
<div class="relative grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-dashed divide-gray-200">
    <div class="p-8 group/item hover:bg-white/40 transition-all duration-300">
        <div class="flex justify-between items-start mb-6">
            <span class="px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-[0.2em] shadow-sm">
                {{ $theme['label_out'] }}
            </span>
            <div class="text-right">
                <p class="text-[9px] font-black text-gray-400 mb-0.5 uppercase tracking-widest">Bus Schedule</p>
                <span class="text-sm font-black text-gray-900 font-mono">{{ $flightInfo['outbound']['date'] }}</span>
            </div>
        </div>

        <div class="flex items-center gap-4 mb-4">
            <div class="flex-col pb-4 border-l-2 border-indigo-200 pl-4 space-y-4">
                <div>
                   <p class="text-[10px] text-gray-400 uppercase font-black">Departure Station</p>
                   <p class="text-xl font-bold text-gray-800">{{ $parts[0] }}</p>
                </div>
                <div class="relative h-4">
                    <svg class="w-4 h-4 text-emerald-300 absolute -left-[25px] top-0" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" /></svg>
                </div>
                <div>
                   <p class="text-[10px] text-gray-400 uppercase font-black">Destination</p>
                   <p class="text-xl font-bold text-gray-800">{{ $parts[1] ?: '--' }}</p>
                </div>
            </div>
        </div>

        <div class="flex justify-between items-end mt-4">
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase mb-0.5 tracking-tighter">Dep. Time</p>
                <p class="text-3xl font-mono font-black text-gray-900 leading-none">{{ $flightInfo['outbound']['time'] ?: 'TBA' }}</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-black text-gray-400 uppercase mb-0.5 tracking-tighter">Baggage / Platform</p>
                <p class="text-xs font-black text-gray-600">{{ $flightInfo['baggage'] ?: '--' }}</p>
            </div>
        </div>
    </div>

    <!-- Return Placeholder or Info -->
    <div class="p-8 group/item hover:bg-white/40 transition-all duration-300 bg-gray-50/20">
         <div class="flex justify-between items-start mb-6">
            <span class="px-3 py-1.5 bg-gray-100 text-gray-500 rounded-lg text-[10px] font-black uppercase tracking-[0.2em] shadow-sm">
                {{ $theme['label_in'] }}
            </span>
            <span class="text-xs font-black text-gray-300">{{ $flightInfo['inbound']['date'] }}</span>
        </div>
        <div class="flex flex-col items-center justify-center h-full text-center opacity-30">
            <svg class="w-12 h-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            <p class="text-[10px] font-black uppercase tracking-widest">Return Journey Details</p>
            <p class="text-xs">{{ $flightInfo['inbound']['route'] ?: 'Not scheduled' }}</p>
        </div>
    </div>
</div>
