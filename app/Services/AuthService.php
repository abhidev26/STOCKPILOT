<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class AuthService
{
    /**
     * Authenticate a user using company ID, email and password.
     *
     * Returns user details on success, or null on failure.
     */
    public function authenticate(
        int $companyId,
        string $email,
        string $password
    ): ?array {

        if ($companyId <= 0 || $email === '' || $password === '') {
            return null;
        }

        $pdo = Database::connection();

        // Find the user inside the specified company.
        $stmt = $pdo->prepare(
            'SELECT
                id,
                company_id,
                name,
                email,
                password_hash,
                status
             FROM users
             WHERE company_id = :company_id
               AND email = :email
             LIMIT 1'
        );

        $stmt->execute([
            'company_id' => $companyId,
            'email' => $email,
        ]);

        $user = $stmt->fetch();

        // Reject missing users and inactive or locked accounts.
        if (!$user || $user['status'] !== 'active') {
            return null;
        }

        // Verify the submitted password against the stored hash.
        if (!password_verify($password, $user['password_hash'])) {
            return null;
        }

        // Upgrade the password hash when PHP recommends it.
        if (password_needs_rehash(
            $user['password_hash'],
            PASSWORD_DEFAULT
        )) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);

            $updateHash = $pdo->prepare(
                'UPDATE users
                 SET password_hash = :password_hash
                 WHERE id = :id AND company_id = :company_id'
            );

            $updateHash->execute([
                'password_hash' => $newHash,
                'id' => $user['id'],
                'company_id' => $user['company_id'],
            ]);
        }

        // Record the successful login time.
        $updateLogin = $pdo->prepare(
            'UPDATE users
             SET last_login_at = CURRENT_TIMESTAMP
             WHERE id = :id AND company_id = :company_id'
        );

        $updateLogin->execute([
            'id' => $user['id'],
            'company_id' => $user['company_id'],
        ]);

        // Never return the password hash.
        unset($user['password_hash']);

        return $user;
    }
}
