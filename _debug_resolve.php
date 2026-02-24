<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Filament\Kesiswaan\Resources\ValidasiKelasResource;

$key = 'a12605a4-9597-462e-96ee-efb596f3fdc9';

// Test resolveRecordRouteBinding
$record = ValidasiKelasResource::resolveRecordRouteBinding($key);
echo "resolveRecordRouteBinding: " . ($record ? $record->nama : 'NULL') . PHP_EOL;

// Test getEloquentQuery
$query = ValidasiKelasResource::getEloquentQuery();
echo "Query SQL: " . $query->toSql() . PHP_EOL;
$found = $query->where('id', $key)->first();
echo "Direct query: " . ($found ? $found->nama : 'NULL') . PHP_EOL;

// Test canAccess
echo "canAccess: " . (ValidasiKelasResource::canAccess() ? 'true' : 'false') . PHP_EOL;

// Test canViewAny
echo "canViewAny: " . (ValidasiKelasResource::canViewAny() ? 'true' : 'false') . PHP_EOL;
