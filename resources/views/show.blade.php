@php $isShared = $isShared ?? false; @endphp
@extends('layout')

@section('content')
    @php
        $showArchived = request('archived') == '1';
        $scheduleToShow = $showArchived
            ? array_filter($day['schedule'], fn ($e) => $e['trashed'])
            : array_filter($day['schedule'], fn ($e) => !$e['trashed']);
        $expensesToShow = $showArchived
            ? $expenses->filter(fn ($ex) => $ex->trashed())
            : $expenses->filter(fn ($ex) => !$ex->trashed());
    @endphp

    <div class="flex justify-between items-center mb-4">
        @php
            $returnLink = $isShared
                ? route('trip.index_shared', ['token' => $trip->share_token])
                : route('trip.show', ['user' => $trip->user, 'trip' => $trip]);
        @endphp
        <a href="{{ $returnLink }}" class="inline-flex items-center text-sm font-medium text-muji-oak hover:underline transition-colors gap-1">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7" />
            </svg>
            返回
        </a>

        @if(!$isShared)
            @auth
                <a href="{{ request()->fullUrlWithQuery(['archived' => $showArchived ? '0' : '1']) }}" class="flex items-center gap-2 text-xs font-black px-3 py-1.5 rounded-full border border-muji-edge transition-all {{ $showArchived ? 'bg-muji-base text-muji-oak' : 'bg-muji-base/50 text-muji-ash hover:bg-muji-wheat/20' }}">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12M10 12v4m4-4v4" />
                    </svg>
                    {{ $showArchived ? '隱藏封存' : '查看封存' }}
                </a>
            @endauth
        @endif
    </div>

    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <div>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-muji-wheat text-muji-oak mb-2">
                        {{ $day['date'] }} ({{ $day['day'] }})
                    </span>
                    <div class="flex items-center gap-3 @auth cursor-pointer hover:opacity-80 transition-all @endauth" @auth onclick="safeOpenModal('daySummaryEditModal')" @endauth>
                        <h1 class="text-3xl font-black text-muji-ink">{{ $day['title'] }}</h1>
                    </div>
                    @if($day['summary'])
                        <p class="text-lg text-muji-ash mt-1">{{ $day['summary'] }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <span class="text-2xl font-black text-muji-oak uppercase tracking-widest">{{ $day['location'] }}</span>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Sidebar Info -->
            <div class="lg:col-span-1 space-y-6">
                @if($day['accommodation'])
                    <div class="muji-card p-6 border-muji-edge">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-muji-ash text-[10px] font-bold uppercase tracking-widest">住宿資訊</h3>
                            @if(!$isShared)
                                @auth
                                    <button type="button" onclick="safeOpenModal('daySummaryEditModal')" class="p-1 text-muji-oak hover:opacity-70 transition-colors" title="編輯住宿與日誌">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                @endauth
                            @endif
                        </div>
                        <h2 class="text-xl font-bold text-muji-ink mb-2">{{ $day['accommodation']['name'] }}</h2>
                        <div class="flex items-start gap-2 text-muji-ash mb-4 cursor-pointer hover:text-muji-oak transition-colors" onclick="openMap('{{ addslashes($day['accommodation']['address']) }}')">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-sm leading-relaxed">{{ $day['accommodation']['address'] }}</span>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between border-b border-muji-edge pb-2">
                                <span class="text-muji-ash font-bold">入住時間</span>
                                <span class="font-black text-muji-ink">{{ $day['accommodation']['check_in'] ?? '15:00' }}</span>
                            </div>
                            @if(isset($day['accommodation']['price']))
                                <div class="flex justify-between border-b border-muji-edge pb-2">
                                    <span class="text-muji-ash font-bold">預訂價格</span>
                                    <span class="font-black text-muji-ink">{{ $day['accommodation']['price'] }}</span>
                                </div>
                            @endif
                            @if(isset($day['accommodation']['note']))
                                <div class="pt-2">
                                    <p class="text-muji-ash italic font-medium">{{ $day['accommodation']['note'] }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Main Timeline -->
            <div class="lg:col-span-2">
                <div class="muji-card p-6 border-muji-edge">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-muji-ash text-[10px] font-bold uppercase tracking-widest">每日行程</h3>
                        @if(!$isShared)
                            @auth
                                <button type="button" onclick="openEventModal()" class="flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 bg-muji-wheat text-muji-oak rounded-full hover:opacity-80 transition-all border border-muji-edge shadow-muji-sm active:scale-95">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    新增活動
                                </button>
                            @endauth
                        @endif
                    </div>
                    <div class="relative pl-8 border-l border-muji-edge space-y-10">
                        @php
                            $now = \Carbon\Carbon::now('Asia/Tokyo');
                            $today = $now->format('Y-m-d');
                            $schedule = array_values($scheduleToShow);
                            $count = count($schedule);

                            for ($i = 0; $i < $count; $i++) {
                                $event = $schedule[$i];
                                $nextEvent = ($i + 1 < $count) ? $schedule[$i + 1] : null;
                                $isArchived = $event['trashed'] ?? false;
                                $status = 'future';

                                // Check Date first
                                if ($currentDate < $today) {
                                    $status = 'past';
                                } elseif ($currentDate > $today) {
                                    $status = 'future';
                                } else {
                                    // It's Today, check time
                                    if (str_contains($event['time'], '✅')) {
                                        $status = 'info';
                                    } elseif (preg_match('/(\d{1,2}:\d{2})/', $event['time'], $matches)) {
                                        $startTime = \Carbon\Carbon::parse($matches[1], 'Asia/Tokyo');
                                        $endTime = null;

                                        // 1. Check if range is explicitly defined "09:00 – 10:40"
                                        if (preg_match('/[–-]\s*(\d{1,2}:\d{2})/', $event['time'], $endMatches)) {
                                            $endTime = \Carbon\Carbon::parse($endMatches[1], 'Asia/Tokyo');
                                        }
                                        // 2. If no explicit end time, use Next Event's Start Time
                                        elseif ($nextEvent && preg_match('/(\d{1,2}:\d{2})/', $nextEvent['time'], $nextMatches)) {
                                            $endTime = \Carbon\Carbon::parse($nextMatches[1], 'Asia/Tokyo');
                                            if ($endTime->lt($startTime)) {
                                                $endTime->addDay();
                                            }
                                        }
                                        // 3. Fallback: Add 1.5 hours
                                        else {
                                            if (str_contains($event['time'], '之後')) {
                                                $endTime = $startTime->copy()->endOfDay();
                                            } else {
                                                $endTime = $startTime->copy()->addMinutes(90);
                                            }
                                        }

                                        if ($now->gt($endTime)) {
                                            $status = 'past';
                                        } elseif ($now->lt($startTime)) {
                                            $status = 'future';
                                        } else {
                                            $status = 'present';
                                        }
                                    }
                                }

                                // Define Styles
                                $containerClass = 'relative group transition-all duration-500 rounded-2xl p-4 -ml-4';
                                if ($isArchived) {
                                    $containerClass .= ' border-2 border-dashed border-red-300 bg-red-50/30 opacity-60';
                                }

                                $dotClass = 'absolute top-[22px] w-4 h-4 rounded-full border-2 z-10 transition-all duration-500';
                                $textClass = 'font-mono text-sm font-medium min-w-[100px] flex-shrink-0 transition-colors duration-500';
                                $titleClass = 'text-lg transition-colors duration-500';
                                $contentClass = 'flex-grow transition-opacity duration-500';

                                // Horizontally center on the border-l (at -16px from container edge)
                                // Dot width is 16px (w-4), center is 8px, so -16 - 8 = -24px
                                if ($isArchived) {
                                    $dotClass .= ' bg-muji-ash border-muji-edge -left-[24px] ';
                                    $textClass .= ' text-muji-ash';
                                    $titleClass .= ' font-black text-muji-ash';
                                } elseif ($status === 'past') {
                                    $containerClass .= ' opacity-40 grayscale';
                                    $dotClass .= ' bg-muji-base border-muji-edge -left-[24px] ';
                                    $textClass .= ' text-muji-ash';
                                    $titleClass .= ' font-black text-muji-ink';
                                } elseif ($status === 'present') {
                                    $containerClass .= ' bg-muji-wheat/10 scale-[1.02] shadow-muji ring-1 ring-muji-oak';
                                    $dotClass .= ' bg-muji-oak border-white shadow-[0_0_0_4px_rgba(156,140,124,0.1)] -left-[24px] scale-125';
                                    $textClass .= ' text-muji-oak font-black';
                                    $titleClass .= ' font-black text-muji-ink';
                                } else {
                                    $dotClass .= ' bg-muji-paper border-muji-edge -left-[24px] ';
                                    $textClass .= ' text-muji-ash';
                                    $titleClass .= ' font-black text-muji-ink group-hover:text-muji-oak transition-colors';
                                }
                        @endphp
                        <div class="{{ $containerClass }}">
                            <div class="{{ $dotClass }}"></div>

                            <div class="flex flex-col sm:flex-row sm:items-baseline gap-2 mb-2">
                                <span class="{{ $textClass }}">{{ $event['time'] }}</span>

                                <div class="{{ $contentClass }}">
                                    <div class="flex items-center justify-between gap-4">
                                        <h4 class="{{ $titleClass }}">
                                            <button onclick="openMap('{{ addslashes($event['map_query'] ?? $event['activity']) }}')" class="hover:underline decoration-muji-wheat decoration-2 text-left">
                                                {{ $event['activity'] }}
                                            </button>
                                            @if($isArchived)
                                                <span class="ml-2 text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-bold">已封存</span>
                                            @endif
                                        </h4>
                                        @if(!$isShared)
                                            @auth
                                                <div class="flex items-center gap-1 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity">
                                                    @if($isArchived)
                                                        @php $restoreEventId = 'restore-event-' . $event['id']; @endphp
                                                        <form id="{{ $restoreEventId }}" action="{{ route('events.restore', ['user' => $trip->user, 'eventId' => $event['id']]) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="button" onclick="confirmAction('還原行程活動？', '確定要將「{{ $event['activity'] }}」移回行程嗎？', '{{ $restoreEventId }}')" class="tooltip tooltip-bottom p-1 text-green-500 hover:bg-green-50 rounded transition-colors" data-tip="還原">
                                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                                </svg>
                                                            </button>
                                                        </form>

                                                        @php $forceEventId = 'force-event-' . $event['id']; @endphp
                                                        <form id="{{ $forceEventId }}" action="{{ route('events.forceDelete', ['user' => $trip->user, 'eventId' => $event['id']]) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" onclick="confirmDelete('永久刪除活動？', '此動作無法復原！確定要永久刪除「{{ $event['activity'] }}」嗎？', '{{ $forceEventId }}')" class="tooltip tooltip-bottom p-1 text-red-600 hover:bg-red-100 rounded transition-colors" data-tip="永久刪除">
                                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @else
                                                        @php $eventDelFormId = 'del-event-' . $event['id']; @endphp
                                                        <div class="flex items-center gap-1">
                                                            <button onclick="openEventModal({{ json_encode($event) }})" class="tooltip tooltip-bottom p-1 text-muji-ash hover:text-muji-oak hover:bg-muji-base rounded transition-colors" data-tip="編輯">
                                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                                </svg>
                                                            </button>
                                                            <form id="{{ $eventDelFormId }}" action="{{ route('events.destroy', ['user' => $trip->user, 'event' => $event['id']]) }}" method="POST" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" onclick="confirmDelete('封存行程活動？', '確定要將「{{ $event['activity'] }}」移至回收桶嗎？', '{{ $eventDelFormId }}')" class="tooltip tooltip-bottom p-1 text-slate-300 hover:text-red-400 hover:bg-red-50 rounded transition-colors" data-tip="封存">
                                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endauth
                                        @endif
                                    </div>

                                    @if(isset($event['sub_activities']) && is_array($event['sub_activities']))
                                        <ul class="mt-2 space-y-1">
                                            @foreach($event['sub_activities'] as $sub)
                                                <li class="flex items-center gap-2 text-muji-ash text-sm">
                                                    <svg class="w-3 h-3 text-muji-wheat" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <button onclick="openMap('{{ addslashes($sub) }}')" class="hover:text-muji-oak hover:underline text-left">
                                                        {{ $sub }}
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    @if($event['note'])
                                        <div class="text-sm text-muji-ash mt-2 bg-muji-base/50 p-2 rounded border border-muji-edge inline-block italic font-medium">
                                            {{ $event['note'] }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @php } @endphp
                    </div>
                </div>

                @if(isset($expenses) && $expenses->isNotEmpty())
                    <div class="mt-8 pt-8 border-t border-muji-edge">
                        <h3 class="text-xl font-black text-muji-ink mb-4 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-muji-oak" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                @php
                                    $dayActiveExpenses = $expenses->filter(fn ($ex) => !$ex->trashed());
                                    $dayTotalBase = 0;
                                    foreach ($dayActiveExpenses as $ex) {
                                        $dayTotalBase += $ex->is_base_currency ? $ex->amount : ($ex->amount * $trip->exchange_rate);
                                    }
                                @endphp
                                {{ $showArchived ? '已封存花費' : '今日花費' }}
                                @if(!$showArchived)
                                    <span class="text-[10px] font-black text-muji-ash ml-2 hidden sm:inline tracking-widest uppercase">(約 {{ $trip->base_currency }} {{ number_format($dayTotalBase) }})</span>
                                @endif
                            </div>

                            @if(!$isShared)
                                @auth
                                    <button onclick="openExpenseModalWithDate('{{ $currentDate }}')" class="flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 bg-muji-paper text-muji-oak rounded-full hover:bg-muji-base transition-all border border-muji-edge shadow-muji-sm active:scale-95">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                        新增花費
                                    </button>
                                @endauth
                            @endif
                        </h3>

                        @if($expensesToShow->isEmpty())
                            <div class="text-center py-12 bg-muji-base/30 rounded-2xl border-2 border-dashed border-muji-edge">
                                <p class="text-muji-ash font-medium italic">目前沒有{{ $showArchived ? '封存的' : '' }}項目</p>
                            </div>
                        @else
                            <div class="grid md:grid-cols-2 gap-4">
                                @foreach($expensesToShow as $expense)
                                    @php $exIsTrashed = $expense->trashed(); @endphp
                                    <div class="muji-card p-4 border border-muji-edge hover:shadow-muji transition-shadow relative group">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex items-center gap-2">
                                                @if($exIsTrashed)
                                                    <span class="p-1.5 bg-red-100 text-red-600 rounded-lg">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </span>
                                                @else
                                                    <span class="p-1.5 bg-muji-base text-muji-oak rounded-lg shadow-muji-sm">
                                                        @php
                                                            $icons = [
                                                                'food' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5',
                                                                'transport' => 'M8 7h12M4 17h16',
                                                                'shopping' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14',
                                                            ];
                                                            $iconPath = $icons[$expense->category] ?? 'M20 7l-8-4-8 4m16 0l-8 4';
                                                        @endphp
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}" />
                                                        </svg>
                                                    </span>
                                                @endif
                                                <div>
                                                    <h4 class="font-black {{ $exIsTrashed ? 'text-red-800 underline' : 'text-muji-ink' }}">{{ $expense->description }}</h4>
                                                    <p class="text-[10px] text-muji-ash capitalize font-black tracking-widest">{{ $expense->category }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-black {{ $exIsTrashed ? 'text-red-600' : 'text-muji-ink' }}">{{ $expense->currency }} {{ number_format($expense->amount) }}</div>
                                                @if(!$expense->is_base_currency)
                                                    <p class="text-[10px] text-muji-ash font-medium italic">≈ {{ $trip->base_currency }} {{ number_format($expense->amount * $trip->exchange_rate) }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        @if(!$isShared)
                                            @auth
                                                <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    @if($exIsTrashed)
                                                        @php $restoreExId = 'restore-ex-' . $expense->id; @endphp
                                                        <form id="{{ $restoreExId }}" action="{{ route('expenses.restore', ['user' => $trip->user, 'expenseId' => $expense->id]) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="button" onclick="confirmAction('還原花費項目？', '確定要將「{{ $expense->description }}」移回花費清單嗎？', '{{ $restoreExId }}')" class="p-1 text-green-500 hover:bg-green-50 rounded transition-colors" title="還原">
                                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                                </svg>
                                                            </button>
                                                        </form>

                                                        @php $forceExId = 'force-ex-' . $expense->id; @endphp
                                                        <form id="{{ $forceExId }}" action="{{ route('expenses.forceDelete', ['user' => $trip->user, 'expenseId' => $expense->id]) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" onclick="confirmDelete('永久刪除花費？', '此動作無法復原！確定要永久刪除「{{ $expense->description }}」嗎？', '{{ $forceExId }}')" class="p-1 text-red-600 hover:bg-red-100 rounded transition-colors" title="永久刪除">
                                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @else
                                                        @php $exDelId = 'del-ex-' . $expense->id; @endphp
                                                        <form id="{{ $exDelId }}" action="{{ route('expenses.destroy', ['user' => $trip->user, 'expense' => $expense->id]) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" onclick="confirmDelete('封存花費項目？', '確定要將「{{ $expense->description }}」移至回收桶嗎？', '{{ $exDelId }}')" class="p-1 text-red-400 hover:bg-red-50 rounded transition-colors" title="封存">
                                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @endauth
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @push('modals')
            @auth
                <!-- Day Edit Modal -->
                <div id="daySummaryEditModal" class="fixed inset-0 z-[2000] hidden overflow-y-auto" role="dialog" aria-modal="true">
                    <div class="flex min-h-full items-center justify-center p-4 text-center">
                        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="safeCloseModal('daySummaryEditModal')"></div>
                        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl flex flex-col max-h-[90vh]">
                            <div class="px-8 py-10 overflow-y-auto custom-scrollbar scroll-smooth">
                                <div class="flex justify-between items-start mb-10">
                                    <div class="flex items-center gap-4">
                                        <div class="p-3 bg-muji-base rounded-2xl text-muji-oak shadow-muji-sm">
                                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </div>
                                        <div class="flex flex-col border-l-2 border-muji-edge pl-4">
                                            <h3 class="text-2xl font-black text-muji-ink leading-tight">編輯日誌摘要</h3>
                                            <p class="text-[10px] font-bold text-muji-ash uppercase tracking-[0.2em] mt-1">完善您的每日旅程日誌</p>
                                        </div>
                                    </div>
                                    <button onclick="safeCloseModal('daySummaryEditModal')" class="text-muji-ash hover:text-muji-ink p-2 rounded-full hover:bg-muji-base transition-all">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <form action="{{ route('day.update', ['user' => $trip->user, 'trip' => $trip, 'date' => request()->route('date')]) }}" method="POST" class="space-y-4">
                                    @csrf
                                    @method('PUT')
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-bold text-muji-ash text-left mb-2">今日主題</label>
                                            <input type="text" name="title" value="{{ $day['title'] }}" placeholder="例如：京都清晨散策" class="w-full px-4 py-3 bg-white border border-muji-edge rounded-xl focus:ring-2 focus:ring-muji-oak text-muji-ink shadow-muji-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-muji-ash text-left mb-2">所在城市/區域</label>
                                            <input type="text" name="location" value="{{ $day['location'] == $trip->name ? '' : $day['location'] }}" placeholder="例如：大阪" class="w-full px-4 py-3 bg-white border border-muji-edge rounded-xl focus:ring-2 focus:ring-muji-oak text-muji-ink shadow-muji-sm">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-muji-ash text-left mb-2">今日摘要</label>
                                        <textarea name="summary" rows="2" placeholder="例如：漫步在道頓堀的街頭，享受章魚燒與大阪燒的美味。" class="w-full px-4 py-3 bg-white border border-muji-edge rounded-xl focus:ring-2 focus:ring-muji-oak text-muji-ink shadow-muji-sm">{{ $day['summary'] }}</textarea>
                                    </div>
                                    <div class="pt-6 border-t border-muji-edge mt-6">
                                        <h4 class="text-xs font-black text-muji-oak uppercase tracking-[0.2em] flex items-center gap-2 mb-4 bg-muji-base/50 self-start px-3 py-1.5 rounded-lg border border-muji-edge">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                            </svg>
                                            住宿資訊
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="sm:col-span-2">
                                                <label class="block text-sm font-bold text-muji-ash text-left mb-2">飯店名稱</label>
                                                <input type="text" name="hotel_name" value="{{ $day['accommodation']['name'] ?? '' }}" placeholder="例如：大阪難波格拉斯麗飯店" class="w-full px-4 py-3 bg-white border border-muji-edge rounded-xl shadow-muji-sm text-muji-ink focus:ring-2 focus:ring-muji-oak transition-all">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="block text-sm font-bold text-muji-ash text-left mb-2">飯店地址</label>
                                                <input type="text" name="hotel_address" value="{{ $day['accommodation']['address'] ?? '' }}" placeholder="例如：大阪市浪速區元町1-4-4" class="w-full px-4 py-3 bg-white border border-muji-edge rounded-xl shadow-muji-sm text-muji-ink focus:ring-2 focus:ring-muji-oak transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-bold text-muji-ash text-left mb-2">預訂價格</label>
                                                <div class="flex relative rounded-xl border border-muji-edge overflow-hidden focus-within:ring-2 focus-within:ring-muji-oak bg-white shadow-muji-sm transition-all">
                                                    <select name="hotel_currency" class="bg-muji-base border-0 border-r border-muji-edge px-3 py-3 text-muji-ink font-black text-xs focus:ring-0 cursor-pointer appearance-none">
                                                        @php
                                                            $rawPrice = $day['accommodation']['price'] ?? '';
                                                            preg_match('/^([^\d]+)?([\d,.]+)/u', $rawPrice, $m);
                                                            $savedCurrency = trim($m[1] ?? $trip->base_currency);
                                                            $savedNum = isset($m[2]) ? str_replace(',', '', $m[2]) : '';
                                                        @endphp
                                                        <option value="{{ $trip->base_currency }}" {{ $savedCurrency == $trip->base_currency ? 'selected' : '' }}>{{ $trip->base_currency }}</option>
                                                        <option value="{{ $trip->target_currency }}" {{ $savedCurrency == $trip->target_currency ? 'selected' : '' }}>{{ $trip->target_currency }}</option>
                                                    </select>
                                                    <input type="number" step="0.01" name="hotel_price_num" value="{{ $savedNum }}" class="flex-1 w-full border-0 bg-transparent focus:ring-0 px-4 py-3 outline-none font-mono text-muji-ink font-black" placeholder="例如：16092">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-bold text-muji-ash text-left mb-2">入住時間</label>
                                                <input type="time" name="hotel_checkin" value="{{ $day['accommodation']['check_in'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-muji-edge rounded-xl shadow-muji-sm text-muji-ink focus:ring-2 focus:ring-muji-oak">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="block text-sm font-bold text-muji-ash text-left mb-2">住宿備註</label>
                                                <input type="text" name="hotel_note" value="{{ $day['accommodation']['note'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-muji-edge rounded-xl shadow-muji-sm text-muji-ink focus:ring-2 focus:ring-muji-oak" placeholder="例如：連住 4 晚">
                                            </div>
                                        </div> {{-- End of grid from line 481 --}}
                                    </div> {{-- End of pt-6 border-t from line 474 (CRITICAL FIX) --}}
                                    <div class="pt-6 mt-8 border-t border-muji-edge flex gap-4">
                                        <button type="button" onclick="safeCloseModal('daySummaryEditModal')" class="flex-1 py-4 bg-muji-base text-muji-ash border border-muji-edge font-black rounded-2xl hover:bg-muji-wheat/20 transition-all active:scale-95 text-center text-sm">取消</button>
                                        <button type="submit" class="flex-1 py-4 bg-muji-oak text-white font-black rounded-2xl hover:opacity-90 shadow-muji transition-all active:scale-95 text-center text-sm">儲存設定</button>
                                    </div>
                                </form>
                            </div> {{-- px-8 --}}
                        </div> {{-- relative --}}
                    </div> {{-- flex --}}
                </div> {{-- id --}}

                <!-- Event Edit Modal -->
                <div id="eventDetailsModal" class="fixed inset-0 z-[2000] hidden overflow-y-auto" role="dialog" aria-modal="true">
                    <div class="flex min-h-full items-center justify-center p-4 text-center">
                        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="safeCloseModal('eventDetailsModal')"></div>
                        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl flex flex-col max-h-[90vh]">
                            <div class="px-8 py-10 overflow-y-auto custom-scrollbar scroll-smooth">
                                <div class="flex justify-between items-start mb-10">
                                    <div class="flex items-center gap-4">
                                        <div class="p-3 bg-muji-base rounded-2xl text-muji-oak shadow-muji-sm">
                                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="flex flex-col border-l-2 border-muji-edge pl-4">
                                            <h3 id="eventModalTitle" class="text-2xl font-black text-muji-ink leading-tight">行程活動</h3>
                                            <p class="text-[10px] font-bold text-muji-ash uppercase tracking-[0.2em] mt-1">規劃您的行程細節</p>
                                        </div>
                                    </div>
                                    <button onclick="safeCloseModal('eventDetailsModal')" class="text-muji-ash hover:text-muji-ink p-2 rounded-full hover:bg-muji-base transition-all">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <form id="eventForm" action="{{ route('events.store', ['user' => $trip->user, 'trip' => $trip, 'date' => request()->route('date')]) }}" method="POST" class="space-y-4" autocomplete="off">
                                    @csrf
                                    <div id="eventMethod"></div>
                                    <div class="space-y-6">
                                        <div>
                                            <label class="block text-sm font-bold text-muji-ash text-left mb-2">活動時間 (例如: 09:00 - 10:30)</label>
                                            <input type="text" name="time" id="event_time" required placeholder="例如：09:00 - 10:30" class="block w-full px-4 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-muji-ash text-left mb-2">活動名稱 / 地點</label>
                                            <input type="text" name="activity" id="event_activity" required placeholder="例如：道頓堀散策" class="block w-full px-4 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-muji-ash text-left mb-2">細分目的地 (逗號隔開)</label>
                                            <input type="text" name="sub_activities" id="event_subs" placeholder="例如：買藥妝、吃拉麵" class="block w-full px-4 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-muji-ash text-left mb-2">行程備註 / 說明</label>
                                            <textarea name="note" id="event_note" rows="2" placeholder="例如：從 3 號出口出來右轉即達" class="block w-full px-4 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium"></textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-muji-ash text-left mb-2">地圖關鍵字 (若不同於活動名稱)</label>
                                            <input type="text" name="map_query" id="event_map" placeholder="例如：道頓堀固力果廣告牌" class="block w-full px-4 py-3 bg-white border border-muji-edge rounded-xl text-muji-ink shadow-muji-sm focus:ring-2 focus:ring-muji-oak transition-all font-medium">
                                        </div>
                                    </div>
                                    <div class="flex gap-4 pt-6 mt-8 border-t border-muji-edge">
                                        <button type="button" onclick="safeCloseModal('eventDetailsModal')" class="flex-1 py-4 bg-muji-base text-muji-ash border border-muji-edge font-black rounded-2xl hover:bg-muji-wheat/20 transition-all active:scale-95 text-center text-sm">取消</button>
                                        <button type="submit" class="flex-1 py-4 bg-muji-oak text-white font-black rounded-2xl hover:opacity-90 shadow-muji transition-all active:scale-95 text-center text-sm">儲存活動</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endauth
        @endpush

        <script>
            /**
             * Open the Event Modal
             * @param {Object|null} event - The event data to edit, or null to create new
             */
            function openEventModal(event = null) {
                console.log('Opening Event Modal...', event);
                const modal = document.getElementById('eventDetailsModal');
                if (!modal) {
                    console.error('Modal "eventDetailsModal" not found in DOM.');
                    return;
                }

                const title = document.getElementById('eventModalTitle');
                const form = document.getElementById('eventForm');
                const methodDiv = document.getElementById('eventMethod');

                if (!form) {
                    console.error('Form "eventForm" not found in modal.');
                    return;
                }

                if (event) {
                    if (title) title.innerText = '編輯行程活動';
                    // Use the username from the trip object (ensure it's properly escaped)
                    form.action = `/{{ $trip->user->username }}/events/${event.id}`;
                    if (methodDiv) methodDiv.innerHTML = '@method("PUT")';

                    // Populate fields safely
                    const setVal = (id, val) => {
                        const el = document.getElementById(id);
                        if (el) el.value = val || '';
                    };

                    setVal('event_time', event.time);
                    setVal('event_activity', event.activity);
                    setVal('event_subs', Array.isArray(event.sub_activities) ? event.sub_activities.join(', ') : (event.sub_activities || ''));
                    setVal('event_note', event.note);
                    setVal('event_map', event.map_query);
                } else {
                    if (title) title.innerText = '新增行程活動';
                    form.action = "{{ route('events.store', ['user' => $trip->user, 'trip' => $trip, 'date' => request()->route('date') ?? (isset($currentDate) ? $currentDate : '')]) }}";
                    if (methodDiv) methodDiv.innerHTML = '';
                    form.reset();
                }

                safeOpenModal('eventDetailsModal');
            }

            function closeEventModal() {
                safeCloseModal('eventDetailsModal');
            }
        </script>
@endsection