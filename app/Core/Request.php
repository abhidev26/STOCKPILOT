<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Immutable view of the current HTTP request.
 */
final class Request
{
    private static string $basePath = '';

    /**
     * @param array<string,mixed> $query
     * @param array<string,mixed> $body
     */
    public function __construct(
        private string $method,
        private string $path,
        private array $query = [],
        private array $body = [],
        private string $ip = ''
    ) {
        $this->method = strtoupper($method);
        $this->path = self::normalizePath($path);
    }

    public static function capture(): self
    {
        $uriPath = rawurldecode((string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'));
        self::$basePath = self::detectBasePath($uriPath, $_SERVER['SCRIPT_NAME'] ?? '');

        $path = self::$basePath !== '' ? substr($uriPath, strlen(self::$basePath)) : $uriPath;

        return new self(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            (string) $path,
            $_GET,
            $_POST,
            (string) ($_SERVER['REMOTE_ADDR'] ?? '')
        );
    }

    /**
     * Works out the sub-folder the app lives in, so the same code runs at
     *   http://localhost:8000/              (base "")
     *   http://localhost/stockpilot-360/    (base "/stockpilot-360")
     *   http://localhost/stockpilot-360/public/ (base "/stockpilot-360/public")
     */
    public static function detectBasePath(string $uriPath, string $scriptName): string
    {
        $dir = str_replace('\\', '/', dirname($scriptName));
        if ($dir === '/' || $dir === '.') {
            $dir = '';
        }

        $candidates = [];
        if ($dir !== '') {
            $candidates[] = $dir;
            $parent = (string) preg_replace('#/public$#', '', $dir);
            if ($parent !== $dir && $parent !== '') {
                $candidates[] = $parent;
            }
        }

        foreach ($candidates as $candidate) {
            if ($uriPath === $candidate || str_starts_with($uriPath, $candidate . '/')) {
                return $candidate;
            }
        }
        return '';
    }

    public static function basePath(): string
    {
        return self::$basePath;
    }

    private static function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '' ? '/' : $path;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }

    public function ip(): string
    {
        return $this->ip;
    }
}
