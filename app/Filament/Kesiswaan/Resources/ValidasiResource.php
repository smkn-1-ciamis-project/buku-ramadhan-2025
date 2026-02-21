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

  public static function getNavigationBadge(): ?string
  {
    $pending = FormSubmission::where('status', 'pending')->count();
    return $pending > 0 ? (string) $pending : null;
  }

  public static function getNavigationBadgeColor(): ?string
  {
    return 'warning';
  }

  /**
   * Kesiswaan melihat SEMUA formulir dari semua kelas.
   */
  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->whereHas('user', function (Builder $q) {
        $q->whereHas('role_user', fn(Builder $rq) => $rq->where('name', 'Siswa'));
      })
      ->with(['user.kelas', 'user.role_user', 'verifier']);
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
        Tables\Columns\TextColumn::make('status')
          ->label('Status')
          ->badge()
          ->color(fn(string $state): string => match ($state) {
            'pending' => 'warning',
            'verified' => 'success',
            'rejected' => 'danger',
          })
          ->formatStateUsing(fn(string $state): string => match ($state) {
            'pending' => 'Menunggu',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
          })
          ->sortable(),
        Tables\Columns\TextColumn::make('verifier.name')
          ->label('Diverifikasi Oleh')
          ->placeholder('-')
          ->toggleable(),
        Tables\Columns\TextColumn::make('created_at')
          ->label('Dikirim')
          ->since()
          ->tooltip(fn($record) => $record->created_at->translatedFormat('d M Y, H:i'))
          ->color('gray')
          ->sortable(),
        Tables\Columns\TextColumn::make('verified_at')
          ->label('Tgl Verifikasi')
          ->since()
          ->tooltip(fn($record) => $record->verified_at?->translatedFormat('d M Y, H:i'))
          ->color('gray')
          ->sortable()
          ->placeholder('-')
          ->toggleable(),
      ])
      ->defaultSort('created_at', 'desc')
      ->filters([
        Tables\Filters\SelectFilter::make('status')
          ->label('Status')
          ->options([
            'pending' => 'Menunggu',
            'verified' => 'Terverifikasi',
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
        Tables\Filters\Filter::make('belum_diverifikasi_guru')
          ->label('Belum diverifikasi guru')
          ->query(fn(Builder $q) => $q->where('status', 'pending'))
          ->toggle(),
        Tables\Filters\Filter::make('sudah_diverifikasi_guru')
          ->label('Sudah diverifikasi guru')
          ->query(fn(Builder $q) => $q->where('status', 'verified'))
          ->toggle(),
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
              Forms\Components\Textarea::make('catatan_guru')
                ->label('Catatan Kesiswaan (opsional)')
                ->rows(2)
                ->placeholder('Tambahkan catatan jika perlu...'),
            ])
            ->action(function (FormSubmission $record, array $data) {
              $record->update([
                'status' => 'verified',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'catatan_guru' => $data['catatan_guru'] ?? $record->catatan_guru,
              ]);
              \Filament\Notifications\Notification::make()
                ->title('Formulir berhasil divalidasi')
                ->success()
                ->send();
            })
            ->visible(fn(FormSubmission $record) => $record->status !== 'verified'),
          Tables\Actions\Action::make('reject')
            ->label('Tolak')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Tolak Formulir')
            ->modalDescription(fn(FormSubmission $record) => "Tolak formulir hari ke-{$record->hari_ke} dari {$record->user->name}?")
            ->modalSubmitActionLabel('Ya, Tolak')
            ->form([
              Forms\Components\Textarea::make('catatan_guru')
                ->label('Alasan Penolakan')
                ->rows(2)
                ->required()
                ->placeholder('Jelaskan alasan penolakan...'),
            ])
            ->action(function (FormSubmission $record, array $data) {
              $record->update([
                'status' => 'rejected',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'catatan_guru' => $data['catatan_guru'],
              ]);
              \Filament\Notifications\Notification::make()
                ->title('Formulir ditolak')
                ->warning()
                ->send();
            })
            ->visible(fn(FormSubmission $record) => $record->status !== 'rejected'),
          Tables\Actions\Action::make('resetStatus')
            ->label('Reset ke Pending')
            ->icon('heroicon-o-arrow-path')
            ->color('gray')
            ->requiresConfirmation()
            ->modalHeading('Reset Status')
            ->modalDescription('Status formulir akan dikembalikan ke Menunggu.')
            ->modalSubmitActionLabel('Ya, Reset')
            ->action(function (FormSubmission $record) {
              $record->update([
                'status' => 'pending',
                'verified_by' => null,
                'verified_at' => null,
                'catatan_guru' => null,
              ]);
              \Filament\Notifications\Notification::make()
                ->title('Status direset ke pending')
                ->info()
                ->send();
            })
            ->visible(fn(FormSubmission $record) => $record->status !== 'pending'),
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
            ->modalDescription('Semua formulir yang dipilih akan divalidasi.')
            ->modalSubmitActionLabel('Ya, Validasi Semua')
            ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
              $count = 0;
              foreach ($records as $record) {
                if ($record->status !== 'verified') {
                  $record->update([
                    'status' => 'verified',
                    'verified_by' => Auth::id(),
                    'verified_at' => now(),
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
              Forms\Components\Textarea::make('catatan_guru')
                ->label('Alasan Penolakan')
                ->rows(2)
                ->required(),
            ])
            ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
              $count = 0;
              foreach ($records as $record) {
                if ($record->status !== 'rejected') {
                  $record->update([
                    'status' => 'rejected',
                    'verified_by' => Auth::id(),
                    'verified_at' => now(),
                    'catatan_guru' => $data['catatan_guru'],
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
