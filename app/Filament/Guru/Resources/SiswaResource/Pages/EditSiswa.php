<?php

namespace App\Filament\Guru\Resources\SiswaResource\Pages;

use App\Filament\Guru\Resources\SiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSiswa extends EditRecord
{
  protected static string $resource = SiswaResource::class;

  protected function getHeaderActions(): array
  {
    return [];
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
