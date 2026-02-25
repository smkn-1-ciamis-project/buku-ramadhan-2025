<x-filament-panels::page>
  {{-- Header: Stat Boxes + Ramadhan Calendar --}}
  @include('filament.kesiswaan.validasi.header-stats', [
    'total'        => $total,
    'menunggu'     => $menunggu,
    'divalidasi'   => $divalidasi,
    'ditolak'      => $ditolak,
    'activeStatus' => $activeStatus,
    'activeDay'    => $activeDay,
    'maxDay'       => $maxDay,
    'calendarCells' => $calendarCells,
  ])

  {{ $this->table }}
</x-filament-panels::page>
