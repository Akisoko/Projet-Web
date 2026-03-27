<?php

namespace App\models;

use App\Core\Model;

class StatistiqueModel extends Model
{
    protected string $table = "Offre";
    protected string $primaryKey = "Id_Offre";

    public function countOffres(): int
    {
        return $this->db->query("SELECT COUNT(*) FROM Offre")->fetchColumn();
    }

    public function countEntreprises(): int
    {
        return $this->db->query("SELECT COUNT(*) FROM Entreprise")->fetchColumn();
    }

    public function countUtilisateurs(): int
    {
        return $this->db->query("SELECT COUNT(*) FROM Utilisateur")->fetchColumn();
    }

    public function countCandidatures(): int
    {
        return $this->db->query("SELECT COUNT(*) FROM Postuler")->fetchColumn();
    }

    public function moyenneCandidaturesParOffre(): float
    {
        $result = $this->db->query("
            SELECT AVG(nb) FROM (
                SELECT COUNT(*) as nb FROM Postuler GROUP BY Id_Offre
            ) as counts
        ")->fetchColumn();
        return round((float)$result, 1);
    }

    public function offresByDomaine(): array
    {
        $stmt = $this->db->query("
            SELECT Domaine_Offre, COUNT(*) as nombre
            FROM Offre
            GROUP BY Domaine_Offre
            ORDER BY nombre DESC
        ");
        return $stmt->fetchAll();
    }

    public function topWishlist(): array
    {
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

    public function offresByEntreprise(): array
    {
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