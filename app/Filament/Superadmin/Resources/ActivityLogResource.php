<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\Superadmin\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
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
            default => 'info',
          })
          ->formatStateUsing(fn(string $state): string => match ($state) {
            'login' => '🟢 Login',
            'logout' => '⚪ Logout',
            'login_failed' => '🔴 Gagal Login',
            default => $state,
          }),
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
          ]),
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
          Tables\Actions\DeleteAction::make(),
        ]),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ])
      ->poll('30s');
  }

  public static function infolist(Infolist $infolist): Infolist
  {
    return $infolist->schema([
      Infolists\Components\Section::make('Detail Aktivitas')->schema([
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
            default => 'info',
          })
          ->formatStateUsing(fn(string $state): string => match ($state) {
            'login' => 'Login',
            'logout' => 'Logout',
            'login_failed' => 'Gagal Login',
            default => $state,
          }),
        Infolists\Components\TextEntry::make('role')
          ->label('Role')
          ->badge()
          ->placeholder('-'),
        Infolists\Components\TextEntry::make('panel')
          ->label('Panel')
          ->placeholder('-'),
      ])->columns(4),

      Infolists\Components\Section::make('Informasi Pengguna')->schema([
        Infolists\Components\TextEntry::make('user.name')
          ->label('Nama')
          ->placeholder('Tidak diketahui'),
        Infolists\Components\TextEntry::make('user.email')
          ->label('Email')
          ->placeholder('-'),
        Infolists\Components\TextEntry::make('user.nisn')
          ->label('NISN')
          ->placeholder('-'),
      ])->columns(3),

      Infolists\Components\Section::make('Informasi Teknis')->schema([
        Infolists\Components\TextEntry::make('ip_address')
          ->label('IP Address')
          ->copyable()
          ->icon('heroicon-o-globe-alt'),
        Infolists\Components\TextEntry::make('location')
          ->label('Lokasi')
          ->icon('heroicon-o-map-pin')
          ->placeholder('Tidak diketahui'),
        Infolists\Components\TextEntry::make('user_agent')
          ->label('User Agent')
          ->columnSpanFull()
          ->wrap(),
      ])->columns(2),
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
