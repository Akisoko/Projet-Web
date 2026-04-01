<?php

namespace App\Core;

use App\Core\Database;
use PDO;

/**
 * Modèle de base de l'application.
 *
 * Fournit les opérations CRUD génériques communes à tous les modèles :
 * - récupération de tous les enregistrements ;
 * - récupération par identifiant ;
 * - création ;
 * - mise à jour ;
 * - suppression ;
 * - comptage ;
 * - pagination.
 *
 * Les modèles enfants doivent définir au minimum :
 * - le nom de la table ;
 * - éventuellement la clé primaire.
 */
class Model
{
    /**
     * Instance PDO utilisée pour toutes les requêtes du modèle.
     *
     * @var PDO
     */
    protected PDO $db;

    /**
     * Nom de la table associée au modèle enfant.
     *
     * Cette propriété doit être définie dans les classes héritées.
     *
     * @var string
     */
    protected string $table;

    /**
     * Nom de la clé primaire de la table.
     *
     * Par défaut : "id".
     * Peut être redéfinie dans les modèles enfants.
     *
     * @var string
     */
    protected string $primaryKey = "id";

    /**
     * Initialise le modèle avec la connexion PDO partagée.
     *
     * @return void
     */
    public function __construct()
    {
        // Récupère la connexion à la base de données via la classe Database.
        $this->db = Database::getConnection();
    }

    /**
     * Récupère tous les enregistrements de la table.
     *
     * @return array Liste complète des enregistrements.
     */
    public function findAll(): array
    {
        // Exécute une requête simple pour récupérer toutes les lignes de la table.
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    /**
     * Récupère un enregistrement à partir de sa clé primaire.
     *
     * @param int $id Identifiant de l'enregistrement recherché.
     * @return array|false Les données trouvées ou false si aucune ligne ne correspond.
     */
    public function findById(int $id): array|false
    {
        // Prépare une requête sécurisée pour récupérer une ligne par identifiant.
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?"
        );

        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Insère un nouvel enregistrement dans la table.
     *
     * Le tableau fourni doit contenir les noms de colonnes comme clés
     * et les valeurs à insérer comme valeurs.
     *
     * @param array $data Données à insérer.
     * @return bool True si l'insertion réussit, false sinon.
     */
    public function create(array $data): bool
    {
        // Construit dynamiquement la liste des colonnes à insérer.
        $columns = implode(',', array_keys($data));

        // Génère autant de placeholders que de valeurs à insérer.
        $placeholders = implode(',', array_fill(0, count($data), '?'));

        // Prépare la requête d'insertion.
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)"
        );

        // Exécute la requête avec les valeurs du tableau.
        return $stmt->execute(array_values($data));
    }

    /**
     * Met à jour un enregistrement existant.
     *
     * @param int $id Identifiant de l'enregistrement à modifier.
     * @param array $data Données à mettre à jour.
     * @return bool True si la mise à jour réussit, false sinon.
     */
    public function update(int $id, array $data): bool
    {
        // Construit dynamiquement la clause SET : colonne1 = ?, colonne2 = ?, etc.
        $fields = implode(',', array_map(fn($col) => "$col = ?", array_keys($data)));

        // Prépare la requête de mise à jour ciblée par clé primaire.
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET $fields WHERE {$this->primaryKey} = ?"
        );

        // Exécute la requête avec les nouvelles valeurs puis l'identifiant en dernier paramètre.
        return $stmt->execute([...array_values($data), $id]);
    }

    /**
     * Supprime un enregistrement par son identifiant.
     *
     * @param int $id Identifiant de l'enregistrement à supprimer.
     * @return bool True si la suppression réussit, false sinon.
     */
    public function delete(int $id): bool
    {
        // Prépare une requête de suppression sécurisée.
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?"
        );

        return $stmt->execute([$id]);
    }

    /**
     * Compte le nombre total d'enregistrements de la table.
     *
     * @return int Nombre total de lignes.
     */
    public function count(): int
    {
        // Utilise COUNT(*) pour récupérer le total des enregistrements.
        return $this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    /**
     * Récupère une page de résultats avec pagination.
     *
     * @param int $page Numéro de page demandé.
     * @param int $parPage Nombre d'éléments par page.
     * @return array Liste paginée des résultats.
     */
    public function findPaginated(int $page, int $parPage): array
    {
        // Calcule le décalage à appliquer selon la page courante.
        $offset = ($page - 1) * $parPage;

        // Prépare une requête avec limite et décalage.
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} LIMIT ? OFFSET ?");

        $stmt->execute([$parPage, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Méthode de recherche générique.
     *
     * Cette méthode est prévue pour être redéfinie dans les modèles enfants
     * selon les besoins métier de chaque entité.
     *
     * @param string $query Texte recherché.
     * @param array $roles Paramètre optionnel utilisé dans certains modèles spécialisés.
     * @return array
     */
    public function search(string $query, array $roles = []): array
    {
        // Implémentation vide dans la classe de base.
        // Chaque modèle enfant peut fournir sa propre logique de recherche.
        return [];
    }
}