<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

/**
 * Single shared PDO connection (created lazily on first use).
 *
 * Rules for the whole project:
 *  - Always use prepared statements with bound parameters. Never concatenate user input into SQL.
 *  - Exceptions are enabled, so a failed query can never be silently ignored.
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            Config::get('database.host'),
            Config::get('database.port'),
            Config::get('database.database'),
            Config::get('database.charset')
        );

        self::$pdo = new PDO(
            $dsn,
            (string) Config::get('database.username'),
            (string) Config::get('database.password'),
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // real server-side prepared statements
                PDO::ATTR_STRINGIFY_FETCHES  => false,
            ]
        );

        return self::$pdo;
    }

    public static function disconnect(): void
    {
        self::$pdo = null;
    }

    /**
     * Try to connect and report the result without throwing.
     *
     * @return array{ok:bool,version:?string,database:?string,error:?string,hint:?string}
     */
    public static function check(): array
    {
        try {
            $row = self::connection()->query('SELECT VERSION() AS v, DATABASE() AS d')->fetch();
            return [
                'ok'       => true,
                'version'  => (string) $row['v'],
                'database' => (string) $row['d'],
                'error'    => null,
                'hint'     => null,
            ];
        } catch (PDOException $e) {
            Logger::error('Database connection check failed', ['message' => $e->getMessage()]);
            return [
                'ok'       => false,
                'version'  => null,
                'database' => null,
                'error'    => $e->getMessage(),
                'hint'     => self::hint($e->getMessage()),
            ];
        }
    }

    /** Beginner-friendly explanation for the most common connection failures. */
    private static function hint(string $message): string
    {
        $code = preg_match('/\[(\d{4})\]/', $message, $m) ? (int) $m[1] : 0;

        return match ($code) {
            1049 => 'The database does not exist yet. Create it in phpMyAdmin (see README, step "Create the database").',
            1045 => 'MySQL rejected the username or password. Check DB_USERNAME and DB_PASSWORD in your .env file.',
            2002 => 'Cannot reach MySQL. Start MySQL in the XAMPP Control Panel and check DB_HOST / DB_PORT in .env.',
            default => 'Check the DB_* values in your .env file and that MySQL is running.',
        };
    }
}
