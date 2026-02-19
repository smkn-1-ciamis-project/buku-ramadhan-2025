<?php

namespace App\Filament\Siswa\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;

class Login extends BaseLogin
{
  protected static string $view = 'filament.siswa.pages.auth.login';

  public bool $showDevicePopup = false;

  protected function getForms(): array
  {
    return [
      'form' => $this->form(
        $this->makeForm()
          ->schema([
            $this->getNisnFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getRememberFormComponent(),
          ])
          ->statePath('data'),
      ),
    ];
  }

  protected function getNisnFormComponent(): Component
  {
    return TextInput::make('nisn')
      ->label('NISN (Nomor Induk Siswa Nasional)')
      ->placeholder('Masukkan 10 digit NISN')
      ->helperText('Contoh: 0012345678')
      ->required()
      ->minLength(10)
      ->maxLength(10)
      ->regex('/^\d{10}$/')
      ->autofocus()
      ->extraInputAttributes([
        'tabindex' => 1,
        'inputmode' => 'numeric',
        'pattern' => '[0-9]*',
        'maxlength' => 10,
        'oninput' => "this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)",
      ])
      ->autocomplete('off');
  }

  protected function getCredentialsFromFormData(array $data): array
  {
    return [
      'nisn' => $data['nisn'],
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

    $data = $this->form->getState();
    $credentials = $this->getCredentialsFromFormData($data);

    // Check if account is already logged in on another device
    $user = \App\Models\User::where('nisn', $credentials['nisn'])->first();

    if ($user && $user->active_session_id && $user->active_session_id !== session()->getId()) {
      // Check if old session is still within 12 hours (still valid)
      if ($user->session_login_at && now()->diffInHours($user->session_login_at, true) < 12) {
        $this->showDevicePopup = true;

        return null;
      }
    }

    // Attempt login — don't use remember_token (we handle session duration ourselves)
    if (! Auth::attempt($credentials, false)) {
      $this->throwFailureValidationException();
    }

    session()->regenerate();

    // Save session tracking data
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $user->update([
      'active_session_id' => session()->getId(),
      'session_login_at' => now(),
    ]);

    return app(LoginResponse::class);
  }

  protected function throwFailureValidationException(): never
  {
    throw ValidationException::withMessages([
      'data.nisn' => 'NISN atau password salah. Pastikan NISN terdiri dari 10 digit angka.',
    ]);
  }
}
