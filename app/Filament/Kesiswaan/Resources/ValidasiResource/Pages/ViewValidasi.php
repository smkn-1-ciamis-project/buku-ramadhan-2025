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
            Infolists\Components\TextEntry::make('status')
              ->label('Status')
              ->badge()
              ->color(fn(string $state): string => match ($state) {
                'pending' => 'warning',
                'verified' => 'success',
                'rejected' => 'danger',
              })
              ->formatStateUsing(fn(string $state): string => match ($state) {
                'pending' => 'Menunggu',
                'verified' => 'Terverifikasi',
                'rejected' => 'Ditolak',
              }),
            Infolists\Components\TextEntry::make('created_at')
              ->label('Waktu Pengiriman')
              ->dateTime('d M Y, H:i'),
            Infolists\Components\TextEntry::make('verifier.name')
              ->label('Diverifikasi Oleh')
              ->placeholder('-'),
            Infolists\Components\TextEntry::make('verified_at')
              ->label('Waktu Verifikasi')
              ->dateTime('d M Y, H:i')
              ->placeholder('-'),
            Infolists\Components\TextEntry::make('catatan_guru')
              ->label('Catatan')
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
          \Filament\Forms\Components\Textarea::make('catatan_guru')
            ->label('Catatan Kesiswaan (opsional)')
            ->rows(2),
        ])
        ->action(function (array $data) {
          $this->record->update([
            'status' => 'verified',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'catatan_guru' => $data['catatan_guru'] ?? $this->record->catatan_guru,
          ]);
          \Filament\Notifications\Notification::make()
            ->title('Formulir berhasil divalidasi')
            ->success()
            ->send();
          $this->refreshFormData(['status', 'verified_by', 'verified_at', 'catatan_guru']);
        })
        ->visible(fn() => $this->record->status !== 'verified'),

      \Filament\Actions\Action::make('reject')
        ->label('Tolak')
        ->icon('heroicon-o-x-circle')
        ->color('danger')
        ->requiresConfirmation()
        ->modalHeading('Tolak Formulir')
        ->modalSubmitActionLabel('Ya, Tolak')
        ->form([
          \Filament\Forms\Components\Textarea::make('catatan_guru')
            ->label('Alasan Penolakan')
            ->rows(2)
            ->required(),
        ])
        ->action(function (array $data) {
          $this->record->update([
            'status' => 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'catatan_guru' => $data['catatan_guru'],
          ]);
          \Filament\Notifications\Notification::make()
            ->title('Formulir ditolak')
            ->warning()
            ->send();
          $this->refreshFormData(['status', 'verified_by', 'verified_at', 'catatan_guru']);
        })
        ->visible(fn() => $this->record->status !== 'rejected'),
    ];
  }
}
