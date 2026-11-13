<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;

/**
 * Stage 1 landing page: a "system check" that proves PHP, the folders and the
 * database connection are all configured correctly.
 */
final class HomeController
{
    /** @param array<string,string> $params */
    public function index(Request $request, array $params = []): Response
    {
        $db = Database::check();
        $logDir = BASE_PATH . '/storage/logs';
        $requiredExtensions = ['pdo_mysql', 'mbstring', 'fileinfo', 'json'];
        $missing = array_values(array_filter($requiredExtensions, static fn (string $e): bool => !extension_loaded($e)));

        $checks = [
            [
                'label'  => 'PHP version (8.2 or newer)',
                'status' => version_compare(PHP_VERSION, '8.2.0', '>=') ? 'ok' : 'fail',
                'detail' => 'Running PHP ' . PHP_VERSION,
            ],
            [
                'label'  => 'Required PHP extensions',
                'status' => $missing === [] ? 'ok' : 'fail',
                'detail' => $missing === []
                    ? implode(', ', $requiredExtensions) . ' loaded'
                    : 'Missing: ' . implode(', ', $missing) . ' (enable them in php.ini)',
            ],
            [
                'label'  => 'Environment file (.env)',
                'status' => is_file(BASE_PATH . '/.env') ? 'ok' : 'warn',
                'detail' => is_file(BASE_PATH . '/.env')
                    ? '.env found'
                    : '.env not found - using defaults. Copy .env.example to .env',
            ],
            [
                'label'  => 'Log folder writable',
                'status' => is_dir($logDir) && is_writable($logDir) ? 'ok' : 'fail',
                'detail' => 'storage/logs',
            ],
            [
                'label'  => 'MySQL connection',
                'status' => $db['ok'] ? 'ok' : 'fail',
                'detail' => $db['ok']
                    ? 'MySQL ' . $db['version'] . ', database "' . $db['database'] . '"'
                    : (string) $db['hint'],
            ],
        ];

        $html = View::render('home/index', [
            'title'     => 'System check',
            'checks'    => $checks,
            'dbError'   => !$db['ok'] && Config::get('app.debug') ? $db['error'] : null,
            'allPassed' => !in_array('fail', array_column($checks, 'status'), true),
        ]);

        return Response::html($html);
    }
}
