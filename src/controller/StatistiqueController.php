<?php

namespace App\Controller;

use App\Core\View;

class StatistiqueController
{
    public function index(): void
    {
        // TODO : récupérer les stats depuis la BDD
        // $stats = [
        //     'total_offres' => OffreModel::count(),
        //     'total_entreprises' => EntrepriseModel::count(),
        //     'total_utilisateurs' => UtilisateurModel::count(),
        //     'total_candidatures' => CandidatureModel::count(),
        //     'offres_par_ville' => OffreModel::countByVille(),
        //     'offres_par_entreprise' => OffreModel::countByEntreprise(),
        // ];

        // Simulation pour l'instant
        $stats = [
            'total_offres' => 24,
            'total_entreprises' => 8,
            'total_utilisateurs' => 42,
            'total_candidatures' => 17,
            'offres_par_ville' => [
                'Paris' => 10,
                'Lyon' => 7,
                'Bordeaux' => 4,
                'Marseille' => 3,
            ],
            'offres_par_entreprise' => [
                'Entreprise A' => 8,
                'Entreprise B' => 6,
                'Entreprise C' => 5,
                'Entreprise D' => 5,
            ],
        ];

        View::render('statistiques.twig', ['stats' => $stats]);
    }
}