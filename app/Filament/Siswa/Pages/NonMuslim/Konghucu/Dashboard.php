<?php

namespace App\Filament\Siswa\Pages\NonMuslim\Konghucu;

use App\Filament\Siswa\Pages\Muslim\Dashboard as MuslimDashboard;
use App\Filament\Siswa\Pages\NonMuslim\Hindu\Dashboard as HinduDashboard;
use App\Filament\Siswa\Pages\NonMuslim\Buddha\Dashboard as BuddhaDashboard;
use App\Filament\Siswa\Pages\NonMuslim\Kristen\Dashboard as KristenDashboard;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Traits\HasPasswordChangeModal;

class Dashboard extends Page
{
    use HasPasswordChangeModal;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard Konghucu';
    protected static ?string $title = '';
    protected static ?string $slug = 'home-konghucu';
    protected static string $view = 'siswa.nonmuslim.konghucu.dashboard';
    protected static bool $shouldRegisterNavigation = false;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $agama = strtolower($user->agama ?? '');

        // Redirect ke dashboard yang sesuai agama user
        $redirectMap = [
            'islam' => MuslimDashboard::getUrl(),
            'kristen' => KristenDashboard::getUrl(),
            'katolik' => KristenDashboard::getUrl(),
            'kristen katolik' => KristenDashboard::getUrl(),
            'hindu' => HinduDashboard::getUrl(),
            'buddha' => BuddhaDashboard::getUrl(),
            'budha' => BuddhaDashboard::getUrl(),
        ];

        if (isset($redirectMap[$agama])) {
            redirect()->to($redirectMap[$agama]);
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
