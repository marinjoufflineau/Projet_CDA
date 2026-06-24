<?php
namespace App\Services;

use App\Config\DatabaseConnection;

final class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name($_ENV['SESSION_NAME'] ?? 'local_bon_plan_session');
            session_start();
        }
    }

    public static function login(string $email, string $password): bool
    {
        $user = DatabaseConnection::getDatabase()->selectCollection('utilisateurs')->findOne([
            'email' => mb_strtolower(trim($email))
        ]);

        if (!$user || !password_verify($password, $user['password'] ?? '')) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (string)$user['_id'],
            'email' => $user['email'],
            'nom' => $user['nom'] ?? '',
            'role' => $user['role'] ?? 'user'
        ];
        return true;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return ($_SESSION['user']['role'] ?? null) === 'admin';
    }

    public static function requireLogin(): void
    {
        if (!self::user()) {
            header('Location: /login.php');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            http_response_code(403);
            exit('Accès administrateur requis.');
        }
    }
}
