<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\RoleUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KelasSeeder extends Seeder
{
  /**
   * Seed kelas, guru wali, dan siswa.
   */
  public function run(): void
  {
    $guruRole = RoleUser::where('name', 'Guru')->first();
    $siswaRole = RoleUser::where('name', 'Siswa')->first();

    if (!$guruRole || !$siswaRole) {
      $this->command->error('Role Guru/Siswa belum ada. Jalankan RoleUserSeeder terlebih dahulu.');
      return;
    }

    $data = $this->getKelasData();

    foreach ($data as $kelasData) {
      // 1. Cari atau buat akun guru wali
      $guru = User::where('email', $kelasData['wali_email'])->first();

      if (!$guru) {
        $guru = User::create([
          'name'              => $kelasData['wali_nama'],
          'email'             => $kelasData['wali_email'],
          'nisn'              => null,
          'role_user_id'      => $guruRole->id,
          'jenis_kelamin'     => $kelasData['wali_jk'] ?? null,
          'email_verified_at' => now(),
          'password'          => Hash::make('guru123'),
        ]);
      }

      // 2. Buat kelas
      $kelas = Kelas::where('nama', $kelasData['nama'])->first();

      if (!$kelas) {
        $kelas = Kelas::create([
          'nama'    => $kelasData['nama'],
          'wali_id' => $guru->id,
        ]);
      } else {
        $kelas->update(['wali_id' => $guru->id]);
      }

      // 3. Buat akun siswa
      foreach ($kelasData['siswa'] as $siswaData) {
        $exists = User::where('nisn', $siswaData['nisn'])->first();

        if (!$exists) {
          User::create([
            'name'              => $siswaData['nama'],
            'email'             => $this->generateEmail($siswaData['nisn']),
            'nisn'              => $siswaData['nisn'],
            'jenis_kelamin'     => $siswaData['jk'],
            'agama'             => $siswaData['agama'] ?? 'Islam',
            'role_user_id'      => $siswaRole->id,
            'kelas_id'          => $kelas->id,
            'email_verified_at' => now(),
            'password'          => Hash::make('siswa123'),
          ]);
        } else {
          // Update kelas & jenis kelamin jika sudah ada
          $exists->update([
            'kelas_id'      => $kelas->id,
            'jenis_kelamin' => $siswaData['jk'],
            'agama'         => $siswaData['agama'] ?? $exists->agama ?? 'Islam',
          ]);
        }
      }

      $this->command->info("✓ Kelas {$kelasData['nama']} — Wali: {$kelasData['wali_nama']} — {$kelasData['jumlah_siswa']} siswa");
    }
  }

  /**
   * Generate email dari NISN.
   */
  private function generateEmail(string $nisn): string
  {
    return $nisn . '@siswa.smkn1ciamis.sch.id';
  }

  /**
   * Data kelas, wali, dan siswa.
   */
  private function getKelasData(): array
  {
    return [
      [
        'nama'        => '10 AKL 1 KLOTER 1',
        'wali_nama'   => 'Irma Sukmarini',
        'wali_email'  => 'irma.sukmarini@smkn1ciamis.sch.id',
        'wali_jk'     => 'P',
        'jumlah_siswa' => 20,
        'siswa'       => [
          ['nisn' => '0104019810', 'nama' => 'Aditia Pratama', 'jk' => 'L', 'agama' => 'Islam'],
          ['nisn' => '3108321796', 'nama' => 'Aisna Mujyulloh', 'jk' => 'P', 'agama' => 'Islam'],
          ['nisn' => '0095749352', 'nama' => 'Aldila Syita Nurfaaizah', 'jk' => 'P', 'agama' => 'Islam'],
          ['nisn' => '0094962235', 'nama' => 'Andini Nur\'azizah', 'jk' => 'P', 'agama' => 'Islam'],
          ['nisn' => '0093124354', 'nama' => 'Anita Kasela', 'jk' => 'P', 'agama' => 'Islam'],
          ['nisn' => '0101640967', 'nama' => 'Aulia Rizqi Juliani', 'jk' => 'P', 'agama' => 'Islam'],
          ['nisn' => '0092720488', 'nama' => 'Cinta Tania', 'jk' => 'P', 'agama' => 'Islam'],
          ['nisn' => '0099642021', 'nama' => 'Dena Nurcahya', 'jk' => 'P', 'agama' => 'Islam'],
          ['nisn' => '0094123476', 'nama' => 'Diki Nugraha', 'jk' => 'L', 'agama' => 'Islam'],
          ['nisn' => '0104170004', 'nama' => 'Dinda Aulia Fazrillah', 'jk' => 'P', 'agama' => 'Islam'],
          ['nisn' => '0093822965', 'nama' => 'Elsa Angelia Noermalisa', 'jk' => 'P', 'agama' => 'Islam'],
          ['nisn' => '0098211042', 'nama' => 'Fiqri Muhammad Yasir', 'jk' => 'L', 'agama' => 'Islam'],
          ['nisn' => '3092096761', 'nama' => 'Frida Afrilla', 'jk' => 'P', 'agama' => 'Islam'],
          ['nisn' => '3098766624', 'nama' => 'Indira Agustina Mulya', 'jk' => 'P', 'agama' => 'Islam'],
          ['nisn' => '3092226049', 'nama' => 'Irsyad Mujahid Al Fani', 'jk' => 'L', 'agama' => 'Islam'],
          ['nisn' => '0106577173', 'nama' => 'Izmi Nurul Azmi', 'jk' => 'P', 'agama' => 'Islam'],
          ['nisn' => '0092243967', 'nama' => 'Kirana Shin', 'jk' => 'P', 'agama' => 'Islam'],
          ['nisn' => '3095892563', 'nama' => 'Mellany Nurtistiany', 'jk' => 'P', 'agama' => 'Islam'],
          ['nisn' => '0098669068', 'nama' => 'Mochammad Fabian Canafarrro', 'jk' => 'L', 'agama' => 'Islam'],
          ['nisn' => '0098360687', 'nama' => 'Muhammad Fajar Fadilla', 'jk' => 'L', 'agama' => 'Islam'],
        ],
      ],
    ];
  }
}
