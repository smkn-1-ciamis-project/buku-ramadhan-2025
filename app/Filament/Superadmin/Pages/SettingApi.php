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
use Illuminate\Support\HtmlString;

class SettingApi extends Page implements HasForms
{
  use InteractsWithForms;

  protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
  protected static ?string $navigationLabel = 'Setting API';
  protected static ?string $navigationGroup = 'Pengaturan';
  protected static ?string $title = 'Setting API & Lokasi';
  protected static ?string $slug = 'setting-api';
  protected static ?int $navigationSort = 11;
  protected static string $view = 'filament.superadmin.pages.setting-api';

  public ?array $data = [];

  public static function shouldRegisterNavigation(): bool
  {
    return RoleUser::checkNav('sa_setting_formulir');
  }

  public function mount(): void
  {
    $settings = AppSetting::getGroup('api') + AppSetting::getGroup('location');

    $this->form->fill([
      'prayer_api_url'    => $settings['prayer_api_url'] ?? 'https://api.aladhan.com/v1/timings/',
      'prayer_api_method' => $settings['prayer_api_method'] ?? 20,
      'quran_api_url'     => $settings['quran_api_url'] ?? 'https://api.alquran.cloud/v1/ayah/',
      'nominatim_api_url' => $settings['nominatim_api_url'] ?? 'https://nominatim.openstreetmap.org/reverse',
      'default_latitude'  => $settings['default_latitude'] ?? -7.3305,
      'default_longitude' => $settings['default_longitude'] ?? 108.3508,
      'default_city'      => $settings['default_city'] ?? 'Ciamis',
    ]);
  }

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('API Jadwal Sholat (Aladhan)')
          ->description('Konfigurasi API yang digunakan untuk mengambil jadwal waktu sholat pada dashboard siswa Muslim.')
          ->icon('heroicon-o-clock')
          ->schema([
            Forms\Components\TextInput::make('prayer_api_url')
              ->label('Base URL API Sholat')
              ->required()
              ->url()
              ->placeholder('https://api.aladhan.com/v1/timings/')
              ->helperText('URL dasar API Aladhan. Format lengkap: {url}{tanggal}?latitude=...&longitude=...&method=...'),
            Forms\Components\Select::make('prayer_api_method')
              ->label('Metode Perhitungan')
              ->required()
              ->options([
                0  => '0 — Jafari (Ithna Ashari)',
                1  => '1 — University of Islamic Sciences, Karachi',
                2  => '2 — Islamic Society of North America (ISNA)',
                3  => '3 — Muslim World League (MWL)',
                4  => '4 — Umm Al-Qura University, Makkah',
                5  => '5 — Egyptian General Authority of Survey',
                7  => '7 — Institute of Geophysics, University of Tehran',
                8  => '8 — Gulf Region',
                9  => '9 — Kuwait',
                10 => '10 — Qatar',
                11 => '11 — Majlis Ugama Islam Singapura',
                12 => '12 — UOIF (France)',
                13 => '13 — Diyanet İşleri Başkanlığı (Turkey)',
                14 => '14 — Spiritual Administration of Muslims of Russia',
                15 => '15 — Moonsighting Committee Worldwide',
                16 => '16 — Dubai',
                20 => '20 — Kementerian Agama RI',
              ])
              ->default(20)
              ->helperText('Metode 20 = Kemenag RI (default untuk Indonesia)'),
          ])
          ->columns(2),

        Forms\Components\Section::make('API Al-Quran')
          ->description('API untuk mengambil ayat harian Al-Quran pada dashboard siswa Muslim.')
          ->icon('heroicon-o-book-open')
          ->schema([
            Forms\Components\TextInput::make('quran_api_url')
              ->label('Base URL API Al-Quran')
              ->required()
              ->url()
              ->placeholder('https://api.alquran.cloud/v1/ayah/')
              ->helperText('URL dasar API Al-Quran Cloud. Format: {url}{surah}:{ayat}/{edition}'),
          ]),

