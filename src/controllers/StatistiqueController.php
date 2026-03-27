<?php

namespace App\controllers;

use App\Core\View;
use App\Core\Auth;
use App\models\StatistiqueModel;

class StatistiqueController
{
    public function index(): void
    {
        Auth::requis();

        $model = new StatistiqueModel();

        $stats = [
            'total_offres'              => $model->countOffres(),
            'total_entreprises'         => $model->countEntreprises(),
            'total_utilisateurs'        => $model->countUtilisateurs(),
            'total_candidatures'        => $model->countCandidatures(),
            'moyenne_candidatures'      => $model->moyenneCandidaturesParOffre(),
            'offres_par_domaine'        => $model->offresByDomaine(),
            'top_wishlist'              => $model->topWishlist(),
            'offres_par_entreprise'     => $model->offresByEntreprise(),
        ];

        View::render('statistiques.twig', ['stats' => $stats]);
    }
}