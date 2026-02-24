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

  private const TABS_PER_PAGE = 7;

  protected function getHeaderActions(): array
  {
    return [];
  }

  public function getTabs(): array
  {
    // 1 Ramadhan 1447H = 19 Feb 2026
    $ramadhanStart = Carbon::create(2026, 2, 19);
    $today = Carbon::today();
    $maxDay = max(0, (int) min(30, $ramadhanStart->diffInDays($today) + 1));

    if ($maxDay < 1) {
      return [
        'semua' => Tab::make('Semua'),
      ];
    }

    $baseQuery = fn() => VerifikasiResource::getEloquentQuery();

    // Determine which page of tabs to show (from URL query param)
    $tabPage = (int) request()->query('tab_page', 1);
    $totalPages = (int) ceil($maxDay / self::TABS_PER_PAGE);
    $tabPage = max(1, min($tabPage, $totalPages));

    // Calculate range: show days descending, paginated
    // Page 1 = most recent days, Page N = oldest days
    $endDay = $maxDay - (($tabPage - 1) * self::TABS_PER_PAGE);
    $startDay = max(1, $endDay - self::TABS_PER_PAGE + 1);

    $tabs = [
      'semua' => Tab::make('Semua')
        ->badge(fn() => $baseQuery()->count())
        ->badgeColor('primary'),
    ];

    // Daily tabs for current page
    for ($day = $endDay; $day >= $startDay; $day--) {
      $d = $day;
      $tabs["hari_{$d}"] = Tab::make("Hari ke-{$d}")
        ->modifyQueryUsing(fn(Builder $query) => $query->where('hari_ke', $d))
        ->badge(fn() => $baseQuery()->where('hari_ke', $d)->count())
        ->badgeColor(fn() => match (true) {
          $baseQuery()->where('hari_ke', $d)->where('status', 'pending')->exists() => 'warning',
          $baseQuery()->where('hari_ke', $d)->where('status', 'rejected')->exists() => 'danger',
          default => 'success',
        });
    }

    // Navigation tabs for pagination (if more than 1 page)
    if ($totalPages > 1) {
      if ($tabPage < $totalPages) {
        $olderPage = $tabPage + 1;
        $tabs['older'] = Tab::make("← Hari {$startDay} ke bawah")
          ->modifyQueryUsing(fn(Builder $q) => $q->where('hari_ke', '<=', $startDay - 1))
          ->badge(fn() => $baseQuery()->where('hari_ke', '<=', $startDay - 1)->count())
          ->badgeColor('gray');
      }
      if ($tabPage > 1) {
        $newerPage = $tabPage - 1;
        $tabs['newer'] = Tab::make("Hari {$endDay} ke atas →")
          ->modifyQueryUsing(fn(Builder $q) => $q->where('hari_ke', '>=', $endDay + 1))
          ->badge(fn() => $baseQuery()->where('hari_ke', '>=', $endDay + 1)->count())
          ->badgeColor('gray');
      }
    }

    return $tabs;
  }

  /**
   * Override getDefaultActiveTab to default to 'semua'
   */
  public function getDefaultActiveTab(): string|int|null
  {
    return 'semua';
  }
}
