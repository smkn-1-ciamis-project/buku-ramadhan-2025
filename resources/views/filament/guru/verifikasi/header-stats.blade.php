<style>
    .stat-box { border: 2px solid #e5e7eb; transition: all 0.15s; }
    .stat-box.box-validated:hover { background: #eff6ff !important; border-color: #60a5fa !important; }
    .stat-box.box-pending:hover   { background: #fffbeb !important; border-color: #fbbf24 !important; }
    .stat-box.box-verified:hover  { background: #f0fdf4 !important; border-color: #4ade80 !important; }
    .stat-box.box-rejected:hover  { background: #fef2f2 !important; border-color: #f87171 !important; }
    .stat-box.active-validated { background: #eff6ff; border-color: #60a5fa; }
    .stat-box.active-pending   { background: #fffbeb; border-color: #fbbf24; }
    .stat-box.active-verified  { background: #f0fdf4; border-color: #4ade80; }
    .stat-box.active-rejected  { background: #fef2f2; border-color: #f87171; }
    .dark .stat-box { border-color: #374151; background: #1f2937; }
    .dark .stat-box.box-validated:hover { background: rgba(30,58,138,0.2) !important; border-color: #3b82f6 !important; }
    .dark .stat-box.box-pending:hover   { background: rgba(120,53,15,0.2) !important; border-color: #f59e0b !important; }
    .dark .stat-box.box-verified:hover  { background: rgba(20,83,45,0.2) !important; border-color: #22c55e !important; }
    .dark .stat-box.box-rejected:hover  { background: rgba(127,29,29,0.2) !important; border-color: #ef4444 !important; }
    .dark .stat-box.active-validated { background: rgba(30,58,138,0.3); border-color: #3b82f6; }
    .dark .stat-box.active-pending   { background: rgba(120,53,15,0.3); border-color: #f59e0b; }
    .dark .stat-box.active-verified  { background: rgba(20,83,45,0.3); border-color: #22c55e; }
    .dark .stat-box.active-rejected  { background: rgba(127,29,29,0.3); border-color: #ef4444; }
</style>

<div class="flex flex-col gap-4 mb-2">

    {{-- TOP: Stat Boxes --}}
    <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr))" class="gap-2 lg:gap-3 w-full shrink-0">

        {{-- Divalidasi --}}
        <button wire:click="filterByStatus('validated')" type="button"
            class="stat-box box-validated rounded-xl p-2 lg:p-3 text-center cursor-pointer shadow-sm {{ $activeStatus === 'validated' ? 'active-validated' : '' }}">
            <p class="text-xl lg:text-2xl font-bold" style="color:{{ $activeStatus === 'validated' ? '#2563eb' : '#111827' }}">
                {{ $divalidasi }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Divalidasi</p>
        </button>

        {{-- Menunggu --}}
        <button wire:click="filterByStatus('pending')" type="button"
            class="stat-box box-pending rounded-xl p-2 lg:p-3 text-center cursor-pointer shadow-sm {{ $activeStatus === 'pending' ? 'active-pending' : '' }}">
            <p class="text-xl lg:text-2xl font-bold" style="color:{{ $activeStatus === 'pending' ? '#d97706' : '#111827' }}">
                {{ $menunggu }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Menunggu</p>
        </button>

        {{-- Diverifikasi --}}
        <button wire:click="filterByStatus('verified')" type="button"
            class="stat-box box-verified rounded-xl p-2 lg:p-3 text-center cursor-pointer shadow-sm {{ $activeStatus === 'verified' ? 'active-verified' : '' }}">
            <p class="text-xl lg:text-2xl font-bold" style="color:{{ $activeStatus === 'verified' ? '#16a34a' : '#111827' }}">
                {{ $diverifikasi }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Diverifikasi</p>
        </button>

        {{-- Ditolak --}}
        <button wire:click="filterByStatus('rejected')" type="button"
            class="stat-box box-rejected rounded-xl p-2 lg:p-3 text-center cursor-pointer shadow-sm {{ $activeStatus === 'rejected' ? 'active-rejected' : '' }}">
            <p class="text-xl lg:text-2xl font-bold" style="color:{{ $activeStatus === 'rejected' ? '#dc2626' : '#111827' }}">
                {{ $ditolak }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ditolak</p>
        </button>

    </div>

    {{-- BOTTOM: Monthly Ramadhan Calendar --}}
    <div class="flex flex-1 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm p-4 flex-col">

        {{-- Header --}}
        <div class="flex items-center justify-center mb-3">
            <span class="text-[10px] lg:text-xs font-semibold text-gray-600 dark:text-gray-300">Kalender Ramadhan 1447 H &mdash; Feb &ndash; Mar 2026</span>
        </div>

        {{-- Weekday labels --}}
        <div class="grid grid-cols-7 gap-1 mb-1">
            @foreach(['SEN','SEL','RAB','KAM','JUM','SAB','MIN'] as $label)
                <div class="text-center font-semibold text-gray-400 dark:text-gray-500" style="font-size:7px;padding:4px 0">{{ $label }}</div>
            @endforeach
        </div>

        {{-- Calendar grid --}}
        <div class="grid grid-cols-7 gap-2 flex-1">
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
                        $hasPending  = ($counts['pending']  ?? 0) > 0;
                        $hasRejected = ($counts['rejected'] ?? 0) > 0;
                        $hasVerified = ($counts['verified'] ?? 0) > 0;
                        $hasAny      = $hasPending || $hasRejected || $hasVerified;

                        // Light mode bg, border, text
                        if ($isToday && $hasAny) {
                            $lightBg    = '#d1fae5'; $lightBorder = '#34d399'; $lightText = '#065f46';
                            $darkBg     = '#064e3b'; $darkBorder  = '#10b981'; $darkText  = '#6ee7b7';
                            $borderW    = '2px';
                            $canClick   = true;
                        } elseif ($isToday && !$hasAny) {
                            $lightBg    = '#ffffff'; $lightBorder = '#34d399'; $lightText = '#065f46';
                            $darkBg     = '#1f2937'; $darkBorder  = '#10b981'; $darkText  = '#9ca3af';
                            $borderW    = '2px';
                            $canClick   = true;
                        } elseif (!$isPast) {
                            $lightBg    = '#f9fafb'; $lightBorder = '#f3f4f6'; $lightText = '#d1d5db';
                            $darkBg     = '#374151'; $darkBorder  = '#4b5563'; $darkText  = '#6b7280';
                            $borderW    = '1px';
                            $canClick   = false;
                        } elseif (!$hasAny) {
                            $lightBg    = '#ffffff'; $lightBorder = '#e5e7eb'; $lightText = '#9ca3af';
                            $darkBg     = '#1f2937'; $darkBorder  = '#4b5563'; $darkText  = '#6b7280';
                            $borderW    = '1px';
                            $canClick   = true;
                        } elseif ($hasPending) {
                            $lightBg    = '#fef3c7'; $lightBorder = '#fcd34d'; $lightText = '#92400e';
                            $darkBg     = '#78350f'; $darkBorder  = '#d97706'; $darkText  = '#fcd34d';
                            $borderW    = '1px';
                            $canClick   = true;
                        } elseif ($hasRejected) {
                            $lightBg    = '#fee2e2'; $lightBorder = '#fca5a5'; $lightText = '#991b1b';
                            $darkBg     = '#7f1d1d'; $darkBorder  = '#ef4444'; $darkText  = '#fca5a5';
                            $borderW    = '1px';
                            $canClick   = true;
                        } elseif ($hasVerified) {
                            $lightBg    = '#dcfce7'; $lightBorder = '#86efac'; $lightText = '#166534';
                            $darkBg     = '#14532d'; $darkBorder  = '#22c55e'; $darkText  = '#86efac';
                            $borderW    = '1px';
                            $canClick   = true;
                        } else {
                            $lightBg    = '#ffffff'; $lightBorder = '#e5e7eb'; $lightText = '#9ca3af';
                            $darkBg     = '#1f2937'; $darkBorder  = '#4b5563'; $darkText  = '#9ca3af';
                            $borderW    = '1px';
                            $canClick   = true;
                        }

                        $activeBorder = $isActive ? 'border-width:2px !important;' : '';
                    @endphp

                    <button
                        @if($canClick) wire:click="filterByDay({{ $isActive ? 'null' : $d }})" @endif
                        type="button"
                        title="Hari ke-{{ $d }} — {{ $cell['masehiDay'] }} {{ $cell['masehiMonthShort'] }}"
                        class="rounded-lg p-2 flex items-center justify-center w-full transition"
                        style="min-height:3.25rem;background:{{ $lightBg }};border:{{ $borderW }} solid {{ $lightBorder }};{{ $activeBorder }}{{ $isActive ? 'border-color:rgb(var(--primary-500));' : '' }}{{ $canClick ? 'cursor:pointer;' : '' }}"
                    >
                        <span class="text-sm font-bold leading-none" style="color:{{ $lightText }}">{{ $cell['masehiDay'] }}</span>
                    </button>

                    <style>
                        .dark button[title="Hari ke-{{ $d }} — {{ $cell['masehiDay'] }} {{ $cell['masehiMonthShort'] }}"] {
                            background: {{ $darkBg }} !important;
                            border-color: {{ $isActive ? 'rgb(var(--primary-400))' : $darkBorder }} !important;
                        }
                        .dark button[title="Hari ke-{{ $d }} — {{ $cell['masehiDay'] }} {{ $cell['masehiMonthShort'] }}"] span {
                            color: {{ $darkText }} !important;
                        }
                    </style>
                @endif
            @endforeach
        </div>

    </div>

</div>
