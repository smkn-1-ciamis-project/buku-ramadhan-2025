<?php

namespace App\Filament\Siswa\Pages\NonMuslim;

use App\Filament\Siswa\Pages\Muslim\Dashboard as MuslimDashboard;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = '';
    protected static ?string $slug = 'home-nonmuslim';
    protected static string $view = 'siswa.nonmuslim.dashboard';
    protected static bool $shouldRegisterNavigation = false;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $agama = strtolower($user->agama ?? '');

        // If user is Muslim, redirect to the Muslim dashboard
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
