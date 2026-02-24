<?php

namespace App\Filament\Guru\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;

class Login extends BaseLogin
{
  protected static string $view = 'filament.guru.pages.auth.login';

  public bool $showErrorPopup = false;
  public string $errorPopupMessage = '';

  protected function getForms(): array
  {
    return [
      'form' => $this->form(
        $this->makeForm()
          ->schema([
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getRememberFormComponent(),
          ])
          ->statePath('data'),
      ),
    ];
  }

  protected function getEmailFormComponent(): Component
  {
    return TextInput::make('email')
      ->label('Email')
      ->placeholder('Masukkan email Anda')
      ->helperText('Gunakan email yang terdaftar di sistem')
      ->required()
      ->email()
      ->validationMessages([
        'required' => 'Email wajib diisi.',
        'email' => 'Format email tidak valid.',
      ])
      ->autofocus()
      ->extraInputAttributes([
        'tabindex' => 1,
      ])
      ->autocomplete('email');
  }

  protected function getCredentialsFromFormData(array $data): array
  {
    return [
      'email' => $data['email'],
      'password' => $data['password'],
    ];
  }

  public function authenticate(): ?LoginResponse
  {
    try {
      $this->rateLimit(5);
    } catch (TooManyRequestsException $exception) {
      $this->getRateLimitedNotification($exception)?->send();

      return null;
    }

    // Validate form — catch and show as centered popup instead of inline
    try {
      $data = $this->form->getState();
    } catch (ValidationException $e) {
      $this->errorPopupMessage = collect($e->errors())->flatten()->first() ?? 'Validasi gagal.';
      $this->showErrorPopup = true;
      return null;
    }

    $credentials = $this->getCredentialsFromFormData($data);

    // Guru boleh multi-device, skip single-session pre-check

    // Attempt login
    $remember = $data['remember'] ?? false;

    if (! Auth::attempt($credentials, $remember)) {
      $this->errorPopupMessage = 'Email atau password salah. Silakan periksa kembali.';
      $this->showErrorPopup = true;
      return null;
    }

    /** @var \App\Models\User $user */
    $user = Auth::user();

    // Verify user has Guru role — prevent non-guru users from triggering session side-effects
    if (! $user->canAccessPanel(\Filament\Facades\Filament::getCurrentPanel())) {
      Auth::logout();
      session()->invalidate();
      session()->regenerateToken();
      $this->errorPopupMessage = 'Akun Anda tidak memiliki akses ke panel Guru.';
      $this->showErrorPopup = true;
      return null;
    }

    // Regenerate session
    session()->regenerate();

    $user->update([
      'active_session_id' => session()->getId(),
      'session_login_at'  => now(),
    ]);

    return app(LoginResponse::class);
  }

  protected function throwFailureValidationException(): never
  {
    throw ValidationException::withMessages([
      'data.email' => 'Email atau password salah.',
    ]);
  }
}
