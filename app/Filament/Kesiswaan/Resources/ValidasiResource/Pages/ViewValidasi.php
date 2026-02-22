<?php

namespace App\Filament\Kesiswaan\Resources\ValidasiResource\Pages;

use App\Filament\Kesiswaan\Resources\ValidasiResource;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

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
              })
              ->formatStateUsing(fn(string $state): string => match ($state) {
                'pending' => 'Menunggu',
                'verified' => 'Diverifikasi',
                'rejected' => 'Ditolak',
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
      \Filament\Actions\Action::make('validate')
        ->label('Validasi')
        ->icon('heroicon-o-shield-check')
        ->color('success')
        ->requiresConfirmation()
        ->modalHeading('Validasi Formulir')
        ->modalSubmitActionLabel('Ya, Validasi')
        ->form([
          \Filament\Forms\Components\Textarea::make('catatan_kesiswaan')
            ->label('Catatan Kesiswaan (opsional)')
            ->rows(2),
        ])
        ->action(function (array $data) {
          $this->record->update([
            'kesiswaan_status' => 'validated',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
            'catatan_kesiswaan' => $data['catatan_kesiswaan'] ?? null,
          ]);
          \Filament\Notifications\Notification::make()
            ->title('Formulir berhasil divalidasi')
            ->success()
            ->send();
          $this->refreshFormData(['kesiswaan_status', 'validated_by', 'validated_at', 'catatan_kesiswaan']);
        })
        ->visible(fn() => $this->record->kesiswaan_status !== 'validated'),

      \Filament\Actions\Action::make('reject')
        ->label('Tolak')
        ->icon('heroicon-o-x-circle')
        ->color('danger')
        ->requiresConfirmation()
        ->modalHeading('Tolak Formulir')
        ->modalSubmitActionLabel('Ya, Tolak')
        ->form([
          \Filament\Forms\Components\Textarea::make('catatan_kesiswaan')
            ->label('Alasan Penolakan')
            ->rows(2)
            ->required(),
        ])
        ->action(function (array $data) {
          $this->record->update([
            'kesiswaan_status' => 'rejected',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
            'catatan_kesiswaan' => $data['catatan_kesiswaan'],
          ]);
          \Filament\Notifications\Notification::make()
            ->title('Formulir ditolak')
            ->warning()
            ->send();
          $this->refreshFormData(['kesiswaan_status', 'validated_by', 'validated_at', 'catatan_kesiswaan']);
        })
        ->visible(fn() => $this->record->kesiswaan_status !== 'rejected'),
    ];
  }
}
