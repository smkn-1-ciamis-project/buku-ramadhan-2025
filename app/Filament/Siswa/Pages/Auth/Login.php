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
  public bool $showErrorPopup = false;
  public string $errorPopupMessage = '';

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
      ->validationMessages([
        'required' => 'NISN wajib diisi.',
        'min' => 'NISN harus 10 digit angka.',
        'max' => 'NISN harus 10 digit angka.',
        'regex' => 'Format NISN tidak valid. Harus 10 digit angka.',
      ])
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

    // Validate form — catch and show as centered popup instead of inline
    try {
      $data = $this->form->getState();
    } catch (ValidationException $e) {
      $this->errorPopupMessage = collect($e->errors())->flatten()->first() ?? 'Validasi gagal.';
      $this->showErrorPopup = true;
      return null;
    }

    $credentials = $this->getCredentialsFromFormData($data);

    // ── Single-session pre-check ───────────────────────────────────────
    // Load the user BEFORE Auth::attempt so we can inspect the existing
    // session without side-effects.
    $existingUser = \App\Models\User::where('nisn', $credentials['nisn'])->first();

    if ($existingUser) {
      $hasActiveSession  = !empty($existingUser->active_session_id);
      $isDifferentDevice = $existingUser->active_session_id !== session()->getId();
      $maxMinutes = \App\Http\Middleware\EnsureSingleSession::getSessionDurationForRole($existingUser->role_user?->name ?? '');
      $sessionStillValid = $existingUser->session_login_at &&
        $existingUser->session_login_at->addMinutes($maxMinutes)->isFuture();

      // Block new login if another device holds a valid (< 12 h) active session.
      if ($hasActiveSession && $isDifferentDevice && $sessionStillValid) {
        $this->showDevicePopup = true;

        return null;
      }
      // If session is >= 12 hours old, fall through — the old session is expired
      // and EnsureSingleSession will kick the old device on its next request.
    }

    // Attempt login — use remember token only when "Ingat Saya" is checked
    $remember = $data['remember'] ?? false;

    if (! Auth::attempt($credentials, $remember)) {
      $this->errorPopupMessage = 'NISN atau password salah. Pastikan NISN terdiri dari 10 digit angka.';
      $this->showErrorPopup = true;
      return null;
    }

    // Regenerate session to prevent session-fixation attacks
    session()->regenerate();

        // Save session tracking data (session ID reflects the NEW ID after regenerate)
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $user->update([
      'active_session_id' => session()->getId(),
      'session_login_at'  => now(),
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
