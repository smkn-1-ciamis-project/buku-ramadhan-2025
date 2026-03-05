<?php

namespace App\Filament\Superadmin\Pages;

use App\Models\PushNotification;
use App\Models\PushSubscription;
use App\Models\RoleUser;
use App\Services\PushNotificationService;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PushNotifikasi extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';
    protected static ?string $navigationLabel = 'Push Notifikasi';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?string $title = 'Push Notifikasi ke Device';
    protected static ?string $slug = 'push-notifikasi';
    protected static ?int $navigationSort = 13;
    protected static string $view = 'filament.superadmin.pages.push-notifikasi';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return RoleUser::checkNav('sa_setting_formulir');
    }

    public function mount(): void
    {
        $this->form->fill([
            'title'  => '',
            'body'   => '',
            'target' => 'all',
            'url'    => '/',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Kirim Push Notifikasi')
                    ->description('Kirim notifikasi langsung ke device pengguna yang sudah mengizinkan notifikasi. Notifikasi akan muncul di bar notifikasi perangkat.')
                    ->icon('heroicon-o-paper-airplane')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Notifikasi')
                            ->placeholder('Contoh: Pengumuman Penting')
                            ->maxLength(100)
                            ->required(),

                        Forms\Components\Textarea::make('body')
                            ->label('Isi Pesan')
                            ->placeholder('Contoh: Jangan lupa mengisi formulir kegiatan hari ini.')
                            ->rows(3)
                            ->maxLength(500)
                            ->required(),

                        Forms\Components\Select::make('target')
                            ->label('Kirim Ke')
                            ->options([
                                'all'       => 'Semua pengguna',
                                'siswa'     => 'Hanya Siswa',
                                'guru'      => 'Hanya Guru',
                                'kesiswaan' => 'Hanya Kesiswaan',
                            ])
                            ->default('all')
                            ->required(),

                        Forms\Components\TextInput::make('url')
                            ->label('URL Tujuan (opsional)')
                            ->placeholder('/')
                            ->helperText('Halaman yang dibuka saat notifikasi diklik. Kosong = halaman utama.')
                            ->maxLength(255),
                    ]),

                Forms\Components\Section::make('Statistik Subscriber')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('stats')
                            ->label('')
                            ->content(function () {
                                $total = PushSubscription::count();
                                $siswa = PushSubscription::whereHas('user', fn($q) => $q->whereHas('role_user', fn($rq) => $rq->whereRaw("LOWER(TRIM(name)) = 'siswa'")))->count();
                                $guru = PushSubscription::whereHas('user', fn($q) => $q->whereHas('role_user', fn($rq) => $rq->whereRaw("LOWER(TRIM(name)) = 'guru'")))->count();
                                $kesiswaan = PushSubscription::whereHas('user', fn($q) => $q->whereHas('role_user', fn($rq) => $rq->whereRaw("LOWER(TRIM(name)) IN ('kesiswaan', 'kepala sekolah')")))->count();

                                return new \Illuminate\Support\HtmlString("
                                    <div style='display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;'>
                                        <div style='background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px;text-align:center;'>
                                            <div style='font-size:28px;font-weight:800;color:#1e40af;'>{$total}</div>
                                            <div style='color:#3b82f6;font-size:13px;font-weight:600;'>Total Subscriber</div>
                                        </div>
                                        <div style='background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px;text-align:center;'>
                                            <div style='font-size:28px;font-weight:800;color:#166534;'>{$siswa}</div>
                                            <div style='color:#22c55e;font-size:13px;font-weight:600;'>Siswa</div>
                                        </div>
                                        <div style='background:#fefce8;border:1px solid #fde68a;border-radius:10px;padding:14px;text-align:center;'>
                                            <div style='font-size:28px;font-weight:800;color:#92400e;'>{$guru}</div>
                                            <div style='color:#f59e0b;font-size:13px;font-weight:600;'>Guru</div>
                                        </div>
                                        <div style='background:#fdf2f8;border:1px solid #fbcfe8;border-radius:10px;padding:14px;text-align:center;'>
                                            <div style='font-size:28px;font-weight:800;color:#9d174d;'>{$kesiswaan}</div>
                                            <div style='color:#ec4899;font-size:13px;font-weight:600;'>Kesiswaan</div>
                                        </div>
                                    </div>
                                ");
                            }),
                    ]),

                Forms\Components\Section::make('Riwayat Notifikasi Terakhir')
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('history')
                            ->label('')
                            ->content(function () {
                                $notifications = PushNotification::orderBy('created_at', 'desc')
                                    ->limit(10)
                                    ->get();

                                if ($notifications->isEmpty()) {
                                    return new \Illuminate\Support\HtmlString('<p style="color:#9ca3af;text-align:center;padding:16px;">Belum ada notifikasi yang dikirim.</p>');
                                }

                                $html = '<div style="display:flex;flex-direction:column;gap:8px;">';
                                foreach ($notifications as $notif) {
                                    $targetLabel = match ($notif->target) {
                                        'all'       => '🌐 Semua',
                                        'siswa'     => '🎓 Siswa',
                                        'guru'      => '👨‍🏫 Guru',
                                        'kesiswaan' => '📋 Kesiswaan',
                                        default     => $notif->target,
                                    };
                                    $date = $notif->created_at->format('d M Y H:i');
                                    $sentBadge = "<span style='background:#dcfce7;color:#166534;padding:2px 8px;border-radius:9999px;font-size:11px;font-weight:600;'>✓ {$notif->sent_count} terkirim</span>";
                                    $failBadge = $notif->failed_count > 0
                                        ? " <span style='background:#fef2f2;color:#991b1b;padding:2px 8px;border-radius:9999px;font-size:11px;font-weight:600;'>✗ {$notif->failed_count} gagal</span>"
                                        : '';

                                    $title = e($notif->title);
                                    $body = e(\Illuminate\Support\Str::limit($notif->body, 80));

                                    $html .= "
                                        <div style='background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px;'>
                                            <div style='display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;'>
                                                <span style='font-weight:700;font-size:14px;'>{$title}</span>
                                                <span style='font-size:11px;color:#9ca3af;'>{$date}</span>
                                            </div>
                                            <div style='font-size:13px;color:#6b7280;margin-bottom:6px;'>{$body}</div>
                                            <div style='display:flex;gap:6px;align-items:center;flex-wrap:wrap;'>
                                                <span style='background:#eff6ff;color:#1e40af;padding:2px 8px;border-radius:9999px;font-size:11px;font-weight:600;'>{$targetLabel}</span>
                                                {$sentBadge}{$failBadge}
                                            </div>
                                        </div>
                                    ";
                                }
                                $html .= '</div>';

                                return new \Illuminate\Support\HtmlString($html);
                            }),
                    ]),
            ])
            ->statePath('data');
    }

    public function send(): void
    {
        $data = $this->form->getState();

        $title  = $data['title'];
        $body   = $data['body'];
        $target = $data['target'] ?? 'all';
        $url    = $data['url'] ?: '/';

        $result = PushNotificationService::send($title, $body, $target, $url);

        if ($result['sent'] === 0 && $result['failed'] === 0) {
            Notification::make()
                ->title('Tidak ada subscriber')
                ->body('Belum ada device yang berlangganan push notifikasi untuk target ini.')
                ->warning()
                ->send();
            return;
        }

        $message = "Terkirim: {$result['sent']}";
        if ($result['failed'] > 0) {
            $message .= " | Gagal: {$result['failed']}";
        }

        Notification::make()
            ->title('Push notifikasi berhasil dikirim!')
            ->body($message)
            ->success()
            ->send();

        // Reset form
        $this->form->fill([
            'title'  => '',
            'body'   => '',
            'target' => 'all',
            'url'    => '/',
        ]);
    }
}
