<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Support\RoleGuideDefaults;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
  public function run(): void
  {
    $settings = [
      // ── Ramadan Schedule ─────────────────────────────────────
      [
        'group'       => 'ramadhan',
        'key'         => 'ramadhan_start_date',
        'value'       => '2026-02-19',
        'type'        => 'date',
        'label'       => 'Tanggal Mulai Ramadhan',
        'description' => 'Tanggal 1 Ramadhan dalam format YYYY-MM-DD',
      ],
      [
        'group'       => 'ramadhan',
        'key'         => 'ramadhan_end_date',
        'value'       => '2026-03-20',
        'type'        => 'date',
        'label'       => 'Tanggal Akhir Ramadhan',
        'description' => 'Tanggal terakhir Ramadhan (hari ke-30) dalam format YYYY-MM-DD',
      ],
      [
        'group'       => 'ramadhan',
        'key'         => 'ramadhan_total_days',
        'value'       => '30',
        'type'        => 'integer',
        'label'       => 'Total Hari Ramadhan',
        'description' => 'Jumlah hari bulan Ramadhan (29 atau 30)',
      ],
      [
        'group'       => 'ramadhan',
        'key'         => 'hijri_year',
        'value'       => '1447 H',
        'type'        => 'string',
        'label'       => 'Tahun Hijriyah',
        'description' => 'Tahun Hijriyah saat ini (contoh: 1447 H)',
      ],

      // ── API Configuration ────────────────────────────────────
      [
        'group'       => 'api',
        'key'         => 'prayer_api_url',
        'value'       => 'https://api.aladhan.com/v1/timings/',
        'type'        => 'string',
        'label'       => 'URL API Jadwal Sholat',
        'description' => 'Base URL API Aladhan untuk jadwal sholat. Format: {url}{tanggal}?latitude=...&longitude=...&method=...',
      ],
      [
        'group'       => 'api',
        'key'         => 'prayer_api_method',
        'value'       => '20',
        'type'        => 'integer',
        'label'       => 'Metode Perhitungan Sholat',
        'description' => 'Metode perhitungan waktu sholat (20 = Kemenag RI, 11 = MWL, 2 = ISNA, dst)',
      ],
      [
        'group'       => 'api',
        'key'         => 'quran_api_url',
        'value'       => 'https://api.alquran.cloud/v1/ayah/',
        'type'        => 'string',
        'label'       => 'URL API Al-Quran',
        'description' => 'Base URL API Al-Quran untuk mengambil ayat harian. Format: {url}{surah}:{ayat}/{edition}',
      ],
      [
        'group'       => 'api',
        'key'         => 'nominatim_api_url',
        'value'       => 'https://nominatim.openstreetmap.org/reverse',
        'type'        => 'string',
        'label'       => 'URL API Geocoding',
        'description' => 'URL API Nominatim untuk reverse geocoding (menampilkan nama lokasi dari koordinat)',
      ],

      // ── Default Location ─────────────────────────────────────
      [
        'group'       => 'location',
        'key'         => 'default_latitude',
        'value'       => '-7.3305',
        'type'        => 'float',
        'label'       => 'Latitude Default',
        'description' => 'Koordinat latitude default sekolah (Ciamis: -7.3305)',
      ],
      [
        'group'       => 'location',
        'key'         => 'default_longitude',
        'value'       => '108.3508',
        'type'        => 'float',
        'label'       => 'Longitude Default',
        'description' => 'Koordinat longitude default sekolah (Ciamis: 108.3508)',
      ],
      [
        'group'       => 'location',
        'key'         => 'default_city',
        'value'       => 'Ciamis',
        'type'        => 'string',
        'label'       => 'Kota Default',
        'description' => 'Nama kota default yang ditampilkan sebelum GPS aktif',
      ],

      // ── Login Guide per Role ────────────────────────────────
      [
        'group'       => 'guide',
        'key'         => 'guide_siswa',
        'value'       => RoleGuideDefaults::forRole('siswa'),
        'type'        => 'string',
        'label'       => 'Buku Panduan Siswa',
        'description' => 'Panduan yang tampil pada halaman login siswa',
      ],
      [
        'group'       => 'guide',
        'key'         => 'guide_guru',
        'value'       => RoleGuideDefaults::forRole('guru'),
        'type'        => 'string',
        'label'       => 'Buku Panduan Guru',
        'description' => 'Panduan yang tampil pada halaman login guru',
      ],
      [
        'group'       => 'guide',
        'key'         => 'guide_kesiswaan',
        'value'       => RoleGuideDefaults::forRole('kesiswaan'),
        'type'        => 'string',
        'label'       => 'Buku Panduan Kesiswaan',
        'description' => 'Panduan yang tampil pada halaman login kesiswaan',
      ],
      [
        'group'       => 'guide',
        'key'         => 'guide_siswa_admin_contacts',
        'value'       => json_encode(RoleGuideDefaults::defaultContacts('siswa')),
        'type'        => 'json',
        'label'       => 'Kontak Admin Siswa',
        'description' => 'Daftar kontak admin yang tampil pada halaman buku panduan siswa',
      ],
      [
        'group'       => 'guide',
        'key'         => 'guide_guru_admin_contacts',
        'value'       => json_encode(RoleGuideDefaults::defaultContacts('guru')),
        'type'        => 'json',
        'label'       => 'Kontak Admin Guru',
        'description' => 'Daftar kontak admin yang tampil pada halaman buku panduan guru',
      ],
      [
        'group'       => 'guide',
        'key'         => 'guide_kesiswaan_admin_contacts',
        'value'       => json_encode(RoleGuideDefaults::defaultContacts('kesiswaan')),
        'type'        => 'json',
        'label'       => 'Kontak Admin Kesiswaan',
        'description' => 'Daftar kontak admin yang tampil pada halaman buku panduan kesiswaan',
      ],
      [
        'group'       => 'guide',
        'key'         => 'guide_siswa_flow_steps',
        'value'       => json_encode(RoleGuideDefaults::defaultFlowSteps('siswa')),
        'type'        => 'json',
        'label'       => 'Infografis Singkat Siswa',
        'description' => 'Langkah infografis singkat pada halaman buku panduan siswa',
      ],
      [
        'group'       => 'guide',
        'key'         => 'guide_guru_flow_steps',
        'value'       => json_encode(RoleGuideDefaults::defaultFlowSteps('guru')),
        'type'        => 'json',
        'label'       => 'Infografis Singkat Guru',
        'description' => 'Langkah infografis singkat pada halaman buku panduan guru',
      ],
      [
        'group'       => 'guide',
        'key'         => 'guide_kesiswaan_flow_steps',
        'value'       => json_encode(RoleGuideDefaults::defaultFlowSteps('kesiswaan')),
        'type'        => 'json',
        'label'       => 'Infografis Singkat Kesiswaan',
        'description' => 'Langkah infografis singkat pada halaman buku panduan kesiswaan',
      ],
    ];

    foreach ($settings as $setting) {
      $record = AppSetting::firstOrCreate(
        ['key' => $setting['key']],
        $setting
      );

      if (
        $setting['group'] === 'guide' &&
        blank($record->value) &&
        filled($setting['value'])
      ) {
        $record->update(['value' => $setting['value']]);
      }
    }
  }
}
