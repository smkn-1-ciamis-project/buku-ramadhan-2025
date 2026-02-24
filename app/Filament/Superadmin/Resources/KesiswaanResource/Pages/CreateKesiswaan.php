<?php

namespace App\Filament\Superadmin\Resources\KesiswaanResource\Pages;

use App\Filament\Superadmin\Resources\KesiswaanResource;
use App\Models\ActivityLog;
use App\Models\RoleUser;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateKesiswaan extends CreateRecord
{
    protected static string $resource = KesiswaanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role_user_id'] = RoleUser::where('name', 'Kesiswaan')->first()?->id;
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
        return 'Kesiswaan berhasil ditambahkan';
    }

    protected function afterCreate(): void
    {
        ActivityLog::log('create_kesiswaan', Auth::user(), [
            'description' => 'Menambahkan kesiswaan baru: ' . $this->record->name,
            'target_user_id' => $this->record->id,
            'target_user' => $this->record->name,
        ]);
    }
}
