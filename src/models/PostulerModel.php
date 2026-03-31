<?php

namespace App\models;

use App\Core\Model;

class PostulerModel extends Model
{
    protected string $table = "Postuler";
    protected string $primaryKey = "Id_Offre";

    public function findByUtilisateur(int $idUtilisateur): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, o.Nom_Offre, e.Nom_Entreprise
            FROM Postuler p
            JOIN Offre o ON p.Id_Offre = o.Id_Offre
            JOIN Entreprise e ON o.Id_Entreprise = e.Id_Entreprise
            WHERE p.Id_Utilisateur = ?
        ");
        $stmt->execute([$idUtilisateur]);
        return $stmt->fetchAll();
    }

    public function findAllCandidatures(): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, o.Nom_Offre, e.Nom_Entreprise, u.Nom_Utilisateur, u.Prenom
            FROM Postuler p
            JOIN Offre o ON p.Id_Offre = o.Id_Offre
            JOIN Entreprise e ON o.Id_Entreprise = e.Id_Entreprise
            JOIN Utilisateur u ON p.Id_Utilisateur = u.Id_Utilisateur
            ORDER BY p.Date_Candid DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function postuler(int $idOffre, int $idUtilisateur, string $cv, string $lettre, string $date): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO Postuler (Id_Offre, Id_Utilisateur, Date_Candid, Lettre_Motivation, CV)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$idOffre, $idUtilisateur, $date, $lettre, $cv]);
    }

    public function findOneCandidature(int $idOffre, int $idUtilisateur): array|false
    {
        $stmt = $this->db->prepare("
            SELECT p.*, o.Nom_Offre, e.Nom_Entreprise, e.Id_Entreprise,
                   u.Nom_Utilisateur, u.Prenom, u.Email
            FROM Postuler p
            JOIN Offre o ON p.Id_Offre = o.Id_Offre
            JOIN Entreprise e ON o.Id_Entreprise = e.Id_Entreprise
            JOIN Utilisateur u ON p.Id_Utilisateur = u.Id_Utilisateur
            WHERE p.Id_Offre = ? AND p.Id_Utilisateur = ?
        ");
        $stmt->execute([$idOffre, $idUtilisateur]);
        return $stmt->fetch();
    }

    public function aDejaPostule(int $idOffre, int $idUtilisateur): bool
    {
        $stmt = $this->db->prepare("
        SELECT COUNT(*) FROM Postuler 
        WHERE Id_Offre = ? AND Id_Utilisateur = ?
    ");
        $stmt->execute([$idOffre, $idUtilisateur]);
        return $stmt->fetchColumn() > 0;
    }
}