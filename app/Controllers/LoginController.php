<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Security;
use App\Core\View;
use App\Services\AuthService;

final class LoginController
{
    /**
     * Display login page.
     */
    public function index(Request $request, array $params): Response
    {
        Security::startSession();

        if (isset($_SESSION['user'])) {
            return Response::redirect(url('/dashboard'));
        }

        return Response::html(
            View::render('auth/login', [
                'title' => 'Sign In | StockPilot 360',
                'error' => null,
                'old' => [
                    'company_id' => '',
                    'email' => '',
                ],
            ], null)
        );
    }

    /**
     * Authenticate company user.
     */
    public function authenticate(Request $request, array $params): Response
    {
        Security::startSession();

        $csrfToken = $request->input('_csrf_token');

        if (
            !is_string($csrfToken) ||
            !Security::verifyCsrfToken($csrfToken)
        ) {
            return Response::html(
                'Security token expired. Please refresh the login page.',
                419
            );
        }

        $companyId = filter_var(
            $request->input('company_id'),
            FILTER_VALIDATE_INT
        );

        $email = strtolower(
            trim((string) $request->input('email', ''))
        );

        $password = (string) $request->input('password', '');

        $old = [
            'company_id' => (string) $request->input('company_id', ''),
            'email' => $email,
        ];

        // Validate submitted details.
        if (
            $companyId === false ||
            $companyId === null ||
            $companyId <= 0 ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            strlen($email) > 190 ||
            $password === '' ||
            strlen($password) > 4096
        ) {
            return $this->showLogin(
                'Please enter valid login details.',
                $old
            );
        }

        try {
            $authService = new AuthService();

            $user = $authService->authenticate(
                $companyId,
                $email,
                $password
            );

            if ($user === null) {
                return $this->showLogin(
                    'Invalid login details or account unavailable.',
                    $old
                );
            }

            // Prevent session fixation.
            Security::regenerateSession();

            $_SESSION['user'] = [
                'id' => (int) $user['id'],
                'company_id' => (int) $user['company_id'],
                'name' => $user['name'],
                'email' => $user['email'],
            ];

            // Generate a fresh CSRF token.
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));

            return Response::redirect(url('/dashboard'));

        } catch (\Throwable $e) {
            error_log(
                'StockPilot authentication error: ' . $e->getMessage()
            );

            return $this->showLogin(
                'Unable to sign in right now. Please try again.',
                $old
            );
        }
    }

    /**
     * Secure logout.
     */
    public function logout(Request $request, array $params): Response
    {
        Security::startSession();

        $csrfToken = $request->input('_csrf_token');

        if (
            !is_string($csrfToken) ||
            !Security::verifyCsrfToken($csrfToken)
        ) {
            return Response::html(
                'Invalid security token. Please refresh and try again.',
                419
            );
        }

        Security::destroySession();

        return Response::redirect(url('/login'));
    }

    /**
     * Render login page with validation error.
     */
    private function showLogin(string $error, array $old): Response
    {
        return Response::html(
            View::render('auth/login', [
                'title' => 'Sign In | StockPilot 360',
                'error' => $error,
                'old' => $old,
            ], null),
            422
        );
    }
}
