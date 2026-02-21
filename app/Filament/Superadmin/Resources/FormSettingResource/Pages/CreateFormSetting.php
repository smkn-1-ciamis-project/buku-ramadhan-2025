<?php

namespace App\Filament\Superadmin\Resources\FormSettingResource\Pages;

use App\Filament\Superadmin\Resources\FormSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFormSetting extends CreateRecord
{
  protected static string $resource = FormSettingResource::class;

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
