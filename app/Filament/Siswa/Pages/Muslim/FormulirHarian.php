<?php

namespace App\Filament\Siswa\Pages\Muslim;

use Filament\Pages\Page;

class FormulirHarian extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Formulir Harian';
    protected static ?string $title = '';
    protected static ?string $slug = 'formulir-harian';
    protected static string $view = 'siswa.muslim.formulir-harian';
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
