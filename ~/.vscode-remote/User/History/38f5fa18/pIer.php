<?php

namespace App\models;

use App\Core\Model;

/**
 * Modèle métier pour la gestion des favoris (wishlist).
 *
 * Permet de :
 * - récupérer les offres mises en favoris par un utilisateur ;
 * - ajouter une offre en wishlist ;
 * - retirer une offre de la wishlist ;
 * - vérifier si une offre est déjà en favoris.
 */
class WishlistModel extends Model
{
    /**
     * Nom de la table associée aux favoris.
     *
     * @var string
     */
    protected string $table = "Wishlist";

    /**
     * Clé primaire déclarée pour ce modèle.
     *
     * @var string
     */
    protected string $primaryKey = "Id_Offre";

    /**
     * Récupère toutes les offres présentes dans la wishlist d'un utilisateur.
     *
     * Les résultats incluent les principales informations de l'offre
     * ainsi que le nom de l'entreprise associée.
     *
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @return array Liste des offres présentes dans sa wishlist.
     */
    public function findByUtilisateur(int $idUtilisateur): array
    {
        // Charge les favoris de l'utilisateur avec les détails utiles à l'affichage.
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

    /**
     * Ajoute une offre dans la wishlist d'un utilisateur.
     *
     * L'utilisation de INSERT IGNORE évite une erreur SQL
     * si l'offre est déjà présente dans les favoris.
     *
     * @param int $idOffre Identifiant de l'offre à ajouter.
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @return bool True si la requête s'exécute correctement, false sinon.
     */
    public function ajouter(int $idOffre, int $idUtilisateur): bool
    {
        // Insère le favori uniquement s'il n'existe pas déjà.
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO Wishlist (Id_Offre, Id_Utilisateur)
            VALUES (?, ?)
        ");

        return $stmt->execute([$idOffre, $idUtilisateur]);
    }

    /**
     * Retire une offre de la wishlist d'un utilisateur.
     *
     * @param int $idOffre Identifiant de l'offre à retirer.
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @return bool True si la suppression s'exécute correctement, false sinon.
     */
    public function retirer(int $idOffre, int $idUtilisateur): bool
    {
        // Supprime le couple offre / utilisateur de la wishlist.
        $stmt = $this->db->prepare("
            DELETE FROM Wishlist WHERE Id_Offre = ? AND Id_Utilisateur = ?
        ");

        return $stmt->execute([$idOffre, $idUtilisateur]);
    }

    /**
     * Vérifie si une offre est déjà présente dans la wishlist d'un utilisateur.
     *
     * @param int $idOffre Identifiant de l'offre.
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @return bool True si l'offre est déjà en favoris, false sinon.
     */
    public function estDansWishlist(int $idOffre, int $idUtilisateur): bool
    {
        // Compte les lignes correspondant à ce favori potentiel.
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM Wishlist WHERE Id_Offre = ? AND Id_Utilisateur = ?
        ");

        $stmt->execute([$idOffre, $idUtilisateur]);

        // Retourne vrai si au moins une ligne existe.
        return $stmt->fetchColumn() > 0;
    }
}