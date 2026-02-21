<?php

namespace App\Filament\Superadmin\Resources\FormSettingResource\Pages;

use App\Filament\Superadmin\Resources\FormSettingResource;
use Filament\Resources\Pages\ListRecords;

class ListFormSettings extends ListRecords
{
  protected static string $resource = FormSettingResource::class;

  protected function getHeaderActions(): array
  {
    return [
      \Filament\Actions\CreateAction::make(),
    ];
  }
}
