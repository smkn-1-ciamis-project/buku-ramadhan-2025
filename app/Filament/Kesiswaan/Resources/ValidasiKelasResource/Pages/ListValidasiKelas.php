<?php

namespace App\Filament\Kesiswaan\Resources\ValidasiKelasResource\Pages;

use App\Filament\Kesiswaan\Resources\ValidasiKelasResource;
use App\Models\FormSubmission;
use App\Models\Kelas;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListValidasiKelas extends ListRecords
{
  protected static string $resource = ValidasiKelasResource::class;

  /** Kelas IDs selected via table checkboxes (wire:model.live) */
  public array $selectedKelasIds = [];

  protected function getHeaderActions(): array
  {
    $selectedCount = count($this->selectedKelasIds);

    $actions = [];

    // Reset selection button (only visible when checkboxes are selected)
    if ($selectedCount > 0) {
      $actions[] = Actions\Action::make('resetSelection')
        ->label('Reset Pilihan (' . $selectedCount . ')')
        ->icon('heroicon-o-x-mark')
        ->color('gray')
        ->size('sm')
        ->action(fn() => $this->selectedKelasIds = []);
    }

    // Main export button — changes label & behavior based on checkbox selection
    $actions[] = Actions\Action::make('exportExcel')
      ->label($selectedCount > 0
        ? 'Export ' . $selectedCount . ' Kelas Terpilih'
        : 'Export Excel')
      ->icon('heroicon-o-arrow-down-tray')
      ->color($selectedCount > 0 ? 'warning' : 'success')
      ->form($selectedCount > 0 ? [] : [
        Forms\Components\Select::make('kelas_ids')
          ->label('Pilih Kelas')
          ->placeholder('Semua Kelas')
          ->options(fn() => Kelas::orderBy('nama')->pluck('nama', 'id')->toArray())
          ->multiple()
          ->searchable()
          ->preload(),
      ])
      ->action(function (array $data) {
        if (count($this->selectedKelasIds) > 0) {
          $kelasIds = $this->selectedKelasIds;
        } else {
          $kelasIds = $data['kelas_ids'] ?? [];
        }
        $url = !empty($kelasIds)
          ? route('kesiswaan.validasi.export', ['kelas' => implode(',', $kelasIds)])
          : route('kesiswaan.validasi.export');
        $this->selectedKelasIds = [];
        return redirect($url);
      });

    return $actions;
  }

  public function getTabs(): array
  {
    $tabs = [
      'semua' => Tab::make('Semua')
        ->badge(fn() => Kelas::count())
        ->badgeColor('gray'),
    ];

    foreach (['10', '11', '12'] as $tingkat) {
      $tabs["kelas_{$tingkat}"] = Tab::make("Kelas {$tingkat}")
        ->modifyQueryUsing(fn(Builder $query) => $query->where('nama', 'like', "{$tingkat} %"))
        ->badge(fn() => Kelas::where('nama', 'like', "{$tingkat} %")->count())
        ->badgeColor('gray');
    }

    return $tabs;
  }

  public function getDefaultActiveTab(): string|int|null
  {
    return 'semua';
  }
}
