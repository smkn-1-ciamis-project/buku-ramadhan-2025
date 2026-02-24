<?php

namespace App\Services;

use App\Models\FormSubmission;
use App\Models\Kelas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KesiswaanExportService
{
  private static Carbon $ramadhanStart;

  private static function init(): void
  {
    static::$ramadhanStart = Carbon::create(2026, 2, 19);
  }

  private static function hariKe(): int
  {
    $today = Carbon::today();
    return $today->gte(static::$ramadhanStart)
      ? min((int) static::$ramadhanStart->diffInDays($today) + 1, 30)
      : 0;
  }

  private static function tanggalHari(int $hari): string
  {
    return static::$ramadhanStart->copy()->addDays($hari - 1)->translatedFormat('d M Y');
  }

  /**
   * Export validasi data per kelas (satu kelas = satu sheet).
   * Jika $kelasId diberikan, hanya kelas itu yang diexport.
   * Berisi data formulir lengkap + status validasi kesiswaan.
   */
  public static function exportValidasi(?string $kelasId = null): StreamedResponse
  {
    static::init();
    $hariKe = static::hariKe();

    if ($kelasId) {
      $kelasList = Kelas::where('id', $kelasId)->with(['wali', 'siswa'])->get();
    } else {
      $kelasList = Kelas::with(['wali', 'siswa'])->orderBy('nama')->get();
    }

    $spreadsheet = new Spreadsheet();
    $spreadsheet->getProperties()
      ->setCreator('Calakan - SMKN 1 Ciamis')
      ->setTitle('Export Validasi Kesiswaan — Ramadhan 1447H');

    if ($kelasList->isEmpty()) {
      $sheet = $spreadsheet->getActiveSheet();
      $sheet->setTitle('Info');
      $sheet->setCellValue('A1', 'Tidak ada data kelas yang ditemukan.');
      return static::streamDownload($spreadsheet, 'Export_Validasi_' . now()->format('Y-m-d') . '.xlsx');
    }

    $exporterName = Auth::user()?->name ?? 'Kesiswaan';
    $sheetIndex = 0;

    foreach ($kelasList as $kelas) {
      $sheet = $sheetIndex === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
      $sheetName = substr($kelas->nama, 0, 31);
      $sheet->setTitle($sheetName);

      $siswaList = $kelas->siswa->sortBy('name')->values();
      $siswaIds = $siswaList->pluck('id');
      $wali = $kelas->wali?->name ?? '-';

      // Get all verified submissions for this class
      $submissions = FormSubmission::whereIn('user_id', $siswaIds)
        ->where('status', 'verified')
        ->with(['user', 'verifier', 'validator'])
        ->orderBy('user_id')
        ->orderBy('hari_ke')
        ->get();

      // Determine if this class has Muslim or mixed students
      $agamaList = $siswaList->pluck('agama')->map(fn($a) => strtolower(trim($a ?? '')))->unique();
      $hasMuslim = $agamaList->contains('islam');
      $hasNonMuslim = $agamaList->contains(fn($a) => $a !== 'islam' && $a !== '');

      // ── Determine headers first to get correct lastCol ──
      $headerRow = 4;
      if ($hasMuslim && !$hasNonMuslim) {
        // All Muslim
        $headers = [
          'A' => 'No',
          'B' => 'Nama Siswa',
          'C' => 'NISN',
          'D' => 'Agama',
          'E' => 'Hari Ke',
          'F' => 'Tanggal',
          'G' => 'Puasa',
          'H' => 'Subuh',
          'I' => 'Dzuhur',
          'J' => 'Ashar',
          'K' => 'Maghrib',
          'L' => 'Isya',
          'M' => 'Tarawih',
          'N' => 'Rowatib',
          'O' => 'Tahajud',
          'P' => 'Dhuha',
          'Q' => 'Tadarus',
          'R' => 'Kegiatan',
          'S' => 'Ceramah',
          'T' => 'Status Guru',
          'U' => 'Verifikator',
          'V' => 'Status Kesiswaan',
          'W' => 'Validator',
          'X' => 'Tgl Validasi',
          'Y' => 'Catatan Kesiswaan',
        ];
        $widths = ['A' => 5, 'B' => 25, 'C' => 14, 'D' => 10, 'E' => 8, 'F' => 13, 'G' => 8, 'H' => 8, 'I' => 8, 'J' => 8, 'K' => 8, 'L' => 8, 'M' => 10, 'N' => 9, 'O' => 9, 'P' => 8, 'Q' => 30, 'R' => 45, 'S' => 25, 'T' => 15, 'U' => 18, 'V' => 17, 'W' => 18, 'X' => 14, 'Y' => 30];
        $lastCol = 'Y';
      } elseif (!$hasMuslim && $hasNonMuslim) {
        // All non-Muslim
        $headers = [
          'A' => 'No',
          'B' => 'Nama Siswa',
          'C' => 'NISN',
          'D' => 'Agama',
          'E' => 'Hari Ke',
          'F' => 'Tanggal',
          'G' => 'Pengendalian Diri',
          'H' => 'Refleksi/Doa',
          'I' => 'Baca Inspiratif',
          'J' => 'Kegiatan',
          'K' => 'Catatan Harian',
          'L' => 'Status Guru',
          'M' => 'Verifikator',
          'N' => 'Status Kesiswaan',
          'O' => 'Validator',
          'P' => 'Tgl Validasi',
          'Q' => 'Catatan Kesiswaan',
        ];
        $widths = ['A' => 5, 'B' => 25, 'C' => 14, 'D' => 10, 'E' => 8, 'F' => 13, 'G' => 16, 'H' => 16, 'I' => 16, 'J' => 45, 'K' => 35, 'L' => 15, 'M' => 18, 'N' => 17, 'O' => 18, 'P' => 14, 'Q' => 30];
        $lastCol = 'Q';
      } else {
        // Mixed — use Muslim layout (wider), non-Muslim data mapped into relevant columns
        $headers = [
          'A' => 'No',
          'B' => 'Nama Siswa',
          'C' => 'NISN',
          'D' => 'Agama',
          'E' => 'Hari Ke',
          'F' => 'Tanggal',
          'G' => 'Puasa',
          'H' => 'Subuh',
          'I' => 'Dzuhur',
          'J' => 'Ashar',
          'K' => 'Maghrib',
          'L' => 'Isya',
          'M' => 'Tarawih',
          'N' => 'Rowatib',
          'O' => 'Tahajud',
          'P' => 'Dhuha',
          'Q' => 'Tadarus',
          'R' => 'Kegiatan',
          'S' => 'Ceramah',
          'T' => 'Status Guru',
          'U' => 'Verifikator',
          'V' => 'Status Kesiswaan',
          'W' => 'Validator',
          'X' => 'Tgl Validasi',
          'Y' => 'Catatan Kesiswaan',
        ];
        $widths = ['A' => 5, 'B' => 25, 'C' => 14, 'D' => 10, 'E' => 8, 'F' => 13, 'G' => 8, 'H' => 8, 'I' => 8, 'J' => 8, 'K' => 8, 'L' => 8, 'M' => 10, 'N' => 9, 'O' => 9, 'P' => 8, 'Q' => 30, 'R' => 45, 'S' => 25, 'T' => 15, 'U' => 18, 'V' => 17, 'W' => 18, 'X' => 14, 'Y' => 30];
        $lastCol = 'Y';
      }

      // ── Title rows (now using final lastCol) ──
      static::mergeTitle($sheet, "A1:{$lastCol}1", 'EXPORT VALIDASI KESISWAAN — RAMADHAN 1447H');
      static::mergeSubtitle(
        $sheet,
        "A2:{$lastCol}2",
        "Kelas: {$kelas->nama}  |  Wali: {$wali}  |  Siswa: {$siswaList->count()}  |  Export oleh: {$exporterName}  |  Tanggal: " . now()->translatedFormat('d F Y')
      );

      // ── Stats summary row ──
      $pendingCount = $submissions->where('kesiswaan_status', 'pending')->count();
      $validatedCount = $submissions->where('kesiswaan_status', 'validated')->count();
      $rejectedCount = $submissions->where('kesiswaan_status', 'rejected')->count();
      $sheet->setCellValue('A3', "Total Formulir: {$submissions->count()}  |  Menunggu: {$pendingCount}  |  Divalidasi: {$validatedCount}  |  Ditolak: {$rejectedCount}");
      $sheet->mergeCells("A3:{$lastCol}3");
      $sheet->getStyle('A3')->applyFromArray([
        'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '6B7280']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
      ]);

      // ── Write header cells ──
      foreach ($headers as $col => $label) {
        $sheet->setCellValue("{$col}{$headerRow}", $label);
      }
      $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray(static::styleHeader());
      $sheet->getRowDimension($headerRow)->setRowHeight(28);
      $sheet->freezePane('E' . ($headerRow + 1));

      // ── Data rows ──
      $dataRow = $headerRow + 1;
      $num = 1;

      foreach ($submissions as $sub) {
        $user = $sub->user;
        if (!$user) continue;

        $data = $sub->data ?? [];
        $isMuslim = User::isMuslimAgama($user->agama);

        $sheet->setCellValue("A{$dataRow}", $num);
        $sheet->setCellValue("B{$dataRow}", $user->name);
        $sheet->setCellValue("C{$dataRow}", $user->nisn ?? '-');
        $sheet->setCellValue("D{$dataRow}", ucfirst($user->agama ?? '-'));
        $sheet->setCellValue("E{$dataRow}", $sub->hari_ke);
        $sheet->setCellValue("F{$dataRow}", static::tanggalHari($sub->hari_ke));

        if ($hasMuslim && !$hasNonMuslim) {
          // Muslim-only layout
          static::fillMuslimCells($sheet, $dataRow, $data);
          static::fillValidationCells($sheet, $dataRow, $sub, 'T');
        } elseif (!$hasMuslim && $hasNonMuslim) {
          // Non-Muslim-only layout
          static::fillNonMuslimCells($sheet, $dataRow, $data);
          static::fillValidationCells($sheet, $dataRow, $sub, 'L');
        } else {
          // Mixed layout
          if ($isMuslim) {
            static::fillMuslimCells($sheet, $dataRow, $data);
          } else {
            // Map non-Muslim data into Muslim layout columns for consistency
            static::fillNonMuslimInMixedLayout($sheet, $dataRow, $data);
          }
          static::fillValidationCells($sheet, $dataRow, $sub, 'T');
        }

        // Zebra striping
        $sheet->getStyle("A{$dataRow}:{$lastCol}{$dataRow}")->applyFromArray([
          'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
        ]);
        if ($num % 2 === 0) {
          $sheet->getStyle("A{$dataRow}:F{$dataRow}")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
          ]);
        }

        // Center alignment for No, Hari Ke
        $sheet->getStyle("A{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $num++;
        $dataRow++;
      }

      // ── Keterangan ──
      $legRow = $dataRow + 1;
      $sheet->setCellValue("A{$legRow}", 'Keterangan Status Kesiswaan:');
      $sheet->getStyle("A{$legRow}")->getFont()->setBold(true)->setSize(9);
      $sheet->setCellValue("B{$legRow}", 'Divalidasi = Lolos validasi kesiswaan');
      $sheet->setCellValue("D{$legRow}", 'Menunggu = Belum divalidasi');
      $sheet->setCellValue("F{$legRow}", 'Ditolak = Ditolak kesiswaan');
      $sheet->getStyle("B{$legRow}:F{$legRow}")->getFont()->setSize(8)->setItalic(true);

      // Column widths
      foreach ($widths as $col => $width) {
        $sheet->getColumnDimension($col)->setWidth($width);
      }

      // Wrap text for long columns
      $wrapCols = isset($headers['Q']) ? ['Q', 'R', 'S', 'Y'] : ['J', 'K', 'Q'];
      foreach ($wrapCols as $col) {
        if (isset($headers[$col])) {
          for ($r = $headerRow + 1; $r < $dataRow; $r++) {
            $sheet->getStyle("{$col}{$r}")->getAlignment()->setWrapText(true);
          }
        }
      }

      $sheetIndex++;
    }

    $spreadsheet->setActiveSheetIndex(0);
    $suffix = $kelasId && $kelasList->count() === 1
      ? str_replace(' ', '_', $kelasList->first()->nama)
      : 'Semua_Kelas';
    return static::streamDownload(
      $spreadsheet,
      "Validasi_Kesiswaan_{$suffix}_" . now()->format('Y-m-d') . '.xlsx'
    );
  }

  // ── Fill Muslim form data (columns G-S) ──
  private static function fillMuslimCells($sheet, int $row, array $data): void
  {
    // Puasa
    $puasa = $data['puasa'] ?? '';
    $sheet->setCellValue("G{$row}", match ($puasa) {
      'ya' => 'Ya',
      'tidak' => 'Tidak',
      default => '—'
    });
    static::colorBool($sheet, "G{$row}", $puasa === 'ya');

    // Sholat wajib
    $sholat = $data['sholat'] ?? [];
    foreach (['subuh' => 'H', 'dzuhur' => 'I', 'ashar' => 'J', 'maghrib' => 'K', 'isya' => 'L'] as $key => $col) {
      $val = $sholat[$key] ?? '';
      $label = match ($val) {
        'jamaah' => 'Jmh',
        'munfarid' => 'Mfr',
        'tidak' => 'Tdk',
        default => '—'
      };
      $sheet->setCellValue("{$col}{$row}", $label);
      $bg = match ($val) {
        'jamaah' => 'DCFCE7',
        'munfarid' => 'FEF9C3',
        'tidak' => 'FEE2E2',
        default => 'F1F5F9'
      };
      $fg = match ($val) {
        'jamaah' => '166534',
        'munfarid' => '92400E',
        'tidak' => '991B1B',
        default => '94A3B8'
      };
      $sheet->getStyle("{$col}{$row}")->applyFromArray([
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
        'font' => ['bold' => true, 'color' => ['rgb' => $fg], 'size' => 9],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
      ]);
    }

    // Tarawih
    $tarawih = $data['tarawih'] ?? '';
    $sheet->setCellValue("M{$row}", match ($tarawih) {
      'jamaah' => 'Jmh',
      'munfarid' => 'Mfr',
      'tidak' => 'Tdk',
      default => '—'
    });
    $bg = match ($tarawih) {
      'jamaah' => 'DCFCE7',
      'munfarid' => 'FEF9C3',
      'tidak' => 'FEE2E2',
      default => 'F1F5F9'
    };
    $fg = match ($tarawih) {
      'jamaah' => '166534',
      'munfarid' => '92400E',
      'tidak' => '991B1B',
      default => '94A3B8'
    };
    $sheet->getStyle("M{$row}")->applyFromArray([
      'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
      'font' => ['bold' => true, 'color' => ['rgb' => $fg], 'size' => 9],
      'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ]);

    // Sholat sunat
    $sunat = $data['sunat'] ?? [];
    foreach (['rowatib' => 'N', 'tahajud' => 'O', 'dhuha' => 'P'] as $key => $col) {
      $val = $sunat[$key] ?? '';
      $sheet->setCellValue("{$col}{$row}", $val === 'ya' ? 'Ya' : ($val === 'tidak' ? 'Tdk' : '—'));
      static::colorBool($sheet, "{$col}{$row}", $val === 'ya');
    }

    // Tadarus
    $entries = $data['tadarus_entries'] ?? [];
    if (empty($entries) && (!empty($data['tadarus_surat'] ?? '') || !empty($data['tadarus_ayat'] ?? ''))) {
      $entries = [['surat' => $data['tadarus_surat'] ?? '', 'ayat' => $data['tadarus_ayat'] ?? '']];
    }
    $entries = array_filter($entries, fn($e) => !empty($e['surat'] ?? '') || !empty($e['ayat'] ?? ''));
    $tadarusText = empty($entries) ? '—' : implode('; ', array_map(fn($e) => ($e['surat'] ?? '-') . ' ayat ' . ($e['ayat'] ?? '-'), $entries));
    $sheet->setCellValue("Q{$row}", $tadarusText);

    // Kegiatan
    $kegiatanLabels = [
      'dzikir_pagi' => 'Dzikir Pagi',
      'olahraga' => 'Olahraga',
      'membantu_ortu' => 'Bantu Ortu',
      'membersihkan_kamar' => 'Bersih Kamar',
      'membersihkan_rumah' => 'Bersih Rumah',
      'membersihkan_halaman' => 'Bersih Halaman',
      'merawat_lingkungan' => 'Rawat Lingkungan',
      'dzikir_petang' => 'Dzikir Petang',
      'sedekah' => 'Sedekah',
      'buka_keluarga' => 'Buka Keluarga',
      'literasi' => 'Literasi',
      'kajian' => 'Kajian Al-Quran',
      'menabung' => 'Menabung',
      'tidur_cepat' => 'Tidur Cepat',
      'bangun_pagi' => 'Bangun Pagi',
    ];
    $kegiatan = $data['kegiatan'] ?? [];
    $done = [];
    foreach ($kegiatanLabels as $key => $label) {
      if (!empty($kegiatan[$key]) && $kegiatan[$key] !== false && $kegiatan[$key] !== 'tidak') {
        $done[] = $label;
      }
    }
    $sheet->setCellValue("R{$row}", empty($done) ? '—' : implode(', ', $done));

    // Ceramah
    $cMode = $data['ceramah_mode'] ?? '';
    $ceramahLabel = match ($cMode) {
      'offline' => 'Offline',
      'online' => 'Online',
      'tidak' => 'Tidak',
      default => '—'
    };
    $tema = !empty($data['ceramah_tema']) ? $data['ceramah_tema'] : (!empty($data['ringkasan_ceramah']) ? mb_strimwidth(strip_tags($data['ringkasan_ceramah']), 0, 80, '...') : '');
    $sheet->setCellValue("S{$row}", $tema ? "{$ceramahLabel}: {$tema}" : $ceramahLabel);
  }

  // ── Fill non-Muslim form data (columns G-K for non-Muslim-only layout) ──
  private static function fillNonMuslimCells($sheet, int $row, array $data): void
  {
    $pengendalian = $data['pengendalian'] ?? [];
    foreach (['pengendalian_diri' => 'G', 'refleksi_doa' => 'H', 'baca_inspiratif' => 'I'] as $key => $col) {
      $val = $pengendalian[$key] ?? '';
      $sheet->setCellValue("{$col}{$row}", $val === 'ya' ? 'Ya' : ($val === 'tidak' ? 'Tidak' : '—'));
      static::colorBool($sheet, "{$col}{$row}", $val === 'ya');
    }

    $kegiatanLabels = [
      'refleksi_pagi' => 'Refleksi Pagi',
      'olahraga' => 'Olahraga',
      'membantu_ortu' => 'Bantu Ortu',
      'membersihkan_kamar' => 'Bersih Kamar',
      'membersihkan_rumah' => 'Bersih Rumah',
      'merawat_lingkungan' => 'Rawat Lingkungan',
      'refleksi_sore' => 'Refleksi Sore',
      'sedekah' => 'Sedekah',
      'makan_keluarga' => 'Makan Keluarga',
      'literasi' => 'Literasi',
      'menulis_ringkasan' => 'Nulis Ringkasan',
      'menabung' => 'Menabung',
      'tidur_lebih_awal' => 'Tidur Awal',
      'bangun_pagi' => 'Bangun Pagi',
      'target_kebaikan' => 'Target Kebaikan',
    ];
    $kegiatan = $data['kegiatan'] ?? [];
    $done = [];
    foreach ($kegiatanLabels as $key => $label) {
      if (($kegiatan[$key] ?? '') === 'ya') {
        $done[] = $label;
      }
    }
    $sheet->setCellValue("J{$row}", empty($done) ? '—' : implode(', ', $done));

    $catatan = $data['catatan'] ?? '';
    $sheet->setCellValue("K{$row}", $catatan ? strip_tags($catatan) : '—');
  }

  // ── Fill non-Muslim data in mixed (Muslim) layout ──
  private static function fillNonMuslimInMixedLayout($sheet, int $row, array $data): void
  {
    // Columns G (Puasa) through P (Dhuha) = N/A for non-Muslim
    foreach (range('G', 'P') as $col) {
      $sheet->setCellValue("{$col}{$row}", '—');
      $sheet->getStyle("{$col}{$row}")->applyFromArray([
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
        'font' => ['color' => ['rgb' => 'CBD5E1']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
      ]);
    }

    // Use Tadarus column (Q) for pengendalian diri summary
    $pengendalian = $data['pengendalian'] ?? [];
    $pItems = [];
    foreach (['pengendalian_diri' => 'Pengendalian', 'refleksi_doa' => 'Refleksi', 'baca_inspiratif' => 'Baca Inspiratif'] as $key => $label) {
      $val = $pengendalian[$key] ?? '';
      if ($val === 'ya') $pItems[] = $label;
    }
    $sheet->setCellValue("Q{$row}", empty($pItems) ? '—' : implode(', ', $pItems));

    // Use Kegiatan column (R) for kegiatan
    $kegiatanLabels = [
      'refleksi_pagi' => 'Refleksi Pagi',
      'olahraga' => 'Olahraga',
      'membantu_ortu' => 'Bantu Ortu',
      'membersihkan_kamar' => 'Bersih Kamar',
      'membersihkan_rumah' => 'Bersih Rumah',
      'merawat_lingkungan' => 'Rawat Lingkungan',
      'refleksi_sore' => 'Refleksi Sore',
      'sedekah' => 'Sedekah',
      'makan_keluarga' => 'Makan Keluarga',
      'literasi' => 'Literasi',
      'menulis_ringkasan' => 'Nulis Ringkasan',
      'menabung' => 'Menabung',
      'tidur_lebih_awal' => 'Tidur Awal',
      'bangun_pagi' => 'Bangun Pagi',
      'target_kebaikan' => 'Target Kebaikan',
    ];
    $kegiatan = $data['kegiatan'] ?? [];
    $done = [];
    foreach ($kegiatanLabels as $key => $label) {
      if (($kegiatan[$key] ?? '') === 'ya') $done[] = $label;
    }
    $sheet->setCellValue("R{$row}", empty($done) ? '—' : implode(', ', $done));

    // Use Ceramah column (S) for catatan
    $catatan = $data['catatan'] ?? '';
    $sheet->setCellValue("S{$row}", $catatan ? strip_tags($catatan) : '—');
  }

  // ── Fill validation columns (Status Guru, Verifikator, Status Kesiswaan, Validator, Tgl Validasi, Catatan) ──
  private static function fillValidationCells($sheet, int $row, FormSubmission $sub, string $startCol): void
  {
    $colIdx = ord($startCol) - ord('A');

    // Status Guru
    $col = chr(65 + $colIdx);
    [$guruLabel, $guruBg, $guruFg] = static::statusCell($sub->status);
    $sheet->setCellValue("{$col}{$row}", $guruLabel);
    $sheet->getStyle("{$col}{$row}")->applyFromArray([
      'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $guruBg]],
      'font' => ['bold' => true, 'color' => ['rgb' => $guruFg], 'size' => 9],
      'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ]);

    // Verifikator
    $col = chr(65 + $colIdx + 1);
    $sheet->setCellValue("{$col}{$row}", $sub->verifier?->name ?? '-');

    // Status Kesiswaan
    $col = chr(65 + $colIdx + 2);
    [$ksLabel, $ksBg, $ksFg] = static::kesiswaanStatusCell($sub->kesiswaan_status);
    $sheet->setCellValue("{$col}{$row}", $ksLabel);
    $sheet->getStyle("{$col}{$row}")->applyFromArray([
      'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $ksBg]],
      'font' => ['bold' => true, 'color' => ['rgb' => $ksFg], 'size' => 9],
      'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ]);

    // Validator
    $col = chr(65 + $colIdx + 3);
    $sheet->setCellValue("{$col}{$row}", $sub->validator?->name ?? '-');

    // Tgl Validasi
    $col = chr(65 + $colIdx + 4);
    $sheet->setCellValue("{$col}{$row}", $sub->validated_at ? $sub->validated_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') : '-');

    // Catatan Kesiswaan
    $col = chr(65 + $colIdx + 5);
    $sheet->setCellValue("{$col}{$row}", $sub->catatan_kesiswaan ?? '-');
  }

  private static function statusCell(string $status): array
  {
    return match ($status) {
      'verified' => ['Terverifikasi', 'DCFCE7', '166534'],
      'pending'  => ['Menunggu',     'FEF9C3', '92400E'],
      'rejected' => ['Ditolak',       'FEE2E2', '991B1B'],
      default    => ['—',             'F1F5F9', '94A3B8'],
    };
  }

  private static function kesiswaanStatusCell(string $status): array
  {
    return match ($status) {
      'validated' => ['Divalidasi', 'DCFCE7', '166534'],
      'pending'   => ['Menunggu',   'FEF9C3', '92400E'],
      'rejected'  => ['Ditolak',     'FEE2E2', '991B1B'],
      default     => ['—',           'F1F5F9', '94A3B8'],
    };
  }

  private static function colorBool($sheet, string $cell, bool $positive): void
  {
    $bg = $positive ? 'DCFCE7' : 'F1F5F9';
    $fg = $positive ? '166534' : '94A3B8';
    $sheet->getStyle($cell)->applyFromArray([
      'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
      'font' => ['bold' => $positive, 'color' => ['rgb' => $fg], 'size' => 9],
      'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ]);
  }

  private static function mergeTitle($sheet, string $range, string $text): void
  {
    $sheet->mergeCells($range);
    [$startCell] = explode(':', $range);
    $sheet->setCellValue($startCell, $text);
    $sheet->getStyle($startCell)->applyFromArray([
      'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
      'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
      'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    ]);
    $sheet->getRowDimension(1)->setRowHeight(32);
  }

  private static function mergeSubtitle($sheet, string $range, string $text): void
  {
    $sheet->mergeCells($range);
    [$startCell] = explode(':', $range);
    $sheet->setCellValue($startCell, $text);
    $sheet->getStyle($startCell)->applyFromArray([
      'font' => ['size' => 9, 'color' => ['rgb' => '374151']],
      'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
      'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ]);
    $sheet->getRowDimension(2)->setRowHeight(20);
  }

  private static function styleHeader(): array
  {
    return [
      'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
      'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
      'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
      'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '93C5FD']]],
    ];
  }

  private static function streamDownload(Spreadsheet $spreadsheet, string $filename): StreamedResponse
  {
    $writer = new Xlsx($spreadsheet);
    return response()->streamDownload(function () use ($writer) {
      $writer->save('php://output');
    }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
  }
}
