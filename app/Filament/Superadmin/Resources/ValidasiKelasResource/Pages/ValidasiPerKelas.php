<?php

namespace App\Filament\Superadmin\Resources\ValidasiKelasResource\Pages;

use App\Filament\Superadmin\Resources\ValidasiKelasResource;
use App\Filament\Superadmin\Resources\ValidasiResource;
use App\Models\ActivityLog;
use App\Models\FormSubmission;
use App\Models\Kelas;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ValidasiPerKelas extends ViewRecord implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ValidasiKelasResource::class;

    protected static string $view = 'filament.superadmin.pages.validasi-per-kelas';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->record->load(['wali', 'siswa']);

        $this->authorizeAccess();
    }

    public function getTitle(): string|Htmlable
    {
        return "Validasi — {$this->record->nama}";
    }

    public function getHeading(): string|Htmlable
    {
        return "Validasi — {$this->record->nama}";
    }

    public function getSubheading(): ?string
    {
        $wali = $this->record->wali?->name ?? '-';
        $total = $this->record->siswa->count();
        return "Wali Kelas: {$wali}  •  Total Siswa: {$total}";
    }

    public function getBreadcrumbs(): array
    {
        return [
            ValidasiKelasResource::getUrl() => 'Validasi Per Kelas',
            '#' => $this->record->nama,
        ];
    }

    public function table(Table $table): Table
    {
        $siswaIds = $this->record->siswa->pluck('id');

        return $table
            ->query(
                FormSubmission::query()
                    ->where('status', 'verified')
                    ->whereIn('user_id', $siswaIds)
                    ->with(['user.kelas', 'user.role_user', 'verifier', 'validator'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Siswa')
                    ->description(fn($record) => $record->user?->nisn)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.nisn')
                    ->label('NISN')
                    ->searchable()
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
                    ->label('Tgl Verifikasi')
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
                Tables\Filters\SelectFilter::make('hari_ke')
                    ->label('Hari Ke')
                    ->options(
                        collect(range(1, 30))->mapWithKeys(fn($d) => [$d => "Hari ke-{$d}"])->toArray()
                    ),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view')
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->url(fn(FormSubmission $record) => ValidasiResource::getUrl('view', ['record' => $record])),
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
                                'status' => 'pending',
                                'verified_by' => null,
                                'verified_at' => null,
                                'catatan_guru' => null,
                                'kesiswaan_status' => 'pending',
                                'validated_by' => null,
                                'validated_at' => null,
                                'catatan_kesiswaan' => null,
                            ]);
                            Cache::forget("submissions_{$record->user_id}");
                            Cache::forget("submission_{$record->user_id}_{$record->hari_ke}");
                            ActivityLog::log('reset_validation', Auth::user(), [
                                'description' => 'Mereset status formulir hari ke-' . $record->hari_ke . ' dari ' . ($record->user?->name ?? '-'),
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
}
