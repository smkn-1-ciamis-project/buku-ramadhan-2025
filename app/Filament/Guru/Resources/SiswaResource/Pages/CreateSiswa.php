<?php

namespace App\Filament\Guru\Resources\SiswaResource\Pages;

use App\Filament\Guru\Resources\SiswaResource;
use App\Models\Kelas;
use App\Models\RoleUser;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class CreateSiswa extends CreateRecord
{
  protected static string $resource = SiswaResource::class;

  protected function afterCreate(): void
  {
    \App\Models\ActivityLog::log('create_siswa', Auth::user(), [
      'description' => 'Menambahkan siswa baru: ' . $this->record->name . ' (NISN: ' . $this->record->nisn . ')',
      'target_id' => $this->record->id,
      'target_name' => $this->record->name,
      'target_nisn' => $this->record->nisn,
    ]);
  }

  protected function mutateFormDataBeforeCreate(array $data): array
  {
    // Auto-assign role Siswa
    $siswaRole = RoleUser::where('name', 'Siswa')->first();
    $data['role_user_id'] = $siswaRole?->id;

    // Auto-assign kelas dari guru wali
    $guru = Auth::user();
    $kelas = Kelas::where('wali_id', $guru->id)->first();
    $data['kelas_id'] = $kelas?->id;

    // Auto-generate email from NISN if not provided
    if (empty($data['email']) && !empty($data['nisn'])) {
      $generatedEmail = $data['nisn'] . '@siswa.buku-ramadhan.id';

      if (User::where('email', $generatedEmail)->exists()) {
        throw ValidationException::withMessages([
          'data.email' => 'Email ' . $generatedEmail . ' sudah digunakan. Silakan isi email secara manual.',
        ]);
      }

      $data['email'] = $generatedEmail;
    }

    // Default password = nisn
    if (empty($data['password'])) {
      $data['password'] = $data['nisn'] ?? 'siswa123';
    }

    // Wajib ubah password saat pertama kali login
    $data['must_change_password'] = true;

    return $data;
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
