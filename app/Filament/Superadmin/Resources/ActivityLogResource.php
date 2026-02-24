<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\Superadmin\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Models\RoleUser;
use Filament\Tables;
use Filament\Tables\Table;

class ActivityLogResource extends Resource
{
  protected static ?string $model = ActivityLog::class;

  protected static ?string $navigationIcon = 'heroicon-o-clock';
  protected static ?string $navigationLabel = 'Log Aktivitas';
  protected static ?string $navigationGroup = 'Pengaturan';
  protected static ?string $modelLabel = 'Log Aktivitas';
  protected static ?string $pluralModelLabel = 'Log Aktivitas';
  protected static ?string $slug = 'log-aktivitas';
  protected static ?int $navigationSort = 8;

  public static function shouldRegisterNavigation(): bool
  {
    return RoleUser::checkNav('sa_log_aktivitas');
  }

  public static function canCreate(): bool
  {
    return false;
  }

  public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
  {
    return false;
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('created_at')
          ->label('Waktu')
          ->since()
          ->tooltip(fn($record) => $record->created_at->translatedFormat('d M Y, H:i:s'))
          ->sortable(),
        Tables\Columns\TextColumn::make('user.name')
          ->label('Pengguna')
          ->searchable()
          ->placeholder('Tidak diketahui')
          ->description(fn($record) => $record->user?->email ?? $record->user?->nisn ?? ''),
        Tables\Columns\TextColumn::make('activity')
          ->label('Aktivitas')
          ->badge()
          ->color(fn(string $state): string => match ($state) {
            'login' => 'success',
            'logout' => 'gray',
            'login_failed' => 'danger',
            'verify_submission', 'validate_submission', 'bulk_verify_submission', 'bulk_validate_submission' => 'success',
            'reject_submission', 'reject_validation', 'bulk_reject_submission', 'bulk_reject_validation' => 'danger',
            'reset_submission', 'reset_validation' => 'warning',
            'create_siswa', 'create_guru', 'create_kesiswaan', 'create_kelas', 'create_role', 'create_form_setting' => 'success',
            'edit_siswa', 'edit_guru', 'edit_kesiswaan', 'edit_kelas', 'edit_role', 'edit_form_setting' => 'warning',
            'delete_siswa', 'delete_guru', 'delete_kesiswaan', 'delete_kelas', 'delete_role', 'delete_form_setting',
            'bulk_delete_siswa', 'bulk_delete_guru', 'bulk_delete_kesiswaan', 'delete_submission', 'bulk_delete_submission' => 'danger',
            'reset_password', 'reset_session' => 'warning',
            'import_siswa', 'import_guru', 'import_kesiswaan', 'import_kelas' => 'info',
            'export_siswa', 'export_guru', 'export_kesiswaan', 'export_rekap' => 'info',
            'backup_data', 'backup_and_delete_data' => 'danger',
            'update_profile', 'change_password' => 'gray',
            'submit_form' => 'info',
            'add_siswa_to_kelas', 'remove_siswa_from_kelas', 'bulk_remove_siswa_from_kelas' => 'warning',
            default => 'gray',
          })
          ->formatStateUsing(fn(string $state): string => match ($state) {
            'login' => '🟢 Login',
            'logout' => '⚪ Logout',
            'login_failed' => '🔴 Gagal Login',
            'verify_submission' => '✅ Verifikasi',
            'reject_submission' => '❌ Tolak Verifikasi',
            'reset_submission' => '🔄 Reset Verifikasi',
            'bulk_verify_submission' => '✅ Verifikasi Massal',
            'validate_submission' => '✅ Validasi',
            'reject_validation' => '❌ Tolak Validasi',
            'reset_validation' => '🔄 Reset Validasi',
            'bulk_validate_submission' => '✅ Validasi Massal',
            'bulk_reject_submission' => '❌ Tolak Massal',
            'bulk_reject_validation' => '❌ Tolak Massal',
            'create_siswa' => '👤 Tambah Siswa',
            'edit_siswa' => '✏️ Edit Siswa',
            'delete_siswa' => '🗑️ Hapus Siswa',
            'bulk_delete_siswa' => '🗑️ Hapus Massal Siswa',
            'create_guru' => '👤 Tambah Guru',
            'edit_guru' => '✏️ Edit Guru',
            'delete_guru' => '🗑️ Hapus Guru',
            'bulk_delete_guru' => '🗑️ Hapus Massal Guru',
            'create_kesiswaan' => '👤 Tambah Kesiswaan',
            'edit_kesiswaan' => '✏️ Edit Kesiswaan',
            'delete_kesiswaan' => '🗑️ Hapus Kesiswaan',
            'bulk_delete_kesiswaan' => '🗑️ Hapus Massal Kesiswaan',
            'create_kelas' => '📚 Tambah Kelas',
            'edit_kelas' => '✏️ Edit Kelas',
            'delete_kelas' => '🗑️ Hapus Kelas',
            'import_siswa' => '📥 Import Siswa',
            'import_guru' => '📥 Import Guru',
            'import_kesiswaan' => '📥 Import Kesiswaan',
            'import_kelas' => '📥 Import Kelas',
            'export_siswa' => '📤 Export Siswa',
            'export_guru' => '📤 Export Guru',
            'export_kesiswaan' => '📤 Export Kesiswaan',
            'export_rekap' => '📤 Export Rekap',
            'reset_password' => '🔑 Reset Password',
            'reset_session' => '🔒 Reset Sesi',
            'update_profile' => '👤 Update Profil',
            'change_password' => '🔑 Ubah Password',
            'submit_form' => '📝 Submit Formulir',
            'backup_data' => '💾 Backup Data',
            'backup_and_delete_data' => '⚠️ Backup & Hapus Data',
            'delete_submission' => '🗑️ Hapus Formulir',
            'bulk_delete_submission' => '🗑️ Hapus Massal Formulir',
            'create_role' => '👤 Tambah Role',
            'edit_role' => '✏️ Edit Role',
            'delete_role' => '🗑️ Hapus Role',
            'create_form_setting' => '⚙️ Tambah Pengaturan',
            'edit_form_setting' => '⚙️ Edit Pengaturan',
            'delete_form_setting' => '🗑️ Hapus Pengaturan',
            'add_siswa_to_kelas' => '➕ Tambah ke Kelas',
            'remove_siswa_from_kelas' => '➖ Keluarkan dari Kelas',
            'bulk_remove_siswa_from_kelas' => '➖ Keluarkan Massal',
            default => ucfirst(str_replace('_', ' ', $state)),
          }),
        Tables\Columns\TextColumn::make('description')
          ->label('Keterangan')
          ->limit(50)
          ->tooltip(fn($record) => $record->description)
          ->placeholder('-')
          ->toggleable()
          ->wrap(),
        Tables\Columns\TextColumn::make('role')
          ->label('Role')
          ->badge()
          ->color(fn(?string $state): string => match (strtolower($state ?? '')) {
            'super admin', 'superadmin' => 'danger',
            'guru' => 'warning',
            'kesiswaan' => 'info',
            'siswa' => 'success',
            default => 'gray',
          })
          ->placeholder('-'),
        Tables\Columns\TextColumn::make('panel')
          ->label('Panel')
          ->badge()
          ->color('gray')
          ->formatStateUsing(fn(?string $state): string => match ($state) {
            'superadmin' => 'Superadmin',
            'guru' => 'Guru',
            'kesiswaan' => 'Kesiswaan',
            'siswa' => 'Siswa',
            default => $state ?? '-',
          })
          ->placeholder('-'),
        Tables\Columns\TextColumn::make('ip_address')
          ->label('IP Address')
          ->copyable()
          ->icon('heroicon-o-globe-alt')
          ->placeholder('-'),
        Tables\Columns\TextColumn::make('location')
          ->label('Lokasi')
          ->icon('heroicon-o-map-pin')
          ->placeholder('Tidak diketahui')
          ->url(fn($record) => ($record->metadata['lat'] ?? null) && ($record->metadata['lon'] ?? null)
            ? 'https://maps.google.com/?q=' . $record->metadata['lat'] . ',' . $record->metadata['lon']
            : null)
          ->openUrlInNewTab()
          ->wrap(),
        Tables\Columns\TextColumn::make('browser')
          ->label('Browser')
          ->getStateUsing(fn($record) => $record->browser)
          ->icon('heroicon-o-computer-desktop')
          ->placeholder('-'),
        Tables\Columns\TextColumn::make('device')
          ->label('Perangkat')
          ->getStateUsing(fn($record) => $record->device)
          ->placeholder('-'),
      ])
      ->defaultSort('created_at', 'desc')
      ->filters([
        Tables\Filters\SelectFilter::make('activity')
          ->label('Aktivitas')
          ->options([
            'login' => 'Login',
            'logout' => 'Logout',
            'login_failed' => 'Gagal Login',
            'verify_submission' => 'Verifikasi Formulir',
            'reject_submission' => 'Tolak Verifikasi',
            'reset_submission' => 'Reset Verifikasi',
            'bulk_verify_submission' => 'Verifikasi Massal',
            'validate_submission' => 'Validasi Formulir',
            'reject_validation' => 'Tolak Validasi',
            'reset_validation' => 'Reset Validasi',
            'bulk_validate_submission' => 'Validasi Massal',
            'bulk_reject_submission' => 'Tolak Massal Verifikasi',
            'bulk_reject_validation' => 'Tolak Massal Validasi',
            'create_siswa' => 'Tambah Siswa',
            'edit_siswa' => 'Edit Siswa',
            'delete_siswa' => 'Hapus Siswa',
            'create_guru' => 'Tambah Guru',
            'edit_guru' => 'Edit Guru',
            'delete_guru' => 'Hapus Guru',
            'create_kesiswaan' => 'Tambah Kesiswaan',
            'edit_kesiswaan' => 'Edit Kesiswaan',
            'delete_kesiswaan' => 'Hapus Kesiswaan',
            'create_kelas' => 'Tambah Kelas',
            'edit_kelas' => 'Edit Kelas',
            'delete_kelas' => 'Hapus Kelas',
            'import_siswa' => 'Import Siswa',
            'import_guru' => 'Import Guru',
            'import_kesiswaan' => 'Import Kesiswaan',
            'import_kelas' => 'Import Kelas',
            'export_siswa' => 'Export Siswa',
            'export_guru' => 'Export Guru',
            'export_kesiswaan' => 'Export Kesiswaan',
            'export_rekap' => 'Export Rekap',
            'reset_password' => 'Reset Password',
            'reset_session' => 'Reset Sesi',
            'update_profile' => 'Update Profil',
            'change_password' => 'Ubah Password',
            'submit_form' => 'Submit Formulir',
            'backup_data' => 'Backup Data',
            'backup_and_delete_data' => 'Backup & Hapus Data',
          ])
          ->searchable(),
        Tables\Filters\SelectFilter::make('role')
          ->label('Role')
          ->options([
            'Super Admin' => 'Super Admin',
            'Guru' => 'Guru',
            'Kesiswaan' => 'Kesiswaan',
            'Siswa' => 'Siswa',
          ]),
        Tables\Filters\SelectFilter::make('panel')
          ->label('Panel')
          ->options([
            'superadmin' => 'Superadmin',
            'guru' => 'Guru',
            'kesiswaan' => 'Kesiswaan',
            'siswa' => 'Siswa',
          ]),
        Tables\Filters\Filter::make('created_at')
          ->form([
            \Filament\Forms\Components\DatePicker::make('from')->label('Dari Tanggal'),
            \Filament\Forms\Components\DatePicker::make('until')->label('Sampai Tanggal'),
          ])
          ->query(function ($query, array $data) {
            return $query
              ->when($data['from'], fn($q, $date) => $q->whereDate('created_at', '>=', $date))
              ->when($data['until'], fn($q, $date) => $q->whereDate('created_at', '<=', $date));
          }),
      ])
      ->actions([
        Tables\Actions\ActionGroup::make([
          Tables\Actions\ViewAction::make(),
        ]),
      ])
      ->bulkActions([])
      ->poll('30s');
  }

  public static function infolist(Infolist $infolist): Infolist
  {
    return $infolist->schema([
      Infolists\Components\Section::make()->schema([
        Infolists\Components\TextEntry::make('created_at')
          ->label('Waktu')
          ->dateTime('d M Y, H:i:s'),
        Infolists\Components\TextEntry::make('activity')
          ->label('Aktivitas')
          ->badge()
          ->color(fn(string $state): string => match ($state) {
            'login' => 'success',
            'logout' => 'gray',
            'login_failed' => 'danger',
            'verify_submission', 'validate_submission', 'bulk_verify_submission', 'bulk_validate_submission' => 'success',
            'reject_submission', 'reject_validation', 'bulk_reject_submission', 'bulk_reject_validation' => 'danger',
            'reset_submission', 'reset_validation' => 'warning',
            'create_siswa', 'create_guru', 'create_kesiswaan', 'create_kelas' => 'success',
            'delete_siswa', 'delete_guru', 'delete_kesiswaan', 'delete_kelas', 'backup_and_delete_data' => 'danger',
            default => 'info',
          })
          ->formatStateUsing(fn(string $state): string => match ($state) {
            'login' => 'Login',
            'logout' => 'Logout',
            'login_failed' => 'Gagal Login',
            default => ucfirst(str_replace('_', ' ', $state)),
          }),
        Infolists\Components\TextEntry::make('description')
          ->label('Keterangan')
          ->placeholder('-')
          ->columnSpan(2),
        Infolists\Components\TextEntry::make('role')
          ->label('Role')
          ->badge()
          ->placeholder('-'),
        Infolists\Components\TextEntry::make('panel')
          ->label('Panel')
          ->placeholder('-'),
        Infolists\Components\TextEntry::make('user.name')
          ->label('Nama')
          ->placeholder('Tidak diketahui'),
        Infolists\Components\TextEntry::make('user.email')
          ->label('Email')
          ->placeholder('-'),
        Infolists\Components\TextEntry::make('user.nisn')
          ->label('NISN')
          ->placeholder('-'),
        Infolists\Components\TextEntry::make('ip_address')
          ->label('IP Address')
          ->copyable()
          ->icon('heroicon-o-globe-alt'),
        Infolists\Components\TextEntry::make('location')
          ->label('Lokasi')
          ->icon('heroicon-o-map-pin')
          ->placeholder('Tidak diketahui')
          ->url(fn($record) => ($record->metadata['lat'] ?? null) && ($record->metadata['lon'] ?? null)
            ? 'https://maps.google.com/?q=' . $record->metadata['lat'] . ',' . $record->metadata['lon']
            : null)
          ->openUrlInNewTab(),
        Infolists\Components\TextEntry::make('user_agent')
          ->label('User Agent')
          ->columnSpan(2),
      ])->columns(4),
    ]);
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListActivityLog::route('/'),
      'view' => Pages\ViewActivityLog::route('/{record}'),
    ];
  }
}
