<?php

namespace App\Filament\Guru\Resources\VerifikasiResource\Pages;

use App\Filament\Guru\Resources\VerifikasiResource;
use Carbon\Carbon;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListVerifikasi extends ListRecords
{
  protected static string $resource = VerifikasiResource::class;

  /** Which status box is active (null = all statuses) */
  public ?string $activeStatusFilter = null;

  /** Which day is selected on the calendar (null = all days) */
  public ?int $activeDayFilter = null;

  protected function getHeaderActions(): array
  {
    return [];
  }

  /**
   * Render 2x2 stat boxes + proper monthly calendar above the table.
   */
  public function getHeader(): ?\Illuminate\Contracts\View\View
  {
    $ramadhanStart = Carbon::create(2026, 2, 19); // 1 Ramadhan 1447H = Thursday
    $today         = Carbon::today();
    $maxDay        = max(0, (int) min(30, $ramadhanStart->diffInDays($today) + 1));

    $baseQuery = VerifikasiResource::getEloquentQuery();

    $total        = (clone $baseQuery)->count();
    $menunggu     = (clone $baseQuery)->where('status', 'pending')->count();
    $diverifikasi = (clone $baseQuery)->where('status', 'verified')->count();
    $divalidasi   = (clone $baseQuery)->where('kesiswaan_status', 'validated')->count();
    $ditolak      = (clone $baseQuery)->where(function ($q) {
      $q->where('status', 'rejected')->orWhere('kesiswaan_status', 'rejected');
    })->count();

    // Per-day stats for calendar coloring
    $dayCounts = [];
    $rows = (clone $baseQuery)
      ->selectRaw('hari_ke, status, count(*) as cnt')
      ->groupBy('hari_ke', 'status')
      ->get();
    foreach ($rows as $row) {
      $dayCounts[$row->hari_ke][$row->status] = $row->cnt;
    }

    // Build calendar cells (7-column Mon–Sun grid)
    // 19 Feb 2026 = isoWeekday 4 (Thursday) → pad 3 empty cells
    $leadingEmpties = $ramadhanStart->dayOfWeekIso - 1; // 3
    $calendarCells  = [];

    for ($i = 0; $i < $leadingEmpties; $i++) {
      $calendarCells[] = null;
    }
    for ($d = 1; $d <= 30; $d++) {
      $date              = $ramadhanStart->copy()->addDays($d - 1);
      $calendarCells[]   = [
        'ramadanDay'      => $d,
        'masehiDay'       => $date->day,
        'masehiMonthShort' => $date->locale('id')->isoFormat('MMM'),
        'isPast'          => $d <= $maxDay,
        'isToday'         => $d === $maxDay && $today->isSameDay($date),
        'counts'          => $dayCounts[$d] ?? [],
      ];
    }
    // Trailing empties to complete last row
    $trailing = (7 - (count($calendarCells) % 7)) % 7;
    for ($i = 0; $i < $trailing; $i++) {
      $calendarCells[] = null;
    }

    return view('filament.guru.verifikasi.header-stats', [
      'total'           => $total,
      'menunggu'        => $menunggu,
      'diverifikasi'    => $diverifikasi,
      'divalidasi'      => $divalidasi,
      'ditolak'         => $ditolak,
      'activeStatus'    => $this->activeStatusFilter,
      'activeDay'       => $this->activeDayFilter,
      'maxDay'          => $maxDay,
      'dayCounts'       => $dayCounts,
      'calendarCells'   => $calendarCells,
    ]);
  }

  /** Stat box clicked: filter by status */
  public function filterByStatus(?string $status): void
  {
    $this->activeStatusFilter = $status;

    if ($status === 'validated') {
      // Divalidasi: filter on kesiswaan_status, clear others
      $this->tableFilters = array_merge($this->tableFilters ?? [], [
        'status'             => ['value' => ''],
        'kesiswaan_status'   => ['value' => 'validated'],
      ]);
    } elseif ($status === 'rejected') {
      // Ditolak: handled via getTableQuery override, clear built-in filters
      $this->tableFilters = array_merge($this->tableFilters ?? [], [
        'status'             => ['value' => ''],
        'kesiswaan_status'   => ['value' => ''],
      ]);
    } else {
      // Regular status filter, clear others
      $this->tableFilters = array_merge($this->tableFilters ?? [], [
        'status'             => ['value' => $status ?? ''],
        'kesiswaan_status'   => ['value' => ''],
      ]);
    }

    $this->resetPage();
  }

  /** Calendar day clicked: filter by hari_ke */
  public function filterByDay(?int $day): void
  {
    $this->activeDayFilter = $day;

    $this->tableFilters = array_merge($this->tableFilters ?? [], [
      'hari_ke' => ['value' => $day !== null ? (string) $day : ''],
    ]);

    $this->resetPage();
  }

  /**
   * Apply combined rejected filter (status OR kesiswaan_status = rejected)
   * when the "Ditolak" stat box is active.
   */
  protected function getTableQuery(): ?Builder
  {
    /** @var Builder $query */
    $query = parent::getTableQuery();

    if ($this->activeStatusFilter === 'rejected') {
      $query->where(function ($q) {
        $q->where('status', 'rejected')
          ->orWhere('kesiswaan_status', 'rejected');
      });
    }

    return $query;
  }
}
