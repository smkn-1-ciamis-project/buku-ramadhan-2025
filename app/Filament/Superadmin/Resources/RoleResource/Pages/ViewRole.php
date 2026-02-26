<?php

namespace App\Filament\Superadmin\Resources\RoleResource\Pages;

use App\Filament\Superadmin\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\MaxWidth;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected static string $view = 'filament.superadmin.resources.role.view';

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    public function getHeading(): string
    {
        return '';
    }

    public function getSubheading(): ?string
    {
        return null;
    }

    public function getTitle(): string
    {
        return 'Detail Role: ' . $this->record->name;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
