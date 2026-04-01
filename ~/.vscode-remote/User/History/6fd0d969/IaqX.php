<?php

namespace App\models;

use App\Core\Model;

/**
 * Modèle métier pour la gestion des notes attribuées aux entreprises.
 *
 * Gère l'enregistrement ou la mise à jour d'une note
 * et d'un commentaire pour une entreprise donnée par un utilisateur.
 */
class NoterModel extends Model
{
    /**
     * Nom de la table associée aux notations.
     *
     * @var string
     */
    protected string $table = "Noter";

    /**
     * Enregistre ou met à jour la note d'un utilisateur sur une entreprise.
     *
     * Si une note existe déjà pour la combinaison entreprise/utilisateur,
     * la requête met à jour la note et le commentaire au lieu d'insérer
     * une nouvelle ligne.
     *
     * Cette logique repose sur une contrainte d'unicité ou une clé unique
     * en base de données sur les colonnes concernées.
     *
     * @param int $idEntreprise Identifiant de l'entreprise notée.
     * @param int $idUtilisateur Identifiant de l'utilisateur qui note.
     * @param int $note Valeur de la note attribuée.
     * @param string|null $commentaire Commentaire associé à la note.
     * @return bool True si l'opération réussit, false sinon.
     */
    public function sauvegarderNote(int $idEntreprise, int $idUtilisateur, int $note, ?string $commentaire): bool
    {
        // Prépare une requête d'insertion avec mise à jour automatique
        // si la note existe déjà pour cet utilisateur et cette entreprise.
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (Id_Entreprise, Id_Utilisateur, Note, Commentaire)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE Note = VALUES(Note), Commentaire = VALUES(Commentaire)
        ");

        // Exécute la requête avec les valeurs fournies.
        return $stmt->execute([$idEntreprise, $idUtilisateur, $note, $commentaire]);
    }
}