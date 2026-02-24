<x-filament-panels::page>
  {{-- Stats ringkasan --}}
  @php
    $siswaIds = $record->siswa->pluck('id');
    $pendingCount = \App\Models\FormSubmission::whereIn('user_id', $siswaIds)->where('status', 'verified')->where('kesiswaan_status', 'pending')->count();
    $validatedCount = \App\Models\FormSubmission::whereIn('user_id', $siswaIds)->where('status', 'verified')->where('kesiswaan_status', 'validated')->count();
    $rejectedCount = \App\Models\FormSubmission::whereIn('user_id', $siswaIds)->where('status', 'verified')->where('kesiswaan_status', 'rejected')->count();
    $totalCount = $pendingCount + $validatedCount + $rejectedCount;
  @endphp

  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <x-filament::section>
      <div class="text-center">
        <div class="text-2xl font-bold text-primary-500">{{ $totalCount }}</div>
        <div class="text-sm text-gray-500 dark:text-gray-400">Total Formulir</div>
      </div>
    </x-filament::section>
    <x-filament::section>
      <div class="text-center">
        <div class="text-2xl font-bold text-warning-500">{{ $pendingCount }}</div>
        <div class="text-sm text-gray-500 dark:text-gray-400">Menunggu</div>
      </div>
    </x-filament::section>
    <x-filament::section>
      <div class="text-center">
        <div class="text-2xl font-bold text-success-500">{{ $validatedCount }}</div>
        <div class="text-sm text-gray-500 dark:text-gray-400">Divalidasi</div>
      </div>
    </x-filament::section>
    <x-filament::section>
      <div class="text-center">
        <div class="text-2xl font-bold text-danger-500">{{ $rejectedCount }}</div>
        <div class="text-sm text-gray-500 dark:text-gray-400">Ditolak</div>
      </div>
    </x-filament::section>
  </div>

  {{ $this->table }}
</x-filament-panels::page>
