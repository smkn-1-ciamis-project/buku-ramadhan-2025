<?php

namespace App\Filament\Siswa\Pages\NonMuslim\Buddha;

use App\Filament\Siswa\Pages\Muslim\Dashboard as MuslimDashboard;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Traits\HasPasswordChangeModal;

class Dashboard extends Page
{
    use HasPasswordChangeModal;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard Buddha';
    protected static ?string $title = '';
    protected static ?string $slug = 'home-buddha';
    protected static string $view = 'siswa.nonmuslim.buddha.dashboard';
    protected static bool $shouldRegisterNavigation = false;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
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
