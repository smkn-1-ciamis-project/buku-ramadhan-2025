<?php

namespace App\Providers\Filament;

use App\Filament\Kesiswaan\Pages\Dashboard as KesiswaanDashboard;
use App\Http\Middleware\EnsureSingleSession;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class KesiswaanPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('kesiswaan')
            ->path('portal-kesiswaan-smkn1')
            ->login()
            ->brandName('Buku Ramadhan')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->navigationGroups([
                NavigationGroup::make('Utama'),
                NavigationGroup::make('Validasi'),
                NavigationGroup::make('Data & Rekap'),
                NavigationGroup::make('Pengaturan')
                    ->collapsed(),
            ])
            ->discoverResources(in: app_path('Filament/Kesiswaan/Resources'), for: 'App\\Filament\\Kesiswaan\\Resources')
            ->discoverPages(in: app_path('Filament/Kesiswaan/Pages'), for: 'App\\Filament\\Kesiswaan\\Pages')
            ->pages([
                KesiswaanDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Kesiswaan/Widgets'), for: 'App\\Filament\\Kesiswaan\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureSingleSession::class,
            ]);
    }
}
