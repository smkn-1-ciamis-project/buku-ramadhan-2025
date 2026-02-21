<?php

namespace App\Filament\Superadmin\Resources\RoleResource\Pages;

use App\Filament\Superadmin\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRole extends ListRecords
{
  protected static string $resource = RoleResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make()->label('Tambah Role'),
    ];
  }
}
