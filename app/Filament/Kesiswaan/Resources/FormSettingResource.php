<?php

namespace App\Filament\Kesiswaan\Resources;

use App\Filament\Kesiswaan\Resources\FormSettingResource\Pages;
use App\Models\FormSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FormSettingResource extends Resource
{
  protected static ?string $model = FormSetting::class;

  protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
  protected static ?string $navigationLabel = 'Setting Formulir';
  protected static ?string $navigationGroup = 'Pengaturan';
  protected static ?string $modelLabel = 'Setting Formulir';
  protected static ?string $pluralModelLabel = 'Setting Formulir';
  protected static ?string $slug = 'setting-formulir';
  protected static ?int $navigationSort = 10;

  public static function shouldRegisterNavigation(): bool
  {
    return \App\Models\RoleUser::checkNav('kesiswaan_setting_formulir');
  }

  public static function canCreate(): bool
  {
    return false;
  }

  public static function canEdit($record): bool
  {
    return false;
  }

  public static function canDelete($record): bool
  {
    return false;
  }

  public static function infolist(Infolist $infolist): Infolist
  {
    return $infolist
      ->schema([
        Infolists\Components\Section::make('Informasi Agama')
          ->schema([
            Infolists\Components\TextEntry::make('agama')
              ->label('Agama')
              ->badge()
              ->color('info'),
            Infolists\Components\IconEntry::make('is_active')
              ->label('Status')
              ->boolean()
              ->trueIcon('heroicon-o-check-circle')
              ->falseIcon('heroicon-o-x-circle')
              ->trueColor('success')
              ->falseColor('danger'),
          ])
          ->columns(2),

        Infolists\Components\Section::make('Bagian Formulir')
          ->schema([
            Infolists\Components\RepeatableEntry::make('sections')
              ->label('')
              ->schema([
                Infolists\Components\TextEntry::make('title')
                  ->label('Judul Bagian')
                  ->weight('bold'),
                Infolists\Components\TextEntry::make('key')
                  ->label('Key')
                  ->badge()
                  ->color('gray'),
                Infolists\Components\TextEntry::make('type')
                  ->label('Tipe')
                  ->badge()
                  ->formatStateUsing(fn(string $state): string => match ($state) {
                    'ya_tidak' => 'Ya / Tidak (single)',
                    'ya_tidak_list' => 'Ya / Tidak (list)',
                    'multi_option' => 'Pilihan Ganda',
                    'checklist_groups' => 'Checklist (grup)',
                    'ya_tidak_groups' => 'Ya / Tidak (grup)',
                    'tadarus' => 'Tadarus Al-Quran',
                    'ceramah' => 'Ringkasan Ceramah',
                    'catatan' => 'Catatan Harian',
                    default => $state,
                  }),
                Infolists\Components\IconEntry::make('enabled')
                  ->label('Aktif')
                  ->boolean()
                  ->trueIcon('heroicon-o-check-circle')
                  ->falseIcon('heroicon-o-x-circle')
                  ->trueColor('success')
                  ->falseColor('danger'),
              ])
              ->columns(4),
          ]),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('agama')
          ->label('Agama')
          ->searchable()
          ->sortable()
          ->weight('bold'),
        Tables\Columns\TextColumn::make('sections')
          ->label('Jumlah Bagian')
          ->formatStateUsing(function ($state) {
            if (!is_array($state)) return '0 bagian';
            $total = count($state);
            $active = count(array_filter($state, fn($s) => $s['enabled'] ?? true));
            return "{$active}/{$total} bagian aktif";
          })
          ->badge()
          ->color('info'),
        Tables\Columns\IconColumn::make('is_active')
          ->label('Status')
          ->boolean()
          ->trueIcon('heroicon-o-check-circle')
          ->falseIcon('heroicon-o-x-circle')
          ->trueColor('success')
          ->falseColor('danger'),
        Tables\Columns\TextColumn::make('updated_at')
          ->label('Terakhir Diubah')
          ->since()
          ->tooltip(fn($record) => $record->updated_at?->translatedFormat('d M Y, H:i'))
          ->sortable(),
      ])
      ->defaultSort('agama')
      ->actions([
        Tables\Actions\ActionGroup::make([
          Tables\Actions\ViewAction::make()
            ->label('Lihat')
            ->icon('heroicon-o-eye')
            ->color('info'),
        ])
          ->icon('heroicon-m-ellipsis-vertical')
          ->tooltip('Aksi'),
      ])
      ->bulkActions([]);
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListFormSettings::route('/'),
      'view' => Pages\ViewFormSetting::route('/{record}'),
    ];
  }
}
