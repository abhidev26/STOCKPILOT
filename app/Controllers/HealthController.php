<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

/**
 * GET /health - machine-readable status, useful for uptime monitors and deployment checks.
 * Returns HTTP 200 when healthy and 503 when the database is unreachable.
 */
final class HealthController
{
    /** @param array<string,string> $params */
    public function index(Request $request, array $params = []): Response
    {
        $db = Database::check();

        $payload = [
            'status'      => $db['ok'] ? 'ok' : 'degraded',
            'app'         => Config::get('app.name'),
            'version'     => Config::get('app.version'),
            'environment' => Config::get('app.env'),
            'php'         => PHP_VERSION,
            'time'        => date('c'),
            'database'    => [
                'connected' => $db['ok'],
                'version'   => $db['version'],
                'name'      => $db['database'],
            ],
        ];

        // Technical error details are only exposed while debugging locally.
        if (!$db['ok'] && Config::get('app.debug')) {
            $payload['database']['error'] = $db['error'];
        }

        return Response::json($payload, $db['ok'] ? 200 : 503);
    }
}
