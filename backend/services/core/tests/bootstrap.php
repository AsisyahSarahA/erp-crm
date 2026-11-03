<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

// Register App\, Database\Seeders\, and Tests\ namespace for services/core
spl_autoload_register(function ($class) {
    if (str_starts_with($class, 'App\\')) {
        $relativeClass = substr($class, 4);
        $file = __DIR__ . '/../app/' . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    if (str_starts_with($class, 'Database\\Seeders\\')) {
        $relativeClass = substr($class, 17);
        $file = __DIR__ . '/../database/seeders/' . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    if (str_starts_with($class, 'Tests\\')) {
        $relativeClass = substr($class, 6);
        $file = __DIR__ . '/' . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    return false;
}, true, true);
