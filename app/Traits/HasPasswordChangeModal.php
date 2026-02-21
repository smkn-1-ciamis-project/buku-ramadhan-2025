<?php

namespace App\Traits;

use Livewire\Attributes\Rule;

/**
 * Trait for Siswa dashboard pages to show a password-change popup
 * when the user still has must_change_password = true.
 */
trait HasPasswordChangeModal
{
    public bool $showPasswordModal = false;

    #[Rule('required|string|min:8')]
    public string $new_password = '';

    #[Rule('required|string|min:8|same:new_password')]
    public string $new_password_confirmation = '';

    public function mountHasPasswordChangeModal(): void
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user && $user->must_change_password) {
            $this->showPasswordModal = true;
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
        $user = auth()->user();
        $user->update([
            'password' => $this->new_password,
            'must_change_password' => false,
        ]);

        $this->showPasswordModal = false;
        $this->new_password = '';
        $this->new_password_confirmation = '';
    }
}
