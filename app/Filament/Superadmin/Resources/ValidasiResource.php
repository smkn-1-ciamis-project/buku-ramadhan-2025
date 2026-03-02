<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\Superadmin\Resources\ValidasiResource\Pages;
use App\Models\ActivityLog;
use App\Models\FormSubmission;
use App\Models\Kelas;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ValidasiResource extends Resource
{
    protected static ?string $model = FormSubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Validasi Formulir (Semua)';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?string $modelLabel = 'Formulir';
    protected static ?string $pluralModelLabel = 'Formulir';
    protected static ?string $slug = 'validasi-formulir';
    protected static ?int $navigationSort = 5;

    public static function shouldRegisterNavigation(): bool
    {
        // Hidden — navigasi dipindahkan ke ValidasiKelasResource (per kelas)
        return false;
    }

    /**
     * Superadmin melihat formulir yang SUDAH diverifikasi oleh guru.
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
            ->defaultSort('kesiswaan_status', 'asc')
            ->modifyQueryUsing(fn(Builder $query) => $query->reorder()->orderByRaw("FIELD(kesiswaan_status, 'pending', 'rejected', 'validated')")->orderBy('verified_at', 'desc'))
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
                    ->options(fn() => Kelas::orderBy('nama')->pluck('nama', 'id')->toArray())
                    ->query(fn(Builder $query, array $data) => $data['value'] ? $query->whereHas('user', fn($q) => $q->where('kelas_id', $data['value'])) : $query)
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
                            Cache::forget("submissions_{$record->user_id}");
                            Cache::forget("submission_{$record->user_id}_{$record->hari_ke}");
                            ActivityLog::log('reset_validation', Auth::user(), [
                                'description' => 'Mereset status validasi formulir hari ke-' . $record->hari_ke . ' dari ' . ($record->user?->name ?? '-'),
                                'submission_id' => $record->id,
                                'target_user' => $record->user?->name,
                                'hari_ke' => $record->hari_ke,
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
            ->bulkActions([]);
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
