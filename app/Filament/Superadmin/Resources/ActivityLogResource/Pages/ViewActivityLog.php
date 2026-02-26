<?php

namespace App\Filament\Superadmin\Resources\ActivityLogResource\Pages;

use App\Filament\Superadmin\Resources\ActivityLogResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\MaxWidth;

class ViewActivityLog extends ViewRecord
{
  protected static string $resource = ActivityLogResource::class;

  protected static string $view = 'filament.superadmin.resources.activity-log.view';

  public function getMaxContentWidth(): MaxWidth | string | null
  {
    return MaxWidth::Full;
  }

  public function getTitle(): string
  {
    return 'Detail Log Aktivitas';
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
