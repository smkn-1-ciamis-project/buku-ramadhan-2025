<?php

namespace App\Filament\Superadmin\Resources\GuruResource\Pages;

use App\Filament\Superadmin\Resources\GuruResource;
use App\Models\ActivityLog;
use App\Models\RoleUser;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateGuru extends CreateRecord
{
  protected static string $resource = GuruResource::class;

  protected function mutateFormDataBeforeCreate(array $data): array
  {
    $data['role_user_id'] = RoleUser::where('name', 'Guru')->first()?->id;
    $data['email_verified_at'] = now();
    $data['must_change_password'] = true;
    return $data;
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }

  protected function getCreatedNotificationTitle(): ?string
  {
    return 'Guru berhasil ditambahkan';
  }

  protected function afterCreate(): void
  {
    ActivityLog::log('create_guru', Auth::user(), [
      'description' => 'Menambahkan guru baru: ' . $this->record->name,
      'target_user_id' => $this->record->id,
      'target_user' => $this->record->name,
    ]);
  }
}
