<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING ALL PANEL PAGES ===\n\n";

$errors = [];

// 1. Test all Siswa pages
echo "--- SISWA PANEL ---\n";
$siswaPages = glob(app_path('Filament/Siswa/Pages/*.php'));
foreach ($siswaPages as $file) {
    $class = 'App\\Filament\\Siswa\\Pages\\' . pathinfo($file, PATHINFO_FILENAME);
    try {
        if (class_exists($class)) {
            echo "  OK: " . basename($file) . "\n";
        } else {
            echo "  MISSING CLASS: $class\n";
            $errors[] = "Siswa: $class not found";
        }
    } catch (\Throwable $e) {
        echo "  ERROR: " . basename($file) . " - " . $e->getMessage() . "\n";
        $errors[] = "Siswa: " . basename($file) . " - " . $e->getMessage();
    }
}

// Check Siswa Auth pages
$siswaAuth = glob(app_path('Filament/Siswa/Pages/Auth/*.php'));
foreach ($siswaAuth as $file) {
    $class = 'App\\Filament\\Siswa\\Pages\\Auth\\' . pathinfo($file, PATHINFO_FILENAME);
    try {
        if (class_exists($class)) {
            echo "  OK: Auth/" . basename($file) . "\n";
        }
    } catch (\Throwable $e) {
        echo "  ERROR: Auth/" . basename($file) . " - " . $e->getMessage() . "\n";
        $errors[] = "Siswa Auth: " . basename($file) . " - " . $e->getMessage();
    }
}

// 2. Test all Guru pages & resources
echo "\n--- GURU PANEL ---\n";
$guruPages = glob(app_path('Filament/Guru/Pages/*.php'));
foreach ($guruPages as $file) {
    $class = 'App\\Filament\\Guru\\Pages\\' . pathinfo($file, PATHINFO_FILENAME);
    try {
        if (class_exists($class)) {
            echo "  OK: " . basename($file) . "\n";
        }
    } catch (\Throwable $e) {
        echo "  ERROR: " . basename($file) . " - " . $e->getMessage() . "\n";
        $errors[] = "Guru: " . basename($file) . " - " . $e->getMessage();
    }
}

$guruResources = glob(app_path('Filament/Guru/Resources/*.php'));
foreach ($guruResources as $file) {
    $class = 'App\\Filament\\Guru\\Resources\\' . pathinfo($file, PATHINFO_FILENAME);
    try {
        if (class_exists($class)) {
            echo "  OK Resource: " . basename($file) . "\n";
        }
    } catch (\Throwable $e) {
        echo "  ERROR: " . basename($file) . " - " . $e->getMessage() . "\n";
        $errors[] = "Guru Resource: " . basename($file) . " - " . $e->getMessage();
    }
}

// Guru resource pages
$guruResourcePages = glob(app_path('Filament/Guru/Resources/*/Pages/*.php'));
foreach ($guruResourcePages as $file) {
    $rel = str_replace(app_path() . '\\', '', $file);
    $class = 'App\\' . str_replace(['/', '\\', '.php'], ['\\', '\\', ''], $rel);
    try {
        if (class_exists($class)) {
            echo "  OK: " . $rel . "\n";
        }
    } catch (\Throwable $e) {
        echo "  ERROR: $rel - " . $e->getMessage() . "\n";
        $errors[] = "Guru: $rel - " . $e->getMessage();
    }
}

// 3. Test all Kesiswaan pages & resources
echo "\n--- KESISWAAN PANEL ---\n";
$kesiswaanPages = glob(app_path('Filament/Kesiswaan/Pages/*.php'));
foreach ($kesiswaanPages as $file) {
    $class = 'App\\Filament\\Kesiswaan\\Pages\\' . pathinfo($file, PATHINFO_FILENAME);
    try {
        if (class_exists($class)) {
            echo "  OK: " . basename($file) . "\n";
        }
    } catch (\Throwable $e) {
        echo "  ERROR: " . basename($file) . " - " . $e->getMessage() . "\n";
        $errors[] = "Kesiswaan: " . basename($file) . " - " . $e->getMessage();
    }
}

$kesiswaanResources = glob(app_path('Filament/Kesiswaan/Resources/*.php'));
foreach ($kesiswaanResources as $file) {
    $class = 'App\\Filament\\Kesiswaan\\Resources\\' . pathinfo($file, PATHINFO_FILENAME);
    try {
        if (class_exists($class)) {
            echo "  OK Resource: " . basename($file) . "\n";
        }
    } catch (\Throwable $e) {
        echo "  ERROR: " . basename($file) . " - " . $e->getMessage() . "\n";
        $errors[] = "Kesiswaan Resource: " . basename($file) . " - " . $e->getMessage();
    }
}

