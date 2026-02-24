<?php

namespace App\Filament\Kesiswaan\Resources\ValidasiKelasResource\Pages;

use App\Filament\Kesiswaan\Resources\ValidasiKelasResource;
use App\Models\FormSubmission;
use App\Models\Kelas;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListValidasiKelas extends ListRecords
{
  protected static string $resource = ValidasiKelasResource::class;

  protected function getHeaderActions(): array
  {
    return [];
  }

  public function getTabs(): array
  {
    $tabs = [
      'semua' => Tab::make('Semua')
        ->badge(fn() => Kelas::count())
        ->badgeColor('primary'),
    ];

    foreach (['10', '11', '12'] as $tingkat) {
      $tabs["kelas_{$tingkat}"] = Tab::make("Kelas {$tingkat}")
        ->modifyQueryUsing(fn(Builder $query) => $query->where('nama', 'like', "{$tingkat} %"))
        ->badge(fn() => Kelas::where('nama', 'like', "{$tingkat} %")->count())
        ->badgeColor(fn() => match (true) {
          Kelas::where('nama', 'like', "{$tingkat} %")
            ->whereHas('siswa', fn(Builder $q) => $q->whereHas('formSubmissions', fn(Builder $sq) => $sq->where('status', 'verified')->where('kesiswaan_status', 'pending')))
            ->exists() => 'warning',
          default => 'success',
        });
    }

    return $tabs;
  }

  public function getDefaultActiveTab(): string|int|null
  {
    return 'semua';
  }
}
