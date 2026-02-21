<?php

namespace App\Filament\Kesiswaan\Resources\ValidasiResource\Pages;

use App\Filament\Kesiswaan\Resources\ValidasiResource;
use Filament\Resources\Pages\ListRecords;

class ListValidasi extends ListRecords
{
  protected static string $resource = ValidasiResource::class;

  protected function getHeaderActions(): array
  {
    return [];
  }
}
