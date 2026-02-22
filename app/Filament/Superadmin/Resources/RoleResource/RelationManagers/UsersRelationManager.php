<?php

namespace App\Filament\Superadmin\Resources\RoleResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';
    protected static ?string $title = 'Daftar Pengguna';
    protected static ?string $label = 'Pengguna';

    public function table(Table $table): Table
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
                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN')
                    ->placeholder('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('JK')
                    ->badge()
                    ->color(fn(?string $state): string => $state === 'L' ? 'info' : 'danger')
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                        default => '-',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->since()
                    ->tooltip(fn($record) => $record->created_at?->translatedFormat('d M Y, H:i'))
                    ->sortable(),
            ])
            ->defaultSort('name', 'asc')
            ->actions([])
            ->bulkActions([]);
    }
}
