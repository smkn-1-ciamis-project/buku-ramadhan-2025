<?php

namespace App\Filament\Siswa\Pages\NonMuslim\Buddha;

use Filament\Pages\Page;

class FormulirHarian extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Formulir Harian Buddha';
    protected static ?string $title = '';
    protected static ?string $slug = 'formulir-harian-buddha';
    protected static string $view = 'siswa.nonmuslim.buddha.formulir-harian';
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
