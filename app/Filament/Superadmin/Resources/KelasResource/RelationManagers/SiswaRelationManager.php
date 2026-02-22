<?php

namespace App\Filament\Superadmin\Resources\KelasResource\RelationManagers;

use App\Models\RoleUser;
use App\Models\User;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;

class SiswaRelationManager extends RelationManager
{
  protected static string $relationship = 'siswa';
  protected static ?string $title = 'Daftar Siswa';
  protected static ?string $modelLabel = 'Siswa';

  public function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->label('Nama')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('nisn')
          ->label('NISN')
          ->searchable()
          ->copyable(),
        Tables\Columns\TextColumn::make('jenis_kelamin')
          ->label('JK')
          ->badge()
          ->color(fn(?string $state): string => $state === 'L' ? 'info' : 'danger')
          ->formatStateUsing(fn(?string $state): string => match ($state) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-',
          }),
        Tables\Columns\TextColumn::make('agama')
          ->label('Agama')
          ->placeholder('-'),
        Tables\Columns\TextColumn::make('email')
          ->label('Email')
          ->placeholder('-')
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('no_hp')
          ->label('No. HP')
          ->placeholder('-')
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->defaultSort('name', 'asc')
      ->filters([
        Tables\Filters\SelectFilter::make('jenis_kelamin')
          ->label('Jenis Kelamin')
          ->options(['L' => 'Laki-laki', 'P' => 'Perempuan']),
      ])
      ->headerActions([
        Tables\Actions\Action::make('tambahSiswaExisting')
          ->label('Ambil Siswa')
          ->icon('heroicon-o-user-plus')
          ->color('success')
          ->form([
            Forms\Components\Select::make('siswa_ids')
              ->label('Pilih Siswa')
              ->multiple()
              ->searchable()
              ->preload()
              ->options(function () {
                $siswaRole = RoleUser::where('name', 'Siswa')->first();
                return User::where('role_user_id', $siswaRole?->id)
                  ->where(function ($q) {
                    $q->whereNull('kelas_id')
                      ->orWhere('kelas_id', '');
                  })
                  ->orderBy('name')
                  ->get()
                  ->mapWithKeys(fn(User $u) => [
                    $u->id => $u->name . ' (' . $u->nisn . ')',
                  ]);
              })
              ->required()
              ->helperText('Hanya menampilkan siswa yang belum terdaftar di kelas manapun.'),
          ])
          ->action(function (array $data): void {
            $kelas = $this->getOwnerRecord();
            $count = User::whereIn('id', $data['siswa_ids'])->update(['kelas_id' => $kelas->id]);

            Notification::make()
              ->title("Berhasil menambahkan {$count} siswa ke kelas {$kelas->nama}")
              ->success()
              ->send();
          }),

        Tables\Actions\CreateAction::make()
          ->label('Buat Siswa Baru')
          ->icon('heroicon-o-plus')
          ->form($this->getFormSchema())
          ->mutateFormDataUsing(function (array $data): array {
            $siswaRole = RoleUser::where('name', 'Siswa')->first();
            $data['role_user_id'] = $siswaRole?->id;
            $data['password'] = Hash::make($data['password'] ?? $data['nisn']);
            $data['must_change_password'] = true;
            if (empty($data['email'])) {
              $data['email'] = $data['nisn'] . '@siswa.buku-ramadhan.id';
            }
            return $data;
          }),

        Tables\Actions\Action::make('downloadTemplate')
          ->label('Download Template')
          ->icon('heroicon-o-arrow-down-tray')
          ->color('gray')
          ->action(function () {
            $kelas = $this->getOwnerRecord();
            $namaKelas = $kelas->nama ?? '-';

            $filePath = storage_path('app/template_import_siswa.xlsx');

            $writer = new XlsxWriter();
            $writer->openToFile($filePath);

            // ─── Styles ───
            $titleStyle = (new Style())
              ->setFontBold()
              ->setFontSize(14);

            $headerStyle = (new Style())
              ->setFontBold()
              ->setFontSize(11)
              ->setBackgroundColor('1e3a5f')
              ->setFontColor('ffffff');

            $infoStyle = (new Style())
              ->setFontSize(10)
              ->setFontColor('666666');

            $exampleStyle = (new Style())
              ->setFontSize(10)
              ->setFontColor('2563eb');

            // ═══════════════════════════════════════════
            // Sheet 1: DATA SISWA
            // ═══════════════════════════════════════════
            $writer->getCurrentSheet()->setName('Data Siswa');

            $writer->addRow(Row::fromValues([
              'TEMPLATE IMPORT DATA SISWA — Buku Ramadhan SMKN 1 Ciamis',
              '',
              '',
              '',
              '',
            ], $titleStyle));

            $writer->addRow(Row::fromValues([
              'Kelas: ' . $namaKelas,
              '',
              'Tanggal: ' . now()->format('d/m/Y'),
              '',
              '',
            ], $infoStyle));

            $writer->addRow(Row::fromValues([
              'PETUNJUK: Isi data mulai baris ke-6. Baris 5-6 adalah contoh (hapus sebelum import). Kelas otomatis diisi sesuai kelas ini.',
            ], $infoStyle));

            $writer->addRow(Row::fromValues(['']));

            $writer->addRow(Row::fromValues([
              'NO',
              'NAMA LENGKAP *',
              'NISN *',
              'JENIS KELAMIN (L/P) *',
              'AGAMA *',
              'EMAIL (opsional)',
              'PASSWORD (opsional)',
            ], $headerStyle));

            $writer->addRow(Row::fromValues([
              '1',
              'Ahmad Fauzi',
              '0012345678',
              'L',
              'Islam',
              '',
              '',
            ], $exampleStyle));

            $writer->addRow(Row::fromValues([
              '2',
              'Siti Aisyah',
              '0012345679',
              'P',
              'Islam',
              'custom@email.com',
              'password123',
            ], $exampleStyle));

            // ═══════════════════════════════════════════
            // Sheet 2: PETUNJUK PENGISIAN
            // ═══════════════════════════════════════════
            $sheet2 = $writer->addNewSheetAndMakeItCurrent();
            $sheet2->setName('Petunjuk Pengisian');

            $writer->addRow(Row::fromValues([
              'PETUNJUK PENGISIAN TEMPLATE IMPORT SISWA',
            ], $titleStyle));
            $writer->addRow(Row::fromValues(['']));

            $writer->addRow(Row::fromValues([
              'INFO KELAS',
            ], (new Style())->setFontBold()->setFontSize(11)->setFontColor('2563eb')));
            $writer->addRow(Row::fromValues([
              'Semua siswa yang diimport akan otomatis masuk ke kelas: ' . $namaKelas,
            ]));
            $writer->addRow(Row::fromValues(['']));

            $guideRows = [
              ['Kolom', 'Keterangan', 'Wajib?', 'Contoh'],
              ['NAMA LENGKAP', 'Nama lengkap siswa', 'YA', 'Ahmad Fauzi'],
              ['NISN', 'Nomor Induk Siswa Nasional (10 digit). Digunakan sebagai username & default password.', 'YA', '0012345678'],
              ['JENIS KELAMIN', 'Isi L (Laki-laki) atau P (Perempuan). Huruf kapital.', 'YA', 'L'],
              ['AGAMA', 'Pilih salah satu: Islam, Kristen, Katolik, Hindu, Buddha, Konghucu', 'YA', 'Islam'],
              ['EMAIL', 'Email siswa. Jika dikosongkan, otomatis: nisn@siswa.buku-ramadhan.id', 'TIDAK', 'siswa@email.com'],
              ['PASSWORD', 'Password akun. Jika dikosongkan, default = NISN siswa', 'TIDAK', ''],
            ];

            $writer->addRow(Row::fromValues($guideRows[0], $headerStyle));
            for ($i = 1; $i < count($guideRows); $i++) {
              $writer->addRow(Row::fromValues($guideRows[$i]));
            }

            $writer->addRow(Row::fromValues(['']));
            $writer->addRow(Row::fromValues([
              'CATATAN PENTING:',
            ], (new Style())->setFontBold()->setFontSize(11)->setFontColor('dc2626')));

            $notes = [
              ['1.', 'Kolom bertanda * wajib diisi. Jika kosong, baris akan dilewati.'],
              ['2.', 'NISN harus unik. Jika sudah terdaftar di sistem, baris akan dilewati.'],
              ['3.', 'Kelas OTOMATIS diisi sesuai kelas yang sedang diedit. Tidak perlu mengisi kelas.'],
              ['4.', 'Hapus baris contoh (biru) di sheet "Data Siswa" sebelum import.'],
              ['5.', 'Akun siswa otomatis dibuat. Login menggunakan NISN sebagai username.'],
              ['6.', 'Agama yang valid: Islam, Kristen, Katolik, Hindu, Buddha, Konghucu'],
            ];

            foreach ($notes as $note) {
              $writer->addRow(Row::fromValues($note));
            }

            $writer->close();

            return response()->download($filePath, 'template_import_siswa.xlsx')->deleteFileAfterSend(true);
          }),

        Tables\Actions\Action::make('importExcel')
          ->label('Import Excel')
          ->icon('heroicon-o-arrow-up-tray')
          ->color('success')
          ->form([
            Forms\Components\FileUpload::make('file')
              ->label('File Excel (.xlsx)')
              ->acceptedFileTypes([
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
              ])
              ->required()
              ->disk('local')
              ->directory('imports')
              ->helperText('Upload file sesuai template. Kolom wajib: Nama, NISN, Jenis Kelamin, Agama. Kelas otomatis diisi.'),
          ])
          ->modalHeading('Import Data Siswa')
          ->modalDescription('Pastikan file Excel sesuai dengan template yang disediakan. Hapus baris contoh (biru) terlebih dahulu.')
          ->modalSubmitActionLabel('Import')
          ->action(function (array $data) {
            $filePath = storage_path('app/' . $data['file']);

            if (!file_exists($filePath)) {
              Notification::make()
                ->title('File tidak ditemukan')
                ->danger()
                ->send();
              return;
            }

            $kelas = $this->getOwnerRecord();
            $siswaRole = RoleUser::where('name', 'Siswa')->first();

            if (!$siswaRole) {
              Notification::make()
                ->title('Role Siswa tidak ditemukan di database')
                ->danger()
                ->send();
              return;
            }

            $reader = new XlsxReader();
            $reader->open($filePath);

            $imported = 0;
            $skipped = 0;
            $errors = [];
            $rowNum = 0;
            $headerRowFound = false;

            foreach ($reader->getSheetIterator() as $sheet) {
              if ($sheet->getIndex() !== 0) {
                continue;
              }

              foreach ($sheet->getRowIterator() as $row) {
                $rowNum++;
                $cells = $row->toArray();

                if (!$headerRowFound) {
                  $firstCells = array_map(fn($c) => strtolower(trim((string) $c)), $cells);
                  $joined = implode('|', $firstCells);
                  if (str_contains($joined, 'nama lengkap') || str_contains($joined, 'nama_lengkap') || str_contains($joined, 'nisn')) {
                    $headerRowFound = true;
                  }
                  continue;
                }

                if (empty(array_filter($cells, fn($c) => trim((string) $c) !== ''))) {
                  continue;
                }

                $nama = trim((string) ($cells[1] ?? ''));
                $nisn = trim((string) ($cells[2] ?? ''));
                $jk = strtoupper(trim((string) ($cells[3] ?? '')));
                $agama = trim((string) ($cells[4] ?? ''));
                $email = trim((string) ($cells[5] ?? ''));
                $password = trim((string) ($cells[6] ?? ''));

                if (empty($nama) || empty($nisn) || empty($jk) || empty($agama)) {
                  $errors[] = "Baris {$rowNum}: Data wajib tidak lengkap (nama/nisn/jk/agama).";
                  $skipped++;
                  continue;
                }

                if (!in_array($jk, ['L', 'P'])) {
                  $errors[] = "Baris {$rowNum}: Jenis kelamin '{$jk}' tidak valid (harus L/P).";
                  $skipped++;
                  continue;
                }

                $validAgama = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
                $matchedAgama = collect($validAgama)->first(fn($a) => strtolower($a) === strtolower($agama));
                if (!$matchedAgama) {
                  $errors[] = "Baris {$rowNum}: Agama '{$agama}' tidak valid.";
                  $skipped++;
                  continue;
                }

                if (User::where('nisn', $nisn)->exists()) {
                  $errors[] = "Baris {$rowNum}: NISN '{$nisn}' ({$nama}) sudah terdaftar.";
                  $skipped++;
                  continue;
                }

                if (empty($email)) {
                  $email = $nisn . '@siswa.buku-ramadhan.id';
                }

                if (User::where('email', $email)->exists()) {
                  $errors[] = "Baris {$rowNum}: Email '{$email}' sudah terdaftar.";
                  $skipped++;
                  continue;
                }

                User::create([
                  'name' => $nama,
                  'nisn' => $nisn,
                  'email' => $email,
                  'jenis_kelamin' => $jk,
                  'agama' => $matchedAgama,
                  'kelas_id' => $kelas->id,
                  'password' => Hash::make($password ?: $nisn),
                  'role_user_id' => $siswaRole->id,
                  'must_change_password' => true,
                ]);

                $imported++;
              }

              break;
            }

            $reader->close();
            @unlink($filePath);

            $message = "Berhasil import {$imported} siswa ke kelas {$kelas->nama}.";
            if ($skipped > 0) {
              $message .= " {$skipped} baris dilewati.";
            }

            $notification = Notification::make()->title($message);

            if (!empty($errors)) {
              $notification->body(implode("\n", array_slice($errors, 0, 8)))
                ->warning();
            } else {
              $notification->success();
            }

            $notification->send();
          }),
      ])
      ->actions([
        Tables\Actions\ActionGroup::make([
          Tables\Actions\EditAction::make()
            ->form($this->getFormSchema()),
          Tables\Actions\Action::make('keluarkan')
            ->label('Keluarkan dari Kelas')
            ->icon('heroicon-o-arrow-right-start-on-rectangle')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Keluarkan Siswa dari Kelas')
            ->modalDescription(fn($record) => "Apakah Anda yakin ingin mengeluarkan {$record->name} dari kelas ini? Siswa tidak akan dihapus, hanya dikeluarkan dari kelas.")
            ->action(function ($record): void {
              $record->update(['kelas_id' => null]);
              Notification::make()
                ->title("{$record->name} telah dikeluarkan dari kelas")
                ->success()
                ->send();
            }),
          Tables\Actions\DeleteAction::make(),
        ]),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\BulkAction::make('keluarkanBulk')
            ->label('Keluarkan dari Kelas')
            ->icon('heroicon-o-arrow-right-start-on-rectangle')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Keluarkan Siswa dari Kelas')
            ->modalDescription('Apakah Anda yakin ingin mengeluarkan siswa yang dipilih dari kelas ini?')
            ->action(function (\Illuminate\Database\Eloquent\Collection $records): void {
              $count = $records->count();
              $records->each(fn($record) => $record->update(['kelas_id' => null]));
              Notification::make()
                ->title("{$count} siswa telah dikeluarkan dari kelas")
                ->success()
                ->send();
            })
            ->deselectRecordsAfterCompletion(),
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  protected function getFormSchema(): array
  {
    return [
      Forms\Components\TextInput::make('name')
        ->label('Nama Lengkap')
        ->required()
        ->maxLength(255),
      Forms\Components\TextInput::make('nisn')
        ->label('NISN')
        ->required()
        ->regex('/^\d{10}$/')
        ->maxLength(10)
        ->unique(ignoreRecord: true)
        ->extraInputAttributes([
          'maxlength' => 10,
          'inputmode' => 'numeric',
          'pattern' => '[0-9]*',
          'oninput' => "this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)",
        ]),
      Forms\Components\Select::make('jenis_kelamin')
        ->label('Jenis Kelamin')
        ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
        ->required(),
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
        ->required(),
      Forms\Components\TextInput::make('email')
        ->label('Email')
        ->email()
        ->helperText('Kosongkan untuk auto-generate dari NISN'),
      Forms\Components\TextInput::make('password')
        ->label('Password')
        ->password()
        ->dehydrateStateUsing(fn(?string $state): ?string => $state ? Hash::make($state) : null)
        ->dehydrated(fn(?string $state): bool => filled($state))
        ->helperText('Kosongkan untuk menggunakan NISN sebagai password'),
      Forms\Components\TextInput::make('no_hp')
        ->label('No. HP')
        ->tel()
        ->maxLength(20),
    ];
  }
}
