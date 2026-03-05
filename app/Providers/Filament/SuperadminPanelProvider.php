<?php

namespace App\Providers\Filament;

use App\Filament\Superadmin\Pages\Auth\Login as SuperadminLogin;
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
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SuperadminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('superadmin')
            ->path('portal-admin-smkn1')
            ->login(SuperadminLogin::class)
            ->brandName('Panel Superadmin')
            ->brandLogo(new HtmlString('<span style="display:flex;align-items:center;gap:0.6rem;"><img src="' . asset('img/logo_smk.png') . '" style="height:2rem;border-radius:4px;flex-shrink:0;"><span style="font-size:0.95rem;font-weight:700;color:inherit;line-height:1;white-space:nowrap;">Panel Superadmin</span></span>'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('img/logo_smk.png'))
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
                ThrottleRequests::class . ':panel',
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureSingleSession::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn() => new HtmlString('
                    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
                    <link rel="manifest" href="/manifest.json">
                    <meta name="theme-color" content="#1e3a8a">
                    <meta name="apple-mobile-web-app-capable" content="yes">
                    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
                    <meta name="apple-mobile-web-app-title" content="Calakan">
                    <link rel="apple-touch-icon" href="/img/icons/icon-152x152.png">
                    <link rel="apple-touch-icon" sizes="192x192" href="/img/icons/icon-192x192.png">

                    <meta name="description" content="Calakan — Aplikasi pencatatan dan monitoring kegiatan ibadah siswa selama bulan Ramadan 1447H. Mendukung 6 agama. SMKN 1 Ciamis.">
                    <meta property="og:type" content="website">
                    <meta property="og:url" content="https://ramadhan.smkn1ciamis.id">
                    <meta property="og:title" content="Calakan — Catatan Amaliyah Kegiatan Ramadan">
                    <meta property="og:description" content="Aplikasi pencatatan dan monitoring kegiatan ibadah siswa selama bulan Ramadan 1447H. Mendukung 6 agama: Islam, Kristen, Katolik, Hindu, Buddha, dan Konghucu. SMKN 1 Ciamis.">
                    <meta property="og:image" content="https://ramadhan.smkn1ciamis.id/img/og_image.jpg">
                    <meta property="og:image:width" content="1200">
                    <meta property="og:image:height" content="630">
                    <meta property="og:image:type" content="image/jpeg">
                    <meta property="og:site_name" content="Calakan — SMKN 1 Ciamis">
                    <meta property="og:locale" content="id_ID">
                    <meta name="twitter:card" content="summary_large_image">
                    <meta name="twitter:title" content="Calakan — Catatan Amaliyah Kegiatan Ramadan">
                    <meta name="twitter:description" content="Aplikasi pencatatan dan monitoring kegiatan ibadah siswa selama bulan Ramadan 1447H. SMKN 1 Ciamis.">
                    <meta name="twitter:image" content="https://ramadhan.smkn1ciamis.id/img/og_image.jpg">

                    <style>
                        html, body { overscroll-behavior: none; }
                        body { padding-top: env(safe-area-inset-top); padding-bottom: env(safe-area-inset-bottom); }
                        .fi-sidebar-header a, .fi-topbar-start a { pointer-events: none !important; cursor: default !important; }
                    </style>
                ')
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn() => new HtmlString('
                    <script>
                        if ("serviceWorker" in navigator && !navigator.userAgent.includes("Calakan-Android")) {
                            window.addEventListener("load", () => {
                                navigator.serviceWorker.register("/sw.js").catch(() => {});
                            });
                        }
                    </script>
                ')
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn() => new HtmlString(view('components.push-subscription')->render())
            );
    }
}
