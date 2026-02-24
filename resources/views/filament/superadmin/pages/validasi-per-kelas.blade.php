<x-filament-panels::page>
  {{-- Stats ringkasan --}}
  @php
    $siswaIds = $record->siswa->pluck('id');
    $pendingCount = \App\Models\FormSubmission::whereIn('user_id', $siswaIds)->where('status', 'verified')->where('kesiswaan_status', 'pending')->count();
    $validatedCount = \App\Models\FormSubmission::whereIn('user_id', $siswaIds)->where('status', 'verified')->where('kesiswaan_status', 'validated')->count();
    $rejectedCount = \App\Models\FormSubmission::whereIn('user_id', $siswaIds)->where('status', 'verified')->where('kesiswaan_status', 'rejected')->count();
    $totalCount = $pendingCount + $validatedCount + $rejectedCount;
  @endphp

  <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:1rem;">
    <div style="border-radius:12px;padding:16px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,.1);" class="bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10">
      <div style="font-size:1.5rem;font-weight:700;" class="text-primary-500">{{ $totalCount }}</div>
      <div style="font-size:.875rem;" class="text-gray-500 dark:text-gray-400">Total Formulir</div>
    </div>
    <div style="border-radius:12px;padding:16px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,.1);" class="bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10">
      <div style="font-size:1.5rem;font-weight:700;" class="text-warning-500">{{ $pendingCount }}</div>
      <div style="font-size:.875rem;" class="text-gray-500 dark:text-gray-400">Menunggu</div>
    </div>
    <div style="border-radius:12px;padding:16px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,.1);" class="bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10">
      <div style="font-size:1.5rem;font-weight:700;" class="text-success-500">{{ $validatedCount }}</div>
      <div style="font-size:.875rem;" class="text-gray-500 dark:text-gray-400">Divalidasi</div>
    </div>
    <div style="border-radius:12px;padding:16px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,.1);" class="bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10">
      <div style="font-size:1.5rem;font-weight:700;" class="text-danger-500">{{ $rejectedCount }}</div>
      <div style="font-size:.875rem;" class="text-gray-500 dark:text-gray-400">Ditolak</div>
    </div>
  </div>

  {{ $this->table }}
</x-filament-panels::page>
