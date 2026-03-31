<?php

namespace App\models;

use App\Core\Model;

class NoterModel extends Model
{
    protected string $table = "Noter";

    public function sauvegarderNote(int $idEntreprise, int $idUtilisateur, int $note, ?string $commentaire): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (Id_Entreprise, Id_Utilisateur, Note, Commentaire)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE Note = VALUES(Note), Commentaire = VALUES(Commentaire)
        ");
        return $stmt->execute([$idEntreprise, $idUtilisateur, $note, $commentaire]);
    }
}
