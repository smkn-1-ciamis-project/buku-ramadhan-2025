<?php

namespace App\Filament\Guru\Resources;

use App\Filament\Guru\Resources\VerifikasiResource\Pages;
use App\Models\FormSubmission;
use App\Models\Kelas;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class VerifikasiResource extends Resource
{
  protected static ?string $model = FormSubmission::class;

  protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
  protected static ?string $navigationLabel = 'Verifikasi Formulir';
  protected static ?string $navigationGroup = 'Kelola Data';
  protected static ?string $modelLabel = 'Formulir';
  protected static ?string $pluralModelLabel = 'Formulir';
  protected static ?string $slug = 'verifikasi';
  protected static ?int $navigationSort = 3;

  /**
   * Hanya tampilkan formulir dari siswa di kelas yang diwalikan guru ini.
   */
  public static function getEloquentQuery(): Builder
  {
    $guru = Auth::user();
    $kelasIds = Kelas::where('wali_id', $guru->id)->pluck('id');

    return parent::getEloquentQuery()
      ->whereHas('user', function (Builder $q) use ($kelasIds) {
        $q->whereIn('kelas_id', $kelasIds)
          ->whereHas('role_user', fn(Builder $rq) => $rq->where('name', 'Siswa'));
      })
      ->with(['user', 'verifier']);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('user.name')
          ->label('Nama Siswa')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('user.nisn')
          ->label('NISN')
          ->searchable(),
        Tables\Columns\TextColumn::make('hari_ke')
          ->label('Hari Ke')
          ->sortable()
          ->badge()
          ->color('info'),
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
            'verified' => 'Diverifikasi',
            'rejected' => 'Ditolak',
          })
          ->sortable(),
        Tables\Columns\TextColumn::make('created_at')
          ->label('Dikirim')
          ->since()
          ->tooltip(fn($record) => $record->created_at->translatedFormat('d M Y, H:i'))
          ->color('gray')
          ->sortable(),
        Tables\Columns\TextColumn::make('verified_at')
          ->label('Diverifikasi')
          ->since()
          ->tooltip(fn($record) => $record->verified_at?->translatedFormat('d M Y, H:i'))
          ->color('gray')
          ->sortable()
          ->placeholder('-'),
      ])
      ->defaultSort('created_at', 'desc')
      ->filters([
        Tables\Filters\SelectFilter::make('status')
          ->label('Status')
          ->options([
            'pending' => 'Menunggu',
            'verified' => 'Diverifikasi',
            'rejected' => 'Ditolak',
          ]),
        Tables\Filters\SelectFilter::make('hari_ke')
          ->label('Hari Ke')
          ->options(
            collect(range(1, 30))->mapWithKeys(fn($d) => [$d => "Hari ke-{$d}"])->toArray()
          ),
      ])
      ->actions([
        Tables\Actions\ActionGroup::make([
          Tables\Actions\ViewAction::make()
            ->label('Lihat')
            ->icon('heroicon-o-eye')
            ->color('info'),
          Tables\Actions\Action::make('verify')
            ->label('Verifikasi')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Verifikasi Formulir')
            ->modalDescription(fn(FormSubmission $record) => "Verifikasi formulir hari ke-{$record->hari_ke} dari {$record->user->name}?")
            ->modalSubmitActionLabel('Ya, Verifikasi')
            ->form([
              \Filament\Forms\Components\Textarea::make('catatan_guru')
                ->label('Catatan (opsional)')
                ->rows(2)
                ->placeholder('Tambahkan catatan jika perlu...'),
            ])
            ->action(function (FormSubmission $record, array $data) {
              $record->update([
                'status' => 'verified',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'catatan_guru' => $data['catatan_guru'] ?? null,
              ]);
              \Filament\Notifications\Notification::make()
                ->title('Formulir berhasil diverifikasi')
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
              \Filament\Forms\Components\Textarea::make('catatan_guru')
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
            ->modalDescription('Status formulir akan dikembalikan ke Menunggu Verifikasi.')
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
          Tables\Actions\BulkAction::make('bulkVerify')
            ->label('Verifikasi Semua')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Verifikasi Formulir Terpilih')
            ->modalDescription('Semua formulir yang dipilih akan diverifikasi.')
            ->modalSubmitActionLabel('Ya, Verifikasi Semua')
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
                ->title("{$count} formulir berhasil diverifikasi")
                ->success()
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
      'index' => Pages\ListVerifikasi::route('/'),
      'view'  => Pages\ViewVerifikasi::route('/{record}'),
    ];
  }

  public static function canCreate(): bool
  {
    return false;
  }
}
