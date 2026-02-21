<?php

namespace App\Filament\Kesiswaan\Resources\FormSettingResource\Pages;

use App\Filament\Kesiswaan\Resources\FormSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFormSetting extends CreateRecord
{
  protected static string $resource = FormSettingResource::class;

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
