<?php

namespace App\Filament\Guru\Resources\VerifikasiResource\Pages;

use App\Filament\Guru\Resources\VerifikasiResource;
use Carbon\Carbon;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListVerifikasi extends ListRecords
{
  protected static string $resource = VerifikasiResource::class;

  protected function getHeaderActions(): array
  {
    return [];
  }

  public function getTabs(): array
  {
    // 1 Ramadhan 1447H = 19 Feb 2026
    $ramadhanStart = Carbon::create(2026, 2, 19);
    $today = Carbon::today();
    $maxDay = max(0, min(30, $ramadhanStart->diffInDays($today) + 1));

    // Jika belum masuk Ramadhan, tampilkan tab "Semua" saja
    if ($maxDay < 1) {
      return [
        'semua' => Tab::make('Semua'),
      ];
    }

    $baseQuery = fn() => VerifikasiResource::getEloquentQuery();

    $tabs = [
      'semua' => Tab::make('Semua')
        ->badge(fn() => $baseQuery()->count())
        ->badgeColor('primary'),
    ];

    // Tab bertahap: hanya muncul sampai hari yang sudah berjalan
    for ($day = $maxDay; $day >= 1; $day--) {
      $d = $day; // closure capture
      $tabs["hari_{$d}"] = Tab::make("Hari ke-{$d}")
        ->modifyQueryUsing(fn(Builder $query) => $query->where('hari_ke', $d))
        ->badge(fn() => $baseQuery()->where('hari_ke', $d)->count())
        ->badgeColor(fn() => match (true) {
          $baseQuery()->where('hari_ke', $d)->where('status', 'pending')->exists() => 'warning',
          $baseQuery()->where('hari_ke', $d)->where('status', 'rejected')->exists() => 'danger',
          default => 'success',
        });
    }

    return $tabs;
  }
}
