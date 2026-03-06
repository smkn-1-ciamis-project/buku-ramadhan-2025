<?php

namespace App\Filament\Superadmin\Pages;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
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
                Forms\Components\Section::make('Panel Siswa')
                    ->icon('heroicon-o-academic-cap')
                    ->description(fn() => 'Total: ' . $this->getUserCount('siswa') . ' siswa terdaftar')
                    ->schema([
                        Forms\Components\Select::make('siswa_id')
                            ->label('Cari Siswa')
                            ->placeholder('Ketik nama atau NISN siswa...')
                            ->searchable()
                            ->searchDebounce(300)
                            ->getSearchResultsUsing(function (string $search) {
                                return User::whereHas('role_user', fn($q) => $q->whereRaw("LOWER(TRIM(name)) = 'siswa'"))
                                    ->where(fn($q) => $q->where('name', 'like', "%{$search}%")
                                        ->orWhere('nisn', 'like', "%{$search}%"))
                                    ->limit(20)
                                    ->get()
                                    ->mapWithKeys(fn($u) => [
                                        $u->id => $u->name
                                            . ($u->nisn ? " — NISN: {$u->nisn}" : '')
                                            . ($u->kelas ? " — {$u->kelas->nama}" : ''),
                                    ]);
                            })
                            ->getOptionLabelUsing(fn($value) => User::find($value)?->name)
                            ->helperText('Cari berdasarkan nama atau NISN, lalu klik tombol untuk masuk.'),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('login_siswa')
                                ->label('Masuk ke Panel Siswa')
                                ->icon('heroicon-o-arrow-right-on-rectangle')
                                ->color('success')
                                ->size('lg')
                                ->requiresConfirmation()
                                ->modalHeading('Konfirmasi SSO')
                                ->modalDescription('Anda akan masuk sebagai siswa yang dipilih di tab baru.')
                                ->modalSubmitActionLabel('Ya, Masuk')
                                ->action(function () {
                                    $userId = $this->data['siswa_id'] ?? null;
                                    if (! $userId) {
                                        Notification::make()->title('Pilih siswa terlebih dahulu')->warning()->send();
                                        return;
                                    }
                                    $url = route('impersonate', $userId);
                                    $this->js("window.open('{$url}', '_blank')");
                                }),
                        ]),
                    ]),

                Forms\Components\Section::make('Panel Guru')
                    ->icon('heroicon-o-user-group')
                    ->description(fn() => 'Total: ' . $this->getUserCount('guru') . ' guru terdaftar')
                    ->schema([
                        Forms\Components\Select::make('guru_id')
                            ->label('Cari Guru')
                            ->placeholder('Ketik nama atau email guru...')
                            ->searchable()
                            ->searchDebounce(300)
                            ->getSearchResultsUsing(function (string $search) {
                                return User::whereHas('role_user', fn($q) => $q->whereRaw("LOWER(TRIM(name)) = 'guru'"))
                                    ->where(fn($q) => $q->where('name', 'like', "%{$search}%")
                                        ->orWhere('email', 'like', "%{$search}%"))
                                    ->limit(20)
                                    ->get()
                                    ->mapWithKeys(fn($u) => [
                                        $u->id => "{$u->name} — {$u->email}",
                                    ]);
                            })
                            ->getOptionLabelUsing(fn($value) => User::find($value)?->name)
                            ->helperText('Cari berdasarkan nama atau email, lalu klik tombol untuk masuk.'),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('login_guru')
                                ->label('Masuk ke Panel Guru')
                                ->icon('heroicon-o-arrow-right-on-rectangle')
                                ->color('warning')
                                ->size('lg')
                                ->requiresConfirmation()
                                ->modalHeading('Konfirmasi SSO')
                                ->modalDescription('Anda akan masuk sebagai guru yang dipilih di tab baru.')
                                ->modalSubmitActionLabel('Ya, Masuk')
                                ->action(function () {
                                    $userId = $this->data['guru_id'] ?? null;
                                    if (! $userId) {
                                        Notification::make()->title('Pilih guru terlebih dahulu')->warning()->send();
                                        return;
                                    }
                                    $url = route('impersonate', $userId);
                                    $this->js("window.open('{$url}', '_blank')");
                                }),
                        ]),
                    ]),

                Forms\Components\Section::make('Panel Kesiswaan')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->description(fn() => 'Total: ' . $this->getUserCount('kesiswaan') . ' staff terdaftar')
                    ->schema([
                        Forms\Components\Select::make('kesiswaan_id')
                            ->label('Cari Kesiswaan / Kepala Sekolah')
                            ->placeholder('Ketik nama atau email...')
                            ->searchable()
                            ->searchDebounce(300)
                            ->getSearchResultsUsing(function (string $search) {
                                return User::whereHas('role_user', fn($q) => $q->whereRaw("LOWER(TRIM(name)) IN ('kesiswaan', 'kepala sekolah')"))
                                    ->where(fn($q) => $q->where('name', 'like', "%{$search}%")
                                        ->orWhere('email', 'like', "%{$search}%"))
                                    ->limit(20)
                                    ->get()
                                    ->mapWithKeys(fn($u) => [
                                        $u->id => "{$u->name} — {$u->email} ({$u->role_user?->name})",
                                    ]);
                            })
                            ->getOptionLabelUsing(fn($value) => User::find($value)?->name)
                            ->helperText('Cari berdasarkan nama atau email, lalu klik tombol untuk masuk.'),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('login_kesiswaan')
                                ->label('Masuk ke Panel Kesiswaan')
                                ->icon('heroicon-o-arrow-right-on-rectangle')
                                ->color('danger')
                                ->size('lg')
                                ->requiresConfirmation()
                                ->modalHeading('Konfirmasi SSO')
                                ->modalDescription('Anda akan masuk sebagai staff kesiswaan yang dipilih di tab baru.')
                                ->modalSubmitActionLabel('Ya, Masuk')
                                ->action(function () {
                                    $userId = $this->data['kesiswaan_id'] ?? null;
                                    if (! $userId) {
                                        Notification::make()->title('Pilih kesiswaan terlebih dahulu')->warning()->send();
                                        return;
                                    }
                                    $url = route('impersonate', $userId);
                                    $this->js("window.open('{$url}', '_blank')");
                                }),
                        ]),
                    ]),

                Forms\Components\Section::make('Informasi Keamanan')
                    ->icon('heroicon-o-shield-check')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('info')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString("
                                <div style='background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:16px;'>
                                    <div style='display:flex;align-items:center;gap:8px;margin-bottom:8px;'>
                                        <span style='font-size:18px;'>🔒</span>
                                        <span style='font-weight:700;color:#1e40af;font-size:15px;'>Hanya Superadmin</span>
                                    </div>
                                    <ul style='margin:0;padding-left:20px;color:#374151;font-size:13px;line-height:1.8;'>
                                        <li>Fitur SSO ini <strong>hanya bisa digunakan oleh Superadmin</strong>.</li>
                                        <li>Setiap aksi SSO akan <strong>dicatat di log aktivitas</strong>.</li>
                                        <li>Gunakan tombol <strong>\"Kembali ke Superadmin\"</strong> di halaman target untuk kembali.</li>
                                        <li>SSO akan membuka panel target di <strong>tab baru</strong>.</li>
                                    </ul>
                                </div>
                            ")),
                    ]),
            ])
            ->statePath('data');
    }

    private function getUserCount(string $role): int
    {
        if ($role === 'kesiswaan') {
            return User::whereHas('role_user', fn($q) => $q->whereRaw("LOWER(TRIM(name)) IN ('kesiswaan', 'kepala sekolah')"))->count();
        }

        return User::whereHas('role_user', fn($q) => $q->whereRaw('LOWER(TRIM(name)) = ?', [$role]))->count();
    }
}
