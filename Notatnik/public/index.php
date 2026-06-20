<?php

require_once __DIR__ . '/../config/config.php';

// Autoloader (prosty, bez composera)
spl_autoload_register(function ($class) {
    $prefix = 'src\\';
    $baseDir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Uruchom router
$router = new Router();
$router->dispatch();