<?php

namespace App\Filament\Superadmin\Resources\SiswaResource\Pages;

use App\Filament\Superadmin\Resources\SiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSiswa extends ListRecords
{
  protected static string $resource = SiswaResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make()->label('Tambah Siswa'),
    ];
  }
}
