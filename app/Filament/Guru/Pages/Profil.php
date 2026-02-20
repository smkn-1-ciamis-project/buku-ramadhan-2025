<?php

namespace App\Filament\Guru\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class Profil extends Page
{
  protected static ?string $navigationIcon = 'heroicon-o-user-circle';
  protected static ?string $navigationLabel = 'Profil Saya';
  protected static ?string $title = 'Profil Guru';
  protected static ?string $slug = 'profil';
  protected static ?int $navigationSort = 4;
  protected static string $view = 'filament.guru.pages.profil';

  public ?string $name = '';
  public ?string $email = '';
  public ?string $no_hp = '';

  public function mount(): void
  {
    $guru = Auth::user();
    $this->name = $guru->name ?? '';
    $this->email = $guru->email ?? '';
    $this->no_hp = $guru->no_hp ?? '';
  }

  public function simpan(): void
  {
    $this->validate([
      'name' => 'required|string|max:255',
      'email' => 'nullable|email|max:255',
      'no_hp' => 'nullable|string|max:20',
    ]);

    $guru = User::findOrFail(Auth::id());
    $guru->update([
      'name' => $this->name,
      'email' => $this->email,
      'no_hp' => $this->no_hp,
    ]);

    Notification::make()
      ->title('Profil berhasil diperbarui!')
      ->success()
      ->send();
  }
}
