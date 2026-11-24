<?php

declare(strict_types=1);

/**
 * Boots the application: constants, autoloading, .env, error settings, timezone.
 * Both public/index.php and tests/run.php include this file.
 */

if (PHP_VERSION_ID < 80200) {
    http_response_code(500);
    exit('StockPilot requires PHP 8.2 or newer. You are running PHP ' . PHP_VERSION . '.');
}

define('BASE_PATH', dirname(__DIR__));

// Use Composer's autoloader when it exists; otherwise fall back to a tiny PSR-4 loader
// so the project runs on XAMPP even if you never run "composer install".
$composerAutoload = BASE_PATH . '/vendor/autoload.php';
if (is_file($composerAutoload)) {
    require $composerAutoload;
} else {
    spl_autoload_register(static function (string $class): void {
        $prefix = 'App\\';
        if (!str_starts_with($class, $prefix)) {
            return;
        }
        $file = BASE_PATH . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) {
            require $file;
        }
    });
    require_once BASE_PATH . '/app/helpers.php';
}

use App\Core\Config;
use App\Core\Env;

Env::load(BASE_PATH . '/.env');

date_default_timezone_set((string) Config::get('app.timezone', 'UTC'));

// Errors: visible while developing, hidden (but logged) in production.
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/php-errors.log');
if (Config::get('app.debug')) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
}

mb_internal_encoding('UTF-8');

use App\Core\Security;

// Start secure StockPilot session.
Security::startSession();
