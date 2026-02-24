<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== form_submissions columns ===\n";
$cols = Schema::getColumnListing('form_submissions');
foreach ($cols as $c) {
  echo "  - $c\n";
}

echo "\n=== kesiswaan_status exists: " . (Schema::hasColumn('form_submissions', 'kesiswaan_status') ? 'YES' : 'NO') . "\n";
echo "=== validated_by exists: " . (Schema::hasColumn('form_submissions', 'validated_by') ? 'YES' : 'NO') . "\n";
echo "=== validated_at exists: " . (Schema::hasColumn('form_submissions', 'validated_at') ? 'YES' : 'NO') . "\n";
echo "=== catatan_kesiswaan exists: " . (Schema::hasColumn('form_submissions', 'catatan_kesiswaan') ? 'YES' : 'NO') . "\n";

echo "\n=== Pending migrations ===\n";
$ran = DB::table('migrations')->pluck('migration')->toArray();
$files = glob('database/migrations/*.php');
foreach ($files as $f) {
  $name = pathinfo($f, PATHINFO_FILENAME);
  $status = in_array($name, $ran) ? '[RAN]' : '[PENDING]';
  if (str_contains($name, 'kesiswaan') || str_contains($name, 'validat') || $status === '[PENDING]') {
    echo "  $status $name\n";
  }
}
