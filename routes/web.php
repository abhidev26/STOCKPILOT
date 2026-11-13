<?php

declare(strict_types=1);

use App\Controllers\HealthController;
use App\Controllers\HomeController;
use App\Core\Router;

/**
 * All web routes. Later stages add login, dashboard, products, etc. here.
 */
return static function (Router $router): void {
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/health', [HealthController::class, 'index']);
};
