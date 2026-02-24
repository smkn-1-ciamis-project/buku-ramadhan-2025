<?php

namespace App\Filament\Kesiswaan\Resources\DataSiswaResource\Pages;

use App\Filament\Kesiswaan\Resources\DataSiswaResource;
use App\Models\Kelas;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListDataSiswa extends ListRecords
{
  protected static string $resource = DataSiswaResource::class;

  public function getTabs(): array
  {
    $tabs = [
      'semua' => Tab::make('Semua')
        ->badge(fn() => \App\Models\User::whereHas('role_user', fn(Builder $q) => $q->where('name', 'Siswa'))->count())
        ->badgeColor('primary'),
    ];

    foreach (['10', '11', '12'] as $tingkat) {
      $tabs["kelas_{$tingkat}"] = Tab::make("Kelas {$tingkat}")
        ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('kelas', fn(Builder $q) => $q->where('nama', 'like', "{$tingkat} %")))
        ->badge(fn() => \App\Models\User::whereHas('role_user', fn(Builder $q) => $q->where('name', 'Siswa'))->whereHas('kelas', fn(Builder $q) => $q->where('nama', 'like', "{$tingkat} %"))->count())
        ->badgeColor('gray');
    }

    return $tabs;
  }

  public function getDefaultActiveTab(): string|int|null
  {
    return 'semua';
  }
}
