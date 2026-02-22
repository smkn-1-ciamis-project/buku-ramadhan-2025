<?php

namespace App\Filament\Kesiswaan\Resources;

use App\Filament\Kesiswaan\Resources\ValidasiResource\Pages;
use App\Models\FormSubmission;
use App\Models\Kelas;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ValidasiResource extends Resource
{
  protected static ?string $model = FormSubmission::class;

  protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
  protected static ?string $navigationLabel = 'Validasi Formulir';
  protected static ?string $navigationGroup = 'Validasi';
  protected static ?string $modelLabel = 'Formulir';
  protected static ?string $pluralModelLabel = 'Formulir';
  protected static ?string $slug = 'validasi';
  protected static ?int $navigationSort = 2;

  public static function shouldRegisterNavigation(): bool
  {
    return \App\Models\RoleUser::checkNav('kesiswaan_validasi');
  }

  public static function getNavigationBadge(): ?string
  {
    // Badge = jumlah formulir yang sudah diverifikasi guru tapi belum divalidasi kesiswaan
    $count = FormSubmission::where('status', 'verified')
      ->where('kesiswaan_status', 'pending')
      ->count();
    return $count > 0 ? (string) $count : null;
  }

  public static function getNavigationBadgeColor(): ?string
  {
    return 'warning';
  }

  /**
   * Kesiswaan hanya melihat formulir yang SUDAH diverifikasi oleh guru.
   */
  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->where('status', 'verified')
      ->whereHas('user', function (Builder $q) {
        $q->whereHas('role_user', fn(Builder $rq) => $rq->where('name', 'Siswa'));
      })
      ->with(['user.kelas', 'user.role_user', 'verifier', 'validator']);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('user.name')
          ->label('Nama Siswa')
          ->searchable()
          ->sortable()
          ->description(fn($record) => $record->user?->kelas?->nama ?? '-'),
        Tables\Columns\TextColumn::make('user.nisn')
          ->label('NISN')
          ->searchable()
          ->toggleable(),
        Tables\Columns\TextColumn::make('user.kelas.nama')
          ->label('Kelas')
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('hari_ke')
          ->label('Hari Ke')
          ->sortable()
          ->badge()
          ->color('info')
          ->alignCenter(),
        Tables\Columns\TextColumn::make('verifier.name')
          ->label('Diverifikasi Oleh')
          ->placeholder('-')
          ->toggleable(),
        Tables\Columns\TextColumn::make('verified_at')
          ->label('Tgl Verifikasi Guru')
          ->since()
          ->tooltip(fn($record) => $record->verified_at?->translatedFormat('d M Y, H:i'))
          ->color('gray')
          ->sortable()
          ->placeholder('-'),
        Tables\Columns\TextColumn::make('kesiswaan_status')
          ->label('Status Validasi')
          ->badge()
          ->color(fn(string $state): string => match ($state) {
            'pending' => 'warning',
            'validated' => 'success',
            'rejected' => 'danger',
            default => 'gray',
          })
          ->formatStateUsing(fn(string $state): string => match ($state) {
            'pending' => 'Menunggu',
            'validated' => 'Divalidasi',
            'rejected' => 'Ditolak',
            default => $state,
          })
          ->sortable(),
        Tables\Columns\TextColumn::make('validator.name')
          ->label('Divalidasi Oleh')
          ->placeholder('-')
          ->toggleable(),
        Tables\Columns\TextColumn::make('validated_at')
          ->label('Tgl Validasi')
          ->since()
          ->tooltip(fn($record) => $record->validated_at?->translatedFormat('d M Y, H:i'))
          ->color('gray')
          ->sortable()
          ->placeholder('-')
          ->toggleable(),
      ])
      ->defaultSort('verified_at', 'desc')
      ->filters([
        Tables\Filters\SelectFilter::make('kesiswaan_status')
          ->label('Status Validasi')
          ->options([
            'pending' => 'Menunggu Validasi',
            'validated' => 'Sudah Divalidasi',
            'rejected' => 'Ditolak',
          ]),
        Tables\Filters\SelectFilter::make('kelas')
          ->label('Kelas')
          ->relationship('user.kelas', 'nama')
          ->searchable()
          ->preload(),
        Tables\Filters\SelectFilter::make('hari_ke')
          ->label('Hari Ke')
          ->options(
            collect(range(1, 30))->mapWithKeys(fn($d) => [$d => "Hari ke-{$d}"])->toArray()
          ),
      ])
      ->actions([
        Tables\Actions\ActionGroup::make([
          Tables\Actions\ViewAction::make()
            ->label('Lihat Detail')
            ->icon('heroicon-o-eye')
            ->color('info'),
          Tables\Actions\Action::make('validate')
            ->label('Validasi')
            ->icon('heroicon-o-shield-check')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Validasi Formulir')
            ->modalDescription(fn(FormSubmission $record) => "Validasi formulir hari ke-{$record->hari_ke} dari {$record->user->name}?")
            ->modalSubmitActionLabel('Ya, Validasi')
            ->form([
              Forms\Components\Textarea::make('catatan_kesiswaan')
                ->label('Catatan Kesiswaan (opsional)')
                ->rows(2)
                ->placeholder('Tambahkan catatan jika perlu...'),
            ])
            ->action(function (FormSubmission $record, array $data) {
              $record->update([
                'kesiswaan_status' => 'validated',
                'validated_by' => Auth::id(),
                'validated_at' => now(),
                'catatan_kesiswaan' => $data['catatan_kesiswaan'] ?? null,
              ]);
              \Filament\Notifications\Notification::make()
                ->title('Formulir berhasil divalidasi')
                ->success()
                ->send();
            })
            ->visible(fn(FormSubmission $record) => $record->kesiswaan_status !== 'validated'),
          Tables\Actions\Action::make('reject')
            ->label('Tolak')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Tolak Formulir')
            ->modalDescription(fn(FormSubmission $record) => "Tolak formulir hari ke-{$record->hari_ke} dari {$record->user->name}?")
            ->modalSubmitActionLabel('Ya, Tolak')
            ->form([
              Forms\Components\Textarea::make('catatan_kesiswaan')
                ->label('Alasan Penolakan')
                ->rows(2)
                ->required()
                ->placeholder('Jelaskan alasan penolakan...'),
            ])
            ->action(function (FormSubmission $record, array $data) {
              $record->update([
                'kesiswaan_status' => 'rejected',
                'validated_by' => Auth::id(),
                'validated_at' => now(),
                'catatan_kesiswaan' => $data['catatan_kesiswaan'],
              ]);
              \Filament\Notifications\Notification::make()
                ->title('Formulir ditolak')
                ->warning()
                ->send();
            })
            ->visible(fn(FormSubmission $record) => $record->kesiswaan_status !== 'rejected'),
          Tables\Actions\Action::make('resetStatus')
            ->label('Reset ke Menunggu')
            ->icon('heroicon-o-arrow-path')
            ->color('gray')
            ->requiresConfirmation()
            ->modalHeading('Reset Status Validasi')
            ->modalDescription('Status validasi kesiswaan akan dikembalikan ke Menunggu.')
            ->modalSubmitActionLabel('Ya, Reset')
            ->action(function (FormSubmission $record) {
              $record->update([
                'kesiswaan_status' => 'pending',
                'validated_by' => null,
                'validated_at' => null,
                'catatan_kesiswaan' => null,
              ]);
              \Filament\Notifications\Notification::make()
                ->title('Status validasi direset')
                ->info()
                ->send();
            })
            ->visible(fn(FormSubmission $record) => $record->kesiswaan_status !== 'pending'),
        ])
          ->icon('heroicon-m-ellipsis-vertical')
          ->tooltip('Aksi'),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\BulkAction::make('bulkValidate')
            ->label('Validasi Semua')
            ->icon('heroicon-o-shield-check')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Validasi Formulir Terpilih')
            ->modalDescription('Semua formulir yang dipilih akan divalidasi oleh kesiswaan.')
            ->modalSubmitActionLabel('Ya, Validasi Semua')
            ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
              $count = 0;
              foreach ($records as $record) {
                if ($record->kesiswaan_status !== 'validated') {
                  $record->update([
                    'kesiswaan_status' => 'validated',
                    'validated_by' => Auth::id(),
                    'validated_at' => now(),
                  ]);
                  $count++;
                }
              }
              \Filament\Notifications\Notification::make()
                ->title("{$count} formulir berhasil divalidasi")
                ->success()
                ->send();
            })
            ->deselectRecordsAfterCompletion(),
          Tables\Actions\BulkAction::make('bulkReject')
            ->label('Tolak Semua')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Tolak Formulir Terpilih')
            ->modalDescription('Semua formulir yang dipilih akan ditolak.')
            ->modalSubmitActionLabel('Ya, Tolak Semua')
            ->form([
              Forms\Components\Textarea::make('catatan_kesiswaan')
                ->label('Alasan Penolakan')
                ->rows(2)
                ->required(),
            ])
            ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
              $count = 0;
              foreach ($records as $record) {
                if ($record->kesiswaan_status !== 'rejected') {
                  $record->update([
                    'kesiswaan_status' => 'rejected',
                    'validated_by' => Auth::id(),
                    'validated_at' => now(),
                    'catatan_kesiswaan' => $data['catatan_kesiswaan'],
                  ]);
                  $count++;
                }
              }
              \Filament\Notifications\Notification::make()
                ->title("{$count} formulir ditolak")
                ->warning()
                ->send();
            })
            ->deselectRecordsAfterCompletion(),
        ]),
      ]);
  }

  public static function getRelations(): array
  {
    return [];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListValidasi::route('/'),
      'view'  => Pages\ViewValidasi::route('/{record}'),
    ];
  }

  public static function canCreate(): bool
  {
    return false;
  }
}
