<?php

namespace App\Filament\Siswa\Pages\NonMuslim\Konghucu;

use App\Models\FormSetting;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class FormulirHarian extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Formulir Harian Konghucu';
    protected static ?string $title = '';
    protected static ?string $slug = 'formulir-harian-konghucu';
    protected static string $view = 'siswa.nonmuslim.konghucu.formulir-harian';
    protected static bool $shouldRegisterNavigation = false;

    public function mount(): void
    {
        $user = Auth::user();
        $agama = $user->agama ?? 'Konghucu';
        $setting = FormSetting::where('agama', $agama)->first();

        if ($setting && !$setting->is_active) {
            abort(403, 'Formulir untuk agama ' . $agama . ' sedang dinonaktifkan oleh kesiswaan.');
        }
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
