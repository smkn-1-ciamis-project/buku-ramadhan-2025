<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\FormSettingRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
  public function __construct(
    private UserRepositoryInterface $userRepo,
    private FormSettingRepositoryInterface $formSettingRepo,
  ) {}

  /**
   * Change user's password.
   *
   * @return array{success: bool, message: string, status?: int}
   */
  public function changePassword(User $user, string $currentPassword, string $newPassword): array
  {
    if (!Hash::check($currentPassword, $user->password)) {
      return [
        'success' => false,
        'message' => 'Password lama tidak sesuai.',
        'status' => 422,
      ];
    }

    $this->userRepo->updatePassword($user, $newPassword);

    // Re-login to update session hash
    Auth::login($user);

    $this->userRepo->updateActiveSession($user, session()->getId());

    return [
      'success' => true,
      'message' => 'Password berhasil diubah.',
    ];
  }

  /**
   * Get form settings for a specific religion.
   *
   * @return array{success: bool, data?: array, message?: string, status?: int}
   */
  public function getFormSettings(string $agama): array
  {
    $setting = $this->formSettingRepo->getForAgama($agama);

    if (!$setting) {
      return [
        'success' => false,
        'message' => 'Setting formulir belum dikonfigurasi untuk agama ini.',
        'status' => 404,
      ];
    }

    if (!$setting->is_active) {
      return [
        'success' => false,
        'inactive' => true,
        'message' => 'Formulir untuk agama ' . $agama . ' sedang dinonaktifkan oleh kesiswaan.',
        'status' => 403,
      ];
    }

    return [
      'success' => true,
      'data' => [
        'agama' => $setting->agama,
        'sections' => collect($setting->sections)
          ->filter(fn($s) => $s['enabled'] ?? true)
          ->values()
          ->toArray(),
      ],
    ];
  }
}
