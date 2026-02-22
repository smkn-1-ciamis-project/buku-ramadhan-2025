<?php

namespace App\Filament\Superadmin\Resources\KesiswaanResource\Pages;

use App\Filament\Superadmin\Resources\KesiswaanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKesiswaan extends EditRecord
{
    protected static string $resource = KesiswaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
