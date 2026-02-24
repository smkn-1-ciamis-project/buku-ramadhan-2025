<?php

namespace App\Filament\Superadmin\Resources\SiswaResource\Pages;

use App\Filament\Superadmin\Resources\SiswaResource;
use App\Models\ActivityLog;
use App\Models\Kelas;
use App\Models\User;
use App\Services\ImportService;
use App\Services\TemplateService;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options;

class ListSiswa extends ListRecords
{
  protected static string $resource = SiswaResource::class;

  public function getTabs(): array
  {
    $tabs = [
      'semua' => \Filament\Resources\Components\Tab::make('Semua')
        ->badge(fn() => \App\Models\User::whereHas('role_user', fn($q) => $q->where('name', 'Siswa'))->count())
        ->badgeColor('primary'),
    ];

    foreach (['10', '11', '12'] as $tingkat) {
      $tabs["kelas_{$tingkat}"] = \Filament\Resources\Components\Tab::make("Kelas {$tingkat}")
        ->modifyQueryUsing(fn($query) => $query->whereHas('kelas', fn($q) => $q->where('nama', 'like', "{$tingkat} %")))
        ->badge(fn() => \App\Models\User::whereHas('role_user', fn($q) => $q->where('name', 'Siswa'))->whereHas('kelas', fn($q) => $q->where('nama', 'like', "{$tingkat} %"))->count())
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
      Actions\CreateAction::make()->label('Tambah Siswa'),

      Actions\Action::make('importSiswa')
        ->label('Import Siswa')
        ->icon('heroicon-o-arrow-up-tray')
        ->color('success')
        ->form([
          FileUpload::make('file')
            ->label('File Excel (.xlsx)')
            ->acceptedFileTypes([
              'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
              'application/vnd.ms-excel',
            ])
            ->maxSize(5120)
            ->required()
            ->disk('local')
            ->directory('imports')
            ->visibility('private'),
        ])
        ->modalHeading('Import Data Siswa')
        ->modalDescription('Upload file Excel sesuai template. Pastikan data sudah benar sebelum import.')
        ->modalSubmitActionLabel('Import')
        ->action(function (array $data) {
          $filePath = storage_path('app/' . $data['file']);

          try {
            $result = ImportService::importSiswa($filePath);
          } catch (\Throwable $e) {
            if (file_exists($filePath)) {
              unlink($filePath);
            }
            Notification::make()
              ->title('Import Gagal')
              ->body('Terjadi kesalahan: ' . $e->getMessage())
              ->danger()
              ->persistent()
              ->send();
            return;
          }

          // Clean up uploaded file
          if (file_exists($filePath)) {
            unlink($filePath);
          }

          if ($result['success'] > 0) {
            ActivityLog::log('import_siswa', Auth::user(), [
              'description' => 'Mengimport ' . $result['success'] . ' data siswa (gagal: ' . $result['failed'] . ')',
              'success' => $result['success'],
              'failed' => $result['failed'],
            ]);
            Notification::make()
              ->title('Import Berhasil')
              ->body("Berhasil: {$result['success']} data. Gagal: {$result['failed']} data.")
              ->success()
              ->send();
          }

          if (!empty($result['errors'])) {
            Notification::make()
              ->title('Beberapa data gagal diimport')
              ->body(implode("\n", array_slice($result['errors'], 0, 5)))
              ->danger()
              ->persistent()
              ->send();
          }

          if ($result['success'] === 0 && $result['failed'] === 0) {
            Notification::make()
              ->title('Tidak ada data')
              ->body('File tidak berisi data untuk diimport.')
              ->warning()
              ->send();
          }
        }),

      Actions\Action::make('downloadTemplate')
        ->label('Download Template')
        ->icon('heroicon-o-arrow-down-tray')
        ->color('gray')
        ->action(function () {
          try {
            return TemplateService::downloadSiswaTemplate();
          } catch (\Throwable $e) {
            Notification::make()
              ->title('Download Gagal')
              ->body('Terjadi kesalahan: ' . $e->getMessage())
              ->danger()
              ->send();
          }
        }),

      Actions\Action::make('exportExcel')
        ->label('Export Excel')
        ->icon('heroicon-o-document-arrow-down')
        ->color('danger')
        ->action(function () {
          try {
            $total = User::whereHas('role_user', fn($q) => $q->where('name', 'Siswa'))->count();
            ActivityLog::log('export_siswa', Auth::user(), [
              'description' => 'Mengekspor data ' . $total . ' siswa ke Excel',
              'format' => 'xlsx',
              'total' => $total,
            ]);
            return $this->exportSiswaExcel();
          } catch (\Throwable $e) {
            Notification::make()
              ->title('Export Gagal')
              ->body('Terjadi kesalahan: ' . $e->getMessage())
              ->danger()
              ->send();
          }
        }),
    ];
  }

