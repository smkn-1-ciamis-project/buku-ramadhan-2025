<?php

namespace App\Filament\Superadmin\Resources\KesiswaanResource\Pages;

use App\Filament\Superadmin\Resources\KesiswaanResource;
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

class ListKesiswaan extends ListRecords
{
    protected static string $resource = KesiswaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Kesiswaan'),

            Actions\Action::make('importKesiswaan')
                ->label('Import Kesiswaan')
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
                ->modalHeading('Import Data Kesiswaan')
                ->modalDescription('Upload file Excel sesuai template. Pastikan data sudah benar sebelum import.')
                ->modalSubmitActionLabel('Import')
                ->action(function (array $data) {
                    $filePath = storage_path('app/' . $data['file']);

                    try {
                        $result = ImportService::importKesiswaan($filePath);
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

                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }

                    if ($result['success'] > 0) {
                        ActivityLog::log('import_kesiswaan', Auth::user(), [
                            'description' => 'Mengimport ' . $result['success'] . ' data kesiswaan (gagal: ' . $result['failed'] . ')',
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
                        return TemplateService::downloadKesiswaanTemplate();
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
                        $kesiswaan = User::whereHas('role_user', fn($q) => $q->where('name', 'Kesiswaan'))
                            ->orderBy('name')
                            ->get();

                        ActivityLog::log('export_kesiswaan', Auth::user(), [
                            'description' => 'Mengekspor data ' . $kesiswaan->count() . ' kesiswaan ke PDF',
                            'format' => 'pdf',
                            'total' => $kesiswaan->count(),
                        ]);

                        $totalL = $kesiswaan->where('jenis_kelamin', 'L')->count();
                        $totalP = $kesiswaan->where('jenis_kelamin', 'P')->count();

                        $pdf = Pdf::loadView('pdf.data-kesiswaan', [
                            'kesiswaan'      => $kesiswaan,
                            'tahunAjaran'    => '2025/2026',
                            'tanggalCetak'   => now()->translatedFormat('d F Y'),
                            'totalKesiswaan' => $kesiswaan->count(),
                            'totalL'         => $totalL,
                            'totalP'         => $totalP,
                        ]);

                        $pdf->setPaper('a4', 'portrait');

                        $filename = 'Data_Kesiswaan_SMKN1_Ciamis_' . now()->format('Ymd_His') . '.pdf';

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
