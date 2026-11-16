<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Security;
use App\Core\Response;

final class AuthMiddleware
{
    /**
     * Check whether a user is authenticated.
     */
    public static function check(): bool
    {
        Security::startSession();

        return isset($_SESSION['user'])
            && is_array($_SESSION['user'])
            && !empty($_SESSION['user']['id'])
            && !empty($_SESSION['user']['company_id']);
    }

    /**
     * Get the authenticated user.
     */
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        return $_SESSION['user'];
    }

    /**
     * Protect a route from unauthenticated access.
     */
    public static function handle(): ?Response
    {
        if (!self::check()) {
            return Response::redirect(url('/login'));
        }

        return null;
    }
}
