<?php

namespace App\Filament\Kesiswaan\Resources\ValidasiKelasResource\Pages;

use App\Filament\Kesiswaan\Resources\ValidasiKelasResource;
use App\Filament\Kesiswaan\Resources\ValidasiResource;
use App\Models\ActivityLog;
use App\Models\FormSubmission;
use App\Models\Kelas;
use Carbon\Carbon;
use Filament\Actions;
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

  protected static string $view = 'filament.kesiswaan.pages.validasi-per-kelas';

  /** Which status box is active (null = all statuses) */
  public ?string $activeStatusFilter = null;

  /** Which day is selected on the calendar (null = all days) */
  public ?int $activeDayFilter = null;

  public function mount(int|string $record): void
  {
    $this->record = $this->resolveRecord($record);
    $this->record->load(['wali', 'siswa']);

    $this->authorizeAccess();
  }

  /** Stat box clicked: filter by kesiswaan_status */
  public function filterByStatus(?string $status): void
  {
    $this->activeStatusFilter = $status;

    $this->tableFilters = array_merge($this->tableFilters ?? [], [
      'kesiswaan_status' => ['value' => $status ?? ''],
    ]);

    $this->resetPage();
  }

  /** Calendar day clicked: filter by hari_ke */
  public function filterByDay(?int $day): void
  {
    $this->activeDayFilter = $day;

    $this->tableFilters = array_merge($this->tableFilters ?? [], [
      'hari_ke' => ['value' => $day !== null ? (string) $day : ''],
    ]);

    $this->resetPage();
  }

  /** Provide calendar + stats data to the blade view. */
  protected function getViewData(): array
  {
    $siswaIds = $this->record->siswa->pluck('id');

    $ramadhanStart = Carbon::create(2026, 2, 19); // 1 Ramadhan 1447H = Thursday
    $today         = Carbon::today();
    $maxDay        = max(0, (int) min(30, $ramadhanStart->diffInDays($today) + 1));

    $baseQuery = FormSubmission::query()
      ->where('status', 'verified')
      ->whereIn('user_id', $siswaIds);

    $total      = (clone $baseQuery)->count();
    $menunggu   = (clone $baseQuery)->where('kesiswaan_status', 'pending')->count();
    $divalidasi = (clone $baseQuery)->where('kesiswaan_status', 'validated')->count();
    $ditolak    = (clone $baseQuery)->where('kesiswaan_status', 'rejected')->count();

    // Per-day stats for calendar coloring (using kesiswaan_status)
    $dayCounts = [];
    $rows = (clone $baseQuery)
      ->selectRaw('hari_ke, kesiswaan_status, count(*) as cnt')
      ->groupBy('hari_ke', 'kesiswaan_status')
      ->get();
    foreach ($rows as $row) {
      $dayCounts[$row->hari_ke][$row->kesiswaan_status] = $row->cnt;
    }

    // Build calendar cells (7-column Mon–Sun grid)
    $leadingEmpties = $ramadhanStart->dayOfWeekIso - 1; // 3
    $calendarCells  = [];

    for ($i = 0; $i < $leadingEmpties; $i++) {
      $calendarCells[] = null;
    }
    for ($d = 1; $d <= 30; $d++) {
      $date            = $ramadhanStart->copy()->addDays($d - 1);
      $calendarCells[] = [
        'ramadanDay'       => $d,
        'masehiDay'        => $date->day,
        'masehiMonthShort' => $date->locale('id')->isoFormat('MMM'),
        'isPast'           => $d <= $maxDay,
        'isToday'          => $d === $maxDay && $today->isSameDay($date),
        'counts'           => $dayCounts[$d] ?? [],
      ];
    }
    // Trailing empties to complete last row
    $trailing = (7 - (count($calendarCells) % 7)) % 7;
    for ($i = 0; $i < $trailing; $i++) {
      $calendarCells[] = null;
    }

    return [
      'total'         => $total,
      'menunggu'      => $menunggu,
      'divalidasi'    => $divalidasi,
      'ditolak'       => $ditolak,
      'activeStatus'  => $this->activeStatusFilter,
      'activeDay'     => $this->activeDayFilter,
      'maxDay'        => $maxDay,
      'calendarCells' => $calendarCells,
    ];
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

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('exportKelas')
        ->label('Export Excel')
        ->icon('heroicon-o-arrow-down-tray')
        ->color('success')
        ->action(fn() => redirect(route('kesiswaan.validasi.export', ['kelas' => $this->record->id]))),
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
          ->alignCenter()
          ->toggleable(isToggledHiddenByDefault: true),
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
      ->checkIfRecordIsSelectableUsing(fn(FormSubmission $record): bool => $record->kesiswaan_status === 'pending')
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
            collect(range(1, 30))->mapWithKeys(fn($d) => [$d => (string) $d])->toArray()
          ),
      ])
      ->actions([
        Tables\Actions\ActionGroup::make([
          Tables\Actions\Action::make('view')
            ->label('Lihat Detail')
            ->icon('heroicon-o-eye')
            ->color('info')
            ->url(fn(FormSubmission $record) => ValidasiResource::getUrl('view', ['record' => $record])),
          Tables\Actions\Action::make('validate')
            ->label('Validasi')
            ->icon('heroicon-o-shield-check')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Validasi Formulir')
            ->modalDescription(fn(FormSubmission $record) => 'Validasi formulir hari ke-' . $record->hari_ke . ' dari ' . ($record->user?->name ?? '-') . '?')
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
              Cache::forget("submissions_{$record->user_id}");
              Cache::forget("submission_{$record->user_id}_{$record->hari_ke}");
              ActivityLog::log('validate_submission', Auth::user(), [
                'description' => 'Memvalidasi formulir hari ke-' . $record->hari_ke . ' dari ' . ($record->user?->name ?? '-'),
                'submission_id' => $record->id,
                'target_user' => $record->user?->name,
                'hari_ke' => $record->hari_ke,
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
            ->modalDescription(fn(FormSubmission $record) => 'Tolak formulir hari ke-' . $record->hari_ke . ' dari ' . ($record->user?->name ?? '-') . '?')
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
              Cache::forget("submissions_{$record->user_id}");
              Cache::forget("submission_{$record->user_id}_{$record->hari_ke}");
              ActivityLog::log('reject_validation', Auth::user(), [
                'description' => 'Menolak formulir hari ke-' . $record->hari_ke . ' dari ' . ($record->user?->name ?? '-'),
                'submission_id' => $record->id,
                'target_user' => $record->user?->name,
                'hari_ke' => $record->hari_ke,
                'alasan' => $data['catatan_kesiswaan'],
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
              Cache::forget("submissions_{$record->user_id}");
              Cache::forget("submission_{$record->user_id}_{$record->hari_ke}");
              ActivityLog::log('reset_validation', Auth::user(), [
                'description' => 'Reset status validasi formulir hari ke-' . $record->hari_ke . ' dari ' . ($record->user?->name ?? '-'),
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
              $bustedUsers = [];
              foreach ($records as $record) {
                if ($record->kesiswaan_status !== 'validated') {
                  $record->update([
                    'kesiswaan_status' => 'validated',
                    'validated_by' => Auth::id(),
                    'validated_at' => now(),
                  ]);
                  Cache::forget("submission_{$record->user_id}_{$record->hari_ke}");
                  $bustedUsers[$record->user_id] = true;
                  $count++;
                }
              }
              foreach (array_keys($bustedUsers) as $userId) {
                Cache::forget("submissions_{$userId}");
              }
              ActivityLog::log('bulk_validate_submission', Auth::user(), [
                'description' => 'Validasi massal ' . $count . ' formulir',
                'count' => $count,
              ]);
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
              $bustedUsers = [];
              foreach ($records as $record) {
                if ($record->kesiswaan_status !== 'rejected') {
                  $record->update([
                    'kesiswaan_status' => 'rejected',
                    'validated_by' => Auth::id(),
                    'validated_at' => now(),
                    'catatan_kesiswaan' => $data['catatan_kesiswaan'],
                  ]);
                  Cache::forget("submission_{$record->user_id}_{$record->hari_ke}");
                  $bustedUsers[$record->user_id] = true;
                  $count++;
                }
              }
              foreach (array_keys($bustedUsers) as $userId) {
                Cache::forget("submissions_{$userId}");
              }
              ActivityLog::log('bulk_reject_validation', Auth::user(), [
                'description' => 'Menolak massal ' . $count . ' formulir',
                'count' => $count,
              ]);
              \Filament\Notifications\Notification::make()
                ->title("{$count} formulir ditolak")
                ->warning()
                ->send();
            })
            ->deselectRecordsAfterCompletion(),
        ]),
      ])
      ->recordUrl(fn(FormSubmission $record) => ValidasiResource::getUrl('view', ['record' => $record]))
      ->emptyStateHeading('Belum ada formulir terverifikasi')
      ->emptyStateDescription('Formulir siswa di kelas ini belum ada yang diverifikasi oleh guru.')
      ->emptyStateIcon('heroicon-o-clipboard-document');
  }
}
