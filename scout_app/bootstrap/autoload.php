<?php
/**
 * PSR-4 Autoloader
 * Automatically loads classes based on namespace
 */

spl_autoload_register(function ($class) {
    // Base directory for the namespace prefix
    $baseDir = __DIR__ . '/../';

    // Namespace mappings
    $prefixes = [
        'App\\Models\\'      => 'src/Models/',
        'App\\Services\\'    => 'src/Services/',
        'App\\Controllers\\' => 'src/Controllers/',
        'Config\\'           => 'config/',
    ];

    foreach ($prefixes as $prefix => $dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . $dir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

// Load configuration
require_once __DIR__ . '/../config/Database.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
