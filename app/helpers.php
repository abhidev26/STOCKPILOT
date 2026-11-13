<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Request;

/**
 * Escape a value for safe output inside HTML (prevents XSS).
 * Use it on EVERY dynamic value printed in a view.
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Build a URL that works whether the app lives at "/" or in a sub-folder. */
function url(string $path = ''): string
{
    return Request::basePath() . '/' . ltrim($path, '/');
}

/** URL of a file inside public/assets. */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/** Shortcut for Config::get('app.name') style lookups. */
function config(string $key, mixed $default = null): mixed
{
    return Config::get($key, $default);
}
