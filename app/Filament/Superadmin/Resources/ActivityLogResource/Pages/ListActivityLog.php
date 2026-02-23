<?php

namespace App\Filament\Superadmin\Resources\ActivityLogResource\Pages;

use App\Filament\Superadmin\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;

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
        ->modalDescription('Semua data log aktivitas akan di-export ke file Excel (.xlsx).')
        ->modalSubmitActionLabel('Download Backup')
        ->action(fn() => $this->exportExcel()),

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
            ->helperText('Data akan di-backup ke Excel terlebih dahulu, lalu semua log dihapus.'),
        ])
        ->modalHeading('Backup & Hapus Semua Log')
        ->modalDescription('Data log akan di-download sebagai Excel (.xlsx), kemudian semua log di database akan dihapus permanen.')
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

          $response = $this->exportExcel();

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
    /** @var User|null $user */
    $user = Auth::user();
    return $user && Hash::check($password, $user->password);
  }

  /**
   * Export all activity logs to an Excel (.xlsx) file.
   */
  private function exportExcel()
  {
    $filename = 'log-aktivitas-' . now()->format('Y-m-d_His') . '.xlsx';
    $tempPath = storage_path('app/' . $filename);

    $options = new Options();
    $writer = new Writer($options);
    $writer->openToFile($tempPath);

    // ── Header style ──
    $headerStyle = (new Style())
      ->setFontBold()
      ->setFontSize(11)
      ->setFontColor(Color::WHITE)
      ->setBackgroundColor(Color::rgb(30, 64, 175))
      ->setShouldWrapText(false);

    // ── Header row ──
    $headers = [
      'No',
      'Tanggal',
      'Jam',
      'Nama Pengguna',
      'Email',
      'NISN',
      'Aktivitas',
      'Status',
      'Role',
      'Panel',
      'IP Address',
      'Lokasi',
      'Browser',
      'Perangkat',
      'OS',
      'User Agent',
    ];

    $headerCells = array_map(fn(string $h) => Cell\StringCell::fromValue($h), $headers);
    $writer->addRow(new Row($headerCells, $headerStyle));

    // ── Data style ──
    $dataStyle = (new Style())
      ->setFontSize(10)
      ->setShouldWrapText(false);

    // ── Data rows — chunked ──
    $rowNum = 0;
    ActivityLog::with('user')
      ->orderBy('created_at', 'desc')
      ->chunk(500, function ($logs) use ($writer, $dataStyle, &$rowNum) {
        foreach ($logs as $log) {
          $rowNum++;

          $activityLabel = match ($log->activity) {
            'login' => 'Login',
            'logout' => 'Logout',
            'login_failed' => 'Gagal Login',
            default => $log->activity,
          };

          $statusLabel = match ($log->activity) {
            'login' => '✅ Berhasil',
            'logout' => '🔒 Keluar',
            'login_failed' => '❌ Gagal',
            default => '-',
          };

          $panelLabel = match ($log->panel) {
            'superadmin' => 'Super Admin',
            'guru' => 'Guru',
            'kesiswaan' => 'Kesiswaan',
            'siswa' => 'Siswa',
            default => $log->panel ?? '-',
          };

          // Parse OS from user_agent
          $os = '-';
          $ua = $log->user_agent ?? '';
          if (str_contains($ua, 'Windows')) $os = 'Windows';
          elseif (str_contains($ua, 'Macintosh') || str_contains($ua, 'Mac OS')) $os = 'macOS';
          elseif (str_contains($ua, 'Android')) $os = 'Android';
          elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) $os = 'iOS';
          elseif (str_contains($ua, 'Linux')) $os = 'Linux';
          elseif (str_contains($ua, 'CrOS')) $os = 'Chrome OS';

          $cells = [
            Cell\NumericCell::fromValue($rowNum),
            Cell\StringCell::fromValue($log->created_at->format('d/m/Y')),
            Cell\StringCell::fromValue($log->created_at->format('H:i:s')),
            Cell\StringCell::fromValue($log->user?->name ?? 'Tidak diketahui'),
            Cell\StringCell::fromValue($log->user?->email ?? '-'),
            Cell\StringCell::fromValue($log->user?->nisn ?? '-'),
            Cell\StringCell::fromValue($activityLabel),
            Cell\StringCell::fromValue($statusLabel),
            Cell\StringCell::fromValue($log->role ?? '-'),
            Cell\StringCell::fromValue($panelLabel),
            Cell\StringCell::fromValue($log->ip_address ?? '-'),
            Cell\StringCell::fromValue($log->location ?? '-'),
            Cell\StringCell::fromValue($log->browser ?? '-'),
            Cell\StringCell::fromValue($log->device ?? '-'),
            Cell\StringCell::fromValue($os),
            Cell\StringCell::fromValue($ua ?: '-'),
          ];

          $writer->addRow(new Row($cells, $dataStyle));
        }
      });

    $writer->close();

    return response()->download($tempPath, $filename, [
      'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ])->deleteFileAfterSend(true);
  }
}
