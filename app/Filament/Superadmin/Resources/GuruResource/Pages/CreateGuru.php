<?php

namespace App\Filament\Superadmin\Resources\GuruResource\Pages;

use App\Filament\Superadmin\Resources\GuruResource;
use App\Models\RoleUser;
use Filament\Resources\Pages\CreateRecord;

class CreateGuru extends CreateRecord
{
  protected static string $resource = GuruResource::class;

  protected function mutateFormDataBeforeCreate(array $data): array
  {
    $data['role_user_id'] = RoleUser::where('name', 'Guru')->first()?->id;
    $data['email_verified_at'] = now();
    return $data;
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
