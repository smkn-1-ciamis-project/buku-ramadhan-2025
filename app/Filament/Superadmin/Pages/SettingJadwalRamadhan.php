<?php

namespace App\Filament\Superadmin\Pages;

use App\Models\AppSetting;
use App\Models\RoleUser;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SettingJadwalRamadhan extends Page implements HasForms
{
  use InteractsWithForms;

  protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
  protected static ?string $navigationLabel = 'Setting Jadwal Ramadhan';
  protected static ?string $navigationGroup = 'Pengaturan';
  protected static ?string $title = 'Setting Jadwal Ramadhan';
  protected static ?string $slug = 'setting-jadwal-ramadhan';
  protected static ?int $navigationSort = 12;
  protected static string $view = 'filament.superadmin.pages.setting-jadwal-ramadhan';

  public ?array $data = [];

  public static function shouldRegisterNavigation(): bool
  {
    return RoleUser::checkNav('sa_setting_formulir');
  }

  public function mount(): void
  {
    $settings = AppSetting::getGroup('ramadhan');

    $this->form->fill([
      'ramadhan_start_date' => $settings['ramadhan_start_date'] ?? '2026-02-19',
      'ramadhan_end_date'   => $settings['ramadhan_end_date'] ?? '2026-03-20',
      'ramadhan_total_days' => $settings['ramadhan_total_days'] ?? 30,
      'hijri_year'          => $settings['hijri_year'] ?? '1447 H',
    ]);
  }

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('Jadwal Ramadhan')
          ->description('Atur tanggal mulai dan akhir bulan Ramadhan. Pengaturan ini akan otomatis diterapkan ke seluruh halaman siswa (Muslim & Non-Muslim).')
          ->icon('heroicon-o-calendar-days')
          ->schema([
            Forms\Components\DatePicker::make('ramadhan_start_date')
              ->label('Tanggal Mulai Ramadhan (1 Ramadhan)')
              ->required()
              ->native(false)
              ->displayFormat('d F Y')
              ->helperText('Tanggal 1 Ramadhan dalam kalender Masehi')
              ->reactive()
              ->afterStateUpdated(function (Forms\Set $set, ?string $state, Forms\Get $get) {
                if ($state) {
                  $start = \Carbon\Carbon::parse($state);
                  $totalDays = (int) ($get('ramadhan_total_days') ?: 30);
                  $end = $start->copy()->addDays($totalDays - 1);
                  $set('ramadhan_end_date', $end->format('Y-m-d'));
                }
              }),
            Forms\Components\DatePicker::make('ramadhan_end_date')
              ->label('Tanggal Akhir Ramadhan (29/30 Ramadhan)')
              ->required()
              ->native(false)
              ->displayFormat('d F Y')
              ->helperText('Tanggal terakhir bulan Ramadhan dalam kalender Masehi')
              ->reactive()
              ->afterStateUpdated(function (Forms\Set $set, ?string $state, Forms\Get $get) {
                if ($state && $get('ramadhan_start_date')) {
                  $start = \Carbon\Carbon::parse($get('ramadhan_start_date'));
                  $end = \Carbon\Carbon::parse($state);
                  $diff = $start->diffInDays($end) + 1;
                  $set('ramadhan_total_days', $diff);
                }
              }),
            Forms\Components\Select::make('ramadhan_total_days')
              ->label('Jumlah Hari Ramadhan')
              ->required()
              ->options([
                29 => '29 Hari',
                30 => '30 Hari',
              ])
              ->default(30)
              ->helperText('Ramadhan biasanya 29 atau 30 hari tergantung rukyatul hilal')
              ->reactive()
              ->afterStateUpdated(function (Forms\Set $set, ?string $state, Forms\Get $get) {
                if ($state && $get('ramadhan_start_date')) {
                  $start = \Carbon\Carbon::parse($get('ramadhan_start_date'));
                  $end = $start->copy()->addDays((int) $state - 1);
                  $set('ramadhan_end_date', $end->format('Y-m-d'));
                }
              }),
            Forms\Components\TextInput::make('hijri_year')
              ->label('Tahun Hijriyah')
              ->required()
              ->placeholder('1447 H')
              ->helperText('Tahun Hijriyah yang ditampilkan di dashboard siswa Muslim (contoh: 1447 H)'),
          ])
          ->columns(2),

        Forms\Components\Section::make('Informasi')
          ->icon('heroicon-o-information-circle')
          ->schema([
            Forms\Components\Placeholder::make('info_text')
              ->label('')
              ->content(new \Illuminate\Support\HtmlString('
                                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                                    <p><strong>Catatan penting:</strong></p>
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Pengaturan ini berlaku untuk <strong>seluruh siswa</strong> (Muslim, Kristen, Hindu, Buddha, Konghucu).</li>
                                        <li>Setelah menyimpan, pengaturan baru akan aktif dalam <strong>maksimal 1 jam</strong> (cache TTL), atau bisa dipercepat dengan clear cache.</li>
                                        <li>Tanggal mulai menentukan hari ke-1 pada kalender siswa.</li>
                                        <li>Pastikan tanggal sesuai dengan keputusan pemerintah terkait penetapan awal Ramadhan.</li>
                                    </ul>
                                </div>
                            ')),
          ]),
      ])
      ->statePath('data');
  }

  public function save(): void
  {
    $data = $this->form->getState();

    $keys = ['ramadhan_start_date', 'ramadhan_end_date', 'ramadhan_total_days', 'hijri_year'];

    foreach ($keys as $key) {
      AppSetting::setValue($key, $data[$key]);
    }

    Notification::make()
      ->title('Jadwal Ramadhan berhasil disimpan')
      ->body('Tanggal ' . $data['ramadhan_start_date'] . ' s.d. ' . $data['ramadhan_end_date'] . ' (' . $data['hijri_year'] . ')')
      ->success()
      ->send();
  }
}
