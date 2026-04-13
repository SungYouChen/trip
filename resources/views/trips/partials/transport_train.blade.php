@php
    $outRoute = $flightInfo['outbound']['route'] ?? '';
    $hasArrow = str_contains($outRoute, ' ➝ ');
    $parts = $hasArrow ? explode(' ➝ ', $outRoute) : [$outRoute, ''];
    $inRoute = $flightInfo['inbound']['route'] ?? '';
    $hasArrowIn = str_contains($inRoute, ' ➝ ');
    $partsIn = $hasArrowIn ? explode(' ➝ ', $inRoute) : [$inRoute, ''];
@endphp

<!-- Train Ticket Specialized UI -->
<div class="relative grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-dashed divide-muji-edge">
    <!-- Decorative Notches -->
    <div class="absolute top-1/2 left-0 -translate-y-1/2 -translate-x-1/2 w-6 h-10 bg-muji-base rounded-r-full hidden md:block z-10 border-r border-muji-edge"></div>
    <div class="absolute top-1/2 right-0 -translate-y-1/2 translate-x-1/2 w-6 h-10 bg-muji-base rounded-l-full hidden md:block z-10 border-l border-muji-edge"></div>

    <!-- Departure Ticket -->
    <div class="p-8 group/item hover:bg-muji-base/40 transition-all duration-300">
        <div class="flex justify-between items-start mb-6">
            <span class="px-3 py-1.5 bg-muji-base text-muji-oak rounded-lg border border-muji-edge text-[10px] font-black uppercase tracking-[0.2em] shadow-muji-sm">
                {{ $theme['label_out'] }}
            </span>
            <div class="text-right">
                <p class="text-[9px] font-black text-muji-ash mb-0.5 uppercase tracking-widest">出發日期 Date</p>
                <span class="text-sm font-black text-muji-ink font-mono">{{ $flightInfo['outbound']['date'] }}</span>
            </div>
        </div>

        <div class="flex items-center justify-between gap-4 py-4 border-y border-dashed border-muji-edge">
            <div class="flex-1">
                <p class="text-[10px] font-black text-muji-ash uppercase mb-1">出發地 From</p>
                <p class="text-2xl font-black text-muji-ink tracking-tight">{{ $parts[0] }}</p>
            </div>
            <div class="text-muji-wheat opacity-50 shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
            </div>
            <div class="flex-1 text-right">
                <p class="text-[10px] font-black text-muji-ash uppercase mb-1">目的地 To</p>
                <p class="text-2xl font-black text-muji-ink tracking-tight">{{ $parts[1] ?: '--' }}</p>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between px-4 py-3 bg-muji-base/50 rounded-2xl border border-muji-edge">
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-muji-ash uppercase mb-0.5">車次 / 座位 No./Seat</span>
                <span class="text-sm font-black text-muji-oak font-mono">{{ $flightInfo['train_no'] ?? 'Unassigned' }} / {{ $flightInfo['train_seat'] ?? '--' }}</span>
            </div>
            <div class="text-right flex flex-col">
                <span class="text-[9px] font-black text-muji-ash uppercase mb-0.5">出發時間 Time</span>
                <span class="text-lg font-black text-muji-ink font-mono">{{ ($flightInfo['outbound']['time'] ?? ($flightInfo['outbound']['time_start'] ?? '')) ?: 'TBA' }}</span>
            </div>
        </div>
    </div>

    <!-- Return Ticket -->
    <div class="p-8 group/item hover:bg-muji-wheat/5 transition-all duration-300">
        <div class="flex justify-between items-start mb-6">
            <span class="px-3 py-1.5 bg-muji-base text-muji-ash rounded-lg border border-muji-edge text-[10px] font-black uppercase tracking-[0.2em] shadow-muji-sm">
                {{ $theme['label_in'] }}
            </span>
            <div class="text-right">
                <p class="text-[9px] font-black text-muji-ash mb-0.5 uppercase tracking-widest">回程日期 Date</p>
                <span class="text-sm font-black text-muji-ink font-mono">{{ $flightInfo['inbound']['date'] }}</span>
            </div>
        </div>

        <div class="flex items-center justify-between gap-4 py-4 border-y border-dashed border-muji-edge">
            <div class="flex-1">
                <p class="text-[10px] font-black text-muji-ash uppercase mb-1">返程起點 From</p>
                <p class="text-2xl font-black text-muji-ink tracking-tight">{{ $partsIn[0] }}</p>
            </div>
            <div class="text-muji-wheat opacity-50 shrink-0 rotate-180">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
            </div>
            <div class="flex-1 text-right">
                <p class="text-[10px] font-black text-muji-ash uppercase mb-1">返程終點 To</p>
                <p class="text-2xl font-black text-muji-ink tracking-tight">{{ $partsIn[1] ?: '--' }}</p>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between px-4 py-3 bg-muji-base/50 rounded-2xl border border-muji-edge">
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-muji-ash uppercase mb-0.5">回程資訊 Info</span>
                <span class="text-sm font-black text-muji-ash font-mono">{{ $flightInfo['train_no'] ?? '--' }}</span>
            </div>
            <div class="text-right flex flex-col">
                <span class="text-[9px] font-black text-muji-ash uppercase mb-0.5">發車時間 Time</span>
                <span class="text-lg font-black text-muji-ink font-mono">{{ ($flightInfo['inbound']['time'] ?? ($flightInfo['inbound']['time_start'] ?? '')) ?: 'TBA' }}</span>
            </div>
        </div>
    </div>
</div>
