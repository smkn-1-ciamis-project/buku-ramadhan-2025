<?php

namespace App\Providers\Filament;

use App\Filament\Superadmin\Pages\Dashboard as SuperadminDashboard;
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
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SuperadminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('superadmin')
            ->path('portal-admin-smkn1')
            ->login()
            ->brandName('Calakan')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->navigationGroups([
                NavigationGroup::make('Utama'),
                NavigationGroup::make('Pengguna'),
                NavigationGroup::make('Akademik'),
                NavigationGroup::make('Pengaturan')
                    ->collapsed(),
                NavigationGroup::make('Akun')
                    ->collapsed(),
            ])
            ->discoverResources(in: app_path('Filament/Superadmin/Resources'), for: 'App\\Filament\\Superadmin\\Resources')
            ->discoverPages(in: app_path('Filament/Superadmin/Pages'), for: 'App\\Filament\\Superadmin\\Pages')
            ->pages([
                SuperadminDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Superadmin/Widgets'), for: 'App\\Filament\\Superadmin\\Widgets')
            ->widgets([])
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
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn() => new HtmlString('
                    <link rel="manifest" href="/manifest.json">
                    <meta name="theme-color" content="#1e3a8a">
                    <meta name="apple-mobile-web-app-capable" content="yes">
                    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
                    <meta name="apple-mobile-web-app-title" content="Calakan">
                    <link rel="apple-touch-icon" href="/img/icons/icon-152x152.png">
                ')
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn() => new HtmlString('
                    <script>
                        if ("serviceWorker" in navigator) {
                            window.addEventListener("load", () => {
                                navigator.serviceWorker.register("/sw.js").catch(() => {});
                            });
                        }
                    </script>
                ')
            );
    }
}
