<?php

namespace App\Repositories\Eloquent;

use App\Models\FormSetting;
use App\Repositories\Contracts\FormSettingRepositoryInterface;

class EloquentFormSettingRepository implements FormSettingRepositoryInterface
{
  public function getForAgama(string $agama): ?FormSetting
  {
    return FormSetting::getForAgama($agama);
  }
}