$kesiswaanResourcePages = glob(app_path('Filament/Kesiswaan/Resources/*/Pages/*.php'));
foreach ($kesiswaanResourcePages as $file) {
    $rel = str_replace(app_path() . '\\', '', $file);
    $class = 'App\\' . str_replace(['/', '\\', '.php'], ['\\', '\\', ''], $rel);
    try {
        if (class_exists($class)) {
            echo "  OK: " . $rel . "\n";
        }
    } catch (\Throwable $e) {
        echo "  ERROR: $rel - " . $e->getMessage() . "\n";
        $errors[] = "Kesiswaan: $rel - " . $e->getMessage();
    }
}

// 4. Test all Superadmin pages & resources
echo "\n--- SUPERADMIN PANEL ---\n";
$superPages = glob(app_path('Filament/Superadmin/Pages/*.php'));
foreach ($superPages as $file) {
    $class = 'App\\Filament\\Superadmin\\Pages\\' . pathinfo($file, PATHINFO_FILENAME);
    try {
        if (class_exists($class)) {
            echo "  OK: " . basename($file) . "\n";
        }
    } catch (\Throwable $e) {
        echo "  ERROR: " . basename($file) . " - " . $e->getMessage() . "\n";
        $errors[] = "Superadmin: " . basename($file) . " - " . $e->getMessage();
    }
}

$superResources = glob(app_path('Filament/Superadmin/Resources/*.php'));
foreach ($superResources as $file) {
    $class = 'App\\Filament\\Superadmin\\Resources\\' . pathinfo($file, PATHINFO_FILENAME);
    try {
        if (class_exists($class)) {
            echo "  OK Resource: " . basename($file) . "\n";
        }
    } catch (\Throwable $e) {
        echo "  ERROR: " . basename($file) . " - " . $e->getMessage() . "\n";
        $errors[] = "Superadmin Resource: " . basename($file) . " - " . $e->getMessage();
    }
}

$superResourcePages = glob(app_path('Filament/Superadmin/Resources/*/Pages/*.php'));
foreach ($superResourcePages as $file) {
    $rel = str_replace(app_path() . '\\', '', $file);
    $class = 'App\\' . str_replace(['/', '\\', '.php'], ['\\', '\\', ''], $rel);
    try {
        if (class_exists($class)) {
            echo "  OK: " . $rel . "\n";
        }
    } catch (\Throwable $e) {
        echo "  ERROR: $rel - " . $e->getMessage() . "\n";
        $errors[] = "Superadmin: $rel - " . $e->getMessage();
    }
}

// 5. Test all Models
echo "\n--- MODELS ---\n";
$models = glob(app_path('Models/*.php'));
foreach ($models as $file) {
    $class = 'App\\Models\\' . pathinfo($file, PATHINFO_FILENAME);
    try {
        if (class_exists($class)) {
            echo "  OK: " . basename($file) . "\n";
        }
    } catch (\Throwable $e) {
        echo "  ERROR: " . basename($file) . " - " . $e->getMessage() . "\n";
        $errors[] = "Models: " . basename($file) . " - " . $e->getMessage();
    }
}

// 6. Test all Services
echo "\n--- SERVICES ---\n";
$services = glob(app_path('Services/*.php'));
foreach ($services as $file) {
    $class = 'App\\Services\\' . pathinfo($file, PATHINFO_FILENAME);
    try {
        if (class_exists($class)) {
            echo "  OK: " . basename($file) . "\n";
        }
    } catch (\Throwable $e) {
        echo "  ERROR: " . basename($file) . " - " . $e->getMessage() . "\n";
        $errors[] = "Services: " . basename($file) . " - " . $e->getMessage();
    }
}

// 7. Test Blade views compile
echo "\n--- BLADE VIEWS ---\n";
$bladeFiles = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(resource_path('views'))
);
$bladeErrors = 0;
foreach ($bladeFiles as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        try {
            $compiled = \Illuminate\Support\Facades\Blade::compileString(file_get_contents($file->getPathname()));
            // Try to parse the compiled PHP
            $tokens = token_get_all($compiled);
        } catch (\Throwable $e) {
            $rel = str_replace(resource_path('views') . '\\', '', $file->getPathname());
            echo "  ERROR: $rel - " . $e->getMessage() . "\n";
            $errors[] = "Blade: $rel - " . $e->getMessage();
            $bladeErrors++;
        }
    }
}
if ($bladeErrors === 0) {
    echo "  All blade views compile OK\n";
}

// Summary
echo "\n=== SUMMARY ===\n";
if (empty($errors)) {
    echo "ALL TESTS PASSED - No errors found!\n";
} else {
    echo count($errors) . " error(s) found:\n";
    foreach ($errors as $e) {
        echo "  - $e\n";
    }
}
