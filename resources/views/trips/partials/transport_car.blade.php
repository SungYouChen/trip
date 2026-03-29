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
    <div class="p-8 group/item hover:bg-white/40 transition-all duration-300">
        <div class="flex justify-between items-start mb-6">
            <span class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg border border-blue-100 text-[10px] font-black uppercase tracking-[0.2em] shadow-sm">
                {{ $theme['label_out'] }}
            </span>
            <div class="text-right">
                <p class="text-[9px] font-black text-gray-400 mb-0.5 uppercase tracking-widest">Reservation ID</p>
                <span class="text-xs font-black text-gray-900 font-mono italic">RENT-{{ substr($trip->name, 0, 3) }}-{{ $flightInfo['outbound']['date'] }}</span>
            </div>
        </div>

        <div class="flex flex-col gap-2 mb-6">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Rental Location</p>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <p class="text-2xl font-black text-gray-800 tracking-tight">{{ $outRoute ?: 'TBA' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-gray-400 uppercase mb-0.5">Vehicle Type</span>
                <span class="text-sm font-black text-gray-900">{{ $flightInfo['car_model'] ?: 'Standard Sedan' }}</span>
            </div>
            <div class="text-right flex flex-col">
                <span class="text-[9px] font-black text-gray-400 uppercase mb-0.5">Pickup Time</span>
                <span class="text-lg font-black text-gray-900 font-mono">{{ $flightInfo['outbound']['time'] ?: 'Anytime' }}</span>
            </div>
        </div>
    </div>

    <!-- Drop-off Voucher -->
    <div class="p-8 group/item hover:bg-white/40 transition-all duration-300">
        <div class="flex justify-between items-start mb-6">
            <span class="px-3 py-1.5 bg-gray-100 text-gray-500 rounded-lg text-[10px] font-black uppercase tracking-[0.2em] shadow-sm">
                {{ $theme['label_in'] }}
            </span>
            <div class="text-right">
                <p class="text-[9px] font-black text-gray-400 mb-0.5 uppercase tracking-widest">Contract Status</p>
                <span class="text-[9px] px-2 py-0.5 bg-green-100 text-green-600 rounded-full font-bold">CONFIRMED</span>
            </div>
        </div>

        <div class="flex flex-col gap-2 mb-6">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Return Location</p>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                <p class="text-2xl font-black text-gray-800 tracking-tight">{{ $inRoute ?: 'TBA' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-gray-400 uppercase mb-0.5">Return Date</span>
                <span class="text-sm font-black text-gray-900 font-mono">{{ $flightInfo['inbound']['date'] }}</span>
            </div>
            <div class="text-right flex flex-col">
                <span class="text-[9px] font-black text-gray-400 uppercase mb-0.5">Drop-off Time</span>
                <span class="text-lg font-black text-gray-900 font-mono">{{ $flightInfo['inbound']['time'] ?: 'Anytime' }}</span>
            </div>
        </div>
    </div>
</div>
