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
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class PushNotifikasi extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';
    protected static ?string $navigationLabel = 'Push Notifikasi';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?string $title = 'Push Notifikasi ke Device';
    protected static ?string $slug = 'push-notifikasi';
    protected static ?int $navigationSort = 13;
    protected static string $view = 'filament.superadmin.pages.push-notifikasi';

    public ?array $data = [];
    public bool $showSubscriberModal = false;
    public string $subscriberRole = 'all';
    public string $subscriberSearch = '';
    public int $subscriberPage = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return RoleUser::checkNav('sa_setting_formulir');
    }

    public function mount(): void
    {
        $this->form->fill([
            'target'   => 'all',
            'template' => null,
            'title'    => '',
            'body'     => '',
            'url'      => '/',
            'send_mode' => 'now',
            'scheduled_at' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Kirim Push Notifikasi')
                    ->description('Kirim notifikasi langsung ke device pengguna yang sudah mengizinkan notifikasi.')
                    ->icon('heroicon-o-paper-airplane')
                    ->schema([
                        Forms\Components\Select::make('target')
                            ->label('Kirim Ke')
                            ->options([
                                'all'       => 'Semua pengguna',
                                'siswa'     => 'Hanya Siswa',
                                'guru'      => 'Hanya Guru',
                                'kesiswaan' => 'Hanya Kesiswaan',
                            ])
                            ->default('all')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(Forms\Set $set) => $set('template', null)),

                        Forms\Components\Select::make('template')
                            ->label('Template Pesan')
                            ->placeholder('Pilih template atau tulis manual...')
                            ->options(function (Forms\Get $get) {
                                $target = $get('target');
                                $limited = in_array($target, ['guru', 'kesiswaan']);

                                $options = [
                                    'maintenance' => 'Maintenance — Pemberitahuan perbaikan sistem',
                                    'pengumuman'  => 'Pengumuman — Informasi umum',
                                    'update'      => 'Update — Fitur baru tersedia',
                                ];

                                if (! $limited) {
                                    $options['reminder'] = 'Pengingat — Isi formulir harian';
                                    $options['jadwal']   = 'Jadwal — Info jadwal kegiatan';
                                }

                                return $options;
                            })
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                if (! $state) return;
                                [$title, $body] = match ($state) {
                                    'maintenance' => [
                                        'Pemberitahuan Maintenance',
                                        'Sistem sedang dalam perbaikan. Mohon maaf atas ketidaknyamanannya, layanan akan segera kembali normal.',
                                    ],
                                    'pengumuman' => [
                                        'Pengumuman Penting',
                                        'Ada informasi penting dari sekolah. Silakan buka aplikasi untuk melihat detail.',
                                    ],
                                    'reminder' => [
                                        'Jangan Lupa Isi Formulir!',
                                        'Hai, sudahkah kamu mengisi formulir kegiatan ibadah hari ini? Yuk segera isi sebelum batas waktu.',
                                    ],
                                    'update' => [
                                        'Fitur Baru Tersedia! 🎉',
                                        'Aplikasi Calakan telah diperbarui dengan fitur baru. Buka aplikasi untuk melihat perubahannya.',
                                    ],
                                    'jadwal' => [
                                        'Info Jadwal Kegiatan',
                                        'Ada jadwal kegiatan Ramadan yang perlu kamu ketahui. Buka aplikasi untuk melihat detail.',
                                    ],
                                    default => ['', ''],
                                };
                                $set('title', $title);
                                $set('body', $body);
                            }),

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

                        Forms\Components\TextInput::make('url')
                            ->label('URL Tujuan (opsional)')
                            ->placeholder('/')
                            ->helperText('Halaman yang dibuka saat notifikasi diklik. Kosong = halaman utama.')
                            ->maxLength(255),

                        Forms\Components\Radio::make('send_mode')
                            ->label('Waktu Pengiriman')
                            ->options([
                                'now'       => 'Kirim Sekarang',
                                'scheduled' => 'Jadwalkan Pengiriman',
                            ])
                            ->default('now')
                            ->live()
                            ->required(),

                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->label('Jadwal Kirim')
                            ->native(false)
                            ->seconds(false)
                            ->minDate(now())
                            ->helperText('Notifikasi akan dikirim otomatis pada waktu yang dijadwalkan.')
                            ->required(fn(Forms\Get $get) => $get('send_mode') === 'scheduled')
                            ->visible(fn(Forms\Get $get) => $get('send_mode') === 'scheduled'),
                    ]),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PushSubscription::query()
                    ->with(['user.role_user', 'user.kelas'])
                    ->whereIn('id', function ($query) {
                        $query->selectRaw('MAX(id)')
                            ->from('push_subscriptions')
                            ->groupBy('user_id');
                    })
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.role_user.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn(string $state): string => match (strtolower(trim($state))) {
                        'siswa' => 'success',
                        'guru' => 'warning',
                        'kesiswaan', 'kepala sekolah' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.nisn')
                    ->label('NISN')
                    ->placeholder('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->placeholder('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.kelas.nama')
                    ->label('Kelas')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'siswa'     => 'Siswa',
                        'guru'      => 'Guru',
                        'kesiswaan' => 'Kesiswaan',
                    ])
                    ->query(function ($query, array $data) {
                        if (empty($data['value'])) return $query;
                        return $query->whereHas('user.role_user', function ($q) use ($data) {
                            if ($data['value'] === 'kesiswaan') {
                                $q->whereRaw("LOWER(TRIM(name)) IN ('kesiswaan', 'kepala sekolah')");
                            } else {
                                $q->whereRaw('LOWER(TRIM(name)) = ?', [$data['value']]);
                            }
                        });
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->heading('Detail Pengikut Push Notifikasi')
            ->description('Daftar pengguna yang sudah berlangganan push notifikasi di perangkat mereka.');
    }

    public function send(): void
    {
        $data = $this->form->getState();

        $title    = $data['title'];
        $body     = $data['body'];
        $target   = $data['target'] ?? 'all';
        $url      = $data['url'] ?: '/';
        $sendMode = $data['send_mode'] ?? 'now';

        if ($sendMode === 'scheduled') {
            $scheduledAt = $data['scheduled_at'];

            PushNotification::create([
                'title'        => $title,
                'body'         => $body,
                'url'          => $url,
                'target'       => $target,
                'scheduled_at' => $scheduledAt,
                'status'       => 'scheduled',
                'sent_count'   => 0,
                'failed_count' => 0,
                'sent_by'      => Auth::id(),
            ]);

            Notification::make()
                ->title('Notifikasi dijadwalkan!')
                ->body('Akan dikirim pada ' . \Carbon\Carbon::parse($scheduledAt)->translatedFormat('d F Y H:i') . ' WIB')
                ->success()
                ->send();

            $this->resetForm();
            return;
        }

        $result = PushNotificationService::send($title, $body, $target, $url);

        if ($result['sent'] === 0 && $result['failed'] === 0) {
            Notification::make()
                ->title('Tidak ada pengikut')
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

        $this->resetForm();
    }

    public function cancelScheduled(string $id): void
    {
        $notif = PushNotification::where('id', $id)
            ->where('status', 'scheduled')
            ->first();

        if ($notif) {
            $notif->update(['status' => 'cancelled']);

            Notification::make()
                ->title('Jadwal dibatalkan')
                ->body("Notifikasi \"{$notif->title}\" berhasil dibatalkan.")
                ->success()
                ->send();
        }
    }

    public function getStatsProperty(): array
    {
        return [
            'total' => PushSubscription::distinct('user_id')->count('user_id'),
            'siswa' => PushSubscription::whereHas('user', fn($q) => $q->whereHas('role_user', fn($rq) => $rq->whereRaw("LOWER(TRIM(name)) = 'siswa'")))->distinct('user_id')->count('user_id'),
            'guru' => PushSubscription::whereHas('user', fn($q) => $q->whereHas('role_user', fn($rq) => $rq->whereRaw("LOWER(TRIM(name)) = 'guru'")))->distinct('user_id')->count('user_id'),
            'kesiswaan' => PushSubscription::whereHas('user', fn($q) => $q->whereHas('role_user', fn($rq) => $rq->whereRaw("LOWER(TRIM(name)) IN ('kesiswaan', 'kepala sekolah')")))->distinct('user_id')->count('user_id'),
        ];
    }

    public function getScheduledNotificationsProperty()
    {
        return PushNotification::where('status', 'scheduled')
            ->orderBy('scheduled_at')
            ->get();
    }

    private function resetForm(): void
    {
        $this->form->fill([
            'target'       => 'all',
            'template'     => null,
            'title'        => '',
            'body'         => '',
            'url'          => '/',
            'send_mode'    => 'now',
            'scheduled_at' => null,
        ]);
    }
}
