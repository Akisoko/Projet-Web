<?php

namespace App\models;

use App\Core\Model;

/**
 * Modèle métier pour la gestion des utilisateurs.
 *
 * Fournit les méthodes de recherche et de récupération
 * d'utilisateurs par email, avec filtrage par rôle.
 */
class UtilisateurModel extends Model
{
    /**
     * Nom de la table des utilisateurs.
     *
     * @var string
     */
    protected string $table = "Utilisateur";

    /**
     * Clé primaire de la table Utilisateur.
     *
     * @var string
     */
    protected string $primaryKey = "Id_Utilisateur";

    /**
     * Recherche un utilisateur par son adresse email.
     *
     * Utilisé principalement pour la connexion et la vérification d'unicité.
     *
     * @param string $email Adresse email à rechercher.
     * @return array|false L'utilisateur trouvé ou false si inexistant.
     */
    public function findByEmail(string $email): array|false
    {
        // Recherche un utilisateur unique par email avec prepared statement.
        $stmt = $this->db->prepare("SELECT * FROM Utilisateur WHERE Email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Recherche des utilisateurs par nom, prénom ou email, filtrés par rôles.
     *
     * Permet une recherche avancée avec restriction par rôle(s).
     * Si aucun rôle n'est fourni, la méthode ne retournera aucun résultat.
     *
     * @param string $query Terme de recherche.
     * @param array $roles Liste des identifiants de rôles autorisés.
     * @return array Liste des utilisateurs correspondant aux critères.
     */
    public function search(string $query, array $roles = []): array
    {
        // Génère dynamiquement les placeholders pour la liste des rôles.
        $placeholders = implode(',', array_fill(0, count($roles), '?'));

        // Recherche multi-champs avec filtrage par rôle(s).
        $stmt = $this->db->prepare("
            SELECT * FROM Utilisateur 
            WHERE (Nom_Utilisateur LIKE ? OR Prenom LIKE ? OR Email LIKE ?) 
            AND Id_Role IN ($placeholders)
        ");

        // Exécute avec le terme de recherche répété + la liste des rôles.
        $stmt->execute(["%$query%", "%$query%", "%$query%", ...$roles]);
        return $stmt->fetchAll();
    }
}