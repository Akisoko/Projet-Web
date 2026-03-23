<?php

namespace App\Core\Model;

use App\Core\Database;

class UtilisateurModel {
    private static function db(): \App\Core\PDO
    {
        return Database::getConnection();
    }

    public static function findByEmail(string $email): array|false {
        $stmt = self::db()->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function create(string $prenom, string $nom, string $email, string $motDePasse): void {
        $hash = password_hash($motDePasse, PASSWORD_BCRYPT);
        $stmt = self::db()->prepare(
            "INSERT INTO utilisateurs (prenom, nom, email, mot_de_passe) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$prenom, $nom, $email, $hash]);
    }

    // Mettre à jour le mot de passe
    public static function updatePassword(int $id, string $motDePasse): void {
        $hash = password_hash($motDePasse, PASSWORD_BCRYPT);
        $stmt = self::db()->prepare(
            "UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?"
        );
        $stmt->execute([$hash, $id]);
    }
}