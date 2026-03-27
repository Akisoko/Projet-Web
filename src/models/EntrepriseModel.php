<?php

namespace App\models;

use App\Core\Model;

class EntrepriseModel extends Model
{
    protected string $table = "Entreprise";
    protected string $primaryKey = "Id_Entreprise";

    public function createEntreprise(array $data): bool
    {
        return $this->create($data);
    }

    public function search(string $query, array $roles = []): array
    {
        $stmt = $this->db->prepare("
        SELECT * FROM Entreprise
        WHERE Nom_Entreprise LIKE ?
        OR Domaine_Entreprise LIKE ?
        OR Description_Entreprise LIKE ?
    ");
        $stmt->execute(["%$query%", "%$query%", "%$query%"]);
        return $stmt->fetchAll();
    }
}