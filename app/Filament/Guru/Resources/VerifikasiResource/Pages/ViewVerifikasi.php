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

        Infolists\Components\Section::make('Validasi Kesiswaan')
          ->schema([
            Infolists\Components\TextEntry::make('kesiswaan_status')
              ->label('Status Validasi')
              ->badge()
              ->color(fn(string $state): string => match ($state) {
                'pending' => 'gray',
                'validated' => 'success',
                'rejected' => 'danger',
                default => 'gray',
              })
              ->formatStateUsing(fn(string $state): string => match ($state) {
                'pending' => 'Menunggu Validasi',
                'validated' => 'Divalidasi',
                'rejected' => 'Ditolak Kesiswaan',
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
          ->columns(3)
          ->visible(fn() => $this->record->status === 'verified'),

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
          \Illuminate\Support\Facades\Cache::forget("submissions_{$this->record->user_id}");
          \Illuminate\Support\Facades\Cache::forget("submission_{$this->record->user_id}_{$this->record->hari_ke}");
          \Filament\Notifications\Notification::make()
            ->title('Formulir berhasil diverifikasi')
            ->success()
            ->send();
          return redirect(VerifikasiResource::getUrl('index'));
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
          \Illuminate\Support\Facades\Cache::forget("submissions_{$this->record->user_id}");
          \Illuminate\Support\Facades\Cache::forget("submission_{$this->record->user_id}_{$this->record->hari_ke}");
          \Filament\Notifications\Notification::make()
            ->title('Formulir ditolak')
            ->warning()
            ->send();
          return redirect(VerifikasiResource::getUrl('index'));
        })
        ->visible(fn() => $this->record->status === 'pending'),
    ];
  }
}