        Forms\Components\Section::make('API Geocoding (Nominatim)')
          ->description('API untuk menampilkan nama lokasi dari koordinat GPS siswa.')
          ->icon('heroicon-o-map-pin')
          ->schema([
            Forms\Components\TextInput::make('nominatim_api_url')
              ->label('Base URL API Geocoding')
              ->required()
              ->url()
              ->placeholder('https://nominatim.openstreetmap.org/reverse')
              ->helperText('URL API Nominatim reverse geocoding. Gratis dan tanpa API key.'),
          ]),

        Forms\Components\Section::make('Lokasi Default Sekolah')
          ->description('Koordinat dan nama kota default yang digunakan sebelum GPS siswa aktif.')
          ->icon('heroicon-o-building-office-2')
          ->schema([
            Forms\Components\TextInput::make('default_latitude')
              ->label('Latitude')
              ->required()
              ->numeric()
              ->step(0.0001)
              ->placeholder('-7.3305')
              ->helperText('Koordinat latitude sekolah (negatif untuk selatan khatulistiwa)'),
            Forms\Components\TextInput::make('default_longitude')
              ->label('Longitude')
              ->required()
              ->numeric()
              ->step(0.0001)
              ->placeholder('108.3508')
              ->helperText('Koordinat longitude sekolah'),
            Forms\Components\TextInput::make('default_city')
              ->label('Nama Kota Default')
              ->required()
              ->placeholder('Ciamis')
              ->helperText('Nama kota yang ditampilkan sebelum lokasi GPS terdeteksi'),
          ])
          ->columns(3),

