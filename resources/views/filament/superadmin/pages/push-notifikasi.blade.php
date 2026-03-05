<x-filament-panels::page>
    {{-- Statistik Pengikut --}}
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem;">
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10" style="padding:1.25rem; text-align:center;">
            <div class="text-3xl font-bold tracking-tight text-primary-600 dark:text-primary-400">{{ $this->stats['total'] }}</div>
            <div class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Total Pengikut</div>
        </div>
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10" style="padding:1.25rem; text-align:center;">
            <div class="text-3xl font-bold tracking-tight text-gray-950 dark:text-white">{{ $this->stats['siswa'] }}</div>
            <div class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Siswa</div>
        </div>
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10" style="padding:1.25rem; text-align:center;">
            <div class="text-3xl font-bold tracking-tight text-gray-950 dark:text-white">{{ $this->stats['guru'] }}</div>
            <div class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Guru</div>
        </div>
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10" style="padding:1.25rem; text-align:center;">
            <div class="text-3xl font-bold tracking-tight text-gray-950 dark:text-white">{{ $this->stats['kesiswaan'] }}</div>
            <div class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Kesiswaan</div>
        </div>
    </div>

    {{-- Form Kirim --}}
    <form wire:submit="send">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" size="lg" icon="heroicon-o-paper-airplane">
                @if(($this->data['send_mode'] ?? 'now') === 'scheduled')
                    Jadwalkan Notifikasi
                @else
                    Kirim Push Notifikasi
                @endif
            </x-filament::button>
        </div>
    </form>

    {{-- Jadwal Antrian --}}
    @if($this->scheduledNotifications->isNotEmpty())
        <div class="mt-8">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                <x-heroicon-o-clock class="w-5 h-5 text-amber-500" />
                Antrian Jadwal Notifikasi
            </h3>
            <div class="space-y-3">
                @foreach($this->scheduledNotifications as $scheduled)
                    <div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-gray-900 dark:text-gray-100 truncate">{{ $scheduled->title }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">{{ $scheduled->body }}</div>
                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold
                                        {{ match($scheduled->target) {
                                            'all' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                                            'siswa' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                                            'guru' => 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300',
                                            'kesiswaan' => 'bg-pink-100 text-pink-700 dark:bg-pink-900 dark:text-pink-300',
                                            default => 'bg-gray-100 text-gray-700'
                                        } }}">
                                        {{ match($scheduled->target) {
                                            'all' => 'Semua',
                                            'siswa' => 'Siswa',
                                            'guru' => 'Guru',
                                            'kesiswaan' => 'Kesiswaan',
                                            default => $scheduled->target
                                        } }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-xs text-amber-600 dark:text-amber-400 font-medium">
                                        <x-heroicon-o-clock class="w-3.5 h-3.5" />
                                        {{ $scheduled->scheduled_at->translatedFormat('d F Y H:i') }} WIB
                                    </span>
                                </div>
                            </div>
                            <button
                                type="button"
                                wire:click="cancelScheduled('{{ $scheduled->id }}')"
                                wire:confirm="Yakin ingin membatalkan jadwal notifikasi ini?"
                                class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-950 dark:hover:bg-red-900 dark:text-red-400 border border-red-200 dark:border-red-800 rounded-lg transition"
                            >
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                                Batalkan
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Tabel Detail Pengikut --}}
    <div class="mt-8">
        {{ $this->table }}
    </div>
</x-filament-panels::page>
