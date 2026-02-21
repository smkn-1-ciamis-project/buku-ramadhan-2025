<?php

namespace App\Filament\Superadmin\Resources\GuruResource\Pages;

use App\Filament\Superadmin\Resources\GuruResource;
use App\Services\ImportService;
use App\Services\TemplateService;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListGuru extends ListRecords
{
  protected static string $resource = GuruResource::class;

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

          $result = ImportService::importGuru($filePath);

          // Clean up uploaded file
          if (file_exists($filePath)) {
            unlink($filePath);
          }

          if ($result['success'] > 0) {
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
        ->action(fn() => TemplateService::downloadGuruTemplate()),
    ];
  }
}
