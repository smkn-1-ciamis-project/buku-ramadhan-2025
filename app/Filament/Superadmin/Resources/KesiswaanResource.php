<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\Superadmin\Resources\KesiswaanResource\Pages;
use App\Models\RoleUser;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class KesiswaanResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Manajemen Kesiswaan';
    protected static ?string $navigationGroup = 'Pengguna';
    protected static ?string $modelLabel = 'Kesiswaan';
    protected static ?string $pluralModelLabel = 'Kesiswaan';
    protected static ?string $slug = 'kesiswaan';
    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return RoleUser::checkNav('sa_kesiswaan');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('role_user', fn(Builder $q) => $q->where('name', 'Kesiswaan'))
            ->with('role_user');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Petugas Kesiswaan')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_hp')
                    ->label('No. HP')
                    ->tel()
                    ->maxLength(20),
                Forms\Components\Select::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                    ->required(),
            ])->columns(2),

            Forms\Components\Section::make('Keamanan')->schema([
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->maxLength(255)
                    ->helperText(fn(string $operation) => $operation === 'edit' ? 'Kosongkan jika tidak ingin mengubah password' : ''),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('JK')
                    ->badge()
                    ->color(fn(string $state): string => $state === 'L' ? 'info' : 'danger')
                    ->formatStateUsing(fn(string $state): string => $state === 'L' ? 'Laki-laki' : 'Perempuan'),
                Tables\Columns\TextColumn::make('no_hp')
                    ->label('No. HP')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->since()
                    ->tooltip(fn($record) => $record->created_at->translatedFormat('d M Y, H:i'))
                    ->sortable(),
            ])
            ->defaultSort('name', 'asc')
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKesiswaan::route('/'),
            'create' => Pages\CreateKesiswaan::route('/create'),
            'edit' => Pages\EditKesiswaan::route('/{record}/edit'),
        ];
    }
}
