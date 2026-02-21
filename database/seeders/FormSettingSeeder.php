<?php

namespace Database\Seeders;

use App\Models\FormSetting;
use Illuminate\Database\Seeder;

class FormSettingSeeder extends Seeder
{
  public function run(): void
  {
    // ═══ ISLAM ═══
    FormSetting::updateOrCreate(
      ['agama' => 'Islam'],
      [
        'is_active' => true,
        'sections' => [
          [
            'key' => 'puasa',
            'title' => 'Puasa',
            'type' => 'ya_tidak',
            'enabled' => true,
            'has_reason' => true,
            'reason_suggestions' => [
              'Sakit (demam, maag, dll)',
              'Haid',
              'Lupa niat / tidak sahur',
              'Kondisi tubuh tidak kuat',
              'Bepergian jauh',
              'Izin orang tua',
            ],
          ],
          [
            'key' => 'sholat_fardu',
            'title' => 'Sholat Fardu',
            'type' => 'multi_option',
            'enabled' => true,
            'options' => ['jamaah', 'munfarid', 'tidak'],
            'items' => [
              ['key' => 'subuh', 'label' => 'Subuh'],
              ['key' => 'dzuhur', 'label' => 'Dzuhur'],
              ['key' => 'ashar', 'label' => 'Ashar'],
              ['key' => 'maghrib', 'label' => 'Maghrib'],
              ['key' => 'isya', 'label' => 'Isya'],
            ],
          ],
          [
            'key' => 'tarawih',
            'title' => 'Sholat Tarawih',
            'type' => 'multi_option',
            'enabled' => true,
            'options' => ['jamaah', 'munfarid', 'tidak'],
            'items' => [
              ['key' => 'tarawih', 'label' => 'Tarawih'],
            ],
          ],
          [
            'key' => 'sholat_sunat',
            'title' => 'Sholat Sunat',
            'type' => 'multi_option',
            'enabled' => true,
            'options' => ['ya', 'tidak'],
            'items' => [
              ['key' => 'rowatib', 'label' => 'Rowatib'],
              ['key' => 'tahajud', 'label' => 'Tahajud'],
              ['key' => 'dhuha', 'label' => 'Dhuha'],
            ],
          ],
          [
            'key' => 'tadarus',
            'title' => 'Tadarus Al-Quran',
            'type' => 'tadarus',
            'enabled' => true,
          ],
          [
            'key' => 'kegiatan',
            'title' => 'Kegiatan Harian',
            'type' => 'checklist_groups',
            'enabled' => true,
            'groups' => [
              [
                'title' => 'Amaliyah Cageur, Bageur dan Bener',
                'items' => [
                  ['key' => 'dzikir_pagi', 'label' => 'Dzikir Pagi'],
                  ['key' => 'olahraga', 'label' => 'Olahraga Ringan'],
                  ['key' => 'membantu_ortu', 'label' => 'Membantu Orang Tua'],
                  ['key' => 'membersihkan_kamar', 'label' => 'Membersihkan Kamar'],
                  ['key' => 'membersihkan_rumah', 'label' => 'Membersihkan Rumah'],
                  ['key' => 'membersihkan_halaman', 'label' => 'Membersihkan Halaman'],
                  ['key' => 'merawat_lingkungan', 'label' => 'Merawat Lingkungan'],
                  ['key' => 'dzikir_petang', 'label' => 'Dzikir Petang'],
                  ['key' => 'sedekah', 'label' => 'Sedekah / Poe Ibu'],
                  ['key' => 'buka_keluarga', 'label' => 'Buka Bersama Keluarga'],
                ],
              ],
              [
                'title' => 'Amaliyah Pancawaluya Pinter',
                'items' => [
                  ['key' => 'kajian', 'label' => 'Kajian Al-Quran, Tafsir & Hadits'],
                ],
              ],
              [
                'title' => 'Amaliyah Pancawaluya Singer',
                'items' => [
                  ['key' => 'menabung', 'label' => 'Menabung'],
                  ['key' => 'tidur_cepat', 'label' => 'Tidur Cepat'],
                  ['key' => 'bangun_pagi', 'label' => 'Bangun Pagi / Sahur'],
                ],
              ],
            ],
          ],
          [
            'key' => 'ceramah',
            'title' => 'Ringkasan Ceramah',
            'type' => 'ceramah',
            'enabled' => true,
          ],
        ],
      ]
    );

    // ═══ NON-MUSLIM SHARED CONFIG (Kristen, Hindu, Buddha, Konghucu) ═══
    $nonMuslimSections = [
      [
        'key' => 'pengendalian_diri',
        'title' => 'Pembiasaan Pengendalian Diri',
        'type' => 'ya_tidak_list',
        'enabled' => true,
        'items' => [
          ['key' => 'pengendalian_diri', 'label' => 'Latihan pengendalian diri (mengurangi jajan / screen time)'],
          ['key' => 'refleksi_doa', 'label' => 'Refleksi / doa sesuai keyakinan'],
          ['key' => 'baca_inspiratif', 'label' => 'Membaca buku inspiratif / nilai moral'],
        ],
      ],
      [
        'key' => 'kegiatan',
        'title' => 'Kegiatan Harian (Pembiasaan Positif)',
        'type' => 'ya_tidak_groups',
        'enabled' => true,
        'groups' => [
          [
            'title' => 'A. Karakter "Sehat, Baik, Benar"',
            'items' => [
              ['key' => 'refleksi_pagi', 'label' => 'Refleksi pagi (menulis rasa syukur)'],
              ['key' => 'olahraga', 'label' => 'Olahraga ringan'],
              ['key' => 'membantu_ortu', 'label' => 'Membantu orang tua'],
              ['key' => 'membersihkan_kamar', 'label' => 'Membersihkan kamar'],
              ['key' => 'membersihkan_rumah', 'label' => 'Membersihkan rumah / halaman'],
              ['key' => 'merawat_lingkungan', 'label' => 'Merawat lingkungan / tanaman'],
              ['key' => 'refleksi_sore', 'label' => 'Refleksi sore'],
              ['key' => 'sedekah', 'label' => 'Aksi berbagi / sedekah'],
              ['key' => 'makan_keluarga', 'label' => 'Makan bersama keluarga tanpa gadget'],
            ],
          ],
          [
            'title' => 'B. Pengembangan Diri "Pinter"',
            'items' => [
              ['key' => 'literasi', 'label' => 'Literasi (membaca buku pengembangan diri / biografi)'],
              ['key' => 'menulis_ringkasan', 'label' => 'Menulis ringkasan / refleksi bacaan'],
            ],
          ],
          [
            'title' => 'C. Kemandirian "Mandiri & Disiplin"',
            'items' => [
              ['key' => 'menabung', 'label' => 'Menabung'],
              ['key' => 'tidur_lebih_awal', 'label' => 'Tidur lebih awal'],
              ['key' => 'bangun_pagi', 'label' => 'Bangun pagi'],
              ['key' => 'target_kebaikan', 'label' => 'Menetapkan target kebaikan harian'],
            ],
          ],
        ],
      ],
      [
        'key' => 'catatan',
        'title' => 'Catatan Harian',
        'type' => 'catatan',
        'enabled' => true,
      ],
    ];

    foreach (['Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama) {
      FormSetting::updateOrCreate(
        ['agama' => $agama],
        [
          'is_active' => true,
          'sections' => $nonMuslimSections,
        ]
      );
    }
  }
}
