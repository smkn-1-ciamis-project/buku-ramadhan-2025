<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Find siswa role
$siswaRole = App\Models\RoleUser::where('name', 'siswa')->first();
echo "Siswa role ID: {$siswaRole->id}\n\n";

$siswaUsers = App\Models\User::where('role_user_id', $siswaRole->id)->get(['id', 'name', 'agama', 'nisn']);

echo "Total siswa: {$siswaUsers->count()}\n\n";

echo "Siswa per agama:\n";
$grouped = $siswaUsers->groupBy('agama');
foreach ($grouped as $agama => $users) {
  echo "  {$agama}: {$users->count()}\n";
}

echo "\nNon-Muslim siswa:\n";
foreach ($siswaUsers->where('agama', '!=', 'Islam') as $s) {
  $subCount = App\Models\FormSubmission::where('user_id', $s->id)->count();
  echo "  [{$s->agama}] {$s->name} (NISN: {$s->nisn}) - {$subCount} submissions\n";
}

echo "\nAll submissions:\n";
$allSubs = App\Models\FormSubmission::with('user')->orderBy('hari_ke')->get();
foreach ($allSubs as $sub) {
  echo "  Hari {$sub->hari_ke}: {$sub->user->name} [{$sub->user->agama}] status={$sub->status} kesiswaan={$sub->kesiswaan_status}\n";
}
