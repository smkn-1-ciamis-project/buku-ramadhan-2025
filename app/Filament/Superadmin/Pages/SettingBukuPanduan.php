<?php

namespace App\Filament\Superadmin\Pages;

use App\Models\AppSetting;
use App\Support\RoleGuideDefaults;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SettingBukuPanduan extends Page implements HasForms
{
  use InteractsWithForms;

  protected static ?string $navigationIcon = 'heroicon-o-book-open';
  protected static ?string $navigationLabel = 'Setting Buku Panduan';
  protected static ?string $navigationGroup = 'Pengaturan';
  protected static ?string $title = 'Setting Buku Panduan Login';
  protected static ?string $slug = 'setting-buku-panduan';
  protected static ?int $navigationSort = 14;
  protected static string $view = 'filament.superadmin.pages.setting-buku-panduan';

  public ?array $data = [];

  public static function shouldRegisterNavigation(): bool
  {
    return true;
  }

  public function mount(): void
  {
    $this->form->fill([
      'guide_siswa' => (string) AppSetting::getValue('guide_siswa', RoleGuideDefaults::forRole('siswa')),
      'guide_guru' => (string) AppSetting::getValue('guide_guru', RoleGuideDefaults::forRole('guru')),
      'guide_kesiswaan' => (string) AppSetting::getValue('guide_kesiswaan', RoleGuideDefaults::forRole('kesiswaan')),
      'guide_siswa_admin_contacts' => $this->normalizeAdminContacts(
        AppSetting::getValue('guide_siswa_admin_contacts', RoleGuideDefaults::defaultContacts('siswa'))
      ),
      'guide_guru_admin_contacts' => $this->normalizeAdminContacts(
        AppSetting::getValue('guide_guru_admin_contacts', RoleGuideDefaults::defaultContacts('guru'))
      ),
      'guide_kesiswaan_admin_contacts' => $this->normalizeAdminContacts(
        AppSetting::getValue('guide_kesiswaan_admin_contacts', RoleGuideDefaults::defaultContacts('kesiswaan'))
      ),
      'guide_siswa_flow_steps' => $this->normalizeFlowSteps(
        AppSetting::getValue('guide_siswa_flow_steps', RoleGuideDefaults::defaultFlowSteps('siswa'))
      ),
      'guide_guru_flow_steps' => $this->normalizeFlowSteps(
        AppSetting::getValue('guide_guru_flow_steps', RoleGuideDefaults::defaultFlowSteps('guru'))
      ),
      'guide_kesiswaan_flow_steps' => $this->normalizeFlowSteps(
        AppSetting::getValue('guide_kesiswaan_flow_steps', RoleGuideDefaults::defaultFlowSteps('kesiswaan'))
      ),
    ]);
  }

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('Panduan Login per Role')
          ->description('Konten di bawah ditampilkan saat pengguna menekan tombol Buku Panduan di halaman login. Role Superadmin tidak memiliki buku panduan. Waktu update akan tercatat otomatis saat tombol simpan ditekan.')
          ->icon('heroicon-o-book-open')
          ->schema([
            Forms\Components\Tabs::make('Panduan per Role')
              ->tabs([
                Forms\Components\Tabs\Tab::make('Siswa')
                  ->schema([
                    Forms\Components\Placeholder::make('guide_siswa_latest_updated_at')
                      ->label('Terakhir diperbarui')
                      ->content(fn() => $this->getLatestUpdatedAtLabel([
                        'guide_siswa',
                        'guide_siswa_admin_contacts',
                        'guide_siswa_flow_steps',
                      ])),
                    Forms\Components\Repeater::make('guide_siswa_admin_contacts')
                      ->label('Daftar Kontak Admin Siswa')
                      ->schema([
                        Forms\Components\TextInput::make('name')
                          ->label('Nama Admin')
                          ->required()
                          ->maxLength(50)
                          ->placeholder('Contoh: Admin 1'),
                        Forms\Components\TextInput::make('phone')
                          ->label('Nomor WhatsApp')
                          ->required()
                          ->tel()
                          ->maxLength(20)
                          ->placeholder('Contoh: 0812xxxxxxx'),
                      ])
                      ->default(RoleGuideDefaults::defaultContacts('siswa'))
                      ->minItems(1)
                      ->addActionLabel('Tambah Admin')
                      ->columns(2)
                      ->columnSpanFull(),
                    Forms\Components\Repeater::make('guide_siswa_flow_steps')
                      ->label('Infografis Singkat Siswa (Grafik Alur)')
                      ->schema([
                        Forms\Components\TextInput::make('title')
                          ->label('Judul Langkah')
                          ->required()
                          ->maxLength(80)
                          ->placeholder('Contoh: Login Akun Siswa'),
                        Forms\Components\TextInput::make('desc')
                          ->label('Deskripsi Singkat')
                          ->required()
                          ->maxLength(180)
                          ->placeholder('Contoh: Masukkan NISN 10 digit dan password aktif.'),
                      ])
                      ->default(RoleGuideDefaults::defaultFlowSteps('siswa'))
                      ->minItems(3)
                      ->addActionLabel('Tambah Langkah Infografis')
                      ->reorderable()
                      ->collapsible()
                      ->columns(2)
                      ->columnSpanFull(),
                    Forms\Components\RichEditor::make('guide_siswa')
                      ->label('Buku Panduan Siswa')
                      ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'h2',
                        'h3',
                        'blockquote',
                        'bulletList',
                        'orderedList',
                        'link',
                        'undo',
                        'redo',
                      ])
                      ->placeholder('Isi panduan login dan penggunaan awal untuk siswa...')
                      ->columnSpanFull(),
                  ]),
                Forms\Components\Tabs\Tab::make('Guru')
                  ->schema([
                    Forms\Components\Placeholder::make('guide_guru_latest_updated_at')
                      ->label('Terakhir diperbarui')
                      ->content(fn() => $this->getLatestUpdatedAtLabel([
                        'guide_guru',
                        'guide_guru_admin_contacts',
                        'guide_guru_flow_steps',
                      ])),
                    Forms\Components\Repeater::make('guide_guru_admin_contacts')
                      ->label('Daftar Kontak Admin Guru')
                      ->schema([
                        Forms\Components\TextInput::make('name')
                          ->label('Nama Admin')
                          ->required()
                          ->maxLength(50),
                        Forms\Components\TextInput::make('phone')
                          ->label('Nomor WhatsApp')
                          ->required()
                          ->tel()
                          ->maxLength(20),
                      ])
                      ->default(RoleGuideDefaults::defaultContacts('guru'))
                      ->minItems(1)
                      ->addActionLabel('Tambah Admin')
                      ->columns(2)
                      ->columnSpanFull(),
                    Forms\Components\Repeater::make('guide_guru_flow_steps')
                      ->label('Infografis Singkat Guru (Grafik Alur)')
                      ->schema([
                        Forms\Components\TextInput::make('title')
                          ->label('Judul Langkah')
                          ->required()
                          ->maxLength(80),
                        Forms\Components\TextInput::make('desc')
                          ->label('Deskripsi Singkat')
                          ->required()
                          ->maxLength(180),
                      ])
                      ->default(RoleGuideDefaults::defaultFlowSteps('guru'))
                      ->minItems(3)
                      ->addActionLabel('Tambah Langkah Infografis')
                      ->reorderable()
                      ->collapsible()
                      ->columns(2)
                      ->columnSpanFull(),
                    Forms\Components\RichEditor::make('guide_guru')
                      ->label('Buku Panduan Guru')
                      ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'h2',
                        'h3',
                        'blockquote',
                        'bulletList',
                        'orderedList',
                        'link',
                        'undo',
                        'redo',
                      ])
                      ->placeholder('Isi panduan login dan penggunaan awal untuk guru...')
                      ->columnSpanFull(),
                  ]),
                Forms\Components\Tabs\Tab::make('Kesiswaan')
                  ->schema([
                    Forms\Components\Placeholder::make('guide_kesiswaan_latest_updated_at')
                      ->label('Terakhir diperbarui')
                      ->content(fn() => $this->getLatestUpdatedAtLabel([
                        'guide_kesiswaan',
                        'guide_kesiswaan_admin_contacts',
                        'guide_kesiswaan_flow_steps',
                      ])),
                    Forms\Components\Repeater::make('guide_kesiswaan_admin_contacts')
                      ->label('Daftar Kontak Admin Kesiswaan')
                      ->schema([
                        Forms\Components\TextInput::make('name')
                          ->label('Nama Admin')
                          ->required()
                          ->maxLength(50),
                        Forms\Components\TextInput::make('phone')
                          ->label('Nomor WhatsApp')
                          ->required()
                          ->tel()
                          ->maxLength(20),
                      ])
                      ->default(RoleGuideDefaults::defaultContacts('kesiswaan'))
                      ->minItems(1)
                      ->addActionLabel('Tambah Admin')
                      ->columns(2)
                      ->columnSpanFull(),
                    Forms\Components\Repeater::make('guide_kesiswaan_flow_steps')
                      ->label('Infografis Singkat Kesiswaan (Grafik Alur)')
                      ->schema([
                        Forms\Components\TextInput::make('title')
                          ->label('Judul Langkah')
                          ->required()
                          ->maxLength(80),
                        Forms\Components\TextInput::make('desc')
                          ->label('Deskripsi Singkat')
                          ->required()
                          ->maxLength(180),
                      ])
                      ->default(RoleGuideDefaults::defaultFlowSteps('kesiswaan'))
                      ->minItems(3)
                      ->addActionLabel('Tambah Langkah Infografis')
                      ->reorderable()
                      ->collapsible()
                      ->columns(2)
                      ->columnSpanFull(),
                    Forms\Components\RichEditor::make('guide_kesiswaan')
                      ->label('Buku Panduan Kesiswaan')
                      ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'h2',
                        'h3',
                        'blockquote',
                        'bulletList',
                        'orderedList',
                        'link',
                        'undo',
                        'redo',
                      ])
                      ->placeholder('Isi panduan login dan penggunaan awal untuk kesiswaan...')
                      ->columnSpanFull(),
                  ]),
              ])
              ->columnSpanFull(),
          ]),
      ])
      ->statePath('data');
  }

  public function save(): void
  {
    $data = $this->form->getState();

    $definitions = [
      'guide_siswa' => [
        'label' => 'Buku Panduan Siswa',
        'description' => 'Panduan yang tampil pada halaman login siswa.',
      ],
      'guide_guru' => [
        'label' => 'Buku Panduan Guru',
        'description' => 'Panduan yang tampil pada halaman login guru.',
      ],
      'guide_kesiswaan' => [
        'label' => 'Buku Panduan Kesiswaan',
        'description' => 'Panduan yang tampil pada halaman login kesiswaan.',
      ],
    ];

    foreach ($definitions as $key => $meta) {
      AppSetting::updateOrCreate(
        ['key' => $key],
        [
          'group' => 'guide',
          'value' => (string) ($data[$key] ?? ''),
          'type' => 'string',
          'label' => $meta['label'],
          'description' => $meta['description'],
        ]
      );
    }

    $dynamicJsonSettings = [
      'guide_siswa_admin_contacts' => [
        'label' => 'Kontak Admin Siswa',
        'description' => 'Daftar kontak admin yang tampil pada halaman buku panduan siswa.',
        'value' => $this->normalizeAdminContacts($data['guide_siswa_admin_contacts'] ?? []),
        'fallback' => RoleGuideDefaults::defaultContacts('siswa'),
      ],
      'guide_guru_admin_contacts' => [
        'label' => 'Kontak Admin Guru',
        'description' => 'Daftar kontak admin yang tampil pada halaman buku panduan guru.',
        'value' => $this->normalizeAdminContacts($data['guide_guru_admin_contacts'] ?? []),
        'fallback' => RoleGuideDefaults::defaultContacts('guru'),
      ],
      'guide_kesiswaan_admin_contacts' => [
        'label' => 'Kontak Admin Kesiswaan',
        'description' => 'Daftar kontak admin yang tampil pada halaman buku panduan kesiswaan.',
        'value' => $this->normalizeAdminContacts($data['guide_kesiswaan_admin_contacts'] ?? []),
        'fallback' => RoleGuideDefaults::defaultContacts('kesiswaan'),
      ],
      'guide_siswa_flow_steps' => [
        'label' => 'Infografis Singkat Siswa',
        'description' => 'Langkah infografis singkat pada halaman buku panduan siswa.',
        'value' => $this->normalizeFlowSteps($data['guide_siswa_flow_steps'] ?? []),
        'fallback' => RoleGuideDefaults::defaultFlowSteps('siswa'),
      ],
      'guide_guru_flow_steps' => [
        'label' => 'Infografis Singkat Guru',
        'description' => 'Langkah infografis singkat pada halaman buku panduan guru.',
        'value' => $this->normalizeFlowSteps($data['guide_guru_flow_steps'] ?? []),
        'fallback' => RoleGuideDefaults::defaultFlowSteps('guru'),
      ],
      'guide_kesiswaan_flow_steps' => [
        'label' => 'Infografis Singkat Kesiswaan',
        'description' => 'Langkah infografis singkat pada halaman buku panduan kesiswaan.',
        'value' => $this->normalizeFlowSteps($data['guide_kesiswaan_flow_steps'] ?? []),
        'fallback' => RoleGuideDefaults::defaultFlowSteps('kesiswaan'),
      ],
    ];

    foreach ($dynamicJsonSettings as $key => $meta) {
      $value = !empty($meta['value']) ? $meta['value'] : $meta['fallback'];

      AppSetting::updateOrCreate(
        ['key' => $key],
        [
          'group' => 'guide',
          'value' => json_encode($value),
          'type' => 'json',
          'label' => $meta['label'],
          'description' => $meta['description'],
        ]
      );
    }

    Notification::make()
      ->title('Buku panduan berhasil disimpan')
      ->body('Waktu update tercatat: ' . now()->format('d M Y H:i'))
      ->success()
      ->send();
  }

  private function getUpdatedAtLabel(string $key): string
  {
    $setting = AppSetting::query()
      ->select(['updated_at'])
      ->where('key', $key)
      ->first();

    return $setting?->updated_at?->format('d M Y H:i') ?? 'Belum pernah diubah';
  }

  /**
   * @param array<int, string> $keys
   */
  private function getLatestUpdatedAtLabel(array $keys): string
  {
    $latest = AppSetting::query()
      ->whereIn('key', $keys)
      ->max('updated_at');

    if (blank($latest)) {
      return 'Belum pernah diubah';
    }

    return \Illuminate\Support\Carbon::parse($latest)->format('d M Y H:i');
  }

  /**
   * @param mixed $contacts
   * @return array<int, array{name: string, phone: string}>
   */
  private function normalizeAdminContacts(mixed $contacts): array
  {
    if (!is_array($contacts)) {
      return [];
    }

    $normalized = [];

    foreach ($contacts as $item) {
      if (!is_array($item)) {
        continue;
      }

      $name = trim((string) ($item['name'] ?? ''));
      $phone = trim((string) ($item['phone'] ?? ''));

      if ($phone === '') {
        continue;
      }

      $normalized[] = [
        'name' => $name !== '' ? $name : 'Admin',
        'phone' => $phone,
      ];
    }

    return $normalized;
  }

  /**
   * @param mixed $steps
   * @return array<int, array{title: string, desc: string}>
   */
  private function normalizeFlowSteps(mixed $steps): array
  {
    if (!is_array($steps)) {
      return [];
    }

    $normalized = [];

    foreach ($steps as $item) {
      if (!is_array($item)) {
        continue;
      }

      $title = trim((string) ($item['title'] ?? ''));
      $desc = trim((string) ($item['desc'] ?? ''));

      if ($title === '' && $desc === '') {
        continue;
      }

      $normalized[] = [
        'title' => $title !== '' ? $title : 'Langkah',
        'desc' => $desc,
      ];
    }

    return $normalized;
  }
}
