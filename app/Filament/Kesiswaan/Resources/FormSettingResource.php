<?php

namespace App\Filament\Kesiswaan\Resources;

use App\Filament\Kesiswaan\Resources\FormSettingResource\Pages;
use App\Models\FormSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
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

  public static function form(Form $form): Form
  {
    return $form->schema([
      Forms\Components\Section::make('Informasi Agama')
        ->schema([
          Forms\Components\Select::make('agama')
            ->label('Agama')
            ->options([
              'Islam' => 'Islam',
              'Kristen' => 'Kristen',
              'Katolik' => 'Katolik',
              'Hindu' => 'Hindu',
              'Buddha' => 'Buddha',
              'Konghucu' => 'Konghucu',
            ])
            ->required()
            ->unique(ignoreRecord: true)
            ->disabled(fn(?FormSetting $record) => $record !== null)
            ->dehydrated(),
          Forms\Components\Toggle::make('is_active')
            ->label('Aktif')
            ->default(true)
            ->helperText('Jika dinonaktifkan, siswa dengan agama ini tidak bisa mengisi formulir.'),
        ])
        ->columns(2),

      Forms\Components\Section::make('Konfigurasi Bagian Formulir')
        ->description('Atur bagian-bagian formulir yang akan ditampilkan kepada siswa. Drag untuk mengubah urutan.')
        ->schema([
          Forms\Components\Repeater::make('sections')
            ->label('')
            ->schema([
              Forms\Components\Grid::make(3)->schema([
                Forms\Components\TextInput::make('key')
                  ->label('Key (unik)')
                  ->required()
                  ->alphaDash()
                  ->placeholder('cth: puasa')
                  ->helperText('Identifier unik, tanpa spasi'),
                Forms\Components\TextInput::make('title')
                  ->label('Judul Bagian')
                  ->required()
                  ->placeholder('cth: Puasa'),
                Forms\Components\Select::make('type')
                  ->label('Tipe')
                  ->options([
                    'ya_tidak' => 'Ya / Tidak (single)',
                    'ya_tidak_list' => 'Ya / Tidak (list)',
                    'multi_option' => 'Pilihan Ganda (per item)',
                    'checklist_groups' => 'Checklist (grup)',
                    'ya_tidak_groups' => 'Ya / Tidak (grup)',
                    'tadarus' => 'Tadarus Al-Quran',
                    'ceramah' => 'Ringkasan Ceramah',
                    'catatan' => 'Catatan Harian',
                  ])
                  ->required()
                  ->live(),
              ]),

              Forms\Components\Toggle::make('enabled')
                ->label('Tampilkan bagian ini')
                ->default(true),

              // ── Has Reason (for ya_tidak) ──
              Forms\Components\Toggle::make('has_reason')
                ->label('Tampilkan input alasan jika "Tidak"')
                ->default(false)
                ->visible(fn(Get $get) => $get('type') === 'ya_tidak'),

              // ── Reason suggestions (for ya_tidak) ──
              Forms\Components\TagsInput::make('reason_suggestions')
                ->label('Saran Alasan')
                ->placeholder('Tambah saran alasan...')
                ->helperText('Saran yang muncul saat siswa memilih "Tidak"')
                ->visible(fn(Get $get) => $get('type') === 'ya_tidak' && $get('has_reason')),

              // ── Options (for multi_option) ──
              Forms\Components\TagsInput::make('options')
                ->label('Pilihan')
                ->placeholder('Tambah pilihan...')
                ->helperText('Opsi yang bisa dipilih siswa per item (cth: jamaah, munfarid, tidak)')
                ->visible(fn(Get $get) => $get('type') === 'multi_option'),

              // ── Items (for ya_tidak_list, multi_option) ──
              Forms\Components\Repeater::make('items')
                ->label('Daftar Item')
                ->schema([
                  Forms\Components\TextInput::make('key')
                    ->label('Key')
                    ->required()
                    ->alphaDash()
                    ->columnSpan(1),
                  Forms\Components\TextInput::make('label')
                    ->label('Label')
                    ->required()
                    ->columnSpan(2),
                ])
                ->columns(3)
                ->addActionLabel('+ Tambah Item')
                ->reorderable()
                ->collapsible()
                ->itemLabel(fn(array $state): ?string => $state['label'] ?? null)
                ->visible(fn(Get $get) => in_array($get('type'), ['ya_tidak_list', 'multi_option'])),

              // ── Groups (for checklist_groups, ya_tidak_groups) ──
              Forms\Components\Repeater::make('groups')
                ->label('Grup Kegiatan')
                ->schema([
                  Forms\Components\TextInput::make('title')
                    ->label('Judul Grup')
                    ->required()
                    ->columnSpanFull(),
                  Forms\Components\Repeater::make('items')
                    ->label('Item dalam grup')
                    ->schema([
                      Forms\Components\TextInput::make('key')
                        ->label('Key')
                        ->required()
                        ->alphaDash()
                        ->columnSpan(1),
                      Forms\Components\TextInput::make('label')
                        ->label('Label')
                        ->required()
                        ->columnSpan(2),
                    ])
                    ->columns(3)
                    ->addActionLabel('+ Tambah Item')
                    ->reorderable()
                    ->collapsible()
                    ->itemLabel(fn(array $state): ?string => $state['label'] ?? null),
                ])
                ->addActionLabel('+ Tambah Grup')
                ->reorderable()
                ->collapsible()
                ->itemLabel(fn(array $state): ?string => $state['title'] ?? null)
                ->visible(fn(Get $get) => in_array($get('type'), ['checklist_groups', 'ya_tidak_groups'])),
            ])
            ->addActionLabel('+ Tambah Bagian')
            ->reorderable()
            ->collapsible()
            ->cloneable()
            ->itemLabel(fn(array $state): ?string => ($state['enabled'] ?? true ? '✅' : '❌') . ' ' . ($state['title'] ?? 'Bagian Baru'))
            ->defaultItems(0),
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
          Tables\Actions\EditAction::make(),
        ]),
      ])
      ->bulkActions([]);
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListFormSettings::route('/'),
      'create' => Pages\CreateFormSetting::route('/create'),
      'edit' => Pages\EditFormSetting::route('/{record}/edit'),
    ];
  }
}
