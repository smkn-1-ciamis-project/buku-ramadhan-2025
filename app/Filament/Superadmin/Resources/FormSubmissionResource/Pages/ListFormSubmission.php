<?php

namespace App\Filament\Superadmin\Resources\FormSubmissionResource\Pages;

use App\Filament\Superadmin\Resources\FormSubmissionResource;
use App\Models\ActivityLog;
use App\Models\FormSubmission;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ListFormSubmission extends ListRecords
{
  protected static string $resource = FormSubmissionResource::class;

  public function getTabs(): array
  {
    $tabs = [
      'semua' => Tab::make('Semua')
        ->badge(fn() => FormSubmission::count())
        ->badgeColor('primary'),
    ];

    foreach (['10', '11', '12'] as $tingkat) {
      $tabs["kelas_{$tingkat}"] = Tab::make("Kelas {$tingkat}")
        ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('user.kelas', fn(Builder $q) => $q->where('nama', 'like', "{$tingkat} %")))
        ->badge(fn() => FormSubmission::whereHas('user.kelas', fn(Builder $q) => $q->where('nama', 'like', "{$tingkat} %"))->count())
        ->badgeColor('gray');
    }

    return $tabs;
  }

  public function getDefaultActiveTab(): string|int|null
  {
    return 'semua';
  }

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('backupLog')
        ->label('Backup Log')
        ->icon('heroicon-o-arrow-down-on-square-stack')
        ->color('info')
        ->form([
          TextInput::make('password')
            ->label('Password Superadmin')
            ->password()
            ->required()
            ->placeholder('Masukkan password untuk konfirmasi'),
        ])
        ->modalHeading('Backup Log Formulir')
        ->modalDescription('Semua data log formulir akan di-backup ke file Excel per kelas. Masukkan password superadmin untuk melanjutkan.')
        ->modalSubmitActionLabel('Backup')
        ->action(function (array $data) {
          return $this->executeBackup($data['password'], false);
        }),

      Actions\Action::make('backupDanHapusLog')
        ->label('Backup & Hapus Semua Log')
        ->icon('heroicon-o-trash')
        ->color('danger')
        ->form([
          TextInput::make('password')
            ->label('Password Superadmin')
            ->password()
            ->required()
            ->placeholder('Masukkan password untuk konfirmasi'),
        ])
        ->modalHeading('Backup & Hapus Semua Log Formulir')
        ->modalDescription('⚠️ PERHATIAN: Semua data log formulir akan di-backup ke Excel kemudian dihapus secara permanen. Masukkan password superadmin untuk melanjutkan.')
        ->modalSubmitActionLabel('Ya, Backup & Hapus')
        ->action(function (array $data) {
          return $this->executeBackup($data['password'], true);
        }),
    ];
  }

  private function executeBackup(string $password, bool $deleteAfter)
  {
    $admin = auth()->user();

    if (!Hash::check($password, $admin->password)) {
      Notification::make()
        ->title('Password Salah')
        ->body('Password superadmin tidak valid.')
        ->danger()
        ->send();
      return;
    }

    try {
      $submissions = FormSubmission::with(['user.kelas', 'user.role_user', 'verifier', 'validator'])
        ->orderBy('created_at', 'desc')
        ->get();

      $count = $submissions->count();

      if ($count === 0) {
        Notification::make()
          ->title('Tidak Ada Data')
          ->body('Tidak ada log formulir untuk di-backup.')
          ->warning()
          ->send();
        return;
      }

      // Group by kelas
      $grouped = $submissions->groupBy(fn($sub) => $sub->user->kelas->nama ?? 'Tanpa Kelas');
      $sortedKeys = $grouped->keys()->sort()->values();

      $spreadsheet = new Spreadsheet();
      $spreadsheet->removeSheetByIndex(0);

      $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
      ];

      $headers = [
        'A' => 'No',
        'B' => 'Nama Siswa',
        'C' => 'NISN',
        'D' => 'Hari Ke',
        'E' => 'Puasa',
        'F' => 'Subuh',
        'G' => 'Dzuhur',
        'H' => 'Ashar',
        'I' => 'Maghrib',
        'J' => 'Isya',
        'K' => 'Tarawih',
        'L' => 'Rowatib',
        'M' => 'Tahajud',
        'N' => 'Dhuha',
        'O' => 'Tadarus',
        'P' => 'Kegiatan Harian',
        'Q' => 'Ceramah',
        'R' => 'Tema Ceramah',
        'S' => 'Status Guru',
        'T' => 'Catatan Guru',
        'U' => 'Status Kesiswaan',
        'V' => 'Catatan Kesiswaan',
        'W' => 'Dikirim',
      ];

      $widths = [
        'A' => 5,
        'B' => 25,
        'C' => 16,
        'D' => 8,
        'E' => 8,
        'F' => 8,
        'G' => 8,
        'H' => 8,
        'I' => 9,
        'J' => 8,
        'K' => 9,
        'L' => 9,
        'M' => 9,
        'N' => 8,
        'O' => 30,
        'P' => 35,
        'Q' => 10,
        'R' => 30,
        'S' => 14,
        'T' => 25,
        'U' => 16,
        'V' => 25,
        'W' => 17,
      ];

      $sholatLabel = fn($v) => match ($v) {
        'jamaah' => 'Jamaah',
        'munfarid' => 'Munfarid',
        'tidak' => 'Tidak',
        default => '-',
      };

      foreach ($sortedKeys as $kelasNama) {
        $kelaSubs = $grouped[$kelasNama]->sortBy([['user.name', 'asc'], ['hari_ke', 'asc']]);

        // Sheet name max 31 chars
        $sheetName = mb_substr(str_replace(['\\', '/', '*', '?', ':', '[', ']'], '-', $kelasNama), 0, 31);
        $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $sheetName);
        $spreadsheet->addSheet($sheet);

        // Write headers
        foreach ($headers as $col => $label) {
          $sheet->setCellValue("{$col}1", $label);
        }
        $lastCol = 'W';
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(24);

        foreach ($widths as $col => $w) {
          $sheet->getColumnDimension($col)->setWidth($w);
        }

        $row = 2;
        foreach ($kelaSubs as $i => $sub) {
          $d = $sub->data ?? [];
          $sholat = $d['sholat'] ?? [];
          $sunat = $d['sunat'] ?? [];

          // Puasa
          $puasa = match ($d['puasa'] ?? '') {
            'ya' => 'Ya',
            'tidak' => 'Tidak',
            default => '-',
          };

          // Tadarus
          $entries = $d['tadarus_entries'] ?? [];
          if (empty($entries) && (!empty($d['tadarus_surat'] ?? '') || !empty($d['tadarus_ayat'] ?? ''))) {
            $entries = [['surat' => $d['tadarus_surat'] ?? '', 'ayat' => $d['tadarus_ayat'] ?? '']];
          }
          $entries = array_filter($entries, fn($e) => !empty($e['surat'] ?? '') || !empty($e['ayat'] ?? ''));
          $tadarusText = empty($entries) ? '-' : implode('; ', array_map(fn($e) => ($e['surat'] ?? '-') . ' ayat ' . ($e['ayat'] ?? '-'), $entries));

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
            'kajian' => 'Kajian',
            'menabung' => 'Menabung',
            'tidur_cepat' => 'Tidur Cepat',
            'bangun_pagi' => 'Bangun Pagi',
          ];
          $kegiatan = $d['kegiatan'] ?? [];
          $done = [];
          foreach ($kegiatanLabels as $key => $label) {
            if (!empty($kegiatan[$key]) && $kegiatan[$key] !== false && $kegiatan[$key] !== 'tidak') {
              $done[] = $label;
            }
          }

          // Ceramah
          $ceramahMode = match ($d['ceramah_mode'] ?? '') {
            'offline' => 'Offline',
            'online' => 'Online',
            'tidak' => 'Tidak',
            default => '-',
          };
          $ceramahTema = $d['ceramah_tema'] ?? ($d['ringkasan_ceramah'] ?? '');
          if (!empty($ceramahTema)) {
            $ceramahTema = mb_strimwidth(strip_tags($ceramahTema), 0, 200, '...');
          }

          // Status labels
          $statusGuru = match ($sub->status) {
            'pending' => 'Menunggu',
            'verified' => 'Diverifikasi',
            'rejected' => 'Ditolak',
            default => $sub->status,
          };
          $statusKesiswaan = match ($sub->kesiswaan_status ?? 'pending') {
            'pending' => 'Menunggu',
            'validated' => 'Divalidasi',
            'rejected' => 'Ditolak',
            default => $sub->kesiswaan_status,
          };

          $sheet->fromArray([
            $i + 1,
            $sub->user->name ?? '-',
            "'" . ($sub->user->nisn ?? '-'),
            $sub->hari_ke,
            $puasa,
            $sholatLabel($sholat['subuh'] ?? ''),
            $sholatLabel($sholat['dzuhur'] ?? ''),
            $sholatLabel($sholat['ashar'] ?? ''),
            $sholatLabel($sholat['maghrib'] ?? ''),
            $sholatLabel($sholat['isya'] ?? ''),
            $sholatLabel($d['tarawih'] ?? ''),
            ($sunat['rowatib'] ?? '') === 'ya' ? 'Ya' : (($sunat['rowatib'] ?? '') === 'tidak' ? 'Tidak' : '-'),
            ($sunat['tahajud'] ?? '') === 'ya' ? 'Ya' : (($sunat['tahajud'] ?? '') === 'tidak' ? 'Tidak' : '-'),
            ($sunat['dhuha'] ?? '') === 'ya' ? 'Ya' : (($sunat['dhuha'] ?? '') === 'tidak' ? 'Tidak' : '-'),
            $tadarusText,
            empty($done) ? '-' : implode(', ', $done),
            $ceramahMode,
            !empty($ceramahTema) ? $ceramahTema : '-',
            $statusGuru,
            $sub->catatan_guru ?? '-',
            $statusKesiswaan,
            $sub->catatan_kesiswaan ?? '-',
            $sub->created_at->format('d/m/Y H:i'),
          ], null, "A{$row}");

          // Alternate row color
          if ($row % 2 === 0) {
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
              'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
            ]);
          }

          $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
          ]);

          $row++;
        }

        // Wrap text for long columns
        foreach (['O', 'P', 'R', 'T', 'V'] as $wrapCol) {
          for ($r = 2; $r < $row; $r++) {
            $sheet->getStyle("{$wrapCol}{$r}")->getAlignment()->setWrapText(true);
          }
        }
      }

      $spreadsheet->setActiveSheetIndex(0);

      $filename = 'backup_log_formulir_' . now()->format('Y-m-d_His') . '.xlsx';
      $tempPath = storage_path("app/{$filename}");

      $writer = new Xlsx($spreadsheet);
      $writer->save($tempPath);

      if ($deleteAfter) {
        FormSubmission::truncate();
        ActivityLog::log('backup_and_delete_data', Auth::user(), [
          'description' => 'Mem-backup dan menghapus ' . $count . ' data formulir (' . $sortedKeys->count() . ' kelas)',
          'count' => $count,
          'kelas_count' => $sortedKeys->count(),
        ]);
        Notification::make()
          ->title('Backup & Hapus Berhasil')
          ->body("Total {$count} data ({$sortedKeys->count()} kelas) berhasil di-backup dan dihapus.")
          ->success()
          ->send();
      } else {
        ActivityLog::log('backup_data', Auth::user(), [
          'description' => 'Mem-backup ' . $count . ' data formulir (' . $sortedKeys->count() . ' kelas)',
          'count' => $count,
          'kelas_count' => $sortedKeys->count(),
        ]);
        Notification::make()
          ->title('Backup Berhasil')
          ->body("Total {$count} data ({$sortedKeys->count()} kelas) berhasil di-backup.")
          ->success()
          ->send();
      }

      return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    } catch (\Throwable $e) {
      Notification::make()
        ->title('Gagal')
        ->body('Terjadi kesalahan: ' . $e->getMessage())
        ->danger()
        ->persistent()
        ->send();
    }
  }
}
