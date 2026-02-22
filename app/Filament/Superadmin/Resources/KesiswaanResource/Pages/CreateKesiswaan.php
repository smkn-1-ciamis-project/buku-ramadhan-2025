<?php

namespace App\Filament\Superadmin\Resources\KesiswaanResource\Pages;

use App\Filament\Superadmin\Resources\KesiswaanResource;
use App\Models\RoleUser;
use Filament\Resources\Pages\CreateRecord;

class CreateKesiswaan extends CreateRecord
{
    protected static string $resource = KesiswaanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role_user_id'] = RoleUser::where('name', 'Kesiswaan')->first()?->id;
        $data['email_verified_at'] = now();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
