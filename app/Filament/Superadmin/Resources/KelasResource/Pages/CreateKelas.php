<?php

namespace App\Filament\Superadmin\Resources\KelasResource\Pages;

use App\Filament\Superadmin\Resources\KelasResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKelas extends CreateRecord
{
  protected static string $resource = KelasResource::class;

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
