<?php

namespace App\Filament\Superadmin\Resources\GuruResource\Pages;

use App\Filament\Superadmin\Resources\GuruResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGuru extends ListRecords
{
  protected static string $resource = GuruResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make()->label('Tambah Guru'),
    ];
  }
}
