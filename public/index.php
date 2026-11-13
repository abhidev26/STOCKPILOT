<?php

declare(strict_types=1);

/**
 * Front controller: every web request enters the application here.
 * (public/.htaccess sends all non-file URLs to this script.)
 */

// PHP's built-in server (php -S) sends every request here, including CSS/JS/images.
// Returning false tells it to serve real files from /public itself. Apache ignores this.
if (PHP_SAPI === 'cli-server') {
    $requested = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $file = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, (string) $requested);
    if ($requested !== '/' && is_file($file) && basename($file) !== 'index.php') {
        return false;
    }
}

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Config;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\View;

try {
    $router = new Router();
    (require BASE_PATH . '/routes/web.php')($router);

    $response = $router->dispatch(Request::capture());
} catch (Throwable $e) {
    Logger::error($e->getMessage(), [
        'type' => $e::class,
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ]);

    try {
        $response = Response::html(
            View::render('errors/500', [
                'title'   => 'Server error',
                'message' => Config::get('app.debug') ? $e->getMessage() : null,
            ]),
            500
        );
    } catch (Throwable) {
        $response = Response::html('<h1>500 - Server error</h1>', 500);
    }
}

$response->send();
