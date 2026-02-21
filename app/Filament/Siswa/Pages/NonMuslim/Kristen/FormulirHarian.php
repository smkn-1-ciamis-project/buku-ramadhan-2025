<?php

namespace App\Filament\Siswa\Pages\NonMuslim\Kristen;

use App\Models\FormSetting;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class FormulirHarian extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Formulir Harian Kristen';
    protected static ?string $title = '';
    protected static ?string $slug = 'formulir-harian-kristen';
    protected static string $view = 'siswa.nonmuslim.kristen.formulir-harian';
    protected static bool $shouldRegisterNavigation = false;

    public function mount(): void
    {
        $user = Auth::user();
        $agama = $user->agama ?? 'Kristen';
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
