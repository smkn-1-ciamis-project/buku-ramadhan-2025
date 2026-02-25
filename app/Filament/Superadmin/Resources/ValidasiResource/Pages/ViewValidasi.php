<?php

namespace App\Filament\Superadmin\Resources\ValidasiResource\Pages;

use App\Filament\Superadmin\Resources\ValidasiKelasResource;
use App\Filament\Superadmin\Resources\ValidasiResource;
use App\Models\ActivityLog;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ViewValidasi extends ViewRecord
{
    protected static string $resource = ValidasiResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Siswa')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Nama Siswa'),
                        Infolists\Components\TextEntry::make('user.nisn')
                            ->label('NISN'),
                        Infolists\Components\TextEntry::make('user.kelas.nama')
                            ->label('Kelas'),
                        Infolists\Components\TextEntry::make('user.agama')
                            ->label('Agama'),
                        Infolists\Components\TextEntry::make('hari_ke')
                            ->label('Hari Ke')
                            ->badge()
                            ->color('info'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Verifikasi Guru')
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status Guru')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'pending' => 'warning',
                                'verified' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'pending' => 'Menunggu',
                                'verified' => 'Diverifikasi',
                                'rejected' => 'Ditolak',
                                default => ucfirst($state),
                            }),
                        Infolists\Components\TextEntry::make('verifier.name')
                            ->label('Diverifikasi Oleh')
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('verified_at')
                            ->label('Waktu Verifikasi')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('catatan_guru')
                            ->label('Catatan Guru')
                            ->placeholder('Tidak ada catatan')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Validasi Kesiswaan')
                    ->schema([
                        Infolists\Components\TextEntry::make('kesiswaan_status')
                            ->label('Status Validasi')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'pending' => 'warning',
                                'validated' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'pending' => 'Menunggu Validasi',
                                'validated' => 'Sudah Divalidasi',
                                'rejected' => 'Ditolak',
                                default => $state,
                            }),
                        Infolists\Components\TextEntry::make('validator.name')
                            ->label('Divalidasi Oleh')
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('validated_at')
                            ->label('Waktu Validasi')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('catatan_kesiswaan')
                            ->label('Catatan Kesiswaan')
                            ->placeholder('Tidak ada catatan')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Isian Formulir')
                    ->schema([
                        Infolists\Components\ViewEntry::make('data')
                            ->label('')
                            ->view('filament.guru.components.form-submission-detail')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('resetStatus')
                ->label('Reset ke Menunggu')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Reset Status Validasi')
                ->modalDescription('Status validasi kesiswaan akan dikembalikan ke Menunggu.')
                ->modalSubmitActionLabel('Ya, Reset')
                ->action(function () {
                    $this->record->update([
                        'status' => 'pending',
                        'verified_by' => null,
                        'verified_at' => null,
                        'catatan_guru' => null,
                        'kesiswaan_status' => 'pending',
                        'validated_by' => null,
                        'validated_at' => null,
                        'catatan_kesiswaan' => null,
                    ]);
                    Cache::forget("submissions_{$this->record->user_id}");
                    Cache::forget("submission_{$this->record->user_id}_{$this->record->hari_ke}");
                    ActivityLog::log('reset_validation', Auth::user(), [
                        'description' => 'Mereset status formulir hari ke-' . $this->record->hari_ke . ' dari ' . ($this->record->user?->name ?? '-'),
                        'submission_id' => $this->record->id,
                        'target_user' => $this->record->user?->name,
                        'hari_ke' => $this->record->hari_ke,
                    ]);
                    \Filament\Notifications\Notification::make()
                        ->title('Status validasi direset')
                        ->info()
                        ->send();
                    $kelasId = $this->record->user?->kelas_id;
                    return $kelasId
                        ? redirect(ValidasiKelasResource::getUrl('validasi-kelas', ['record' => $kelasId]))
                        : redirect(ValidasiKelasResource::getUrl());
                })
                ->visible(fn() => $this->record->kesiswaan_status !== 'pending'),
        ];
    }
}
