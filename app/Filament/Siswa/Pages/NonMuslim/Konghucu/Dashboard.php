<?php

namespace App\Filament\Siswa\Pages\NonMuslim\Konghucu;

use App\Filament\Siswa\Pages\Muslim\Dashboard as MuslimDashboard;
use Filament\Pages\Page;
use App\Models\User;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard Konghucu';
    protected static ?string $title = '';
    protected static ?string $slug = 'home-konghucu';
    protected static string $view = 'siswa.nonmuslim.konghucu.dashboard';
    protected static bool $shouldRegisterNavigation = false;

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();
        $agama = strtolower($user->agama ?? '');

        if ($agama === 'islam') {
            redirect()->to(MuslimDashboard::getUrl());
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
