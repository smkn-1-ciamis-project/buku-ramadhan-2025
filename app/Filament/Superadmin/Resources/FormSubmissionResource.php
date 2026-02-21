<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\Superadmin\Resources\FormSubmissionResource\Pages;
use App\Models\FormSubmission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FormSubmissionResource extends Resource
{
  protected static ?string $model = FormSubmission::class;

  protected static ?string $navigationIcon = 'heroicon-o-document-text';
  protected static ?string $navigationLabel = 'Log Formulir';
  protected static ?string $navigationGroup = 'Akademik';
  protected static ?string $modelLabel = 'Formulir';
  protected static ?string $pluralModelLabel = 'Formulir';
  protected static ?string $slug = 'log-formulir';
  protected static ?int $navigationSort = 6;

  public static function canCreate(): bool
  {
    return false;
  }

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->with(['user.kelas', 'user.role_user', 'verifier']);
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
        Tables\Columns\TextColumn::make('user.kelas.nama')
          ->label('Kelas')
          ->badge()
          ->color('info')
          ->placeholder('-'),
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
        Tables\Columns\TextColumn::make('verifier.name')
          ->label('Diverifikasi Oleh')
          ->placeholder('-'),
        Tables\Columns\TextColumn::make('created_at')
          ->label('Dikirim')
          ->since()
          ->tooltip(fn($record) => $record->created_at->translatedFormat('d M Y, H:i'))
          ->color('gray')
          ->sortable(),
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
        Tables\Filters\SelectFilter::make('kelas')
          ->label('Kelas')
          ->relationship('user.kelas', 'nama')
          ->searchable()
          ->preload(),
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
      ]);
  }

  public static function infolist(Infolist $infolist): Infolist
  {
    return $infolist->schema([
      Infolists\Components\Section::make('Informasi Siswa')->schema([
        Infolists\Components\TextEntry::make('user.name')->label('Nama Siswa'),
        Infolists\Components\TextEntry::make('user.nisn')->label('NISN'),
        Infolists\Components\TextEntry::make('user.kelas.nama')->label('Kelas'),
      ])->columns(3),
      Infolists\Components\Section::make('Detail Formulir')->schema([
        Infolists\Components\TextEntry::make('hari_ke')->label('Hari Ke')->badge()->color('info'),
        Infolists\Components\TextEntry::make('status')
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
          }),
        Infolists\Components\TextEntry::make('created_at')
          ->label('Dikirim Pada')
          ->dateTime('d M Y, H:i'),
        Infolists\Components\TextEntry::make('verifier.name')
          ->label('Diverifikasi Oleh')
          ->placeholder('-'),
        Infolists\Components\TextEntry::make('verified_at')
          ->label('Diverifikasi Pada')
          ->dateTime('d M Y, H:i')
          ->placeholder('-'),
        Infolists\Components\TextEntry::make('catatan_guru')
          ->label('Catatan Guru')
          ->placeholder('Tidak ada catatan')
          ->columnSpanFull(),
      ])->columns(3),
    ]);
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListFormSubmission::route('/'),
      'view' => Pages\ViewFormSubmission::route('/{record}'),
    ];
  }
}
