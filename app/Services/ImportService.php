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
     * Normalize phone number — restore leading '0' stripped by Excel.
     */
    private static function normalizeNoHp(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }
        // Remove any non-digit characters
        $digits = preg_replace('/[^0-9]/', '', $value);
        if (empty($digits)) {
            return null;
        }
        // Indonesian mobile numbers: if starts with 8 and length 9-13, prepend 0
        if (str_starts_with($digits, '8') && strlen($digits) >= 9 && strlen($digits) <= 13) {
            $digits = '0' . $digits;
        }
        // If starts with 62 (country code), replace with 0
        if (str_starts_with($digits, '62') && strlen($digits) >= 11) {
            $digits = '0' . substr($digits, 2);
        }
        return $digits;
    }
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
            $noHp = self::normalizeNoHp($row['F'] ?? '');
            $password = trim($row['G'] ?? 'siswa123');

            // Skip empty rows
            if (empty($nama) && empty($nisn)) {
                continue;
            }

            // Normalize agama alias (e.g. Budha → Buddha, Khonghucu → Konghucu)
            $agama = \App\Models\User::normalizeAgama($agama) ?? $agama;

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
            $noHp = self::normalizeNoHp($row['E'] ?? '');
            $password = trim($row['F'] ?? 'guru123');

            // Skip empty rows
            if (empty($nama)) {
                continue;
            }

            // Normalize agama alias (Budha → Buddha, Khonghucu → Konghucu)
            if (!empty($agama)) {
                $agama = \App\Models\User::normalizeAgama($agama) ?? $agama;
            }

            // Auto-generate email from name if not provided
            if (empty($email)) {
                $email = self::generateGuruEmail($nama);
            }

            // Validate
            $validator = Validator::make([
                'nama' => $nama,
                'email' => $email,
                'jenis_kelamin' => $jk,
                'agama' => $agama,
            ], [
                'nama' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'jenis_kelamin' => 'required|in:L,P',
                'agama' => 'nullable|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
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

    /**
     * Generate email from guru name.
     * E.g. "Drs. Ahmad Suryadi, M.Pd" => "ahmad.suryadi@smkn1ciamis.sch.id"
     */
    private static function generateGuruEmail(string $nama): string
    {
        // Remove common titles/degrees
        $titles = [
            'Prof\.?',
            'Dr\.?',
            'Drs\.?',
            'Dra\.?',
            'Ir\.?',
            'Hj\.?',
            'H\.?',
            'S\.?Pd\.?',
            'S\.?Kom\.?',
            'S\.?E\.?',
            'S\.?Ag\.?',
            'S\.?Sos\.?',
            'S\.?H\.?',
            'S\.?T\.?',
            'S\.?Si\.?',
            'S\.?I\.?P\.?',
            'M\.?Pd\.?',
            'M\.?Kom\.?',
            'M\.?M\.?',
            'M\.?Si\.?',
            'M\.?Ag\.?',
            'M\.?T\.?',
            'M\.?A\.?',
            'M\.?Sc\.?',
            'M\.?Eng\.?',
            'M\.?Ed\.?',
            'M\.?Hum\.?',
            'M\.?Kes\.?',
            'Ph\.?D\.?',
            'MBA'
        ];
        $cleaned = $nama;
        foreach ($titles as $title) {
            $cleaned = preg_replace('/\b' . $title . '\b/i', '', $cleaned);
        }

        // Remove commas, dots, extra spaces
        $cleaned = preg_replace('/[,\.]+/', ' ', $cleaned);
        $cleaned = preg_replace('/\s+/', ' ', trim($cleaned));

        // Convert to lowercase slug: "Ahmad Suryadi" => "ahmad.suryadi"
        $parts = explode(' ', strtolower($cleaned));
        $parts = array_filter($parts, fn($p) => strlen($p) > 0);
        $slug = implode('.', $parts);

        // Remove non-alphanumeric (except dots)
        $slug = preg_replace('/[^a-z0-9\.]/', '', $slug);

        $baseEmail = $slug . '@smkn1ciamis.sch.id';

        // Ensure uniqueness
        $email = $baseEmail;
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = $slug . $counter . '@smkn1ciamis.sch.id';
            $counter++;
        }

        return $email;
    }

    /**
     * Import kelas + guru (wali) + siswa from multi-sheet Excel file.
     *
     * Step 1: Sheet "Data Guru" — create guru accounts first
     * Step 2: Sheet "Daftar Kelas" — create kelas + auto-assign wali by email
     * Step 3: Other sheets (nama = nama kelas) — create siswa per kelas
     *
     * @return array{guru_created: int, kelas_created: int, siswa_created: int, wali_assigned: int, failed: int, errors: array}
     */
    public static function importKelas(string $filePath): array
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);

        $result = [
            'guru_created' => 0,
            'kelas_created' => 0,
            'siswa_created' => 0,
            'wali_assigned' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $roleGuruId = RoleUser::where('name', 'Guru')->first()?->id;
        $roleSiswaId = RoleUser::where('name', 'Siswa')->first()?->id;

        if (!$roleSiswaId) {
            $result['errors'][] = 'Role Siswa tidak ditemukan di database.';
            return $result;
        }
        if (!$roleGuruId) {
            $result['errors'][] = 'Role Guru tidak ditemukan di database.';
            return $result;
        }

        // ══════════════════════════════════════════════
        // STEP 1: Import Guru dari sheet "Data Guru"
        // ══════════════════════════════════════════════
        $guruSheet = null;
        foreach ($spreadsheet->getSheetNames() as $name) {
            if (mb_strtolower(trim($name)) === 'data guru') {
                $guruSheet = $spreadsheet->getSheetByName($name);
                break;
            }
        }

        if ($guruSheet) {
            $guruRows = $guruSheet->toArray(null, true, true, true);
            $isFirstRow = true;

            foreach ($guruRows as $rowIndex => $row) {
                if ($isFirstRow) {
                    $isFirstRow = false;
                    continue;
                }

                $nama = trim($row['A'] ?? '');
                $email = trim($row['B'] ?? '');
                $jk = strtoupper(trim($row['C'] ?? ''));
                $agama = trim($row['D'] ?? '');
                $noHp = self::normalizeNoHp($row['E'] ?? '');
                $password = trim($row['F'] ?? 'guru123');

                if (empty($nama)) {
                    continue;
                }

                // Normalize agama alias (Budha → Buddha, Khonghucu → Konghucu)
                if (!empty($agama)) {
                    $agama = \App\Models\User::normalizeAgama($agama) ?? $agama;
                }

                // Auto-generate email if empty
                if (empty($email)) {
                    $email = self::generateGuruEmail($nama);
                }

                // Validate
                $validator = Validator::make([
                    'nama' => $nama,
                    'email' => $email,
                    'jenis_kelamin' => $jk,
                    'agama' => $agama,
                ], [
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'jenis_kelamin' => 'required|in:L,P',
                    'agama' => 'nullable|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
                ]);

                if ($validator->fails()) {
                    $result['failed']++;
                    $result['errors'][] = "Sheet \"Data Guru\" baris {$rowIndex}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                // Skip if email already exists
                if (User::where('email', $email)->exists()) {
                    // Not an error — guru already registered
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
                        'role_user_id' => $roleGuruId,
                        'email_verified_at' => now(),
                    ]);
                    $result['guru_created']++;
                } catch (\Exception $e) {
                    $result['failed']++;
                    $result['errors'][] = "Sheet \"Data Guru\" baris {$rowIndex}: " . $e->getMessage();
                }
            }
        }

        // ══════════════════════════════════════════════
        // STEP 2: Import Kelas dari sheet "Daftar Kelas"
        // ══════════════════════════════════════════════
        $kelasSheet = null;
        foreach ($spreadsheet->getSheetNames() as $name) {
            if (mb_strtolower(trim($name)) === 'daftar kelas') {
                $kelasSheet = $spreadsheet->getSheetByName($name);
                break;
            }
        }

        if (!$kelasSheet) {
            $result['errors'][] = 'Sheet "Daftar Kelas" tidak ditemukan dalam file.';
            return $result;
        }

        $kelasRows = $kelasSheet->toArray(null, true, true, true);
        $kelasMap = [];
        $isFirstRow = true;

        foreach ($kelasRows as $rowIndex => $row) {
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            $namaKelas = trim($row['A'] ?? '');
            $emailWali = trim($row['B'] ?? '');

            if (empty($namaKelas)) {
                continue;
            }

            // Check if kelas already exists
            $existing = Kelas::where('nama', $namaKelas)->first();
            if ($existing) {
                $kelasMap[$namaKelas] = ['kelas' => $existing, 'existing' => true];
                $result['errors'][] = "Kelas \"{$namaKelas}\" sudah ada, siswa akan ditambahkan ke kelas ini.";
            } else {
                try {
                    $kelas = Kelas::create(['nama' => $namaKelas]);
                    $kelasMap[$namaKelas] = ['kelas' => $kelas, 'existing' => false];
                    $result['kelas_created']++;
                } catch (\Exception $e) {
                    $result['failed']++;
                    $result['errors'][] = "Gagal membuat kelas \"{$namaKelas}\": " . $e->getMessage();
                    continue;
                }
            }

            // Assign wali guru by email
            if (!empty($emailWali)) {
                $guru = User::where('email', $emailWali)
                    ->where('role_user_id', $roleGuruId)
                    ->first();

                if ($guru) {
                    $kelasModel = $kelasMap[$namaKelas]['kelas'];
                    $kelasModel->update(['wali_id' => $guru->id]);
                    $result['wali_assigned']++;
                } else {
                    $result['errors'][] = "Wali kelas \"{$namaKelas}\": guru dengan email \"{$emailWali}\" tidak ditemukan.";
                }
            }
        }

        // ══════════════════════════════════════════════
        // STEP 3: Import Siswa dari sheet per kelas
        // ══════════════════════════════════════════════
        $sheetNames = $spreadsheet->getSheetNames();
        $skipSheets = ['daftar kelas', 'data guru', 'petunjuk pengisian', 'petunjuk'];

        foreach ($sheetNames as $sheetName) {
            $normalizedName = mb_strtolower(trim($sheetName));

            if (in_array($normalizedName, $skipSheets)) {
                continue;
            }

            // Find matching kelas
            $kelasEntry = $kelasMap[$sheetName] ?? null;
            if (!$kelasEntry) {
                foreach ($kelasMap as $kName => $kEntry) {
                    if (mb_strtolower(trim($kName)) === $normalizedName) {
                        $kelasEntry = $kEntry;
                        break;
                    }
                }
            }

            if (!$kelasEntry) {
                $result['errors'][] = "Sheet \"{$sheetName}\" tidak cocok dengan kelas manapun di \"Daftar Kelas\". Dilewati.";
                continue;
            }

            $kelasModel = $kelasEntry['kelas'];
            $siswaSheet = $spreadsheet->getSheetByName($sheetName);
            $siswaRows = $siswaSheet->toArray(null, true, true, true);

            $isFirstRow = true;
            foreach ($siswaRows as $rowIndex => $row) {
                if ($isFirstRow) {
                    $isFirstRow = false;
                    continue;
                }

                $nama = trim($row['A'] ?? '');
                $nisn = trim($row['B'] ?? '');
                $jk = strtoupper(trim($row['C'] ?? ''));
                $agama = trim($row['D'] ?? '');
                $noHp = self::normalizeNoHp($row['E'] ?? '');
                $password = trim($row['F'] ?? 'siswa123');

                if (empty($nama) && empty($nisn)) {
                    continue;
                }

                // Normalize agama alias (Budha → Buddha, Khonghucu → Konghucu)
                $agama = \App\Models\User::normalizeAgama($agama) ?? $agama;

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
                    $result['failed']++;
                    $result['errors'][] = "Sheet \"{$sheetName}\" baris {$rowIndex}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                if (strlen($nisn) > 10) {
                    $result['failed']++;
                    $result['errors'][] = "Sheet \"{$sheetName}\" baris {$rowIndex}: NISN \"{$nisn}\" melebihi 10 digit.";
                    continue;
                }

                // Check duplicate NISN — update kelas if exists
                if (User::where('nisn', $nisn)->exists()) {
                    $existingSiswa = User::where('nisn', $nisn)->first();
                    if ($existingSiswa->kelas_id !== $kelasModel->id) {
                        $existingSiswa->update(['kelas_id' => $kelasModel->id]);
                        $result['errors'][] = "Sheet \"{$sheetName}\" baris {$rowIndex}: NISN {$nisn} sudah ada, dipindahkan ke kelas \"{$sheetName}\".";
                    }
                    continue;
                }

                $email = $nisn . '@siswa.smkn1ciamis.sch.id';

                try {
                    User::create([
                        'name' => $nama,
                        'nisn' => $nisn,
                        'email' => $email,
                        'jenis_kelamin' => $jk,
                        'agama' => $agama,
                        'kelas_id' => $kelasModel->id,
                        'no_hp' => $noHp ?: null,
                        'password' => $password ?: 'siswa123',
                        'must_change_password' => true,
                        'role_user_id' => $roleSiswaId,
                        'email_verified_at' => now(),
                    ]);
                    $result['siswa_created']++;
                } catch (\Exception $e) {
                    $result['failed']++;
                    $result['errors'][] = "Sheet \"{$sheetName}\" baris {$rowIndex}: " . $e->getMessage();
                }
            }
        }

        return $result;
    }

    /**
     * Import kesiswaan from Excel file.
     *
     * @return array{success: int, failed: int, errors: array}
     */
    public static function importKesiswaan(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $roleId = RoleUser::where('name', 'Kesiswaan')->first()?->id;

        if (!$roleId) {
            return ['success' => 0, 'failed' => 0, 'errors' => ['Role Kesiswaan tidak ditemukan di database.']];
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
            $noHp = self::normalizeNoHp($row['D'] ?? '');
            $password = trim($row['E'] ?? '');

            // Skip empty rows
            if (empty($nama) && empty($email)) {
                continue;
            }

            // Auto-generate email if not provided
            if (empty($email)) {
                $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '.', trim($nama)));
                $slug = trim($slug, '.');
                $email = $slug . '@kesiswaan.smkn1ciamis.sch.id';
                $counter = 1;
                while (User::where('email', $email)->exists()) {
                    $email = $slug . $counter . '@kesiswaan.smkn1ciamis.sch.id';
                    $counter++;
                }
            }

            // Default password = email
            if (empty($password)) {
                $password = $email;
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
                    'no_hp' => $noHp ?: null,
                    'password' => $password,
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
