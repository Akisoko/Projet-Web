<?php

namespace App\models;

use App\Core\Model;

/**
 * Modèle métier pour la gestion des candidatures.
 *
 * Gère l'enregistrement des candidatures, la récupération
 * des candidatures d'un utilisateur, la consultation globale
 * et la vérification d'une candidature existante.
 */
class PostulerModel extends Model
{
    /**
     * Nom de la table associée aux candidatures.
     *
     * @var string
     */
    protected string $table = "Postuler";

    /**
     * Clé primaire définie pour le modèle.
     *
     * @var string
     */
    protected string $primaryKey = "Id_Offre";

    /**
     * Récupère toutes les candidatures d'un utilisateur donné.
     *
     * Les résultats incluent aussi le nom de l'offre
     * et le nom de l'entreprise associée.
     *
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @return array Liste des candidatures de l'utilisateur.
     */
    public function findByUtilisateur(int $idUtilisateur): array
    {
        // Récupère les candidatures d'un utilisateur avec les informations liées à l'offre et à l'entreprise.
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

    /**
     * Récupère l'ensemble des candidatures enregistrées.
     *
     * Les résultats sont enrichis avec les informations de l'offre,
     * de l'entreprise et de l'utilisateur ayant postulé.
     *
     * @return array Liste complète des candidatures.
     */
    public function findAllCandidatures(): array
    {
        // Charge toutes les candidatures avec les données utiles à l'affichage administratif.
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

    /**
     * Enregistre une nouvelle candidature.
     *
     * La candidature contient :
     * - l'offre visée ;
     * - l'utilisateur candidat ;
     * - la date de candidature ;
     * - le chemin du CV ;
     * - le chemin éventuel de la lettre de motivation.
     *
     * @param int $idOffre Identifiant de l'offre visée.
     * @param int $idUtilisateur Identifiant de l'utilisateur candidat.
     * @param string $cv Chemin du fichier CV.
     * @param string $lettre Chemin du fichier de lettre de motivation.
     * @param string $date Date de candidature.
     * @return bool True si l'insertion réussit, false sinon.
     */
    public function postuler(int $idOffre, int $idUtilisateur, string $cv, string $lettre, string $date): bool
    {
        // Prépare l'insertion d'une nouvelle candidature en base.
        $stmt = $this->db->prepare("
            INSERT INTO Postuler (Id_Offre, Id_Utilisateur, Date_Candid, Lettre_Motivation, CV)
            VALUES (?, ?, ?, ?, ?)
        ");

        return $stmt->execute([$idOffre, $idUtilisateur, $date, $lettre, $cv]);
    }

    /**
     * Récupère une candidature précise à partir de l'offre et de l'utilisateur.
     *
     * Les données retournées incluent aussi :
     * - l'offre ;
     * - l'entreprise ;
     * - les informations du candidat.
     *
     * @param int $idOffre Identifiant de l'offre.
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @return array|false Les données de la candidature ou false si introuvable.
     */
    public function findOneCandidature(int $idOffre, int $idUtilisateur): array|false
    {
        // Charge une candidature précise avec toutes les informations nécessaires à son affichage détaillé.
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

    /**
     * Vérifie si un utilisateur a déjà postulé à une offre donnée.
     *
     * @param int $idOffre Identifiant de l'offre.
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @return bool True si une candidature existe déjà, false sinon.
     */
    public function aDejaPostule(int $idOffre, int $idUtilisateur): bool
    {
        // Compte le nombre de candidatures correspondant au couple offre / utilisateur.
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM Postuler 
            WHERE Id_Offre = ? AND Id_Utilisateur = ?
        ");

        $stmt->execute([$idOffre, $idUtilisateur]);

        // Retourne vrai si au moins une candidature existe déjà.
        return $stmt->fetchColumn() > 0;
    }
}