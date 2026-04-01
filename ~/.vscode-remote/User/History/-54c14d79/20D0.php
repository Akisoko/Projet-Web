<?php

namespace App\models;

use App\Core\Model;

/**
 * Modèle métier pour la gestion des offres.
 *
 * Hérite du modèle de base et ajoute des méthodes spécifiques
 * pour récupérer les offres avec les informations de leur entreprise,
 * gérer la recherche, la pagination et les dernières offres publiées.
 */
class OffreModel extends Model
{
    /**
     * Nom de la table associée aux offres.
     *
     * @var string
     */
    protected string $table = "Offre";

    /**
     * Clé primaire de la table Offre.
     *
     * @var string
     */
    protected string $primaryKey = "Id_Offre";

    /**
     * Crée une nouvelle offre en base de données.
     *
     * Cette méthode sert de wrapper métier autour de create().
     *
     * @param array $data Données de l'offre à insérer.
     * @return bool True si la création réussit, false sinon.
     */
    public function createOffre(array $data): bool
    {
        // Délègue la création à la méthode générique héritée du modèle de base.
        return $this->create($data);
    }

    /**
     * Récupère toutes les offres avec le nom de leur entreprise associée.
     *
     * Le LEFT JOIN permet de retourner les offres même si aucune entreprise
     * liée n'est trouvée.
     *
     * @return array Liste complète des offres enrichies avec le nom d'entreprise.
     */
    public function findAllWithEntreprise(): array
    {
        // Récupère toutes les offres et joint le nom de l'entreprise associée.
        $stmt = $this->db->query("
            SELECT o.*, e.Nom_Entreprise 
            FROM Offre o
            LEFT JOIN Entreprise e ON o.Id_Entreprise = e.Id_Entreprise
        ");

        return $stmt->fetchAll();
    }

    /**
     * Récupère une offre par son identifiant avec les informations de son entreprise.
     *
     * @param int $id Identifiant de l'offre recherchée.
     * @return array|false Les données de l'offre ou false si elle n'existe pas.
     */
    public function findByIdWithEntreprise(int $id): array|false
    {
        // Prépare une requête sécurisée pour récupérer une offre et son entreprise.
        $stmt = $this->db->prepare("
            SELECT o.*, e.Nom_Entreprise 
            FROM Offre o
            LEFT JOIN Entreprise e ON o.Id_Entreprise = e.Id_Entreprise
            WHERE o.Id_Offre = ?
        ");

        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Récupère une page d'offres avec les informations de l'entreprise associée.
     *
     * @param int $page Numéro de la page demandée.
     * @param int $parPage Nombre d'éléments par page.
     * @return array Liste paginée des offres enrichies.
     */
    public function findPaginatedWithEntreprise(int $page, int $parPage): array
    {
        // Calcule le décalage à appliquer selon la page courante.
        $offset = ($page - 1) * $parPage;

        // Prépare une requête paginée avec jointure sur les entreprises.
        $stmt = $this->db->prepare("
            SELECT o.*, e.Nom_Entreprise
            FROM Offre o
            LEFT JOIN Entreprise e ON o.Id_Entreprise = e.Id_Entreprise
            LIMIT ? OFFSET ?
        ");

        $stmt->execute([$parPage, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Recherche des offres par nom, domaine ou description.
     *
     * Le nom de l'entreprise est aussi récupéré pour enrichir l'affichage
     * des résultats dans les vues.
     *
     * @param string $query Terme de recherche.
     * @param array $roles Paramètre non utilisé ici, conservé pour compatibilité avec le modèle parent.
     * @return array Liste des offres correspondant à la recherche.
     */
    public function search(string $query, array $roles = []): array
    {
        // Recherche les offres sur plusieurs champs textuels.
        $stmt = $this->db->prepare("
            SELECT o.*, e.Nom_Entreprise
            FROM Offre o
            LEFT JOIN Entreprise e ON o.Id_Entreprise = e.Id_Entreprise
            WHERE o.Nom_Offre LIKE ?
            OR o.Domaine_Offre LIKE ?
            OR o.Description_Offre LIKE ?
        ");

        $stmt->execute(["%$query%", "%$query%", "%$query%"]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les dernières offres publiées.
     *
     * Les résultats sont triés par date décroissante afin d'afficher
     * les offres les plus récentes en premier.
     *
     * @param int $limite Nombre maximal d'offres à retourner.
     * @return array Liste des dernières offres.
     */
    public function findDernieres(int $limite = 6): array
    {
        // Récupère les offres les plus récentes avec leur entreprise associée.
        $stmt = $this->db->prepare("
            SELECT o.*, e.Nom_Entreprise
            FROM Offre o
            LEFT JOIN Entreprise e ON o.Id_Entreprise = e.Id_Entreprise
            ORDER BY o.Date_Offre DESC
            LIMIT ?
        ");

        $stmt->execute([$limite]);
        return $stmt->fetchAll();
    }
}