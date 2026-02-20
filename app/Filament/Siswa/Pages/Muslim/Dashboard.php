<?php

namespace App\Filament\Siswa\Pages\Muslim;

use App\Filament\Siswa\Pages\NonMuslim\Kristen\Dashboard as KristenDashboard;
use App\Filament\Siswa\Pages\NonMuslim\Hindu\Dashboard as HinduDashboard;
use App\Filament\Siswa\Pages\NonMuslim\Buddha\Dashboard as BuddhaDashboard;
use App\Filament\Siswa\Pages\NonMuslim\Konghucu\Dashboard as KonghucuDashboard;
use Filament\Pages\Page;
use App\Models\User;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = '';
    protected static ?string $slug = 'home';
    protected static string $view = 'siswa.muslim.dashboard';

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();
        $agama = strtolower($user->agama ?? '');

        $redirectMap = [
            'kristen' => KristenDashboard::getUrl(),
            'katolik' => KristenDashboard::getUrl(),
            'kristen katolik' => KristenDashboard::getUrl(),
            'hindu' => HinduDashboard::getUrl(),
            'buddha' => BuddhaDashboard::getUrl(),
            'budha' => BuddhaDashboard::getUrl(),
            'konghucu' => KonghucuDashboard::getUrl(),
            'khonghucu' => KonghucuDashboard::getUrl(),
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
