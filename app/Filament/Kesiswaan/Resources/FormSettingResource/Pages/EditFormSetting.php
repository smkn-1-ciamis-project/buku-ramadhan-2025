<?php

namespace App\Filament\Kesiswaan\Resources\FormSettingResource\Pages;

use App\Filament\Kesiswaan\Resources\FormSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFormSetting extends EditRecord
{
  protected static string $resource = FormSettingResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\DeleteAction::make(),
    ];
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
