<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportService
{
    /**
     * Import siswa from Excel file.
     *
     * @return array{success: int, failed: int, errors: array}
     */
    public static function importSiswa(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $roleId = RoleUser::where('name', 'Siswa')->first()?->id;

        if (!$roleId) {
            return ['success' => 0, 'failed' => 0, 'errors' => ['Role Siswa tidak ditemukan di database.']];
        }

        $success = 0;
        $failed = 0;
        $errors = [];

        // Skip header row (row 1)
        $isFirstRow = true;
        foreach ($rows as $rowIndex => $row) {
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            $nama = trim($row['A'] ?? '');
            $nisn = trim($row['B'] ?? '');
            $jk = strtoupper(trim($row['C'] ?? ''));
            $agama = trim($row['D'] ?? '');
            $kelas = trim($row['E'] ?? '');
            $noHp = trim($row['F'] ?? '');
            $password = trim($row['G'] ?? 'siswa123');

            // Skip empty rows
            if (empty($nama) && empty($nisn)) {
                continue;
            }

            // Validate
            $validator = Validator::make([
                'nama' => $nama,
                'nisn' => $nisn,
                'jenis_kelamin' => $jk,
                'agama' => $agama,
            ], [
                'nama' => 'required|string|max:255',
                'nisn' => 'required|string|max:10',
                'jenis_kelamin' => 'required|in:L,P',
                'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            ]);

            if ($validator->fails()) {
                $failed++;
                $errors[] = "Baris {$rowIndex}: " . implode(', ', $validator->errors()->all());
                continue;
            }

            // Check duplicate NISN
            if (User::where('nisn', $nisn)->exists()) {
                $failed++;
                $errors[] = "Baris {$rowIndex}: NISN {$nisn} sudah terdaftar.";
                continue;
            }

            // Find kelas
            $kelasId = null;
            if (!empty($kelas)) {
                $kelasModel = Kelas::where('nama', 'like', "%{$kelas}%")->first();
                $kelasId = $kelasModel?->id;
            }

            // Generate email from NISN
            $email = $nisn . '@siswa.smkn1ciamis.sch.id';

            try {
                User::create([
                    'name' => $nama,
                    'nisn' => $nisn,
                    'email' => $email,
                    'jenis_kelamin' => $jk,
                    'agama' => $agama,
                    'kelas_id' => $kelasId,
                    'no_hp' => $noHp ?: null,
                    'password' => $password ?: 'siswa123',
                    'must_change_password' => true,
                    'role_user_id' => $roleId,
                    'email_verified_at' => now(),
                ]);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Baris {$rowIndex}: " . $e->getMessage();
            }
        }

        return compact('success', 'failed', 'errors');
    }

    /**
     * Import guru from Excel file.
     *
     * @return array{success: int, failed: int, errors: array}
     */
    public static function importGuru(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $roleId = RoleUser::where('name', 'Guru')->first()?->id;

        if (!$roleId) {
            return ['success' => 0, 'failed' => 0, 'errors' => ['Role Guru tidak ditemukan di database.']];
        }

        $success = 0;
        $failed = 0;
        $errors = [];

        // Skip header row (row 1)
        $isFirstRow = true;
        foreach ($rows as $rowIndex => $row) {
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            $nama = trim($row['A'] ?? '');
            $email = trim($row['B'] ?? '');
            $jk = strtoupper(trim($row['C'] ?? ''));
            $agama = trim($row['D'] ?? '');
            $noHp = trim($row['E'] ?? '');
            $password = trim($row['F'] ?? 'guru123');

            // Skip empty rows
            if (empty($nama) && empty($email)) {
                continue;
            }

            // Validate
            $validator = Validator::make([
                'nama' => $nama,
                'email' => $email,
                'jenis_kelamin' => $jk,
            ], [
                'nama' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'jenis_kelamin' => 'required|in:L,P',
            ]);

            if ($validator->fails()) {
                $failed++;
                $errors[] = "Baris {$rowIndex}: " . implode(', ', $validator->errors()->all());
                continue;
            }

            // Check duplicate email
            if (User::where('email', $email)->exists()) {
                $failed++;
                $errors[] = "Baris {$rowIndex}: Email {$email} sudah terdaftar.";
                continue;
            }

            try {
                User::create([
                    'name' => $nama,
                    'email' => $email,
                    'jenis_kelamin' => $jk,
                    'agama' => $agama ?: null,
                    'no_hp' => $noHp ?: null,
                    'password' => $password ?: 'guru123',
                    'role_user_id' => $roleId,
                    'email_verified_at' => now(),
                ]);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Baris {$rowIndex}: " . $e->getMessage();
            }
        }

        return compact('success', 'failed', 'errors');
    }
}
