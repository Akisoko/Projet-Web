<?php

namespace App\models;

use App\Core\Model;

class WishlistModel extends Model
{
    protected string $table = "Wishlist";
    protected string $primaryKey = "Id_Offre";

    public function findByUtilisateur(int $idUtilisateur): array
    {
        $stmt = $this->db->prepare("
            SELECT w.*, o.Nom_Offre, o.Description_Offre, o.Domaine_Offre,
                   o.Remuneration, o.Date_Offre, o.Nombre_Etudiants,
                   e.Nom_Entreprise
            FROM Wishlist w
            JOIN Offre o ON w.Id_Offre = o.Id_Offre
            JOIN Entreprise e ON o.Id_Entreprise = e.Id_Entreprise
            WHERE w.Id_Utilisateur = ?
        ");
        $stmt->execute([$idUtilisateur]);
        return $stmt->fetchAll();
    }

    public function ajouter(int $idOffre, int $idUtilisateur): bool
    {
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO Wishlist (Id_Offre, Id_Utilisateur)
            VALUES (?, ?)
        ");
        return $stmt->execute([$idOffre, $idUtilisateur]);
    }

    public function retirer(int $idOffre, int $idUtilisateur): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM Wishlist WHERE Id_Offre = ? AND Id_Utilisateur = ?
        ");
        return $stmt->execute([$idOffre, $idUtilisateur]);
    }

    public function estDansWishlist(int $idOffre, int $idUtilisateur): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM Wishlist WHERE Id_Offre = ? AND Id_Utilisateur = ?
        ");
        $stmt->execute([$idOffre, $idUtilisateur]);
        return $stmt->fetchColumn() > 0;
    }
}