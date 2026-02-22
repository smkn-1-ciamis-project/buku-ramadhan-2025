<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Rule;

/**
 * Trait for Siswa dashboard pages to show a password-change popup
 * when the user still has must_change_password = true.
 */
trait HasPasswordChangeModal
{
    public bool $showPasswordModal = false;
    public bool $isNisnPassword = false;

    #[Rule('required|string|min:8')]
    public string $new_password = '';

    #[Rule('required|string|same:new_password')]
    public string $new_password_confirmation = '';

    protected function messages(): array
    {
        return [
            'new_password.required'              => 'Password baru wajib diisi.',
            'new_password.min'                   => 'Password minimal 8 karakter.',
            'new_password_confirmation.required' => 'Konfirmasi password wajib diisi.',
            'new_password_confirmation.same'     => 'Konfirmasi password tidak cocok.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'new_password'              => 'password baru',
            'new_password_confirmation' => 'konfirmasi password baru',
        ];
    }

    public function mountHasPasswordChangeModal(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user && $user->must_change_password) {
            $this->showPasswordModal = true;
            $this->isNisnPassword = true;
            return;
        }

        // Cek apakah password masih sama dengan NISN
        if ($user && $user->nisn && Hash::check($user->nisn, $user->password)) {
            $this->showPasswordModal = true;
            $this->isNisnPassword = true;
        }
    }

    public function simpanPasswordBaru(): void
    {
        $this->validate([
            'new_password' => 'required|string|min:8',
            'new_password_confirmation' => 'required|string|same:new_password',
        ], [
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password minimal 8 karakter.',
            'new_password_confirmation.required' => 'Konfirmasi password wajib diisi.',
            'new_password_confirmation.same' => 'Konfirmasi password tidak cocok.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Tidak boleh menggunakan NISN sebagai password baru
        if ($user->nisn && $this->new_password === $user->nisn) {
            $this->addError('new_password', 'Password baru tidak boleh sama dengan NISN Anda.');
            return;
        }

        $user->update([
            'password' => $this->new_password,
            'must_change_password' => false,
        ]);

        // Re-login agar session hash password diperbarui
        // sehingga AuthenticateSession tidak logout user
        Auth::login($user);

        // Update active_session_id agar EnsureSingleSession
        // tidak menendang user karena session ID berubah
        $user->updateQuietly([
            'active_session_id' => Session::getId(),
        ]);

        $this->showPasswordModal = false;
        $this->isNisnPassword = false;
        $this->new_password = '';
        $this->new_password_confirmation = '';
    }
}
