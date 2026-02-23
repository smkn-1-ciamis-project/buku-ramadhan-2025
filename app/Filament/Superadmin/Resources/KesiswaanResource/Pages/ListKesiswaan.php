<?php

namespace App\Filament\Superadmin\Resources\KesiswaanResource\Pages;

use App\Filament\Superadmin\Resources\KesiswaanResource;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListKesiswaan extends ListRecords
{
    protected static string $resource = KesiswaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Kesiswaan'),

            Actions\Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    try {
                        $kesiswaan = User::whereHas('role_user', fn($q) => $q->where('name', 'Kesiswaan'))
                            ->orderBy('name')
                            ->get();

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