  /**
   * Export semua siswa ke Excel dengan sheet per kelas + info wali.
   */
  private function exportSiswaExcel()
  {
    $filename = 'Data_Siswa_SMKN1_Ciamis_' . now()->format('Ymd_His') . '.xlsx';
    $tempPath = storage_path('app/' . $filename);

    $options = new Options();
    $writer = new Writer($options);
    $writer->openToFile($tempPath);

    // ── Styles ──
    $titleStyle = (new Style())
      ->setFontBold()
      ->setFontSize(14)
      ->setFontColor(Color::rgb(30, 58, 95));

    $infoStyle = (new Style())
      ->setFontSize(10)
      ->setFontColor(Color::rgb(100, 100, 100));

    $infoValueStyle = (new Style())
      ->setFontBold()
      ->setFontSize(10);

    $headerStyle = (new Style())
      ->setFontBold()
      ->setFontSize(10)
      ->setFontColor(Color::WHITE)
      ->setBackgroundColor(Color::rgb(30, 58, 95))
      ->setShouldWrapText(false);

    $dataStyle = (new Style())
      ->setFontSize(10)
      ->setShouldWrapText(false);

    $summaryStyle = (new Style())
      ->setFontBold()
      ->setFontSize(10)
      ->setBackgroundColor(Color::rgb(219, 234, 254));

    // ── Ambil semua kelas beserta wali dan siswa ──
    $kelasList = Kelas::with(['wali', 'siswa' => function ($q) {
      $q->whereHas('role_user', fn($rq) => $rq->where('name', 'Siswa'))
        ->orderBy('name');
    }])->orderBy('nama')->get();

    // ── Siswa tanpa kelas ──
    $tanpaKelas = User::whereNull('kelas_id')
      ->whereHas('role_user', fn($q) => $q->where('name', 'Siswa'))
      ->orderBy('name')
      ->get();

    $grandTotal = 0;
    $grandL = 0;
    $grandP = 0;

    // ═══ SHEET: REKAP ═══
    $writer->getCurrentSheet()->setName('Rekap');

    $writer->addRow(Row::fromValues(['REKAP DATA SISWA — SMKN 1 CIAMIS'], $titleStyle));
    $writer->addRow(Row::fromValues([
      'Tahun Ajaran: 2025/2026',
      '',
      '',
      'Tanggal Cetak: ' . now()->translatedFormat('d F Y'),
    ], $infoStyle));
    $writer->addRow(Row::fromValues(['']));

    // Rekap header
    $writer->addRow(new Row([
      Cell\StringCell::fromValue('No'),
      Cell\StringCell::fromValue('Kelas'),
      Cell\StringCell::fromValue('Wali Kelas'),
      Cell\StringCell::fromValue('Laki-laki'),
      Cell\StringCell::fromValue('Perempuan'),
      Cell\StringCell::fromValue('Total Siswa'),
    ], $headerStyle));

    $rekapNo = 0;
    foreach ($kelasList as $kelas) {
      $siswa = $kelas->siswa;
      $l = $siswa->where('jenis_kelamin', 'L')->count();
      $p = $siswa->where('jenis_kelamin', 'P')->count();
      $total = $siswa->count();
      $grandTotal += $total;
      $grandL += $l;
      $grandP += $p;
      $rekapNo++;

      $writer->addRow(new Row([
        Cell\NumericCell::fromValue($rekapNo),
        Cell\StringCell::fromValue($kelas->nama),
        Cell\StringCell::fromValue($kelas->wali?->name ?? 'Belum ditentukan'),
        Cell\NumericCell::fromValue($l),
        Cell\NumericCell::fromValue($p),
        Cell\NumericCell::fromValue($total),
      ], $dataStyle));
    }

    // Siswa tanpa kelas di rekap
    if ($tanpaKelas->isNotEmpty()) {
      $tkL = $tanpaKelas->where('jenis_kelamin', 'L')->count();
      $tkP = $tanpaKelas->where('jenis_kelamin', 'P')->count();
      $grandTotal += $tanpaKelas->count();
      $grandL += $tkL;
      $grandP += $tkP;
      $rekapNo++;

      $writer->addRow(new Row([
        Cell\NumericCell::fromValue($rekapNo),
        Cell\StringCell::fromValue('Belum Ada Kelas'),
        Cell\StringCell::fromValue('-'),
        Cell\NumericCell::fromValue($tkL),
        Cell\NumericCell::fromValue($tkP),
        Cell\NumericCell::fromValue($tanpaKelas->count()),
      ], $dataStyle));
    }

    // Grand total row
    $writer->addRow(new Row([
      Cell\StringCell::fromValue(''),
      Cell\StringCell::fromValue('TOTAL KESELURUHAN'),
      Cell\StringCell::fromValue(''),
      Cell\NumericCell::fromValue($grandL),
      Cell\NumericCell::fromValue($grandP),
      Cell\NumericCell::fromValue($grandTotal),
    ], $summaryStyle));

    // ═══ SHEET PER KELAS ═══
    foreach ($kelasList as $kelas) {
      $sheet = $writer->addNewSheetAndMakeItCurrent();
      // Sheet name max 31 chars in Excel
      $sheetName = mb_substr($kelas->nama, 0, 31);
      $sheet->setName($sheetName);

      $siswa = $kelas->siswa;
      $totalL = $siswa->where('jenis_kelamin', 'L')->count();
      $totalP = $siswa->where('jenis_kelamin', 'P')->count();

      // Title & info
      $writer->addRow(Row::fromValues(['DATA SISWA — ' . $kelas->nama], $titleStyle));
      $writer->addRow(Row::fromValues([
        'Wali Kelas: ' . ($kelas->wali?->name ?? 'Belum ditentukan'),
        '',
        '',
        'Tahun Ajaran: 2025/2026',
      ], $infoStyle));
      $writer->addRow(Row::fromValues([
        'Total: ' . $siswa->count() . ' siswa (L: ' . $totalL . ', P: ' . $totalP . ')',
        '',
        '',
        'Tanggal Cetak: ' . now()->translatedFormat('d F Y'),
      ], $infoStyle));
      $writer->addRow(Row::fromValues(['']));

      // Data header
      $writer->addRow(new Row([
        Cell\StringCell::fromValue('No'),
        Cell\StringCell::fromValue('Nama Lengkap'),
        Cell\StringCell::fromValue('NISN'),
        Cell\StringCell::fromValue('JK'),
        Cell\StringCell::fromValue('Agama'),
        Cell\StringCell::fromValue('Email'),
        Cell\StringCell::fromValue('No. HP'),
      ], $headerStyle));

      // Data rows
      $no = 0;
      foreach ($siswa as $s) {
        $no++;
        $writer->addRow(new Row([
          Cell\NumericCell::fromValue($no),
          Cell\StringCell::fromValue($s->name),
          Cell\StringCell::fromValue($s->nisn ?? '-'),
          Cell\StringCell::fromValue($s->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'),
          Cell\StringCell::fromValue($s->agama ?? '-'),
          Cell\StringCell::fromValue($s->email ?? '-'),
          Cell\StringCell::fromValue($s->no_hp ?? '-'),
        ], $dataStyle));
      }

      if ($siswa->isEmpty()) {
        $writer->addRow(Row::fromValues(['', 'Belum ada data siswa'], $infoStyle));
      }

      // Summary row
      $writer->addRow(Row::fromValues(['']));
      $writer->addRow(new Row([
        Cell\StringCell::fromValue(''),
        Cell\StringCell::fromValue('TOTAL'),
        Cell\StringCell::fromValue(''),
        Cell\StringCell::fromValue('L: ' . $totalL),
        Cell\StringCell::fromValue('P: ' . $totalP),
        Cell\StringCell::fromValue('Total: ' . $siswa->count()),
        Cell\StringCell::fromValue(''),
      ], $summaryStyle));
    }

    // ═══ SHEET: BELUM ADA KELAS ═══
    if ($tanpaKelas->isNotEmpty()) {
      $sheet = $writer->addNewSheetAndMakeItCurrent();
      $sheet->setName('Belum Ada Kelas');

      $tkL = $tanpaKelas->where('jenis_kelamin', 'L')->count();
      $tkP = $tanpaKelas->where('jenis_kelamin', 'P')->count();

      $writer->addRow(Row::fromValues(['DATA SISWA — BELUM ADA KELAS'], $titleStyle));
      $writer->addRow(Row::fromValues([
        'Total: ' . $tanpaKelas->count() . ' siswa (L: ' . $tkL . ', P: ' . $tkP . ')',
        '',
        '',
        'Tanggal Cetak: ' . now()->translatedFormat('d F Y'),
      ], $infoStyle));
      $writer->addRow(Row::fromValues(['']));

      $writer->addRow(new Row([
        Cell\StringCell::fromValue('No'),
        Cell\StringCell::fromValue('Nama Lengkap'),
        Cell\StringCell::fromValue('NISN'),
        Cell\StringCell::fromValue('JK'),
        Cell\StringCell::fromValue('Agama'),
        Cell\StringCell::fromValue('Email'),
        Cell\StringCell::fromValue('No. HP'),
      ], $headerStyle));

      $no = 0;
      foreach ($tanpaKelas as $s) {
        $no++;
        $writer->addRow(new Row([
          Cell\NumericCell::fromValue($no),
          Cell\StringCell::fromValue($s->name),
          Cell\StringCell::fromValue($s->nisn ?? '-'),
          Cell\StringCell::fromValue($s->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'),
          Cell\StringCell::fromValue($s->agama ?? '-'),
          Cell\StringCell::fromValue($s->email ?? '-'),
          Cell\StringCell::fromValue($s->no_hp ?? '-'),
        ], $dataStyle));
      }

      $writer->addRow(Row::fromValues(['']));
      $writer->addRow(new Row([
        Cell\StringCell::fromValue(''),
        Cell\StringCell::fromValue('TOTAL'),
        Cell\StringCell::fromValue(''),
        Cell\StringCell::fromValue('L: ' . $tkL),
        Cell\StringCell::fromValue('P: ' . $tkP),
        Cell\StringCell::fromValue('Total: ' . $tanpaKelas->count()),
        Cell\StringCell::fromValue(''),
      ], $summaryStyle));
    }

    $writer->close();

    return response()->download($tempPath, $filename, [
      'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ])->deleteFileAfterSend(true);
  }
}
