
<?php

declare(strict_types=1);

use App\Controllers\HealthController;
use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\DashboardController;
use App\Core\Router;

return static function (Router $router): void {

    // Public pages
    $router->get('/', [HomeController::class, 'index']);

    $router->get('/health', [HealthController::class, 'index']);

    // Authentication pages
    $router->get('/login', [LoginController::class, 'index']);

    $router->post('/login', [LoginController::class, 'authenticate']);

    $router->post('/logout', [LoginController::class, 'logout']);

    // Protected dashboard
    $router->get('/dashboard', [DashboardController::class, 'index']);
};
