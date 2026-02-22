<?php

namespace App\Filament\Superadmin\Resources\KesiswaanResource\Pages;

use App\Filament\Superadmin\Resources\KesiswaanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKesiswaan extends ListRecords
{
    protected static string $resource = KesiswaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Kesiswaan'),
        ];
    }
}
