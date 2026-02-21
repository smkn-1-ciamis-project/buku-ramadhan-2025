<?php

namespace App\Filament\Superadmin\Resources\SiswaResource\Pages;

use App\Filament\Superadmin\Resources\SiswaResource;
use App\Models\RoleUser;
use Filament\Resources\Pages\CreateRecord;

class CreateSiswa extends CreateRecord
{
  protected static string $resource = SiswaResource::class;

  protected function mutateFormDataBeforeCreate(array $data): array
  {
    $data['role_user_id'] = RoleUser::where('name', 'Siswa')->first()?->id;
    $data['email_verified_at'] = now();
    return $data;
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
