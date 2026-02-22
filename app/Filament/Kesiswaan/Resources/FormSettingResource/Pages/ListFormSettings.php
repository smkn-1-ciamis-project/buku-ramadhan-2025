<?php

namespace App\Filament\Kesiswaan\Resources\FormSettingResource\Pages;

use App\Filament\Kesiswaan\Resources\FormSettingResource;
use Filament\Resources\Pages\ListRecords;

class ListFormSettings extends ListRecords
{
  protected static string $resource = FormSettingResource::class;

  protected function getHeaderActions(): array
  {
    return [];
  }
}
