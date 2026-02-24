<?php

namespace App\Filament\Superadmin\Resources\RekapKelasResource\Pages;

use App\Filament\Superadmin\Resources\RekapKelasResource;
use App\Models\Kelas;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListRekapKelas extends ListRecords
{
    protected static string $resource = RekapKelasResource::class;

    public function getTabs(): array
    {
        $tabs = [
            'semua' => Tab::make('Semua')
                ->badge(fn() => Kelas::count())
                ->badgeColor('primary'),
        ];

        foreach (['10', '11', '12'] as $tingkat) {
            $tabs["kelas_{$tingkat}"] = Tab::make("Kelas {$tingkat}")
                ->modifyQueryUsing(fn(Builder $query) => $query->where('nama', 'like', "{$tingkat} %"))
                ->badge(fn() => Kelas::where('nama', 'like', "{$tingkat} %")->count())
                ->badgeColor('gray');
        }

        return $tabs;
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'semua';
    }
}
