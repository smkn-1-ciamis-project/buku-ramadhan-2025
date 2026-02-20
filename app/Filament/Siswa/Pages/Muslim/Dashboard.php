<?php

namespace App\Filament\Siswa\Pages\Muslim;

use App\Filament\Siswa\Pages\NonMuslim\Dashboard as NonMuslimDashboard;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = '';
    protected static ?string $slug = 'home';
    protected static string $view = 'siswa.muslim.dashboard';

    public function mount(): void
    {
        $agama = strtolower(auth()->user()->agama ?? '');

        // Non-Muslim students get redirected to their own dashboard
        if ($agama !== '' && $agama !== 'islam') {
            redirect()->to(NonMuslimDashboard::getUrl());
        }
    }

    public function getHeading(): string
    {
        return '';
    }

    public function getSubheading(): ?string
    {
        return null;
    }
}
