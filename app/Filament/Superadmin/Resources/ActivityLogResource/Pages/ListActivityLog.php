<?php

namespace App\Filament\Superadmin\Resources\ActivityLogResource\Pages;

use App\Filament\Superadmin\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListActivityLog extends ListRecords
{
  protected static string $resource = ActivityLogResource::class;

  protected function getHeaderActions(): array
  {
    return [
      // ── Backup Semua Log ──
      Actions\Action::make('backupLog')
        ->label('Backup Log')
        ->icon('heroicon-o-arrow-down-tray')
        ->color('info')
        ->requiresConfirmation()
        ->modalHeading('Backup Log Aktivitas')
        ->modalDescription('Semua data log aktivitas akan di-export ke file CSV.')
        ->modalSubmitActionLabel('Download Backup')
        ->action(fn() => $this->exportCsv()),

      // ── Backup & Hapus Semua Log ──
      Actions\Action::make('backupAndDeleteLog')
        ->label('Backup & Hapus')
        ->icon('heroicon-o-archive-box-arrow-down')
        ->color('warning')
        ->form([
          TextInput::make('password')
            ->label('Password Super Admin')
            ->password()
            ->revealable()
            ->required()
            ->helperText('Data akan di-backup ke CSV terlebih dahulu, lalu semua log dihapus.'),
        ])
        ->modalHeading('Backup & Hapus Semua Log')
        ->modalDescription('Data log akan di-download sebagai CSV, kemudian semua log di database akan dihapus permanen.')
        ->modalSubmitActionLabel('Backup & Hapus')
        ->action(function (array $data) {
          if (!$this->verifySuperadminPassword($data['password'])) {
            Notification::make()
              ->title('Password salah')
              ->body('Password Super Admin yang Anda masukkan tidak sesuai.')
              ->danger()
              ->send();
            return;
          }

          $response = $this->exportCsv();

          ActivityLog::truncate();

          Notification::make()
            ->title('Backup & Hapus Berhasil')
            ->body('Semua log telah di-backup dan dihapus dari database.')
            ->success()
            ->send();

          return $response;
        }),
    ];
  }

  /**
   * Verify the given password against the current authenticated superadmin.
   */
  private function verifySuperadminPassword(string $password): bool
  {
    $user = auth()->user();
    return $user && Hash::check($password, $user->password);
  }

  /**
   * Export all activity logs to a CSV streamed response.
   */
  private function exportCsv(): StreamedResponse
  {
    $filename = 'log-aktivitas-' . now()->format('Y-m-d_His') . '.csv';

    return response()->streamDownload(function () {
      $handle = fopen('php://output', 'w');

      // BOM for Excel UTF-8 compatibility
      fwrite($handle, "\xEF\xBB\xBF");

      // Header row
      fputcsv($handle, [
        'Waktu',
        'Nama Pengguna',
        'Email / NISN',
        'Aktivitas',
        'Role',
        'Panel',
        'IP Address',
        'Lokasi',
        'Browser',
        'Perangkat',
        'User Agent',
      ]);

      // Data rows — chunked to handle large datasets
      ActivityLog::with('user')
        ->orderBy('created_at', 'desc')
        ->chunk(500, function ($logs) use ($handle) {
          foreach ($logs as $log) {
            fputcsv($handle, [
              $log->created_at->format('Y-m-d H:i:s'),
              $log->user?->name ?? 'Tidak diketahui',
              $log->user?->email ?? $log->user?->nisn ?? '-',
              match ($log->activity) {
                'login' => 'Login',
                'logout' => 'Logout',
                'login_failed' => 'Gagal Login',
                default => $log->activity,
              },
              $log->role ?? '-',
              match ($log->panel) {
                'superadmin' => 'Superadmin',
                'guru' => 'Guru',
                'kesiswaan' => 'Kesiswaan',
                'siswa' => 'Siswa',
                default => $log->panel ?? '-',
              },
              $log->ip_address ?? '-',
              $log->location ?? '-',
              $log->browser ?? '-',
              $log->device ?? '-',
              $log->user_agent ?? '-',
            ]);
          }
        });

      fclose($handle);
    }, $filename, [
      'Content-Type' => 'text/csv; charset=UTF-8',
    ]);
  }
}
