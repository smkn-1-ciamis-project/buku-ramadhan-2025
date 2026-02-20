<?php

namespace App\Filament\Guru\Resources\SiswaResource\Pages;

use App\Filament\Guru\Resources\SiswaResource;
use App\Models\Kelas;
use App\Models\RoleUser;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

class ListSiswa extends ListRecords
{
  protected static string $resource = SiswaResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make()
        ->label('Tambah Siswa')
        ->icon('heroicon-o-plus'),

      Actions\Action::make('downloadTemplate')
        ->label('Download Template')
        ->icon('heroicon-o-arrow-down-tray')
        ->color('gray')
        ->action(function () {
          $guru = Auth::user();
          $kelas = Kelas::where('wali_id', $guru->id)->first();
          $namaKelas = $kelas?->nama ?? '-';

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
          // Sheet 1: DATA SISWA (template utama)
          // ═══════════════════════════════════════════
          $writer->getCurrentSheet()->setName('Data Siswa');

          // Title row
          $writer->addRow(Row::fromValues([
            'TEMPLATE IMPORT DATA SISWA — Buku Ramadhan SMKN 1 Ciamis',
            '',
            '',
            '',
            '',
          ], $titleStyle));

          // Info rows
          $writer->addRow(Row::fromValues([
            'Wali Kelas: ' . $guru->name,
            '',
            'Kelas: ' . $namaKelas,
            '',
            'Tanggal: ' . now()->format('d/m/Y'),
          ], $infoStyle));

          $writer->addRow(Row::fromValues([
            'PETUNJUK: Isi data mulai baris ke-6. Baris 5-6 adalah contoh (hapus sebelum import). Kelas otomatis diisi sesuai kelas Anda.',
          ], $infoStyle));

          // Empty row separator
          $writer->addRow(Row::fromValues(['']));

          // Header row (row 5 in Excel)
          $writer->addRow(Row::fromValues([
            'NO',
            'NAMA LENGKAP *',
            'NISN *',
            'JENIS KELAMIN (L/P) *',
            'AGAMA *',
            'EMAIL (opsional)',
            'PASSWORD (opsional)',
          ], $headerStyle));

          // Example rows
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
          $writer->addRow(Row::fromValues([
            'Wali kelas: ' . $guru->name,
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
            ['3.', 'Kelas OTOMATIS diisi sesuai kelas yang Anda walikan. Tidak perlu mengisi kelas.'],
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

      Actions\Action::make('importExcel')
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

          $guru = Auth::user();
          $kelas = Kelas::where('wali_id', $guru->id)->first();
          $siswaRole = RoleUser::where('name', 'Siswa')->first();

          if (!$kelas) {
            Notification::make()
              ->title('Anda belum ditugaskan sebagai wali kelas')
              ->danger()
              ->send();
            return;
          }

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
            // Only read the first sheet
            if ($sheet->getIndex() !== 0) {
              continue;
            }

            foreach ($sheet->getRowIterator() as $row) {
              $rowNum++;
              $cells = $row->toArray();

              // Skip rows until we find the header row
              if (!$headerRowFound) {
                $firstCells = array_map(fn($c) => strtolower(trim((string) $c)), $cells);
                $joined = implode('|', $firstCells);
                if (str_contains($joined, 'nama lengkap') || str_contains($joined, 'nama_lengkap') || str_contains($joined, 'nisn')) {
                  $headerRowFound = true;
                }
                continue;
              }

              // Skip empty rows
              if (empty(array_filter($cells, fn($c) => trim((string) $c) !== ''))) {
                continue;
              }

              // Template columns: NO, NAMA, NISN, JK, AGAMA, EMAIL, PASSWORD
              $nama = trim((string) ($cells[1] ?? ''));
              $nisn = trim((string) ($cells[2] ?? ''));
              $jk = strtoupper(trim((string) ($cells[3] ?? '')));
              $agama = trim((string) ($cells[4] ?? ''));
              $email = trim((string) ($cells[5] ?? ''));
              $password = trim((string) ($cells[6] ?? ''));

              // Validate required fields
              if (empty($nama) || empty($nisn) || empty($jk) || empty($agama)) {
                $errors[] = "Baris {$rowNum}: Data wajib tidak lengkap (nama/nisn/jk/agama).";
                $skipped++;
                continue;
              }

              // Validate jenis_kelamin
              if (!in_array($jk, ['L', 'P'])) {
                $errors[] = "Baris {$rowNum}: Jenis kelamin '{$jk}' tidak valid (harus L/P).";
                $skipped++;
                continue;
              }

              // Validate agama
              $validAgama = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
              $matchedAgama = collect($validAgama)->first(fn($a) => strtolower($a) === strtolower($agama));
              if (!$matchedAgama) {
                $errors[] = "Baris {$rowNum}: Agama '{$agama}' tidak valid.";
                $skipped++;
                continue;
              }

              // Check duplicate NISN
              if (User::where('nisn', $nisn)->exists()) {
                $errors[] = "Baris {$rowNum}: NISN '{$nisn}' ({$nama}) sudah terdaftar.";
                $skipped++;
                continue;
              }

              // Auto-generate email from NISN if empty
              if (empty($email)) {
                $email = $nisn . '@siswa.buku-ramadhan.id';
              }

              // Check duplicate email
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
              ]);

              $imported++;
            }

            break; // Only first sheet
          }

          $reader->close();

          // Clean up uploaded file
          @unlink($filePath);

          $message = "Berhasil import {$imported} siswa.";
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
    ];
  }
}
