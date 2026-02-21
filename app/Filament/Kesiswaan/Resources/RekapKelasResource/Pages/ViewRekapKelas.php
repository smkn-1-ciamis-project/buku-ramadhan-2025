<?php

namespace App\Filament\Kesiswaan\Resources\RekapKelasResource\Pages;

use App\Filament\Kesiswaan\Resources\RekapKelasResource;
use App\Models\FormSubmission;
use App\Models\Kelas;
use App\Models\User;
use Carbon\Carbon;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewRekapKelas extends ViewRecord
{
  protected static string $resource = RekapKelasResource::class;

  public function infolist(Infolist $infolist): Infolist
  {
    $kelas = $this->record;
    $kelas->load(['wali', 'siswa']);

    $siswaIds = $kelas->siswa->pluck('id');
    $totalSiswa = $siswaIds->count();

    $ramadhanStart = Carbon::create(2026, 2, 19);
    $today = Carbon::today();
    $hariKe = $today->gte($ramadhanStart) ? min((int) $ramadhanStart->diffInDays($today) + 1, 30) : 0;

    $totalSubmissions = FormSubmission::whereIn('user_id', $siswaIds)->count();
    $verified = FormSubmission::whereIn('user_id', $siswaIds)->where('status', 'verified')->count();
    $pending  = FormSubmission::whereIn('user_id', $siswaIds)->where('status', 'pending')->count();
    $rejected = FormSubmission::whereIn('user_id', $siswaIds)->where('status', 'rejected')->count();

    $todaySubmit = $hariKe > 0
      ? FormSubmission::whereIn('user_id', $siswaIds)->where('hari_ke', $hariKe)->count()
      : 0;

    // Per-siswa progress
    $siswaProgress = $kelas->siswa->map(function ($siswa) {
      $total = FormSubmission::where('user_id', $siswa->id)->count();
      $verifiedCount = FormSubmission::where('user_id', $siswa->id)->where('status', 'verified')->count();
      $pendingCount  = FormSubmission::where('user_id', $siswa->id)->where('status', 'pending')->count();
      $rejectedCount = FormSubmission::where('user_id', $siswa->id)->where('status', 'rejected')->count();
      return [
        'name' => $siswa->name,
        'nisn' => $siswa->nisn ?? '-',
        'total' => $total,
        'verified' => $verifiedCount,
        'pending' => $pendingCount,
        'rejected' => $rejectedCount,
      ];
    })->sortBy('name')->values();

    return $infolist
      ->schema([
        Infolists\Components\Section::make('Info Kelas')
          ->schema([
            Infolists\Components\TextEntry::make('nama')
              ->label('Kelas'),
            Infolists\Components\TextEntry::make('wali.name')
              ->label('Wali Kelas')
              ->placeholder('-'),
            Infolists\Components\TextEntry::make('stat_siswa')
              ->label('Jumlah Siswa')
              ->state((string) $totalSiswa)
              ->badge()
              ->color('info'),
            Infolists\Components\TextEntry::make('hari_ke_state')
              ->label('Hari Ke')
              ->state($hariKe > 0 ? "Hari ke-{$hariKe}" : 'Belum dimulai')
              ->badge()
              ->color($hariKe > 0 ? 'success' : 'gray'),
          ])
          ->columns(4),

        Infolists\Components\Section::make('Statistik Formulir')
          ->schema([
            Infolists\Components\TextEntry::make('stat_total')
              ->label('Total Formulir')
              ->state((string) $totalSubmissions)
              ->badge()
              ->color('info'),
            Infolists\Components\TextEntry::make('stat_today')
              ->label('Submit Hari Ini')
              ->state("{$todaySubmit}/{$totalSiswa}")
              ->badge()
              ->color($todaySubmit >= $totalSiswa ? 'success' : 'warning'),
            Infolists\Components\TextEntry::make('stat_verified')
              ->label('Verified')
              ->state((string) $verified)
              ->badge()
              ->color('success'),
            Infolists\Components\TextEntry::make('stat_pending')
              ->label('Pending')
              ->state((string) $pending)
              ->badge()
              ->color($pending > 0 ? 'warning' : 'success'),
            Infolists\Components\TextEntry::make('stat_rejected')
              ->label('Rejected')
              ->state((string) $rejected)
              ->badge()
              ->color($rejected > 0 ? 'danger' : 'gray'),
          ])
          ->columns(5),

        Infolists\Components\Section::make('Progress Per Siswa')
          ->schema([
            Infolists\Components\ViewEntry::make('siswa_progress_table')
              ->view('filament.kesiswaan.components.siswa-progress-table')
              ->state($siswaProgress->toArray()),
          ]),
      ]);
  }
}
