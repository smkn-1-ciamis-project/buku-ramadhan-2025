<?php

namespace App\Filament\Guru\Resources\VerifikasiResource\Pages;

use App\Filament\Guru\Resources\VerifikasiResource;
use Filament\Resources\Pages\ListRecords;

class ListVerifikasi extends ListRecords
{
  protected static string $resource = VerifikasiResource::class;

  protected function getHeaderActions(): array
  {
    return [];
  }
}
