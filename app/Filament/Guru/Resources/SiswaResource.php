<?php

namespace App\Filament\Guru\Resources;

use App\Filament\Guru\Resources\SiswaResource\Pages;
use App\Models\Kelas;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SiswaResource extends Resource
{
  protected static ?string $model = User::class;

  protected static ?string $navigationIcon = 'heroicon-o-user-group';
  protected static ?string $navigationLabel = 'Manajemen Siswa';
  protected static ?string $modelLabel = 'Siswa';
  protected static ?string $pluralModelLabel = 'Siswa';
  protected static ?string $slug = 'siswa';
  protected static ?int $navigationSort = 2;

  /**
   * Hanya tampilkan siswa dari kelas yang diwalikan guru ini.
   */
  public static function getEloquentQuery(): Builder
  {
    $guru = Auth::user();

    // Ambil id kelas yang diwalikan guru ini
    $kelasIds = Kelas::where('wali_id', $guru->id)->pluck('id');

    return parent::getEloquentQuery()
      ->whereIn('kelas_id', $kelasIds)
      ->whereHas('role_user', fn(Builder $q) => $q->where('name', 'Siswa'));
  }

  public static function form(Form $form): Form
  {
    $guru = Auth::user();
    $kelasIds = Kelas::where('wali_id', $guru->id)->pluck('id');

    return $form
      ->schema([
        Forms\Components\Section::make('Data Siswa')
          ->schema([
            Forms\Components\TextInput::make('name')
              ->label('Nama Lengkap')
              ->required()
              ->maxLength(255),
            Forms\Components\TextInput::make('nisn')
              ->label('NISN')
              ->required()
              ->maxLength(10),
            Forms\Components\TextInput::make('email')
              ->label('Email')
              ->email()
              ->required(),
            Forms\Components\Select::make('jenis_kelamin')
              ->label('Jenis Kelamin')
              ->options([
                'L' => 'Laki-laki',
                'P' => 'Perempuan',
              ])
              ->required(),
            Forms\Components\Select::make('agama')
              ->label('Agama')
              ->options([
                'Islam'     => 'Islam',
                'Kristen'   => 'Kristen',
                'Katolik'   => 'Katolik',
                'Hindu'     => 'Hindu',
                'Buddha'    => 'Buddha',
                'Konghucu'  => 'Konghucu',
              ])
              ->required(),
            Forms\Components\Select::make('kelas_id')
              ->label('Kelas')
              ->options(Kelas::whereIn('id', $kelasIds)->pluck('nama', 'id'))
              ->required(),
          ])
          ->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    $guru = Auth::user();
    $kelasOptions = Kelas::where('wali_id', $guru->id)->pluck('nama', 'id')->toArray();

    return $table
      ->columns([
        Tables\Columns\TextColumn::make('nisn')
          ->label('NISN')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('name')
          ->label('Nama')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('jenis_kelamin')
          ->label('JK')
          ->badge()
          ->color(fn(string $state): string => match ($state) {
            'L' => 'info',
            'P' => 'danger',
            default => 'gray',
          })
          ->formatStateUsing(fn(string $state): string => match ($state) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-',
          }),
        Tables\Columns\TextColumn::make('agama')
          ->label('Agama')
          ->badge()
          ->color('success')
          ->sortable(),
        Tables\Columns\TextColumn::make('kelas.nama')
          ->label('Kelas')
          ->sortable(),
      ])
      ->defaultSort('name')
      ->filters([
        Tables\Filters\SelectFilter::make('kelas_id')
          ->label('Kelas')
          ->options($kelasOptions),
        Tables\Filters\SelectFilter::make('jenis_kelamin')
          ->label('Jenis Kelamin')
          ->options([
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
          ]),
        Tables\Filters\SelectFilter::make('agama')
          ->label('Agama')
          ->options([
            'Islam'    => 'Islam',
            'Kristen'  => 'Kristen',
            'Katolik'  => 'Katolik',
            'Hindu'    => 'Hindu',
            'Buddha'   => 'Buddha',
            'Konghucu' => 'Konghucu',
          ]),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
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
      'index'  => Pages\ListSiswa::route('/'),
      'edit'   => Pages\EditSiswa::route('/{record}/edit'),
    ];
  }
}
