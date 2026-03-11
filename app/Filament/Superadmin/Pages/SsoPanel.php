<?php

namespace App\Filament\Superadmin\Pages;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Filament\Pages\Page;

class SsoPanel extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $title = 'SSO Panel';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-on-rectangle';
    protected static ?string $navigationLabel = 'SSO Panel';
    protected static ?string $navigationGroup = 'Utama';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.superadmin.pages.sso-panel';

    public ?array $data = [];

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $role = strtolower(trim($user->role_user?->name ?? ''));
        if (! in_array($role, ['super admin', 'superadmin'])) {
            abort(403);
        }

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Akses Panel Langsung')
                    ->icon('heroicon-o-squares-2x2')
                    ->description('Masuk ke panel lain sebagai Superadmin. Anda tetap login dengan akun Superadmin.')
                    ->schema([
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('open_guru')
                                ->label('Panel Guru')
                                ->icon('heroicon-o-user-group')
                                ->color('info')
                                ->size('lg')
                                ->extraAttributes(['style' => 'width:100%'])
                                ->url('/portal-guru-smkn1', shouldOpenInNewTab: true),

                            Forms\Components\Actions\Action::make('open_kesiswaan')
                                ->label('Panel Kesiswaan')
                                ->icon('heroicon-o-clipboard-document-list')
                                ->color('info')
                                ->size('lg')
                                ->extraAttributes(['style' => 'width:100%'])
                                ->url('/portal-kesiswaan-smkn1', shouldOpenInNewTab: true),
                        ])->fullWidth(),
                    ]),

                Forms\Components\Section::make('Panel Siswa Per Agama')
                    ->icon('heroicon-o-globe-alt')
                    ->description('Preview dashboard siswa untuk setiap agama.')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('open_islam')
                                ->label('Islam')
                                ->color('info')
                                ->size('lg')
                                ->extraAttributes(['style' => 'width:100%'])
                                ->url('/siswa/home', shouldOpenInNewTab: true),

                            Forms\Components\Actions\Action::make('open_kristen')
                                ->label('Kristen')
                                ->color('info')
                                ->size('lg')
                                ->extraAttributes(['style' => 'width:100%'])
                                ->url('/siswa/home-kristen', shouldOpenInNewTab: true),

                            Forms\Components\Actions\Action::make('open_katolik')
                                ->label('Katolik')
                                ->color('info')
                                ->size('lg')
                                ->extraAttributes(['style' => 'width:100%'])
                                ->url('/siswa/home-kristen', shouldOpenInNewTab: true),

                            Forms\Components\Actions\Action::make('open_hindu')
                                ->label('Hindu')
                                ->color('info')
                                ->size('lg')
                                ->extraAttributes(['style' => 'width:100%'])
                                ->url('/siswa/home-hindu', shouldOpenInNewTab: true),

                            Forms\Components\Actions\Action::make('open_buddha')
                                ->label('Buddha')
                                ->color('info')
                                ->size('lg')
                                ->extraAttributes(['style' => 'width:100%'])
                                ->url('/siswa/home-buddha', shouldOpenInNewTab: true),

                            Forms\Components\Actions\Action::make('open_konghucu')
                                ->label('Konghucu')
                                ->color('info')
                                ->size('lg')
                                ->extraAttributes(['style' => 'width:100%'])
                                ->url('/siswa/home-konghucu', shouldOpenInNewTab: true),
                        ])->fullWidth(),
                    ]),

                Forms\Components\Section::make('Informasi')
                    ->icon('heroicon-o-shield-check')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('info')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString("
                                <div style='background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:16px;'>
                                    <div style='display:flex;align-items:center;gap:8px;margin-bottom:8px;'>
                                        <span style='font-weight:700;color:#1e40af;font-size:15px;'>Akses Superadmin</span>
                                    </div>
                                    <ul style='margin:0;padding-left:20px;color:#374151;font-size:13px;line-height:1.8;'>
                                        <li>Anda masuk ke panel lain <strong>tetap sebagai akun Superadmin</strong>.</li>
                                        <li>Tidak perlu memilih akun — langsung akses panel yang dituju.</li>
                                        <li>Panel akan terbuka di <strong>tab baru</strong>.</li>
                                        <li>Fitur ini <strong>hanya tersedia untuk Superadmin</strong>.</li>
                                    </ul>
                                </div>
                            ")),
                    ]),
            ])
            ->statePath('data');
    }
}
