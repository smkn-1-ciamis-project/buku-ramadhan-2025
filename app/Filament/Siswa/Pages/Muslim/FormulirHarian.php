<?php

namespace App\Filament\Siswa\Pages\Muslim;

use App\Models\FormSetting;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class FormulirHarian extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Formulir Harian';
    protected static ?string $title = '';
    protected static ?string $slug = 'formulir-harian';
    protected static string $view = 'siswa.muslim.formulir-harian';
    protected static bool $shouldRegisterNavigation = false;

    public function mount(): void
    {
        $user = Auth::user();
        $agama = $user->agama ?? 'Islam';
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
