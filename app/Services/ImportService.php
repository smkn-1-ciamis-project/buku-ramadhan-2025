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
            if (empty($nama)) {
                continue;
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
}
