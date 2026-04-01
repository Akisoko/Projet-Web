<?php

namespace App\controllers;

use App\Core\View;
use App\Core\Auth;
use App\models\StatistiqueModel;

/**
 * Contrôleur dédié aux statistiques globales de l'application.
 *
 * Agrège les métriques clés et les tableaux pour l'affichage
 * dans l'interface d'administration.
 */
class StatistiqueController
{
    /**
     * Affiche le tableau de bord des statistiques globales.
     *
     * La méthode récupère les métriques essentielles :
     * - comptes totaux ;
     * - moyennes et répartition par domaine ;
     * - classements des wishlists.
     *
     * @return void
     */
    public function index(): void
    {
        // Initialise ou reprend la session utilisateur.
        Auth::session();

        // Instancie le modèle spécialisé dans les calculs statistiques.
        $model = new StatistiqueModel();

        // Prépare l'ensemble des métriques agrégées à envoyer à la vue.
        $stats = [
            // Comptes globaux
            'total_offres'              => $model->countOffres(),
            'total_entreprises'         => $model->countEntreprises(),
            'total_utilisateurs'        => $model->countUtilisateurs(),
            'total_candidatures'        => $model->countCandidatures(),

            // Moyennes et répartition
            'moyenne_candidatures'      => $model->moyenneCandidaturesParOffre(),
            'offres_par_domaine'        => $model->offresByDomaine(),
            'offres_par_entreprise'     => $model->offresByEntreprise(),

            // Classements
            'top_wishlist'              => $model->topWishlist(),
        ];

        // Affiche la vue des statistiques avec toutes les métriques préparées.
        View::render('statistiques.twig', ['stats' => $stats]);
    }
}