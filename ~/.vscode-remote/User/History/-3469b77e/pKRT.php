<?php

namespace App\models;

use App\Core\Model;

/**
 * Modèle métier pour la gestion des entreprises.
 *
 * Hérite de la classe Model de base et ajoute :
 * - la table "Entreprise" ;
 * - la clé primaire "Id_Entreprise" ;
 * - une méthode de recherche par nom, domaine ou description.
 */
class EntrepriseModel extends Model
{
    /**
     * Nom de la table associée à ce modèle.
     *
     * @var string
     */
    protected string $table = "Entreprise";

    /**
     * Clé primaire de la table Entreprise.
     *
     * @var string
     */
    protected string $primaryKey = "Id_Entreprise";

    /**
     * Crée une nouvelle entreprise en base de données.
     *
     * Cette méthode est un wrapper autour de create() de la classe parent
     * pour respecter la convention de nommage métier.
     *
     * @param array $data Données de l'entreprise à créer.
     * @return bool True si l'insertion réussit, false sinon.
     */
    public function createEntreprise(array $data): bool
    {
        // Délègue à la méthode générique create() de la classe de base.
        return $this->create($data);
    }

    /**
     * Recherche des entreprises par nom, domaine ou description.
     *
     * Recherche insensible à la casse avec wildcards sur les trois principaux
     * champs textuels de la table. Utilise des prepared statements pour la sécurité.
     *
     * @param string $query Terme de recherche.
     * @param array $roles Paramètre non utilisé pour ce modèle (hérité de Model).
     * @return array Liste des entreprises correspondant à la recherche.
     */
    public function search(string $query, array $roles = []): array
    {
        // Recherche dans les trois champs textuels principaux avec LIKE et wildcards.
        $stmt = $this->db->prepare("
            SELECT * FROM Entreprise
            WHERE Nom_Entreprise LIKE ?
            OR Domaine_Entreprise LIKE ?
            OR Description_Entreprise LIKE ?
        ");

        // Les wildcards %query% sont ajoutées côté PHP et non côté client,
        // ce qui reste sécurisé avec prepared statements.
        $stmt->execute(["%$query%", "%$query%", "%$query%"]);

        return $stmt->fetchAll();
    }
}