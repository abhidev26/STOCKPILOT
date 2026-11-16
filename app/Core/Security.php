<?php

declare(strict_types=1);

namespace App\Core;

final class Security
{
    /**
     * Start a secure session.
     */
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        if (headers_sent()) {
            throw new \RuntimeException(
                'Cannot start session because headers were already sent.'
            );
        }

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');

        $isHttps = isset($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== ''
            && strtolower((string) $_SERVER['HTTPS']) !== 'off';

        session_name('STOCKPILOT_SESSION');

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        if (!session_start()) {
            throw new \RuntimeException('Unable to start secure session.');
        }
    }

    /**
     * Generate or return the current CSRF token.
     */
    public static function csrfToken(): string
    {
        self::startSession();

        if (
            !isset($_SESSION['_csrf_token']) ||
            !is_string($_SESSION['_csrf_token'])
        ) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }

    /**
     * Validate a submitted CSRF token.
     */
    public static function verifyCsrfToken(?string $token): bool
    {
        self::startSession();

        if (
            !is_string($token) ||
            $token === '' ||
            !isset($_SESSION['_csrf_token'])
        ) {
            return false;
        }

        return hash_equals($_SESSION['_csrf_token'], $token);
    }

    /**
     * Regenerate the session ID after login.
     */
    public static function regenerateSession(): void
    {
        self::startSession();

        if (!session_regenerate_id(true)) {
            throw new \RuntimeException(
                'Unable to regenerate session ID.'
            );
        }
    }

    /**
     * Destroy the current session.
     */
    public static function destroySession(): void
    {
        self::startSession();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }

        session_destroy();
    }
}
