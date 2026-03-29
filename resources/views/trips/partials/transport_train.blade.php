@php
    $outRoute = $flightInfo['outbound']['route'] ?? '';
    $hasArrow = str_contains($outRoute, ' ➝ ');
    $parts = $hasArrow ? explode(' ➝ ', $outRoute) : [$outRoute, ''];
    $inRoute = $flightInfo['inbound']['route'] ?? '';
    $hasArrowIn = str_contains($inRoute, ' ➝ ');
    $partsIn = $hasArrowIn ? explode(' ➝ ', $inRoute) : [$inRoute, ''];
@endphp

<!-- Train Ticket Specialized UI -->
<div class="relative grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-dashed divide-gray-200">
    <!-- Decorative Notches -->
    <div class="absolute top-1/2 left-0 -translate-y-1/2 -translate-x-1/2 w-6 h-10 bg-gray-50 rounded-r-full hidden md:block z-10 border-r border-gray-100"></div>
    <div class="absolute top-1/2 right-0 -translate-y-1/2 translate-x-1/2 w-6 h-10 bg-gray-50 rounded-l-full hidden md:block z-10 border-l border-gray-100"></div>

    <!-- Departure Ticket -->
    <div class="p-8 group/item hover:bg-white/40 transition-all duration-300">
        <div class="flex justify-between items-start mb-6">
            <span class="px-3 py-1.5 bg-amber-50 text-amber-600 rounded-lg border border-amber-100 text-[10px] font-black uppercase tracking-[0.2em] shadow-sm">
                {{ $theme['label_out'] }}
            </span>
            <div class="text-right">
                <p class="text-[9px] font-black text-gray-400 mb-0.5 uppercase tracking-widest">Entry Date</p>
                <span class="text-sm font-black text-gray-900 font-mono">{{ $flightInfo['outbound']['date'] }}</span>
            </div>
        </div>

        <div class="flex items-center justify-between gap-4 py-4 border-y border-dashed border-gray-100">
            <div class="flex-1">
                <p class="text-[10px] font-black text-gray-300 uppercase mb-1">From</p>
                <p class="text-2xl font-black text-gray-800 tracking-tight">{{ $parts[0] }}</p>
            </div>
            <div class="text-amber-200 opacity-50 shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
            </div>
            <div class="flex-1 text-right">
                <p class="text-[10px] font-black text-gray-300 uppercase mb-1">To</p>
                <p class="text-2xl font-black text-gray-800 tracking-tight">{{ $parts[1] ?: '--' }}</p>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between px-4 py-3 bg-gray-50 rounded-2xl border border-gray-100">
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-gray-400 uppercase mb-0.5">Train No. / Seat</span>
                <span class="text-sm font-black text-indigo-600 font-mono">{{ $flightInfo['train_no'] ?? 'Unassigned' }} / {{ $flightInfo['train_seat'] ?? '--' }}</span>
            </div>
            <div class="text-right flex flex-col">
                <span class="text-[9px] font-black text-gray-400 uppercase mb-0.5">Dep. Time</span>
                <span class="text-lg font-black text-gray-900 font-mono">{{ $flightInfo['outbound']['time'] ?: 'TBA' }}</span>
            </div>
        </div>
    </div>

    <!-- Return Ticket -->
    <div class="p-8 group/item hover:bg-white/40 transition-all duration-300">
        <div class="flex justify-between items-start mb-6">
            <span class="px-3 py-1.5 bg-gray-100 text-gray-500 rounded-lg text-[10px] font-black uppercase tracking-[0.2em] shadow-sm">
                {{ $theme['label_in'] }}
            </span>
            <div class="text-right">
                <p class="text-[9px] font-black text-gray-400 mb-0.5 uppercase tracking-widest">Return Date</p>
                <span class="text-sm font-black text-gray-900 font-mono">{{ $flightInfo['inbound']['date'] }}</span>
            </div>
        </div>

        <div class="flex items-center justify-between gap-4 py-4 border-y border-dashed border-gray-100">
            <div class="flex-1">
                <p class="text-[10px] font-black text-gray-300 uppercase mb-1">From</p>
                <p class="text-2xl font-black text-gray-800 tracking-tight">{{ $partsIn[0] }}</p>
            </div>
            <div class="text-gray-200 opacity-50 shrink-0 rotate-180">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
            </div>
            <div class="flex-1 text-right">
                <p class="text-[10px] font-black text-gray-300 uppercase mb-1">To</p>
                <p class="text-2xl font-black text-gray-800 tracking-tight">{{ $partsIn[1] ?: '--' }}</p>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between px-4 py-3 bg-gray-50 rounded-2xl border border-gray-100">
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-gray-400 uppercase mb-0.5">Return Info</span>
                <span class="text-sm font-black text-gray-600 font-mono">{{ $flightInfo['train_no'] ?? '--' }}</span>
            </div>
            <div class="text-right flex flex-col">
                <span class="text-[9px] font-black text-gray-400 uppercase mb-0.5">Dep. Time</span>
                <span class="text-lg font-black text-gray-900 font-mono">{{ $flightInfo['inbound']['time'] ?: 'TBA' }}</span>
            </div>
        </div>
    </div>
</div>
