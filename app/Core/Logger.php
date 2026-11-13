<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Tiny file logger. Writes to storage/logs/app-YYYY-MM-DD.log
 */
final class Logger
{
    /** @param array<string,mixed> $context */
    public static function error(string $message, array $context = []): void
    {
        self::write('ERROR', $message, $context);
    }

    /** @param array<string,mixed> $context */
    public static function info(string $message, array $context = []): void
    {
        self::write('INFO', $message, $context);
    }

    /** @param array<string,mixed> $context */
    private static function write(string $level, string $message, array $context): void
    {
        $dir = BASE_PATH . '/storage/logs';
        $line = sprintf(
            "[%s] %s: %s%s\n",
            date('Y-m-d H:i:s'),
            $level,
            $message,
            $context ? ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR) : ''
        );

        $ok = is_dir($dir) && is_writable($dir)
            && @file_put_contents($dir . '/app-' . date('Y-m-d') . '.log', $line, FILE_APPEND | LOCK_EX) !== false;

        if (!$ok) {
            // Fall back to PHP's own error log so the message is never lost.
            error_log(trim($line));
        }
    }
}
