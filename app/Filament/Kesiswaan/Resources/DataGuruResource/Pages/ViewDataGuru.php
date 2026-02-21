<?php

namespace App\Filament\Kesiswaan\Resources\DataGuruResource\Pages;

use App\Filament\Kesiswaan\Resources\DataGuruResource;
use App\Models\FormSubmission;
use App\Models\Kelas;
use App\Models\User;
use Carbon\Carbon;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewDataGuru extends ViewRecord
{
  protected static string $resource = DataGuruResource::class;

  public function infolist(Infolist $infolist): Infolist
  {
    $guru = $this->record;
    $kelasList = Kelas::where('wali_id', $guru->id)->get();
    $kelasIds = $kelasList->pluck('id');
    $siswaIds = User::whereIn('kelas_id', $kelasIds)
      ->whereHas('role_user', fn($q) => $q->where('name', 'Siswa'))
      ->pluck('id');
    $totalSiswa = $siswaIds->count();

    $totalVerified = FormSubmission::where('verified_by', $guru->id)->where('status', 'verified')->count();
    $totalRejected = FormSubmission::where('verified_by', $guru->id)->where('status', 'rejected')->count();
    $totalPending  = FormSubmission::whereIn('user_id', $siswaIds)->where('status', 'pending')->count();
    $totalSubmissions = FormSubmission::whereIn('user_id', $siswaIds)->count();

    $kelasNames = $kelasList->pluck('nama')->implode(', ') ?: '-';

    return $infolist
      ->schema([
        Infolists\Components\Section::make('Profil Guru')
          ->schema([
            Infolists\Components\TextEntry::make('name')->label('Nama'),
            Infolists\Components\TextEntry::make('email')->label('Email'),
            Infolists\Components\TextEntry::make('no_hp')->label('No. HP')->placeholder('-'),
            Infolists\Components\TextEntry::make('kelas_wali_names')
              ->label('Kelas Wali')
              ->state($kelasNames),
          ])
          ->columns(4),

        Infolists\Components\Section::make('Statistik Verifikasi')
          ->schema([
            Infolists\Components\TextEntry::make('stat_siswa')
              ->label('Total Siswa')
              ->state((string) $totalSiswa)
              ->badge()
              ->color('info'),
            Infolists\Components\TextEntry::make('stat_submissions')
              ->label('Total Formulir Masuk')
              ->state((string) $totalSubmissions)
              ->badge()
              ->color('info'),
            Infolists\Components\TextEntry::make('stat_verified')
              ->label('Diverifikasi')
              ->state((string) $totalVerified)
              ->badge()
              ->color('success'),
            Infolists\Components\TextEntry::make('stat_pending')
              ->label('Menunggu Verifikasi')
              ->state((string) $totalPending)
              ->badge()
              ->color($totalPending > 0 ? 'warning' : 'success'),
            Infolists\Components\TextEntry::make('stat_rejected')
              ->label('Ditolak')
              ->state((string) $totalRejected)
              ->badge()
              ->color($totalRejected > 0 ? 'danger' : 'gray'),
          ])
          ->columns(5),
      ]);
  }
}
