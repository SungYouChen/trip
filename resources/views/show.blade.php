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
                <div class="flex items-center gap-3 mb-2 flex-wrap">
                    <span class="inline-block px-3 py-1.5 rounded-full text-xs font-bold bg-muji-wheat text-muji-oak">
                        {{ $day['date'] }} ({{ $day['day'] }})
                    </span>
                    @if($day['date_obj'] && $day['date_obj']->isBetween(now()->subDays(1), now()->addDays(15)))
                    <div class="weather-indicator tooltip-bottom flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-black bg-muji-base border border-muji-edge shadow-muji-sm" 
                         data-date="{{ $day['date_obj']->format('Y-m-d') }}" 
                         data-location="{{ $day['location'] }}"
                         data-tooltip="氣象同步中..">
                        <span class="text-muji-oak uppercase tracking-widest mr-1">{{ $day['location'] }}</span>
                        <div class="weather-icon flex items-center justify-center min-w-[14px]"><span class="animate-pulse">◌</span></div>
                        <span class="weather-temp font-black text-muji-ink">-- / --°C</span>
                    </div>
                    @endif
                </div>
                <div class="flex items-center gap-3 @if(!$isShared) @auth cursor-pointer hover:opacity-80 transition-all @endauth @endif" @if(!$isShared) @auth onclick="safeOpenModal('daySummaryEditModal')" @endauth @endif>
                    <h1 class="text-3xl font-black text-muji-ink">{{ $day['title'] }}</h1>
                </div>
                @if($day['summary'])
                    <p class="text-lg text-muji-ash mt-1">{{ $day['summary'] }}</p>
                @endif
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
                                <button type="button" onclick="safeOpenModal('daySummaryEditModal')" class="p-1 text-muji-oak hover:opacity-70 transition-colors" data-tooltip="編輯住宿與日誌">
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
                        <div class="flex flex-col">
                            <span class="text-sm leading-relaxed">{{ $day['accommodation']['address'] }}</span>
                        </div>
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
                            <div class="flex gap-2">
                                <button type="button" onclick="openMapViewModal()" class="flex items-center justify-center w-[46px] h-[46px] bg-muji-base text-muji-oak rounded-2xl hover:opacity-80 transition-all border border-muji-edge shadow-muji-sm active:scale-95 tooltip-bottom" data-tooltip="地圖模式">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                                <button type="button" onclick="openEventModal()" class="flex items-center justify-center w-[46px] h-[46px] bg-muji-wheat text-muji-oak rounded-2xl hover:opacity-80 transition-all border border-muji-edge shadow-muji-sm active:scale-95 tooltip-bottom" data-tooltip="新增活動">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                            </div>
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

                            if ($currentDate < $today) {
                                $status = 'past';
                            } elseif ($currentDate > $today) {
                                $status = 'future';
                            } else {
                                if (str_contains($event['time'], '✅')) {
                                    $status = 'info';
                                } elseif (preg_match('/(\d{1,2}:\d{2})/', $event['time'], $matches)) {
                                    $startTime = \Carbon\Carbon::parse($matches[1], 'Asia/Tokyo');
                                    $endTime = null;
                                    if (preg_match('/[–-]\s*(\d{1,2}:\d{2})/', $event['time'], $endMatches)) {
                                        $endTime = \Carbon\Carbon::parse($endMatches[1], 'Asia/Tokyo');
                                    } elseif ($nextEvent && preg_match('/(\d{1,2}:\d{2})/', $nextEvent['time'], $nextMatches)) {
                                        $endTime = \Carbon\Carbon::parse($nextMatches[1], 'Asia/Tokyo');
                                        if ($endTime->lt($startTime)) $endTime->addDay();
                                    } else {
                                        if (str_contains($event['time'], '之後')) {
                                            $endTime = $startTime->copy()->endOfDay();
                                        } else {
                                            $endTime = $startTime->copy()->addMinutes(90);
                                        }
                                    }
                                    if ($now->gt($endTime)) $status = 'past';
                                    elseif ($now->lt($startTime)) $status = 'future';
                                    else $status = 'present';
                                }
                            }

                            $containerClass = 'relative group transition-all duration-500 rounded-2xl p-4 -ml-4';
                            if ($isArchived) $containerClass .= ' border-2 border-dashed border-red-300 bg-red-50/30 opacity-60';
                            $dotClass = 'absolute top-[22px] w-4 h-4 rounded-full border-2 z-10 transition-all duration-500';
                            $textClass = 'font-mono text-sm font-medium min-w-[100px] flex-shrink-0 transition-colors duration-500';
                            $titleClass = 'text-lg transition-colors duration-500';
                            $contentClass = 'flex-grow transition-opacity duration-500';

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
                                                        <button type="button" onclick="confirmAction('還原行程活動？', '確定要將「{{ $event['activity'] }}」移回行程嗎？', '{{ $restoreEventId }}')" class="tooltip tooltip-bottom p-1 text-green-500 hover:bg-green-50 rounded transition-colors" data-tooltip="還原">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                    @php $forceEventId = 'force-event-' . $event['id']; @endphp
                                                    <form id="{{ $forceEventId }}" action="{{ route('events.forceDelete', ['user' => $trip->user, 'eventId' => $event['id']]) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" onclick="confirmDelete('永久刪除活動？', '此動作無法復原！確定要永久刪除「{{ $event['activity'] }}」嗎？', '{{ $forceEventId }}')" class="tooltip tooltip-bottom p-1 text-red-600 hover:bg-red-100 rounded transition-colors" data-tooltip="永久刪除">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @else
                                                    @php $eventDelFormId = 'del-event-' . $event['id']; @endphp
                                                    <div class="flex items-center gap-1">
                                                        <button onclick="openEventModal({{ json_encode($event) }})" class="tooltip tooltip-bottom p-1 text-muji-ash hover:text-muji-oak hover:bg-muji-base rounded transition-colors" data-tooltip="編輯">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                            </svg>
                                                        </button>
                                                        <form id="{{ $eventDelFormId }}" action="{{ route('events.destroy', ['user' => $trip->user, 'event' => $event['id']]) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" onclick="confirmDelete('封存行程活動？', '確定要將「{{ $event['activity'] }}」移至回收桶嗎？', '{{ $eventDelFormId }}')" class="tooltip tooltip-bottom p-1 text-slate-300 hover:text-red-400 hover:bg-red-50 rounded transition-colors" data-tooltip="封存">
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
                                                        $icons = ['food' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5', 'transport' => 'M8 7h12M4 17h16', 'shopping' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14'];
                                                        $iconPath = $icons[$expense->category] ?? 'M20 7l-8-4-8 4m16 0l-8 4';
                                                    @endphp
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}" />
                                                    </svg>
                                                </span>
                                            @endif
                                            <div>
                                                <h4 class="font-black {{ $exIsTrashed ? 'text-red-800 underline' : 'text-muji-ink' }}">{{ $expense->description }}</h4>
                                                @php $catMap = ['food' => '飲食', 'transport' => '交通', 'shopping' => '購物', 'entertainment' => '遊玩', 'hotel' => '住宿', 'other' => '其他']; @endphp
                                                <p class="text-[10px] text-muji-ash capitalize font-black tracking-widest">{{ $catMap[$expense->category] ?? $expense->category }}</p>
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
                                                        <button type="button" onclick="confirmAction('還原花費項目？', '確定要將「{{ $expense->description }}」移回花費清單嗎？', '{{ $restoreExId }}')" class="p-1 text-green-500 hover:bg-green-50 rounded transition-colors" data-tooltip="還原內容">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                                                        </button>
                                                    </form>
                                                    @php $forceExId = 'force-ex-' . $expense->id; @endphp
                                                    <form id="{{ $forceExId }}" action="{{ route('expenses.forceDelete', ['user' => $trip->user, 'expenseId' => $expense->id]) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" onclick="confirmDelete('永久刪除花費？', '此動作無法復原！確定要永久刪除「{{ $expense->description }}」嗎？', '{{ $forceExId }}')" class="p-1 text-red-600 hover:bg-red-100 rounded transition-colors" data-tooltip="永久刪除">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                        </button>
                                                    </form>
                                                @else
                                                    @php $exDelId = 'del-ex-' . $expense->id; @endphp
                                                    <form id="{{ $exDelId }}" action="{{ route('expenses.destroy', ['user' => $trip->user, 'expense' => $expense->id]) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" onclick="confirmDelete('封存花費項目？', '確定要將「{{ $expense->description }}」移至回收桶嗎？', '{{ $exDelId }}')" class="p-1 text-red-400 hover:bg-red-50 rounded transition-colors" data-tooltip="移至回收桶">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
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

    <!-- 協作討論區 -->
    <div class="mt-12 border-muji-edge animate-in fade-in slide-in-from-bottom-4 duration-700 w-full mb-12">
        <div class="muji-card p-4 sm:p-6 border border-muji-edge shadow-muji shadow-muji-oak/5 rounded-[32px] w-full">
            <div class="mb-6 w-full">
                <h3 class="text-xl font-black text-muji-ink flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-muji-wheat/30 flex items-center justify-center text-muji-oak shadow-muji-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                    </span>
                    協作討論區
                </h3>
            </div>

            <div class="grid lg:grid-cols-4 gap-8">
                <div class="lg:col-span-3">
                    <div class="space-y-6 mb-10 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                        @forelse($day['comments'] as $comment)
                            <div class="flex gap-4 group/comment relative animate-in fade-in slide-in-from-left-2 duration-300">
                                <div class="w-10 h-10 rounded-2xl bg-muji-wheat/20 flex-shrink-0 flex items-center justify-center text-xs font-black text-muji-oak border border-muji-edge shadow-sm">
                                    {{ mb_substr($comment['user_name'], 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-baseline justify-between mb-1.5">
                                        <div class="flex items-baseline gap-3">
                                            <span class="text-sm font-black text-muji-ink">{{ $comment['user_name'] }}</span>
                                            <span class="text-[10px] text-muji-ash/40 font-bold uppercase tracking-tighter">{{ $comment['time'] }}</span>
                                        </div>
                                        @if($comment['can_delete'])
                                            @php $commentDelFormId = 'del-comment-' . $comment['id']; @endphp
                                            <form id="{{ $commentDelFormId }}" action="{{ route('day.comments.destroy', ['commentId' => $comment['id']]) }}" method="POST" class="opacity-0 group-hover/comment:opacity-100 transition-opacity">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete('刪除此則留言？', '此動作無法復原！確定要永久刪除嗎？', '{{ $commentDelFormId }}')" class="text-red-400 hover:text-red-600 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    <div class="bg-muji-paper p-4 rounded-3xl rounded-tl-none border border-muji-edge/60 text-sm text-muji-ink leading-relaxed shadow-muji-sm hover:shadow-muji transition-all duration-300 whitespace-pre-wrap">@php 
                                        $content = e($comment['content']);
                                        $content = preg_replace('/(https?:\/\/[^\s]+)/', '<a href="$1" target="_blank" class="text-muji-oak hover:underline break-all underline decoration-dotted decoration-muji-wheat">$1</a>', $content);
                                        echo $content;
                                    @endphp</div>
                                </div>
                            </div>
                        @empty
                            <div class="py-16 flex flex-col items-center justify-center opacity-30 select-none">
                                <svg class="w-16 h-16 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                <p class="text-[10px] font-black tracking-widest uppercase">目前還沒有討論，來當第一個留言的人吧！</p>
                            </div>
                        @endforelse
                    </div>

                    <form action="{{ $isShared ? route('day.comments.store_shared', ['dayId' => $day['id']]) : route('day.comments.store', ['dayId' => $day['id']]) }}" method="POST" class="relative mt-8 pt-8 border-t border-muji-edge/20">
                        @csrf
                        @if($isShared)
                            <div class="mb-4">
                                <label class="block text-[10px] font-black text-muji-ash uppercase tracking-widest mb-1.5 ml-1">留言暱稱</label>
                                <input type="text" name="user_name" required placeholder="您的暱稱" class="w-full sm:w-1/3 bg-muji-paper border-muji-edge rounded-xl text-sm px-4 py-2.5 focus:ring-muji-oak focus:border-muji-oak shadow-sm transition-all h-[46px]">
                            </div>
                        @endif
                        <div class="flex gap-3 items-end">
                            <div class="flex-1">
                                <label class="block text-[10px] font-black text-muji-ash uppercase tracking-widest mb-1.5 ml-1">發言內容</label>
                                <textarea name="content" rows="3" required placeholder="例如：我想吃這家大腸鍋（貼上連結）" class="w-full bg-muji-paper border-muji-edge rounded-[24px] text-sm p-4 focus:ring-muji-oak focus:border-muji-oak transition-all resize-none shadow-sm"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button type="submit" class="px-8 h-[50px] bg-muji-oak text-white rounded-full flex items-center justify-center gap-2 hover:opacity-90 active:scale-95 transition-all shadow-muji font-black text-sm tracking-widest">發送留言</button>
                        </div>
                    </form>
                </div>

                <div class="lg:col-span-1">
                    <div class="muji-card p-6 bg-muji-base/30 border border-muji-edge sticky top-24 rounded-[24px]">
                        <h4 class="text-xs font-black text-muji-ink uppercase tracking-widest mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-muji-oak" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            協作小撇步
                        </h4>
                        <ul class="space-y-4 text-xs text-muji-ash leading-relaxed font-medium">
                            <li class="flex gap-2">
                                <span class="text-muji-oak font-black">●</span>
                                <span>您可以直接貼上 Google Maps 或食記連結，系統會自動轉換。</span>
                            </li>
                            <li class="flex gap-2">
                                <span class="text-muji-oak font-black">●</span>
                                <span>這是一個公開的討論區，持有連結的旅伴皆可發言。</span>
                            </li>
                            <li class="flex gap-2">
                                <span class="text-muji-oak font-black">●</span>
                                <span>討論結果可以直接由行程擁有者更新至上方的詳細清單。</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
        @if(!$isShared)
            @auth
                <!-- Day Edit Modal -->
                <div id="daySummaryEditModal" class="fixed inset-0 z-[2000] hidden overflow-y-auto" role="dialog" aria-modal="true">
                    <div class="flex min-h-full items-center justify-center p-4 text-center">
                        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="safeCloseModal('daySummaryEditModal')"></div>
                        <div class="relative transform overflow-hidden rounded-[40px] muji-glass text-left shadow-2xl transition-all w-full max-w-lg flex flex-col max-h-[90vh]">
                            <button onclick="safeCloseModal('daySummaryEditModal')" class="absolute top-6 right-6 text-muji-ash hover:text-muji-oak p-2 rounded-full hover:bg-muji-base transition-all group z-50">
                                <svg class="w-6 h-6 group-hover:rotate-90 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                            <form action="{{ route('day.update', ['user' => $trip->user, 'trip' => $trip, 'date' => request()->route('date')]) }}" method="POST" class="flex flex-col h-full overflow-hidden" onsubmit="handleAjaxSubmit(event, this, 'daySummaryEditModal')">
                                @csrf
                                @method('PUT')
                                <div class="px-5 sm:px-8 py-8 sm:py-10 overflow-y-auto custom-scrollbar scroll-smooth">
                                    <div class="flex justify-between items-start mb-8 sm:mb-10">
                                        <div class="flex items-center gap-4">
                                            <div class="p-3 bg-muji-base rounded-2xl text-muji-oak shadow-muji-sm">
                                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            </div>
                                            <div class="flex flex-col border-l-2 border-muji-edge pl-4">
                                                <h3 class="text-2xl font-black text-muji-ink leading-tight">編輯日誌摘要</h3>
                                                <p class="text-[10px] font-bold text-muji-ash uppercase tracking-[0.2em] mt-1">完善您的每日旅程日誌</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-bold text-muji-ash mb-2 ml-1">今日主題</label>
                                                <input type="text" name="title" value="{{ $day['title'] }}" class="w-full px-4 py-3 muji-input">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-bold text-muji-ash mb-2 ml-1">所在城市/區域</label>
                                                <input type="text" name="location" value="{{ $day['location'] == $trip->name ? '' : $day['location'] }}" class="w-full px-4 py-3 muji-input">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-muji-ash mb-2 ml-1">今日摘要</label>
                                            <textarea name="summary" rows="2" class="w-full px-4 py-3 muji-input">{{ $day['summary'] }}</textarea>
                                        </div>
                                        <div class="pt-6 border-t border-muji-edge mt-6">
                                            <h4 class="text-xs font-black text-muji-oak uppercase tracking-[0.2em] flex items-center gap-2 mb-4 bg-muji-base/50 self-start px-3 py-1.5 rounded-lg border border-muji-edge">住宿資訊</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="sm:col-span-2">
                                                    <label class="block text-sm font-bold text-muji-ash mb-2 ml-1">飯店名稱</label>
                                                    <input type="text" name="hotel_name" value="{{ $day['accommodation']['name'] ?? '' }}" class="w-full px-4 py-3 muji-input">
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <label class="block text-sm font-bold text-muji-ash mb-2 ml-1">飯店地址</label>
                                                    <input type="text" name="hotel_address" value="{{ $day['accommodation']['address'] ?? '' }}" class="w-full px-4 py-3 muji-input">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-bold text-muji-ash mb-2 ml-1">預訂價格</label>
                                                    <div class="flex relative rounded-xl border border-muji-edge overflow-hidden bg-muji-paper shadow-muji-sm">
                                                        <select name="hotel_currency" class="bg-muji-base border-r border-muji-edge px-3 py-3 text-muji-ink font-black text-xs appearance-none">
                                                            @php
                                                                $rawPrice = $day['accommodation']['price'] ?? '';
                                                                preg_match('/^([^\d]+)?([\d,.]+)/u', $rawPrice, $m);
                                                                $savedCurrency = trim($m[1] ?? $trip->base_currency);
                                                                $savedNum = isset($m[2]) ? str_replace(',', '', $m[2]) : '';
                                                            @endphp
                                                            <option value="{{ $trip->base_currency }}" {{ $savedCurrency == $trip->base_currency ? 'selected' : '' }}>{{ $trip->base_currency }}</option>
                                                            <option value="{{ $trip->target_currency }}" {{ $savedCurrency == $trip->target_currency ? 'selected' : '' }}>{{ $trip->target_currency }}</option>
                                                        </select>
                                                        <input type="number" step="0.01" name="hotel_price_num" value="{{ $savedNum }}" class="flex-1 border-0 bg-transparent px-4 py-3 font-mono text-muji-ink font-black outline-none">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-bold text-muji-ash mb-2 ml-1">入住時間</label>
                                                    <input type="time" name="hotel_checkin" value="{{ $day['accommodation']['check_in'] ?? '' }}" class="w-full px-4 py-3 muji-input">
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <label class="block text-sm font-bold text-muji-ash mb-2 ml-1">住宿備註</label>
                                                    <input type="text" name="hotel_note" value="{{ $day['accommodation']['note'] ?? '' }}" class="w-full px-4 py-3 muji-input">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pt-8 flex gap-4 pb-10 border-t border-muji-edge mt-8">
                                            <button type="button" onclick="safeCloseModal('daySummaryEditModal')" class="flex-1 h-[46px] flex items-center justify-center bg-muji-base text-muji-ash border border-muji-edge font-black rounded-[24px] hover:bg-muji-wheat/20 transition-all active:scale-95 text-sm">取消</button>
                                            <button type="submit" class="flex-1 h-[46px] flex items-center justify-center bg-muji-oak text-white font-black rounded-[24px] hover:opacity-90 shadow-muji transition-all active:scale-95 text-sm">儲存設定</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Event Details Modal -->
                <div id="eventDetailsModal" class="fixed inset-0 z-[2000] hidden overflow-y-auto" role="dialog" aria-modal="true">
                    <div class="flex min-h-full items-center justify-center p-4">
                        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="safeCloseModal('eventDetailsModal')"></div>
                        <div class="relative transform overflow-hidden rounded-[40px] muji-glass text-left shadow-2xl transition-all w-full max-w-lg flex flex-col max-h-[90vh]">
                            <button onclick="safeCloseModal('eventDetailsModal')" class="absolute top-6 right-6 text-muji-ash hover:text-muji-oak p-2 rounded-full hover:bg-muji-base transition-all group z-50">
                                <svg class="w-6 h-6 group-hover:rotate-90 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                            <form id="eventForm" action="{{ route('events.store', ['user' => $trip->user, 'trip' => $trip, 'date' => request()->route('date')]) }}" method="POST" class="flex flex-col h-full overflow-hidden" autocomplete="off" onsubmit="handleAjaxSubmit(event, this, 'eventDetailsModal')">
                                @csrf
                                <div id="eventMethod"></div>
                                <div class="px-5 sm:px-8 py-8 sm:py-10 overflow-y-auto custom-scrollbar scroll-smooth">
                                    <div class="flex justify-between items-start mb-8 sm:mb-10">
                                        <div class="flex items-center gap-4">
                                            <div class="p-3 bg-muji-base rounded-2xl text-muji-oak shadow-muji-sm">
                                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            </div>
                                            <div class="flex flex-col border-l-2 border-muji-edge pl-4">
                                                <h3 id="eventModalTitle" class="text-2xl font-black text-muji-ink leading-tight">行程活動</h3>
                                                <p class="text-[10px] font-bold text-muji-ash uppercase tracking-[0.2em] mt-1">規劃您的行程細節</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="space-y-6">
                                        <div>
                                            <label class="block text-sm font-bold text-muji-ash mb-2 ml-1">活動時間</label>
                                            <input type="text" name="time" id="event_time" required placeholder="09:00 - 10:30" class="w-full px-4 py-3 muji-input">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-muji-ash mb-2 ml-1">活動地點</label>
                                            <div class="flex gap-2">
                                                <input type="text" name="activity" id="event_activity" required class="flex-1 px-4 py-3 muji-input" onblur="tryGeocode(this.value)">
                                                <button type="button" onclick="toggleLocationPicker()" class="p-3 bg-muji-base text-muji-oak border border-muji-edge rounded-xl hover:bg-muji-wheat/30"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg></button>
                                            </div>
                                        </div>
                                        <div id="locationPicker" class="hidden h-48 rounded-2xl border border-muji-edge overflow-hidden mb-4"><div id="pickerMap" class="w-full h-full"></div></div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[10px] font-black text-muji-ash uppercase tracking-widest mb-1.5 ml-1">緯度 Latitude</label>
                                                <input type="text" name="latitude" id="event_lat" readonly placeholder="等待定位..." class="w-full px-4 py-2.5 bg-muji-base/30 border border-muji-edge rounded-xl text-xs font-mono text-muji-ink outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-black text-muji-ash uppercase tracking-widest mb-1.5 ml-1">經度 Longitude</label>
                                                <input type="text" name="longitude" id="event_lng" readonly placeholder="等待定位..." class="w-full px-4 py-2.5 bg-muji-base/30 border border-muji-edge rounded-xl text-xs font-mono text-muji-ink outline-none">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-muji-ash mb-2 ml-1">詳細地址</label>
                                            <input type="text" name="address" id="event_address" class="w-full px-4 py-3 muji-base/30 border border-muji-edge rounded-xl text-sm italic">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-muji-ash mb-2 ml-1">子行程 (逗號隔開)</label>
                                            <input type="text" name="sub_activities" id="event_subs" class="w-full px-4 py-3 muji-input">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-muji-ash mb-2 ml-1">備註</label>
                                            <textarea name="note" id="event_note" rows="2" class="w-full px-4 py-3 muji-input"></textarea>
                                        </div>

                                        <div class="pt-8 flex gap-4 border-t border-muji-edge mt-8">
                                            <button type="button" onclick="safeCloseModal('eventDetailsModal')" class="flex-1 h-[46px] rounded-[24px] bg-muji-base text-muji-ash border border-muji-edge font-black active:scale-95 transition-all text-sm">取消</button>
                                            <button type="submit" class="flex-1 h-[46px] rounded-[24px] bg-muji-oak text-white font-black active:scale-95 transition-all text-sm">儲存活動</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endauth
        @endif

        <div id="mapViewModal" class="fixed inset-0 z-[3000] hidden" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-md" onclick="safeCloseModal('mapViewModal')"></div>
            <div class="absolute inset-4 sm:inset-10 muji-glass rounded-[40px] shadow-2xl flex flex-col overflow-hidden border border-white/20">
                <div class="px-6 py-4 border-b border-muji-edge flex justify-between items-center bg-muji-paper/80">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-muji-oak text-white flex items-center justify-center font-black text-xs shadow-muji-sm">MAP</span>
                        <h3 class="text-lg font-black text-muji-ink">{{ $day['date'] }} 路線規劃預覽</h3>
                    </div>
                    <button onclick="safeCloseModal('mapViewModal')" class="p-2 text-muji-ash hover:text-muji-oak transition-colors"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                <div id="itineraryMap" class="flex-grow w-full bg-muji-base"></div>
                <div class="px-6 py-4 border-t border-muji-edge bg-muji-paper/90 flex flex-wrap gap-6 items-center justify-between">
                    <div class="flex gap-4 items-center">
                        <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-muji-oak"></div><span class="text-xs font-bold text-muji-ash uppercase tracking-widest">行程打點</span></div>
                        <div id="trafficInfo" class="text-xs font-black text-muji-oak bg-muji-wheat/30 px-3 py-1 rounded-full border border-muji-edge hidden anim-pulse">🚗 預估交通總合: <span id="totalTravelTime">--</span></div>
                    </div>
                    <div class="text-[10px] text-muji-ash/50 font-medium italic">※ 交通時間僅供參考，實際請依各地圖導航為準</div>
                </div>
            </div>
        </div>
    @endpush

    @if(!$isShared)
        @auth
            <script>
                function openEventModal(event = null) {
                    const title = document.getElementById('eventModalTitle');
                    const form = document.getElementById('eventForm');
                    const methodDiv = document.getElementById('eventMethod');
                    if (!form) return;
                    const setVal = (id, val) => { const el = document.getElementById(id); if (el) el.value = val || ''; };

                    if (event) {
                        if (title) title.innerText = '編輯行程活動';
                        form.action = `/{{ $trip->user->username }}/events/${event.id}`;
                        if (methodDiv) methodDiv.innerHTML = '@method("PUT")';
                        setVal('event_time', event.time);
                        setVal('event_activity', event.activity);
                        setVal('event_address', event.address);
                        setVal('event_subs', Array.isArray(event.sub_activities) ? event.sub_activities.join(', ') : (event.sub_activities || ''));
                        setVal('event_note', event.note);
                        setVal('event_lat', event.latitude);
                        setVal('event_lng', event.longitude);
                        if (event.latitude && event.longitude) updatePickerPosition(event.latitude, event.longitude);
                    } else {
                        if (title) title.innerText = '新增行程活動';
                        form.action = "{{ route('events.store', ['user' => $trip->user->username, 'trip' => $trip, 'date' => request()->route('date') ?? (isset($currentDate) ? $currentDate : '')]) }}";
                        if (methodDiv) methodDiv.innerHTML = '';
                        form.reset();
                        setVal('event_lat', ''); setVal('event_lng', ''); setVal('event_address', '');
                    }
                    safeOpenModal('eventDetailsModal');
                }
            </script>
        @endauth
    @endif

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let itineraryMap, pickerMap, pickerMarker;
        const scheduleData = @json($day['schedule']);
        const cartoUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        const attribution = '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>';

        function openMapViewModal() { safeOpenModal('mapViewModal'); setTimeout(initItineraryMap, 300); }
        function initItineraryMap() {
            if (itineraryMap) itineraryMap.remove();
            const events = scheduleData.filter(e => !e.trashed && e.latitude && e.longitude);
            if (events.length === 0) {
                document.getElementById('itineraryMap').innerHTML = `<div class="h-full flex flex-col items-center justify-center text-muji-ash opacity-50 p-10 text-center"><p class="font-bold">尚未標註任何具座標的地點</p></div>`;
                return;
            }
            itineraryMap = L.map('itineraryMap').setView([parseFloat(events[0].latitude), parseFloat(events[0].longitude)], 13);
            L.tileLayer(cartoUrl, { attribution }).addTo(itineraryMap);
            const coordinates = [];
            events.forEach((ev, index) => {
                const lat = parseFloat(ev.latitude); const lng = parseFloat(ev.longitude);
                coordinates.push([lat, lng]);
                const icon = L.divIcon({ className: 'custom-div-icon', html: `<div class="w-8 h-8 rounded-full bg-muji-oak border-2 border-white shadow-lg flex items-center justify-center text-white font-black text-xs">${index + 1}</div>`, iconSize:[32,32], iconAnchor:[16,32] });
                L.marker([lat, lng], {icon}).bindPopup(`<div class="p-2 font-black text-muji-ink">${ev.activity}</div>`).addTo(itineraryMap);
            });
            if (coordinates.length > 1) {
                const poly = L.polyline(coordinates, {color:'#9c8c7c', weight:4, opacity:0.6, dashArray:'10,10'}).addTo(itineraryMap);
                itineraryMap.fitBounds(poly.getBounds(), {padding:[50,50]});
                calculateTraffic(coordinates);
            } else { itineraryMap.setView(coordinates[0], 15); }
        }
        async function calculateTraffic(coords) {
            if (coords.length < 2) return;
            const timeSpan = document.getElementById('totalTravelTime');
            try {
                const query = coords.map(c => c[1] + ',' + c[0]).join(';');
                const resp = await fetch(`https://router.project-osrm.org/route/v1/driving/${query}?overview=false`);
                const data = await resp.json();
                if (data.routes && data.routes[0]) {
                    const mins = Math.round(data.routes[0].duration / 60);
                    timeSpan.innerText = mins >= 60 ? `${Math.floor(mins/60)}時 ${mins%60}分` : `${mins}分鐘`;
                    document.getElementById('trafficInfo').classList.remove('hidden');
                }
            } catch (e) { console.error('Traffic failed:', e); }
        }
        function toggleLocationPicker() { const p = document.getElementById('locationPicker'); p.classList.toggle('hidden'); if (!p.classList.contains('hidden')) setTimeout(initPickerMap, 200); }
        function initPickerMap() {
            if (pickerMap) return;
            let start = [35.681, 139.767];
            const la = document.getElementById('event_lat').value, ln = document.getElementById('event_lng').value;
            if (la && ln) start = [parseFloat(la), parseFloat(ln)];
            pickerMap = L.map('pickerMap').setView(start, 14);
            L.tileLayer(cartoUrl, { attribution }).addTo(pickerMap);
            pickerMarker = L.marker(start, {draggable:true}).addTo(pickerMap);
            pickerMap.on('move', () => { const c = pickerMap.getCenter(); pickerMarker.setLatLng(c); document.getElementById('event_lat').value = c.lat.toFixed(6); document.getElementById('event_lng').value = c.lng.toFixed(6); });
        }
        function updatePickerPosition(lat, lng) { const c = [lat, lng]; if (pickerMap) { pickerMap.setView(c, 14); pickerMarker.setLatLng(c); } }
        async function tryGeocode(q) {
            if (!q || q.length < 2) return;
            const la = document.getElementById('event_lat'), lo = document.getElementById('event_lng');
            if (la.value) return;
            const input = document.getElementById('event_activity');
            input.classList.add('animate-pulse');
            try {
                const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&limit=1&countrycodes=jp`;
                const r = await fetch(url); const d = await r.json();
                if (d && d.length > 0) {
                    la.value = parseFloat(d[0].lat).toFixed(6); lo.value = parseFloat(d[0].lon).toFixed(6);
                    document.getElementById('event_address').value = d[0].display_name;
                    input.classList.remove('animate-pulse');
                    if (document.getElementById('locationPicker').classList.contains('hidden')) toggleLocationPicker();
                    setTimeout(() => updatePickerPosition(la.value, lo.value), 400);
                }
            } catch (e) { input.classList.remove('animate-pulse'); }
        }
        function findMyLocation() {
            if (!navigator.geolocation) return alert('不支援定位');
            navigator.geolocation.getCurrentPosition((pos) => {
                const la = pos.coords.latitude, lo = pos.coords.longitude;
                document.getElementById('event_lat').value = la.toFixed(6); document.getElementById('event_lng').value = lo.toFixed(6);
                if (document.getElementById('locationPicker').classList.contains('hidden')) toggleLocationPicker();
                setTimeout(() => updatePickerPosition(la, lo), 200);
            }, (err) => alert('定位失敗'));
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const indicators = document.querySelectorAll('.weather-indicator');
            indicators.forEach(async (el) => {
                const loc = el.dataset.location; const date = el.dataset.date; if (!loc) return;
                try {
                    el.classList.remove('hidden'); el.classList.add('inline-flex');
                    let r = await fetch(`https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(loc)}&count=1&format=json`);
                    let d = await r.json();
                    if (d.results) {
                        const wR = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${d.results[0].latitude}&longitude=${d.results[0].longitude}&daily=weather_code,temperature_2m_max,temperature_2m_min&timezone=auto&start_date=${date}&end_date=${date}`);
                        const wD = await wR.json();
                        if (wD.daily) {
                            el.querySelector('.weather-icon').innerHTML = '☀️';
                            el.querySelector('.weather-temp').innerText = `${Math.round(wD.daily.temperature_2m_max[0])}°/${Math.round(wD.daily.temperature_2m_min[0])}°C`;
                        }
                    }
                } catch (e) { el.classList.add('hidden'); }
            });
        });
    </script>
@endsection