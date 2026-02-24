<?php

namespace App\Filament\Superadmin\Resources\GuruResource\Pages;

use App\Filament\Superadmin\Resources\GuruResource;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ImportService;
use App\Services\TemplateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListGuru extends ListRecords
{
  protected static string $resource = GuruResource::class;

  public function getTabs(): array
  {
    $tabs = [
      'semua' => \Filament\Resources\Components\Tab::make('Semua')
        ->badge(fn() => \App\Models\User::whereHas('role_user', fn($q) => $q->where('name', 'Guru'))->count())
        ->badgeColor('primary'),
    ];

    foreach (['10', '11', '12'] as $tingkat) {
      $tabs["kelas_{$tingkat}"] = \Filament\Resources\Components\Tab::make("Kelas {$tingkat}")
        ->modifyQueryUsing(fn($query) => $query->whereHas('kelasWali', fn($q) => $q->where('nama', 'like', "{$tingkat} %")))
        ->badge(fn() => \App\Models\User::whereHas('role_user', fn($q) => $q->where('name', 'Guru'))->whereHas('kelasWali', fn($q) => $q->where('nama', 'like', "{$tingkat} %"))->count())
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
      Actions\CreateAction::make()->label('Tambah Guru'),

      Actions\Action::make('importGuru')
        ->label('Import Guru')
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
        ->modalHeading('Import Data Guru')
        ->modalDescription('Upload file Excel sesuai template. Pastikan data sudah benar sebelum import.')
        ->modalSubmitActionLabel('Import')
        ->action(function (array $data) {
          $filePath = storage_path('app/' . $data['file']);

          try {
            $result = ImportService::importGuru($filePath);
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
            ActivityLog::log('import_guru', Auth::user(), [
              'description' => 'Mengimport ' . $result['success'] . ' data guru (gagal: ' . $result['failed'] . ')',
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
            return TemplateService::downloadGuruTemplate();
          } catch (\Throwable $e) {
            Notification::make()
              ->title('Download Gagal')
              ->body('Terjadi kesalahan: ' . $e->getMessage())
              ->danger()
              ->send();
          }
        }),

      Actions\Action::make('exportPdf')
        ->label('Export PDF')
        ->icon('heroicon-o-document-arrow-down')
        ->color('danger')
        ->action(function () {
          try {
            $guru = User::whereHas('role_user', fn($q) => $q->where('name', 'Guru'))
              ->with('kelasWali')
              ->orderBy('name')
              ->get();

            ActivityLog::log('export_guru', Auth::user(), [
              'description' => 'Mengekspor data ' . $guru->count() . ' guru ke PDF',
              'format' => 'pdf',
              'total' => $guru->count(),
            ]);

            $totalL = $guru->where('jenis_kelamin', 'L')->count();
            $totalP = $guru->where('jenis_kelamin', 'P')->count();
            $totalWali = $guru->filter(fn($g) => $g->kelasWali->isNotEmpty())->count();

            $pdf = Pdf::loadView('pdf.data-guru', [
              'guru'         => $guru,
              'tahunAjaran'  => '2025/2026',
              'tanggalCetak' => now()->translatedFormat('d F Y'),
              'totalGuru'    => $guru->count(),
              'totalL'       => $totalL,
              'totalP'       => $totalP,
              'totalWali'    => $totalWali,
            ]);

            $pdf->setPaper('a4', 'portrait');

            $filename = 'Data_Guru_SMKN1_Ciamis_' . now()->format('Ymd_His') . '.pdf';

            return response()->streamDownload(
              fn() => print($pdf->output()),
              $filename,
              ['Content-Type' => 'application/pdf']
            );
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
}
