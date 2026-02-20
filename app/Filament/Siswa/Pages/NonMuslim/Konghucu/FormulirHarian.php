<?php

namespace App\Filament\Siswa\Pages\NonMuslim\Konghucu;

use Filament\Pages\Page;

class FormulirHarian extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Formulir Harian Konghucu';
    protected static ?string $title = '';
    protected static ?string $slug = 'formulir-harian-konghucu';
    protected static string $view = 'siswa.nonmuslim.konghucu.formulir-harian';
    protected static bool $shouldRegisterNavigation = false;

    public function getHeading(): string
    {
        return '';
    }

    public function getSubheading(): ?string
    {
        return null;
    }
}
