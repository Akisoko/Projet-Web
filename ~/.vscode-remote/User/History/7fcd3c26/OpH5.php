<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Classe utilitaire responsable de la connexion à la base de données.
 *
 * Implémente un accès centralisé à PDO via une instance unique
 * afin d'éviter de recréer plusieurs connexions pendant la même requête.
 */
class Database
{
    /**
     * Instance unique de connexion PDO partagée par l'application.
     *
     * La propriété est statique pour être réutilisable partout
     * sans réinstancier la connexion.
     *
     * @var PDO|null
     */
    private static ?PDO $pdo = null;

    /**
     * Retourne la connexion PDO active.
     *
     * Si aucune connexion n'existe encore, la méthode la crée
     * avec la configuration définie dans les constantes d'environnement.
     *
     * @return PDO La connexion PDO prête à être utilisée.
     */
    public static function getConnection(): PDO
    {
        // Si la connexion n'existe pas encore, on l'initialise.
        if (self::$pdo === null) {
            try {
                // Création de la connexion PDO à MySQL avec configuration sécurisée.
                self::$pdo = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                    DB_USER,
                    DB_PASS,
                    [
                        // Déclenche des exceptions en cas d'erreur SQL.
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                        // Retourne les résultats sous forme de tableaux associatifs par défaut.
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                        // Désactive l'émulation des requêtes préparées pour privilégier les requêtes natives.
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $e) {
                // Arrête l'application si la connexion échoue.
                die($e->getMessage());
            }
        }

        // Retourne l'instance PDO existante ou nouvellement créée.
        return self::$pdo;
    }
}