        Forms\Components\Section::make('Referensi API Gratis')
          ->description('Daftar API gratis yang bisa digunakan sebagai alternatif. Salin URL lalu paste ke kolom di atas.')
          ->icon('heroicon-o-light-bulb')
          ->collapsed()
          ->schema([
            Forms\Components\Placeholder::make('api_references')
              ->label('')
              ->content(new HtmlString(self::getApiReferencesHtml())),
          ]),
      ])
      ->statePath('data');
  }

  public function save(): void
  {
    $data = $this->form->getState();

    $mappings = [
      'prayer_api_url'    => 'api',
      'prayer_api_method' => 'api',
      'quran_api_url'     => 'api',
      'nominatim_api_url' => 'api',
      'default_latitude'  => 'location',
      'default_longitude' => 'location',
      'default_city'      => 'location',
    ];

    foreach ($mappings as $key => $group) {
      AppSetting::setValue($key, $data[$key]);
    }

    Notification::make()
      ->title('Setting API berhasil disimpan')
      ->success()
      ->send();
  }

  private static function getApiReferencesHtml(): string
  {
    return <<<'HTML'
    <style>.api-ref-table code{pointer-events:none;user-select:all;cursor:text;background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:0.78rem;color:#334155;display:inline-block;max-width:100%;word-break:break-all;}</style>
    <div class="api-ref-table" style="font-size:0.85rem;line-height:1.7;color:#374151;">

      <div style="margin-bottom:14px;padding:10px 14px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;font-size:0.8rem;color:#1e40af;">
        ℹ️ URL di bawah adalah <strong>Base URL</strong> — aplikasi otomatis menambahkan parameter (tanggal, koordinat, dll) saat memanggil API. <strong>Jangan diklik langsung</strong>, cukup salin dan paste ke kolom form di atas.
      </div>

      <h3 style="margin:0 0 8px;font-size:1rem;color:#1e40af;">🕌 Jadwal Sholat / Prayer Times</h3>
      <table style="width:100%;border-collapse:collapse;margin-bottom:20px;font-size:0.82rem;">
        <thead>
          <tr style="background:#eff6ff;text-align:left;">
            <th style="padding:6px 10px;border:1px solid #dbeafe;">Nama</th>
            <th style="padding:6px 10px;border:1px solid #dbeafe;">Base URL</th>
            <th style="padding:6px 10px;border:1px solid #dbeafe;">API Key?</th>
            <th style="padding:6px 10px;border:1px solid #dbeafe;">Keterangan</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;font-weight:600;">Aladhan ⭐</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;"><code>https://api.aladhan.com/v1/timings/</code></td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;color:#16a34a;">Tidak</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;">Default saat ini. 17 metode hisab, termasuk Kemenag RI. Stabil & cepat.</td>
          </tr>
          <tr style="background:#f9fafb;">
            <td style="padding:6px 10px;border:1px solid #e5e7eb;font-weight:600;">MyQuran</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;"><code>https://api.myquran.com/v2/sholat/jadwal/</code></td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;color:#16a34a;">Tidak</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;">API Indonesia. Gunakan kode kota (misal: 1301 = Ciamis). Format: <code>/kota_id/yyyy/mm/dd</code></td>
          </tr>
          <tr>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;font-weight:600;">Waktu Solat (Malaysia)</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;"><code>https://waktusolat.app/api/v2/solat/</code></td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;color:#16a34a;">Tidak</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;">Berbasis zona (Malaysia/Singapore). Cocok untuk perbandingan data.</td>
          </tr>
          <tr style="background:#f9fafb;">
            <td style="padding:6px 10px;border:1px solid #e5e7eb;font-weight:600;">PrayTimes.org</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;"><code>http://api.pray.zone/v2/times/</code></td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;color:#16a34a;">Tidak</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;">Berbasis nama kota. Contoh: <code>?city=Ciamis&country=Indonesia</code></td>
          </tr>
        </tbody>
      </table>

      <h3 style="margin:0 0 8px;font-size:1rem;color:#1e40af;">📖 Al-Quran</h3>
      <table style="width:100%;border-collapse:collapse;margin-bottom:20px;font-size:0.82rem;">
        <thead>
          <tr style="background:#eff6ff;text-align:left;">
            <th style="padding:6px 10px;border:1px solid #dbeafe;">Nama</th>
            <th style="padding:6px 10px;border:1px solid #dbeafe;">Base URL</th>
            <th style="padding:6px 10px;border:1px solid #dbeafe;">API Key?</th>
            <th style="padding:6px 10px;border:1px solid #dbeafe;">Keterangan</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;font-weight:600;">Al-Quran Cloud ⭐</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;"><code>https://api.alquran.cloud/v1/ayah/</code></td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;color:#16a34a;">Tidak</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;">Default saat ini. 90+ edisi (Arab, terjemahan Indonesia, dll). Format: <code>/nomor_ayah/edisi</code></td>
          </tr>
          <tr style="background:#f9fafb;">
            <td style="padding:6px 10px;border:1px solid #e5e7eb;font-weight:600;">Quran.com API v4</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;"><code>https://api.quran.com/api/v4/</code></td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;color:#16a34a;">Tidak</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;">Fitur lengkap: audio, tafsir, terjemahan. Contoh: <code>/verses/by_key/2:255</code></td>
          </tr>
          <tr>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;font-weight:600;">Equran.id</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;"><code>https://equran.id/api/v2/</code></td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;color:#16a34a;">Tidak</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;">API Indonesia. Terjemahan bahasa Indonesia built-in. Contoh: <code>/surat/2/255</code></td>
          </tr>
          <tr style="background:#f9fafb;">
            <td style="padding:6px 10px;border:1px solid #e5e7eb;font-weight:600;">MyQuran Quran</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;"><code>https://api.myquran.com/v2/quran/ayat/</code></td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;color:#16a34a;">Tidak</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;">API Indonesia. Terjemahan & audio. Contoh: <code>/2/255</code> (surah 2 ayat 255)</td>
          </tr>
          <tr>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;font-weight:600;">Quran API (fawazahmed0)</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;"><code>https://cdn.jsdelivr.net/gh/fawazahmed0/quran-api@1/</code></td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;color:#16a34a;">Tidak</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;">CDN-based, sangat cepat. 90+ terjemahan. Static JSON (tidak ada rate limit).</td>
          </tr>
        </tbody>
      </table>

      <h3 style="margin:0 0 8px;font-size:1rem;color:#1e40af;">📍 Geocoding / Reverse Geocoding</h3>
      <table style="width:100%;border-collapse:collapse;margin-bottom:20px;font-size:0.82rem;">
        <thead>
          <tr style="background:#eff6ff;text-align:left;">
            <th style="padding:6px 10px;border:1px solid #dbeafe;">Nama</th>
            <th style="padding:6px 10px;border:1px solid #dbeafe;">Base URL</th>
            <th style="padding:6px 10px;border:1px solid #dbeafe;">API Key?</th>
            <th style="padding:6px 10px;border:1px solid #dbeafe;">Keterangan</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;font-weight:600;">Nominatim (OSM) ⭐</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;"><code>https://nominatim.openstreetmap.org/reverse</code></td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;color:#16a34a;">Tidak</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;">Default saat ini. Max 1 req/detik. Format: <code>?lat=X&lon=Y&format=json</code></td>
          </tr>
          <tr style="background:#f9fafb;">
            <td style="padding:6px 10px;border:1px solid #e5e7eb;font-weight:600;">BigDataCloud</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;"><code>https://api.bigdatacloud.net/data/reverse-geocode-client</code></td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;color:#16a34a;">Tidak (client)</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;">Gratis untuk client-side. Format: <code>?latitude=X&longitude=Y&localityLanguage=id</code></td>
          </tr>
          <tr>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;font-weight:600;">Geocode.xyz</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;"><code>https://geocode.xyz/</code></td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;color:#16a34a;">Tidak (throttled)</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;">Forward & reverse. Format: <code>/-7.33,108.35?json=1</code>. Rate limit ketat.</td>
          </tr>
          <tr style="background:#f9fafb;">
            <td style="padding:6px 10px;border:1px solid #e5e7eb;font-weight:600;">LocationIQ</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;"><code>https://us1.locationiq.com/v1/reverse</code></td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;color:#f59e0b;">Ya (gratis)</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;">5.000 req/hari gratis. Daftar di locationiq.com untuk API key.</td>
          </tr>
        </tbody>
      </table>

      <h3 style="margin:0 0 8px;font-size:1rem;color:#1e40af;">🔧 API Pendukung Lainnya</h3>
      <table style="width:100%;border-collapse:collapse;margin-bottom:10px;font-size:0.82rem;">
        <thead>
          <tr style="background:#eff6ff;text-align:left;">
            <th style="padding:6px 10px;border:1px solid #dbeafe;">Nama</th>
            <th style="padding:6px 10px;border:1px solid #dbeafe;">Base URL</th>
            <th style="padding:6px 10px;border:1px solid #dbeafe;">API Key?</th>
            <th style="padding:6px 10px;border:1px solid #dbeafe;">Keterangan</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;font-weight:600;">Aladhan Hijri Calendar</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;"><code>https://api.aladhan.com/v1/gpiToH/</code></td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;color:#16a34a;">Tidak</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;">Konversi Gregorian → Hijriah. Format: <code>/dd-mm-yyyy</code></td>
          </tr>
          <tr style="background:#f9fafb;">
            <td style="padding:6px 10px;border:1px solid #e5e7eb;font-weight:600;">Aladhan Asma ul Husna</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;"><code>https://api.aladhan.com/v1/asmaAlHusna</code></td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;color:#16a34a;">Tidak</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;">99 Asmaul Husna + arti. Bisa filter: <code>/1,2,3</code></td>
          </tr>
          <tr>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;font-weight:600;">Islamic Calendar Events</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;"><code>https://api.aladhan.com/v1/hpiToG/</code></td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;color:#16a34a;">Tidak</td>
            <td style="padding:6px 10px;border:1px solid #e5e7eb;">Konversi Hijriah → Gregorian. Untuk menghitung tanggal hari besar Islam.</td>
          </tr>
        </tbody>
      </table>

      <div style="margin-top:12px;padding:10px 14px;background:#fefce8;border:1px solid #fde68a;border-radius:6px;font-size:0.8rem;color:#92400e;">
        💡 <strong>Tips:</strong><br>
        • API bertanda ⭐ adalah yang sedang aktif digunakan.<br>
        • Semua API di atas gratis tanpa biaya. Yang bertanda <span style="color:#f59e0b;font-weight:600;">Ya (gratis)</span> memerlukan registrasi untuk API key gratis.<br>
        • URL adalah <strong>base URL</strong> — aplikasi akan menambahkan parameter secara otomatis (tanggal, koordinat, dll).<br>
        • Pastikan endpoint yang dipilih kompatibel dengan format yang diharapkan aplikasi sebelum mengganti.
      </div>
    </div>
    HTML;
  }
}
