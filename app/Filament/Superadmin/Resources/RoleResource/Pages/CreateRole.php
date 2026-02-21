<?php

namespace App\Filament\Superadmin\Resources\RoleResource\Pages;

use App\Filament\Superadmin\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
  protected static string $resource = RoleResource::class;

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
