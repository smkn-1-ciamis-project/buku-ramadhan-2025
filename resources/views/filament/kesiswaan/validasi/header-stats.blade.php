<div class="flex flex-col gap-4 mb-2">

    {{-- TOP: Stat Boxes --}}
    <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr))" class="gap-2 lg:gap-3 w-full shrink-0">

        {{-- Total --}}
        <button wire:click="filterByStatus(null)" type="button"
            class="rounded-xl border p-2 lg:p-3 text-center cursor-pointer shadow-sm transition
                {{ $activeStatus === null
                    ? 'bg-blue-50 border-blue-400 dark:bg-blue-900/30 dark:border-blue-500'
                    : 'bg-white border-gray-200 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/60' }}">
            <p class="text-xl lg:text-2xl font-bold {{ $activeStatus === null ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-white' }}">
                {{ $total }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Total</p>
        </button>

        {{-- Menunggu --}}
        <button wire:click="filterByStatus('pending')" type="button"
            class="rounded-xl border p-2 lg:p-3 text-center cursor-pointer shadow-sm transition
                {{ $activeStatus === 'pending'
                    ? 'bg-amber-50 border-amber-400 dark:bg-amber-900/30 dark:border-amber-500'
                    : 'bg-white border-gray-200 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/60' }}">
            <p class="text-xl lg:text-2xl font-bold {{ $activeStatus === 'pending' ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white' }}">
                {{ $menunggu }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Menunggu</p>
        </button>

        {{-- Divalidasi --}}
        <button wire:click="filterByStatus('validated')" type="button"
            class="rounded-xl border p-2 lg:p-3 text-center cursor-pointer shadow-sm transition
                {{ $activeStatus === 'validated'
                    ? 'bg-green-50 border-green-400 dark:bg-green-900/30 dark:border-green-500'
                    : 'bg-white border-gray-200 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/60' }}">
            <p class="text-xl lg:text-2xl font-bold {{ $activeStatus === 'validated' ? 'text-green-600 dark:text-green-400' : 'text-gray-900 dark:text-white' }}">
                {{ $divalidasi }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Divalidasi</p>
        </button>

        {{-- Ditolak --}}
        <button wire:click="filterByStatus('rejected')" type="button"
            class="rounded-xl border p-2 lg:p-3 text-center cursor-pointer shadow-sm transition
                {{ $activeStatus === 'rejected'
                    ? 'bg-red-50 border-red-400 dark:bg-red-900/30 dark:border-red-500'
                    : 'bg-white border-gray-200 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/60' }}">
            <p class="text-xl lg:text-2xl font-bold {{ $activeStatus === 'rejected' ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                {{ $ditolak }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ditolak</p>
        </button>

    </div>

    {{-- BOTTOM: Monthly Ramadhan Calendar --}}
    <div class="flex flex-1 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm p-3 flex-col">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-3">
            <span class="text-[10px] lg:text-xs font-semibold text-gray-600 dark:text-gray-300">Kalender Ramadhan 1447 H &mdash; Feb &ndash; Mar 2026</span>
        </div>

        {{-- Weekday labels --}}
        <div class="grid grid-cols-7 gap-0.5 mb-0.5">
            @foreach(['SEN','SEL','RAB','KAM','JUM','SAB','MIN'] as $label)
                <div class="text-center text-[5px] font-semibold text-gray-400 dark:text-gray-500 py-0.5">{{ $label }}</div>
            @endforeach
        </div>

        {{-- Calendar grid --}}
        <div class="grid grid-cols-7 gap-1 flex-1">
            @foreach($calendarCells as $cell)
                @if($cell === null)
                    <div></div>
                @else
                    @php
                        $d           = $cell['ramadanDay'];
                        $isPast      = $cell['isPast'];
                        $isToday     = $cell['isToday'];
                        $counts      = $cell['counts'];
                        $isActive    = $activeDay === $d;
                        $hasPending   = ($counts['pending']   ?? 0) > 0;
                        $hasRejected  = ($counts['rejected']  ?? 0) > 0;
                        $hasValidated = ($counts['validated'] ?? 0) > 0;
                        $hasAny       = $hasPending || $hasRejected || $hasValidated;

                        if ($isToday) {
                            $cellBg   = 'bg-emerald-100 border-2 border-emerald-400 dark:bg-emerald-900/40 dark:border-emerald-500 cursor-pointer hover:bg-emerald-200';
                            $numColor = 'text-emerald-700 dark:text-emerald-300';
                        } elseif (!$isPast) {
                            $cellBg   = 'bg-gray-50 dark:bg-gray-700/40 border border-gray-100 dark:border-gray-700';
                            $numColor = 'text-gray-300 dark:text-gray-600';
                        } elseif ($hasPending) {
                            $cellBg   = 'bg-amber-100 border border-amber-300 dark:bg-amber-900/40 dark:border-amber-600 cursor-pointer hover:bg-amber-200';
                            $numColor = 'text-amber-800 dark:text-amber-300';
                        } elseif ($hasRejected) {
                            $cellBg   = 'bg-red-100 border border-red-300 dark:bg-red-900/40 dark:border-red-600 cursor-pointer hover:bg-red-200';
                            $numColor = 'text-red-800 dark:text-red-300';
                        } elseif ($hasValidated) {
                            $cellBg   = 'bg-green-100 border border-green-300 dark:bg-green-900/40 dark:border-green-600 cursor-pointer hover:bg-green-200';
                            $numColor = 'text-green-800 dark:text-green-300';
                        } else {
                            $cellBg   = 'bg-gray-50 dark:bg-gray-700/40 border border-gray-100 dark:border-gray-700 cursor-pointer hover:bg-gray-100';
                            $numColor = 'text-gray-400 dark:text-gray-500';
                        }

                        // Active day: use a thicker blue border instead of ring (avoids clipping)
                        if ($isActive) {
                            $cellBg = preg_replace('/border(-2)?\s+border-\S+/', '', $cellBg);
                            $cellBg .= ' border-2 border-primary-500 dark:border-primary-400';
                        }

                        $canClick = $isPast; // All past days are clickable
                    @endphp

                    <button
                        @if($canClick) wire:click="filterByDay({{ $isActive ? 'null' : $d }})" @endif
                        type="button"
                        title="Hari ke-{{ $d }} — {{ $cell['masehiDay'] }} {{ $cell['masehiMonthShort'] }}"
                        class="rounded-md p-1 flex items-center justify-center w-full h-full transition {{ $cellBg }}"
                    >
                        <span class="text-xs font-bold leading-none {{ $numColor }}">{{ $cell['masehiDay'] }}</span>
                    </button>
                @endif
            @endforeach
        </div>

    </div>

</div>
