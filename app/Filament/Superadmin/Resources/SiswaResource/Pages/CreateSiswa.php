<?php

namespace App\Filament\Superadmin\Resources\SiswaResource\Pages;

use App\Filament\Superadmin\Resources\SiswaResource;
use App\Models\ActivityLog;
use App\Models\RoleUser;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

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

  protected function afterCreate(): void
  {
    ActivityLog::log('create_siswa', Auth::user(), [
      'description' => 'Menambahkan siswa baru: ' . $this->record->name . ' (NISN: ' . ($this->record->nisn ?? '-') . ')',
      'target_user_id' => $this->record->id,
      'target_user' => $this->record->name,
      'nisn' => $this->record->nisn,
    ]);
  }
}
