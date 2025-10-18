<?php

// Temporary cache clearing script for live server
// DELETE THIS FILE after use for security reasons

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "<h2>Clearing Laravel Cache...</h2>";

try {
    // Clear application cache
    Artisan::call('cache:clear');
    echo "<p>✓ Application cache cleared</p>";
    
    // Clear config cache
    Artisan::call('config:clear');
    echo "<p>✓ Config cache cleared</p>";
    
    // Clear route cache
    Artisan::call('route:clear');
    echo "<p>✓ Route cache cleared</p>";
    
    // Clear view cache
    Artisan::call('view:clear');
    echo "<p>✓ View cache cleared</p>";
    
    // Optimize clear
    Artisan::call('optimize:clear');
    echo "<p>✓ Optimize cache cleared</p>";
    
    echo "<h3 style='color: green;'>✓ All caches cleared successfully!</h3>";
    echo "<p><strong>IMPORTANT: Delete this file (clear-cache.php) for security!</strong></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

