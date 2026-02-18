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
      ->numeric()
      ->autofocus()
      ->extraInputAttributes(['tabindex' => 1])
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

    if (! Auth::attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
      $this->throwFailureValidationException();
    }

    session()->regenerate();

    return app(LoginResponse::class);
  }

  protected function throwFailureValidationException(): never
  {
    throw ValidationException::withMessages([
      'data.nisn' => 'NISN atau password salah. Pastikan NISN terdiri dari 10 digit angka.',
    ]);
  }
}
