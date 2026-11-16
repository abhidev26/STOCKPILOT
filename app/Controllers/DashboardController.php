<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Middleware\AuthMiddleware;

final class DashboardController
{
    public function index(Request $request, array $params): Response
    {
        // Verify user session.
        $redirect = AuthMiddleware::handle();

        if ($redirect !== null) {
            return $redirect;
        }

        $user = AuthMiddleware::user();

        if ($user === null) {
            return Response::redirect(url('/login'));
        }

        $companyId = (int) $user['company_id'];

        try {
            $pdo = Database::connection();

            // Fetch company-specific dashboard statistics.
            $statistics = [];

            $tables = [
                'products' => 'Total Products',
                'warehouses' => 'Total Warehouses',
                'suppliers' => 'Total Suppliers',
                'stock_movements' => 'Stock Movements',
            ];

            foreach ($tables as $table => $label) {
                $stmt = $pdo->prepare(
                    "SELECT COUNT(*) FROM {$table}
                     WHERE company_id = :company_id"
                );

                $stmt->execute([
                    'company_id' => $companyId,
                ]);

                $statistics[$table] = (int) $stmt->fetchColumn();
            }

            return Response::html(
                View::render('dashboard/index', [
                    'title' => 'Dashboard | StockPilot 360',
                    'user' => $user,
                    'statistics' => $statistics,
                ])
            );

        } catch (\Throwable $e) {
            error_log(
                'StockPilot dashboard error: ' . $e->getMessage()
            );

            return Response::html(
                'Unable to load dashboard statistics. Please try again.',
                500
            );
        }
    }
}
