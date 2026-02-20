<?php

namespace App\Filament\Guru\Resources\VerifikasiResource\Pages;

use App\Filament\Guru\Resources\VerifikasiResource;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewVerifikasi extends ViewRecord
{
  protected static string $resource = VerifikasiResource::class;

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
                'pending' => 'Menunggu Verifikasi',
                'verified' => 'Diverifikasi',
                'rejected' => 'Ditolak',
              }),
            Infolists\Components\TextEntry::make('created_at')
              ->label('Waktu Pengiriman')
              ->dateTime('d M Y, H:i'),
            Infolists\Components\TextEntry::make('catatan_guru')
              ->label('Catatan Guru')
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
      \Filament\Actions\Action::make('verify')
        ->label('Verifikasi')
        ->icon('heroicon-o-check-circle')
        ->color('success')
        ->requiresConfirmation()
        ->modalHeading('Verifikasi Formulir')
        ->modalSubmitActionLabel('Ya, Verifikasi')
        ->form([
          \Filament\Forms\Components\Textarea::make('catatan_guru')
            ->label('Catatan (opsional)')
            ->rows(2),
        ])
        ->action(function (array $data) {
          $this->record->update([
            'status' => 'verified',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'catatan_guru' => $data['catatan_guru'] ?? null,
          ]);
          \Filament\Notifications\Notification::make()
            ->title('Formulir berhasil diverifikasi')
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
