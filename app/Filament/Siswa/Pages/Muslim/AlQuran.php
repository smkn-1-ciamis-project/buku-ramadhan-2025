<?php

namespace App\Filament\Siswa\Pages\Muslim;

use Filament\Pages\Page;

class AlQuran extends Page
{
  protected static ?string $navigationIcon = 'heroicon-o-book-open';
  protected static ?string $navigationLabel = 'Al-Quran';
  protected static ?string $title = '';
  protected static ?string $slug = 'al-quran';
  protected static string $view = 'siswa.muslim.al-quran';
  protected static bool $shouldRegisterNavigation = false;

  public function mount(): void
  {
    //
  }

  public function getHeading(): string
  {
    return '';
  }

  public function getSubheading(): ?string
  {
    return null;
  }
}
