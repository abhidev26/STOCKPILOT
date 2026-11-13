<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Minimal .env loader (no external packages).
 * Real environment variables take priority over values in the .env file.
 */
final class Env
{
    /** @var array<string,string> */
    private static array $vars = [];

    public static function load(string $path): void
    {
        if (!is_file($path) || !is_readable($path)) {
            return;
        }
        self::$vars = self::parse((string) file_get_contents($path)) + self::$vars;
    }

    /**
     * Parse the text of a .env file into key => value pairs.
     *
     * @return array<string,string>
     */
    public static function parse(string $contents): array
    {
        $result = [];
        foreach (preg_split('/\R/', $contents) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ($value !== '' && ($value[0] === '"' || $value[0] === "'")) {
                $quote = $value[0];
                $end = strrpos($value, $quote);
                $value = $end > 0 ? substr($value, 1, $end - 1) : substr($value, 1);
            } else {
                // Strip trailing comments on unquoted values: KEY=value # comment
                $value = trim((string) preg_replace('/\s+#.*$/', '', $value));
            }

            if ($key !== '') {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $real = getenv($key);
        if ($real !== false) {
            return $real;
        }
        return self::$vars[$key] ?? $default;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = self::get($key);
        if ($value === null) {
            return $default;
        }
        return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
    }
}
