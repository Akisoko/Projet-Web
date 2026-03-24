<?php

namespace App\models;

use App\Core\Model;

class OffreModel extends Model
{
    protected string $table = "Offre";
    protected string $primaryKey = "Id_Offre";

    public function createOffre(array $data): bool
    {
        return $this->create($data);
    }

    public function findAllWithEntreprise(): array
    {
        $stmt = $this->db->query("
        SELECT o.*, e.Nom_Entreprise 
        FROM Offre o
        LEFT JOIN Entreprise e ON o.Id_Entreprise = e.Id_Entreprise
    ");
        return $stmt->fetchAll();
    }

    public function findByIdWithEntreprise(int $id): array|false
    {
        $stmt = $this->db->prepare("
        SELECT o.*, e.Nom_Entreprise 
        FROM Offre o
        LEFT JOIN Entreprise e ON o.Id_Entreprise = e.Id_Entreprise
        WHERE o.Id_Offre = ?
    ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}