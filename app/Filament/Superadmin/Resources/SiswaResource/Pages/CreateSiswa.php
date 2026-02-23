<?php

namespace App\Filament\Superadmin\Resources\SiswaResource\Pages;

use App\Filament\Superadmin\Resources\SiswaResource;
use App\Models\RoleUser;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSiswa extends CreateRecord
{
  protected static string $resource = SiswaResource::class;

  protected function mutateFormDataBeforeCreate(array $data): array
  {
    $data['role_user_id'] = RoleUser::where('name', 'Siswa')->first()?->id;
    $data['email_verified_at'] = now();
    $data['must_change_password'] = true;

    // Auto-generate email from NISN if empty
    if (empty($data['email']) && !empty($data['nisn'])) {
      $data['email'] = $data['nisn'] . '@siswa.smkn1ciamis.sch.id';
    }

    return $data;
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }

  protected function getCreatedNotificationTitle(): ?string
  {
    return 'Siswa berhasil ditambahkan';
  }
}
