<?php

namespace App\Core;

class Auth
{
    public static function session(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function utilisateur(): array|null
    {
        self::session();
        return $_SESSION['utilisateur'] ?? null;
    }

    public static function role(): int|null
    {
        $user = self::utilisateur();
        return $user ? (int)$user['Id_Role'] : null;
    }

    public static function estConnecte(): bool
    {
        self::session();
        return isset($_SESSION['utilisateur']);
    }

    public static function estAdmin(): bool
    {
        return self::role() === 1;
    }

    public static function estEtudiant(): bool
    {
        return self::role() === 2;
    }

    public static function estPilote(): bool
    {
        return self::role() === 3;
    }

    public static function requis(): void
    {
        if (!self::estConnecte()) {
            header('Location: /connexion');
            exit;
        }
    }

    public static function requisRole(array $roles): void
    {
        self::requis();
        if (!in_array(self::role(), $roles)) {
            header('Location: /accueil');
            exit;
        }
    }
}