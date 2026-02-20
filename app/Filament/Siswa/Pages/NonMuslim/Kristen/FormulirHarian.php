<?php

namespace App\Filament\Siswa\Pages\NonMuslim\Kristen;

use Filament\Pages\Page;

class FormulirHarian extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Formulir Harian Kristen';
    protected static ?string $title = '';
    protected static ?string $slug = 'formulir-harian-kristen';
    protected static string $view = 'siswa.nonmuslim.kristen.formulir-harian';
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
