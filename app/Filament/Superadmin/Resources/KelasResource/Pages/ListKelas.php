<?php

namespace App\Filament\Superadmin\Resources\KelasResource\Pages;

use App\Filament\Superadmin\Resources\KelasResource;
use App\Services\ImportService;
use App\Services\TemplateService;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListKelas extends ListRecords
{
  protected static string $resource = KelasResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make()->label('Tambah Kelas'),

      Actions\Action::make('importKelas')
        ->label('Import Kelas')
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
        ->modalHeading('Import Kelas, Wali & Siswa')
        ->modalDescription('Upload file Excel sesuai template. Sheet "Daftar Kelas" berisi nama kelas & wali, sheet lainnya berisi data siswa per kelas.')
        ->modalSubmitActionLabel('Import')
        ->action(function (array $data) {
          $filePath = storage_path('app/' . $data['file']);

          try {
            $result = ImportService::importKelas($filePath);
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

          $totalCreated = ($result['guru_created'] ?? 0) + $result['kelas_created'] + $result['siswa_created'];

          if ($totalCreated > 0 || $result['wali_assigned'] > 0) {
            $body = collect([
              ($result['guru_created'] ?? 0) > 0 ? "Guru baru: {$result['guru_created']}" : null,
              $result['kelas_created'] > 0 ? "Kelas baru: {$result['kelas_created']}" : null,
              $result['wali_assigned'] > 0 ? "Wali ditugaskan: {$result['wali_assigned']}" : null,
              $result['siswa_created'] > 0 ? "Siswa baru: {$result['siswa_created']}" : null,
              $result['failed'] > 0 ? "Gagal: {$result['failed']}" : null,
            ])->filter()->implode(' | ');

            Notification::make()
              ->title('Import Berhasil')
              ->body($body)
              ->success()
              ->send();
          }

          if (!empty($result['errors'])) {
            Notification::make()
              ->title('Catatan Import')
              ->body(implode("\n", array_slice($result['errors'], 0, 8)))
              ->warning()
              ->persistent()
              ->send();
          }

          if ($totalCreated === 0 && $result['wali_assigned'] === 0 && $result['failed'] === 0) {
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
            return TemplateService::downloadKelasTemplate();
          } catch (\Throwable $e) {
            Notification::make()
              ->title('Download Gagal')
              ->body('Terjadi kesalahan: ' . $e->getMessage())
              ->danger()
              ->send();
          }
        }),
    ];
  }
}
