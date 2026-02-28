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

class RekapExportService
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

    public static function exportRekapSiswa(): StreamedResponse
    {
        static::init();
        $guru      = Auth::user();
        $kelasList = Kelas::where('wali_id', $guru->id)->with('siswa')->get();
        $hariKe    = static::hariKe();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Calakan - SMKN 1 Ciamis')
            ->setTitle('Rekap Aktivitas Siswa Ramadhan 1447H');

        if ($kelasList->isEmpty()) {
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Info');
            $sheet->setCellValue('A1', 'Belum ada kelas yang diwalikan untuk akun ini.');
            $filename = 'Rekap_Siswa_' . now()->format('Y-m-d') . '.xlsx';
            return static::streamDownload($spreadsheet, $filename);
        }

        $sheetIndex = 0;
        foreach ($kelasList as $kelas) {
            $sheet = $sheetIndex === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $sheet->setTitle(substr($kelas->nama, 0, 31));

            $maxHari     = max($hariKe, 1);
            $lastColIdx  = 9 + $maxHari - 1;
            $lastCol     = static::colLetter($lastColIdx);

            static::mergeTitle($sheet, "A1:{$lastCol}1", 'REKAP AKTIVITAS HARIAN SISWA — RAMADHAN 1447H');
            static::mergeSubtitle(
                $sheet,
                "A2:{$lastCol}2",
                "Kelas: {$kelas->nama}  |  Wali Kelas: {$guru->name}  |  Export: " . now()->translatedFormat('d F Y')
            );

            foreach (['A' => 'No', 'B' => 'Nama Siswa', 'C' => 'NISN', 'D' => 'Agama', 'E' => 'Total', 'F' => 'Terverifikasi', 'G' => 'Menunggu', 'H' => 'Ditolak', 'I' => 'Belum'] as $col => $label) {
                $sheet->setCellValue("{$col}3", $label);
            }
            $dailyCols = [];
            for ($d = 1; $d <= $maxHari; $d++) {
                $col = static::colLetter(8 + $d);
                $sheet->setCellValue("{$col}3", "H{$d}");
                $sheet->setCellValue("{$col}4", static::tanggalHari($d));
                $dailyCols[$d] = $col;
                $sheet->getColumnDimension($col)->setWidth(9);
                $sheet->getStyle("{$col}4")->applyFromArray([
                    'font' => ['size' => 8],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
                ]);
            }
            $sheet->getStyle("A3:{$lastCol}3")->applyFromArray(static::styleHeader());
            $sheet->getStyle("A4:I4")->applyFromArray([
                'font' => ['size' => 8],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
            ]);
            $sheet->getRowDimension(4)->setRowHeight(30);
            $sheet->freezePane('E5');

            $siswaList = $kelas->siswa->sortBy('name')->values();

            // Batch: fetch ALL submissions for all siswa in this kelas at once (avoid N+1)
            $allSiswaIds = $siswaList->pluck('id');
            $allSubsBatch = FormSubmission::whereIn('user_id', $allSiswaIds)->get()->groupBy('user_id');

            $row = 5;
            foreach ($siswaList as $idx => $siswa) {
                $subs     = $allSubsBatch->get($siswa->id, collect());
                $total    = $subs->count();
                $verified = $subs->where('status', 'verified')->count();
                $pending  = $subs->where('status', 'pending')->count();
                $rejected = $subs->where('status', 'rejected')->count();
                $belum    = max($hariKe - $total, 0);
                $rate     = $hariKe > 0 ? min(round(($total / $hariKe) * 100), 100) : 0;

                $sheet->setCellValue("A{$row}", $idx + 1);
                $sheet->setCellValue("B{$row}", $siswa->name);
                $sheet->setCellValue("C{$row}", $siswa->nisn ?? '-');
                $sheet->setCellValue("D{$row}", ucfirst($siswa->agama ?? '-'));
                $sheet->setCellValue("E{$row}", $total);
                $sheet->setCellValue("F{$row}", $verified);
                $sheet->setCellValue("G{$row}", $pending);
                $sheet->setCellValue("H{$row}", $rejected);
                $rateColor = $rate >= 80 ? 'DCFCE7' : ($rate >= 50 ? 'FEF9C3' : 'FEE2E2');
                $sheet->setCellValue("I{$row}", $belum);
                $sheet->getStyle("I{$row}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $rateColor]], 'font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

                $subMap = $subs->keyBy('hari_ke');
                foreach ($dailyCols as $d => $col) {
                    $sub = $subMap->get($d);
                    if ($sub) {
                        [$label, $bg, $fg] = static::statusCell($sub->status);
                        $sheet->setCellValue("{$col}{$row}", $label);
                        $sheet->getStyle("{$col}{$row}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]], 'font' => ['bold' => true, 'color' => ['rgb' => $fg], 'size' => 10], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
                    } else {
                        $sheet->setCellValue("{$col}{$row}", '—');
                        $sheet->getStyle("{$col}{$row}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']], 'font' => ['color' => ['rgb' => 'CBD5E1']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
                    }
                }
                foreach (['A', 'E', 'F', 'G', 'H'] as $c) {
                    $sheet->getStyle("{$c}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]]]);
                if ($idx % 2 === 1) {
                    $sheet->getStyle("A{$row}:D{$row}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']]]);
                }
                $row++;
            }

            $allSubs = $allSubsBatch->flatten(1);
            $sheet->mergeCells("A{$row}:D{$row}");
            $sheet->setCellValue("A{$row}", "TOTAL ({$siswaList->count()} siswa)");
            $sheet->setCellValue("E{$row}", $allSubs->count());
            $sheet->setCellValue("F{$row}", $allSubs->where('status', 'verified')->count());
            $sheet->setCellValue("G{$row}", $allSubs->where('status', 'pending')->count());
            $sheet->setCellValue("H{$row}", $allSubs->where('status', 'rejected')->count());
            $sheet->setCellValue("I{$row}", max($siswaList->count() * $hariKe - $allSubs->count(), 0));
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray(['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '93C5FD']]], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

            $legRow = $row + 2;
            $sheet->setCellValue("A{$legRow}", 'Keterangan:');
            $sheet->getStyle("A{$legRow}")->getFont()->setBold(true);
            $sheet->setCellValue("B{$legRow}", '✓ = Terverifikasi');
            $sheet->setCellValue("C{$legRow}", '⏳ = Menunggu');
            $sheet->setCellValue("D{$legRow}", '✗ = Ditolak');
            $sheet->setCellValue("E{$legRow}", '— = Belum Lapor');

            foreach (['A' => 5, 'B' => 28, 'C' => 14, 'D' => 10, 'E' => 9, 'F' => 13, 'G' => 11, 'H' => 9, 'I' => 9] as $c => $w) {
                $sheet->getColumnDimension($c)->setWidth($w);
            }
            $sheetIndex++;
        }

        $spreadsheet->setActiveSheetIndex(0);
        return static::streamDownload($spreadsheet, 'Rekap_Aktivitas_' . str_replace(' ', '_', $guru->name) . '_' . now()->format('Y-m-d') . '.xlsx');
    }

    public static function exportDetailSiswa(User $siswa): StreamedResponse
    {
        static::init();
        $siswa->load(['kelas', 'formSubmissions']);
        $hariKe      = static::hariKe();
        $submissions = $siswa->formSubmissions;
        $subMap      = $submissions->keyBy('hari_ke');
        $isMuslim    = User::isMuslimAgama($siswa->agama);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Calakan - SMKN 1 Ciamis')->setTitle("Rekap {$siswa->name}");
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Detail Harian');

        $lastColRange = $isMuslim ? 'T' : 'K';
        static::mergeTitle($sheet, "A1:{$lastColRange}1", 'REKAP AKTIVITAS HARIAN SISWA — RAMADHAN 1447H');
        $snisn  = $siswa->nisn ?? '-';
        $skelas = $siswa->kelas?->nama ?? '-';
        $sagama = ucfirst($siswa->agama ?? '-');
        static::mergeSubtitle(
            $sheet,
            "A2:{$lastColRange}2",
            "Nama: {$siswa->name}  |  NISN: {$snisn}  |  Kelas: {$skelas}  |  Agama: {$sagama}  |  Export: " . now()->translatedFormat('d F Y')
        );

        $total    = $submissions->count();
        $verified = $submissions->where('status', 'verified')->count();
        $pending  = $submissions->where('status', 'pending')->count();
        $rejected = $submissions->where('status', 'rejected')->count();
        $belum    = max($hariKe - $total, 0);
        $rate     = $hariKe > 0 ? min(round(($total / $hariKe) * 100), 100) : 0;

        $sheet->setCellValue('A4', 'RINGKASAN');
        $sheet->getStyle('A4')->applyFromArray(['font' => ['bold' => true, 'size' => 11]]);
        foreach ([['Total Laporan', $total], ['Terverifikasi', $verified], ['Menunggu', $pending], ['Ditolak', $rejected], ['Belum Lapor', $belum], ['Kepatuhan', "{$rate}%"]] as $i => [$l, $v]) {
            $r = 5 + $i;
            $sheet->setCellValue("A{$r}", $l);
            $sheet->setCellValue("B{$r}", $v);
            $sheet->getStyle("A{$r}")->getFont()->setBold(true);
        }

        $tableStartRow = 12;
        if ($isMuslim) {
            $headers = ['A' => 'Hari', 'B' => 'Tanggal', 'C' => 'Status', 'D' => 'Puasa', 'E' => 'Subuh', 'F' => 'Dzuhur', 'G' => 'Ashar', 'H' => 'Maghrib', 'I' => 'Isya', 'J' => 'Tarawih', 'K' => 'Rowatib', 'L' => 'Tahajud', 'M' => 'Dhuha', 'N' => 'Tadarus (Surat & Ayat)', 'O' => 'Kegiatan Dilakukan', 'P' => 'Ceramah', 'Q' => 'Tema Ceramah', 'R' => 'Waktu Kirim', 'S' => 'Waktu Verifikasi', 'T' => 'Catatan Guru'];
            $widths   = ['A' => 6, 'B' => 13, 'C' => 14, 'D' => 8, 'E' => 9, 'F' => 9, 'G' => 9, 'H' => 9, 'I' => 8, 'J' => 10, 'K' => 9, 'L' => 10, 'M' => 8, 'N' => 35, 'O' => 50, 'P' => 12, 'Q' => 30, 'R' => 13, 'S' => 13, 'T' => 35];
        } else {
            $headers = ['A' => 'Hari', 'B' => 'Tanggal', 'C' => 'Status', 'D' => 'Pengendalian Diri', 'E' => 'Refleksi/Doa', 'F' => 'Baca Inspiratif', 'G' => 'Kegiatan Dilakukan', 'H' => 'Catatan Harian', 'I' => 'Waktu Kirim', 'J' => 'Waktu Verifikasi', 'K' => 'Catatan Guru'];
            $widths   = ['A' => 6, 'B' => 13, 'C' => 14, 'D' => 18, 'E' => 18, 'F' => 20, 'G' => 50, 'H' => 40, 'I' => 13, 'J' => 13, 'K' => 35];
        }
        $lastCol = array_key_last($headers);
        foreach ($headers as $col => $label) {
            $sheet->setCellValue("{$col}{$tableStartRow}", $label);
        }
        $sheet->getStyle("A{$tableStartRow}:{$lastCol}{$tableStartRow}")->applyFromArray(static::styleHeader());
        $sheet->getRowDimension($tableStartRow)->setRowHeight(30);
        $sheet->freezePane('D' . ($tableStartRow + 1));

        $dataRow = $tableStartRow + 1;
        $maxHari = max($hariKe, 1);
        for ($d = 1; $d <= $maxHari; $d++) {
            $sub  = $subMap->get($d);
            $data = $sub ? ($sub->data ?? []) : [];

            $sheet->setCellValue("A{$dataRow}", $d);
            $sheet->setCellValue("B{$dataRow}", static::tanggalHari($d));
            $sheet->getStyle("A{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($sub) {
                [$statusLabel, $bgColor, $fgColor] = static::statusCell($sub->status);
                $sheet->setCellValue("C{$dataRow}", $statusLabel);
                $sheet->getStyle("C{$dataRow}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]], 'font' => ['bold' => true, 'color' => ['rgb' => $fgColor]], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

                if ($isMuslim) {
                    static::fillMuslimRow($sheet, $dataRow, $data);
                    $sheet->setCellValue("R{$dataRow}", $sub->created_at->timezone('Asia/Jakarta')->format('d/m H:i'));
                    $sheet->setCellValue("S{$dataRow}", $sub->verified_at ? $sub->verified_at->timezone('Asia/Jakarta')->format('d/m H:i') : '-');
                    $sheet->setCellValue("T{$dataRow}", $sub->catatan_guru ?? '-');
                } else {
                    static::fillNonMuslimRow($sheet, $dataRow, $data);
                    $sheet->setCellValue("I{$dataRow}", $sub->created_at->timezone('Asia/Jakarta')->format('d/m H:i'));
                    $sheet->setCellValue("J{$dataRow}", $sub->verified_at ? $sub->verified_at->timezone('Asia/Jakarta')->format('d/m H:i') : '-');
                    $sheet->setCellValue("K{$dataRow}", $sub->catatan_guru ?? '-');
                }
            } else {
                $sheet->setCellValue("C{$dataRow}", 'Belum Lapor');
                $sheet->getStyle("C{$dataRow}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']], 'font' => ['bold' => true, 'color' => ['rgb' => 'DC2626']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
            }

            $sheet->getStyle("A{$dataRow}:{$lastCol}{$dataRow}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]]]);
            if ($d % 2 === 0) {
                $sheet->getStyle("A{$dataRow}:B{$dataRow}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']]]);
            }

            $sheet->getRowDimension($dataRow)->setRowHeight(-1);
            $dataRow++;
        }

        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
        $wrapCols = $isMuslim ? ['N', 'O', 'Q', 'T'] : ['G', 'H', 'K'];
        foreach ($wrapCols as $col) {
            for ($r = $tableStartRow + 1; $r < $dataRow; $r++) {
                $sheet->getStyle("{$col}{$r}")->getAlignment()->setWrapText(true);
            }
        }

        return static::streamDownload($spreadsheet, 'Detail_' . str_replace(' ', '_', $siswa->name) . '_' . now()->format('Y-m-d') . '.xlsx');
    }

    private static function fillMuslimRow($sheet, int $row, array $data): void
    {
        $puasa = $data['puasa'] ?? '';
        $sheet->setCellValue("D{$row}", match ($puasa) {
            'ya' => 'Ya',
            'tidak' => 'Tidak',
            default => '—'
        });
        static::colorBool($sheet, "D{$row}", $puasa === 'ya');

        $sholat = $data['sholat'] ?? [];
        foreach (['subuh' => 'E', 'dzuhur' => 'F', 'ashar' => 'G', 'maghrib' => 'H', 'isya' => 'I'] as $key => $col) {
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
            $sheet->getStyle("{$col}{$row}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]], 'font' => ['bold' => true, 'color' => ['rgb' => $fg], 'size' => 9], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
        }

        $tarawih = $data['tarawih'] ?? '';
        $sheet->setCellValue("J{$row}", match ($tarawih) {
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
        $sheet->getStyle("J{$row}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]], 'font' => ['bold' => true, 'color' => ['rgb' => $fg], 'size' => 9], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

        $sunat = $data['sunat'] ?? [];
        foreach (['rowatib' => 'K', 'tahajud' => 'L', 'dhuha' => 'M'] as $key => $col) {
            $val = $sunat[$key] ?? '';
            $sheet->setCellValue("{$col}{$row}", $val === 'ya' ? 'Ya' : ($val === 'tidak' ? 'Tdk' : '—'));
            static::colorBool($sheet, "{$col}{$row}", $val === 'ya');
        }

        $entries = $data['tadarus_entries'] ?? [];
        if (empty($entries) && (!empty($data['tadarus_surat'] ?? '') || !empty($data['tadarus_ayat'] ?? ''))) {
            $entries = [['surat' => $data['tadarus_surat'] ?? '', 'ayat' => $data['tadarus_ayat'] ?? '']];
        }
        $entries = array_filter($entries, fn($e) => !empty($e['surat'] ?? '') || !empty($e['ayat'] ?? ''));
        $tadarusText = empty($entries) ? '—' : implode("\n", array_map(fn($e) => ($e['surat'] ?? '-') . ' ayat ' . ($e['ayat'] ?? '-'), $entries));
        $sheet->setCellValue("N{$row}", $tadarusText);

        $kegiatanLabels = ['dzikir_pagi' => 'Dzikir Pagi', 'olahraga' => 'Olahraga', 'membantu_ortu' => 'Bantu Ortu', 'membersihkan_kamar' => 'Bersih Kamar', 'membersihkan_rumah' => 'Bersih Rumah', 'membersihkan_halaman' => 'Bersih Halaman', 'merawat_lingkungan' => 'Rawat Lingkungan', 'dzikir_petang' => 'Dzikir Petang', 'sedekah' => 'Sedekah', 'buka_keluarga' => 'Buka Keluarga', 'literasi' => 'Literasi', 'kajian' => 'Kajian Al-Quran', 'menabung' => 'Menabung', 'tidur_cepat' => 'Tidur Cepat', 'bangun_pagi' => 'Bangun Pagi'];
        $kegiatan = $data['kegiatan'] ?? [];
        $done = [];
        foreach ($kegiatanLabels as $key => $label) {
            if (!empty($kegiatan[$key]) && $kegiatan[$key] !== false && $kegiatan[$key] !== 'tidak') {
                $done[] = $label;
            }
        }
        $sheet->setCellValue("O{$row}", empty($done) ? '—' : implode(', ', $done));
        if (!empty($done)) {
            $sheet->getStyle("O{$row}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']], 'font' => ['size' => 9, 'color' => ['rgb' => '166534']]]);
        }

        $cMode = $data['ceramah_mode'] ?? '';
        $sheet->setCellValue("P{$row}", match ($cMode) {
            'offline' => 'Offline',
            'online' => 'Online',
            'tidak' => 'Tidak',
            default => '—'
        });
        $sheet->setCellValue("Q{$row}", !empty($data['ceramah_tema']) ? $data['ceramah_tema'] : (!empty($data['ringkasan_ceramah']) ? mb_strimwidth(strip_tags($data['ringkasan_ceramah']), 0, 80, '...') : '—'));
    }

    private static function fillNonMuslimRow($sheet, int $row, array $data): void
    {
        $pengendalian = $data['pengendalian'] ?? [];
        foreach (['pengendalian_diri' => 'D', 'refleksi_doa' => 'E', 'baca_inspiratif' => 'F'] as $key => $col) {
            $val = $pengendalian[$key] ?? '';
            $sheet->setCellValue("{$col}{$row}", $val === 'ya' ? 'Ya' : ($val === 'tidak' ? 'Tidak' : '—'));
            static::colorBool($sheet, "{$col}{$row}", $val === 'ya');
        }

        $kegiatanLabels = ['refleksi_pagi' => 'Refleksi Pagi', 'olahraga' => 'Olahraga', 'membantu_ortu' => 'Bantu Ortu', 'membersihkan_kamar' => 'Bersih Kamar', 'membersihkan_rumah' => 'Bersih Rumah', 'merawat_lingkungan' => 'Rawat Lingkungan', 'refleksi_sore' => 'Refleksi Sore', 'sedekah' => 'Sedekah', 'makan_keluarga' => 'Makan Keluarga', 'literasi' => 'Literasi', 'menulis_ringkasan' => 'Nulis Ringkasan', 'menabung' => 'Menabung', 'tidur_lebih_awal' => 'Tidur Awal', 'bangun_pagi' => 'Bangun Pagi', 'target_kebaikan' => 'Target Kebaikan'];
        $kegiatan = $data['kegiatan'] ?? [];
        $done = [];
        foreach ($kegiatanLabels as $key => $label) {
            if (($kegiatan[$key] ?? '') === 'ya') {
                $done[] = $label;
            }
        }
        $sheet->setCellValue("G{$row}", empty($done) ? '—' : implode(', ', $done));

        $catatan = $data['catatan'] ?? '';
        $sheet->setCellValue("H{$row}", $catatan ? strip_tags($catatan) : '—');
    }

    private static function statusCell(string $status): array
    {
        return match ($status) {
            'verified' => ['✓ Terverifikasi', 'DCFCE7', '166534'],
            'pending'  => ['⏳ Menunggu',     'FEF9C3', '92400E'],
            'rejected' => ['✗ Ditolak',       'FEE2E2', '991B1B'],
            default    => ['—',               'F1F5F9', '94A3B8'],
        };
    }

    private static function colorBool($sheet, string $cell, bool $positive): void
    {
        $bg = $positive ? 'DCFCE7' : 'F1F5F9';
        $fg = $positive ? '166534' : '94A3B8';
        $sheet->getStyle($cell)->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]], 'font' => ['bold' => $positive, 'color' => ['rgb' => $fg], 'size' => 9], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
    }

    private static function mergeTitle($sheet, string $range, string $text): void
    {
        $sheet->mergeCells($range);
        [$startCell] = explode(':', $range);
        $sheet->setCellValue($startCell, $text);
        $sheet->getStyle($startCell)->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]]);
        $sheet->getRowDimension(1)->setRowHeight(32);
    }

    private static function mergeSubtitle($sheet, string $range, string $text): void
    {
        $sheet->mergeCells($range);
        [$startCell] = explode(':', $range);
        $sheet->setCellValue($startCell, $text);
        $sheet->getStyle($startCell)->applyFromArray(['font' => ['size' => 9, 'color' => ['rgb' => '374151']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
        $sheet->getRowDimension(2)->setRowHeight(20);
    }

    private static function styleHeader(): array
    {
        return ['font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '93C5FD']]]];
    }

    private static function colLetter(int $index): string
    {
        $letter = '';
        while ($index >= 0) {
            $letter = chr($index % 26 + 65) . $letter;
            $index = intval($index / 26) - 1;
        }
        return $letter;
    }

    private static function streamDownload(Spreadsheet $spreadsheet, string $filename): StreamedResponse
    {
        $writer = new Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}
