<?php

namespace App\Repositories\Contracts;

use App\Models\FormSetting;

interface FormSettingRepositoryInterface
{
  /**
   * Get form setting for a specific religion.
   */
  public function getForAgama(string $agama): ?FormSetting;
}
