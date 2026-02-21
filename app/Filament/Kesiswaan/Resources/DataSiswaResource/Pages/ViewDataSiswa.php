<?php

namespace App\Filament\Kesiswaan\Resources\DataSiswaResource\Pages;

use App\Filament\Kesiswaan\Resources\DataSiswaResource;
use App\Models\FormSubmission;
use Carbon\Carbon;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewDataSiswa extends ViewRecord
{
  protected static string $resource = DataSiswaResource::class;

  public function infolist(Infolist $infolist): Infolist
  {
    $user = $this->record;

    // Calculate Ramadhan day
    $ramadhanStart = Carbon::create(2026, 2, 19, 0, 0, 0, 'Asia/Jakarta');
    $ramadhanEnd   = Carbon::create(2026, 3, 20, 23, 59, 59, 'Asia/Jakarta');
    $now = now('Asia/Jakarta');
    $isRamadhan = $now->gte($ramadhanStart) && $now->lte($ramadhanEnd);
    $hariKe = $isRamadhan ? min((int) $ramadhanStart->diffInDays($now) + 1, 30) : 0;

    // Submission stats
    $submissions = FormSubmission::where('user_id', $user->id)->get();
    $totalSubmit = $submissions->count();
    $verified    = $submissions->where('status', 'verified')->count();
    $pending     = $submissions->where('status', 'pending')->count();
    $rejected    = $submissions->where('status', 'rejected')->count();
    $submittedDays = $submissions->pluck('hari_ke')->toArray();
    $missingDays = [];
    for ($d = 1; $d <= $hariKe; $d++) {
      if (!in_array($d, $submittedDays)) {
        $missingDays[] = $d;
      }
    }
    $progress = $hariKe > 0 ? round(($totalSubmit / $hariKe) * 100) : 0;

    return $infolist
      ->schema([
        Infolists\Components\Section::make('Profil Siswa')
          ->schema([
            Infolists\Components\TextEntry::make('name')->label('Nama'),
            Infolists\Components\TextEntry::make('nisn')->label('NISN'),
            Infolists\Components\TextEntry::make('email')->label('Email'),
            Infolists\Components\TextEntry::make('kelas.nama')->label('Kelas'),
            Infolists\Components\TextEntry::make('agama')->label('Agama'),
            Infolists\Components\TextEntry::make('jenis_kelamin')
              ->label('Jenis Kelamin')
              ->formatStateUsing(fn(?string $s) => $s === 'P' ? 'Perempuan' : 'Laki-laki'),
          ])
          ->columns(3),

        Infolists\Components\Section::make('Statistik Formulir')
          ->schema([
            Infolists\Components\TextEntry::make('progress')
              ->label('Progress Pengisian')
              ->state("{$totalSubmit}/{$hariKe} hari ({$progress}%)")
              ->badge()
              ->color($progress >= 80 ? 'success' : ($progress >= 50 ? 'warning' : 'danger')),
            Infolists\Components\TextEntry::make('stat_verified')
              ->label('Terverifikasi')
              ->state((string) $verified)
              ->badge()
              ->color('success'),
            Infolists\Components\TextEntry::make('stat_pending')
              ->label('Menunggu')
              ->state((string) $pending)
              ->badge()
              ->color('warning'),
            Infolists\Components\TextEntry::make('stat_rejected')
              ->label('Ditolak')
              ->state((string) $rejected)
              ->badge()
              ->color('danger'),
            Infolists\Components\TextEntry::make('missing')
              ->label('Hari Belum Mengisi')
              ->state(count($missingDays) > 0 ? implode(', ', array_map(fn($d) => "Hari {$d}", $missingDays)) : 'Semua hari sudah diisi ✅')
              ->columnSpanFull()
              ->color(count($missingDays) > 0 ? 'danger' : 'success'),
          ])
          ->columns(4),
      ]);
  }
}
