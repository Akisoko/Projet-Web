<?php

namespace App\Core;

/**
 * Classe utilitaire pour la gestion centralisée de l'authentification.
 *
 * Fournit des méthodes statiques pour :
 * - la gestion des sessions PHP ;
 * - la vérification de l'état de connexion ;
 * - les contrôles d'accès par rôle.
 */
class Auth
{
    /**
     * Initialise ou reprend la session PHP courante.
     *
     * Vérifie l'état de la session avant d'appeler `session_start()`
     * pour éviter les erreurs en cas de session déjà active.
     *
     * @return void
     */
    public static function session(): void
    {
        // Ne démarre une session que si elle n'existe pas déjà.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Récupère les informations de l'utilisateur connecté depuis la session.
     *
     * @return array|null Les données utilisateur ou null si non connecté.
     */
    public static function utilisateur(): array|null
    {
        self::session();
        return $_SESSION['utilisateur'] ?? null;
    }

    /**
     * Retourne le rôle de l'utilisateur connecté.
     *
     * @return int|null L'identifiant du rôle ou null si non connecté.
     */
    public static function role(): int|null
    {
        $user = self::utilisateur();
        return $user ? (int)$user['Id_Role'] : null;
    }

    /**
     * Vérifie si un utilisateur est actuellement connecté.
     *
     * @return bool True si connecté, false sinon.
     */
    public static function estConnecte(): bool
    {
        self::session();
        return isset($_SESSION['utilisateur']);
    }

    /**
     * Vérifie si l'utilisateur connecté est un administrateur (rôle 1).
     *
     * @return bool True si administrateur, false sinon.
     */
    public static function estAdmin(): bool
    {
        return self::role() === 1;
    }

    /**
     * Vérifie si l'utilisateur connecté est un étudiant (rôle 2).
     *
     * @return bool True si étudiant, false sinon.
     */
    public static function estEtudiant(): bool
    {
        return self::role() === 2;
    }

    /**
     * Vérifie si l'utilisateur connecté est un pilote (rôle 3).
     *
     * @return bool True si pilote, false sinon.
     */
    public static function estPilote(): bool
    {
        return self::role() === 3;
    }

    /**
     * Redirige vers la page de connexion si l'utilisateur n'est pas authentifié.
     *
     * @return void
     */
    public static function requis(): void
    {
        if (!self::estConnecte()) {
            header('Location: /connexion');
            exit;
        }
    }

    /**
     * Vérifie qu'un utilisateur connecté possède l'un des rôles spécifiés.
     *
     * Combine `requis()` et une vérification de rôle spécifique.
     *
     * @param array $roles Liste des rôles autorisés (ex: [1, 3]).
     * @return void
     */
    public static function requisRole(array $roles): void
    {
        // Vérifie d'abord que l'utilisateur est connecté.
        self::requis();

        // Vérifie que le rôle de l'utilisateur est dans la liste autorisée.
        if (!in_array(self::role(), $roles)) {
            header('Location: /accueil');
            exit;
        }
    }
}