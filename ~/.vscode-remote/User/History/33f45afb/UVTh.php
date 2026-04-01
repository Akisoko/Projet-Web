<?php

namespace App\models;

use App\Core\Model;

/**
 * Modèle dédié au calcul des statistiques globales de l'application.
 *
 * Fournit des indicateurs clés sur :
 * - les comptes globaux (offres, entreprises, utilisateurs, candidatures) ;
 * - les moyennes et répartitions par domaine/entreprise ;
 * - les tops wishlist.
 */
class StatistiqueModel extends Model
{
    /**
     * Table par défaut définie (non utilisée directement ici).
     *
     * @var string
     */
    protected string $table = "Offre";

    /**
     * Clé primaire par défaut (non utilisée directement ici).
     *
     * @var string
     */
    protected string $primaryKey = "Id_Offre";

    /**
     * Compte le nombre total d'offres publiées.
     *
     * @return int Nombre d'offres en base.
     */
    public function countOffres(): int
    {
        // Compte toutes les offres disponibles.
        return $this->db->query("SELECT COUNT(*) FROM Offre")->fetchColumn();
    }

    /**
     * Compte le nombre total d'entreprises enregistrées.
     *
     * @return int Nombre d'entreprises en base.
     */
    public function countEntreprises(): int
    {
        // Compte toutes les entreprises référencées.
        return $this->db->query("SELECT COUNT(*) FROM Entreprise")->fetchColumn();
    }

    /**
     * Compte le nombre total d'utilisateurs inscrits.
     *
     * @return int Nombre d'utilisateurs en base.
     */
    public function countUtilisateurs(): int
    {
        // Compte tous les utilisateurs de la plateforme.
        return $this->db->query("SELECT COUNT(*) FROM Utilisateur")->fetchColumn();
    }

    /**
     * Compte le nombre total de candidatures déposées.
     *
     * @return int Nombre de candidatures en base.
     */
    public function countCandidatures(): int
    {
        // Compte toutes les candidatures soumises.
        return $this->db->query("SELECT COUNT(*) FROM Postuler")->fetchColumn();
    }

    /**
     * Calcule la moyenne de candidatures par offre.
     *
     * Utilise une sous-requête pour compter les candidatures par offre,
     * puis calcule la moyenne de ces comptes.
     *
     * @return float Moyenne arrondie à 1 décimale.
     */
    public function moyenneCandidaturesParOffre(): float
    {
        // Calcule la moyenne du nombre de candidatures par offre.
        $result = $this->db->query("
            SELECT AVG(nb) FROM (
                SELECT COUNT(*) as nb FROM Postuler GROUP BY Id_Offre
            ) as counts
        ")->fetchColumn();

        // Arrondit le résultat à une décimale pour l'affichage.
        return round((float)$result, 1);
    }

    /**
     * Récupère la répartition des offres par domaine d'activité.
     *
     * @return array Tableau associatif [domaine => nombre_offres], trié par nombre décroissant.
     */
    public function offresByDomaine(): array
    {
        // Compte les offres par domaine et trie par popularité.
        $stmt = $this->db->query("
            SELECT Domaine_Offre, COUNT(*) as nombre
            FROM Offre
            GROUP BY Domaine_Offre
            ORDER BY nombre DESC
        ");

        return $stmt->fetchAll();
    }

    /**
     * Récupère les 5 offres les plus ajoutées en wishlist.
     *
     * @return array Top 5 des offres les plus populaires en favoris.
     */
    public function topWishlist(): array
    {
        // Identifie les offres les plus populaires via les wishlists.
        $stmt = $this->db->query("
            SELECT o.Nom_Offre, e.Nom_Entreprise, COUNT(*) as nombre
            FROM Wishlist w
            JOIN Offre o ON w.Id_Offre = o.Id_Offre
            JOIN Entreprise e ON o.Id_Entreprise = e.Id_Entreprise
            GROUP BY w.Id_Offre
            ORDER BY nombre DESC
            LIMIT 5
        ");

        return $stmt->fetchAll();
    }

    /**
     * Récupère le nombre d'offres publiées par entreprise.
     *
     * @return array Tableau associatif [nom_entreprise => nombre_offres], trié par nombre décroissant.
     */
    public function offresByEntreprise(): array
    {
        // Classe les entreprises par nombre d'offres publiées.
        $stmt = $this->db->query("
            SELECT e.Nom_Entreprise, COUNT(*) as nombre
            FROM Offre o
            JOIN Entreprise e ON o.Id_Entreprise = e.Id_Entreprise
            GROUP BY o.Id_Entreprise
            ORDER BY nombre DESC
        ");

        return $stmt->fetchAll();
    }
}