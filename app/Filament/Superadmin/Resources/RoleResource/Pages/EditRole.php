<?php

namespace App\Filament\Superadmin\Resources\RoleResource\Pages;

use App\Filament\Superadmin\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\MaxWidth;

class EditRole extends EditRecord
{
  protected static string $resource = RoleResource::class;

  protected static string $view = 'filament.superadmin.resources.role.edit';

  public function getMaxContentWidth(): MaxWidth | string | null
  {
    return MaxWidth::Full;
  }

  protected function mutateFormDataBeforeFill(array $data): array
  {
    // Determine which menu set applies based on role name
    $menus = match ($data['name'] ?? null) {
      'Super Admin' => RoleResource::getSuperadminMenus(),
      'Guru' => RoleResource::getGuruMenus(),
      'Kesiswaan' => RoleResource::getKesiswaanMenus(),
      default => [],
    };

    // Fill missing menu_visibility keys with true (all visible by default)
    $existing = $data['menu_visibility'] ?? [];
    foreach ($menus as $key => $label) {
      if (!array_key_exists($key, $existing)) {
        $existing[$key] = true;
      }
    }
    $data['menu_visibility'] = $existing;

    return $data;
  }

  public function deleteAction(): Actions\DeleteAction
  {
    return Actions\DeleteAction::make()
      ->icon('heroicon-o-trash')
      ->requiresConfirmation();
  }

  protected function getHeaderActions(): array
  {
    return [];
  }

  public function getTitle(): string
  {
    return 'Ubah Role — ' . $this->record->name;
  }

  public function getHeading(): string
  {
    return '';
  }

  public function getSubheading(): ?string
  {
    return null;
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
