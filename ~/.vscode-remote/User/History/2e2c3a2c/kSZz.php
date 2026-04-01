<?php

namespace App\controllers;

use App\Core\View;
use App\Core\Auth;
use App\models\UtilisateurModel;
use App\models\OffreModel;
use App\models\EntrepriseModel;
use App\models\WishlistModel;
use App\models\StatistiqueModel;

/**
 * Contrôleur principal de la page d'accueil et de la recherche globale.
 *
 * Gère l'affichage de la page d'accueil, des mentions légales
 * et de la recherche transversale sur les offres, entreprises et profils.
 */
class HomeController
{
    /**
     * Affiche la page d'accueil avec des données adaptées au rôle de l'utilisateur.
     *
     * Tout le monde voit les dernières offres.
     * Les étudiants voient en plus leur wishlist.
     * Les administrateurs et pilotes voient des statistiques globales.
     *
     * @return void
     */
    public function accueil(): void
    {
        // Initialise ou reprend la session utilisateur.
        Auth::session();

        // Tableau qui contiendra toutes les données envoyées à la vue.
        $data = [];

        // Récupère les dernières offres visibles sur la page d'accueil pour tous les utilisateurs.
        $offreModel = new OffreModel();
        $data['dernieres_offres'] = $offreModel->findDernieres(6);

        // Si l'utilisateur connecté est un étudiant, on récupère sa wishlist.
        if (Auth::estEtudiant()) {
            $wishlistModel = new WishlistModel();
            $data['wishlist'] = $wishlistModel->findByUtilisateur(Auth::utilisateur()['Id_Utilisateur']);
        }

        // Si l'utilisateur est administrateur ou pilote, on affiche des statistiques globales.
        if (Auth::estAdmin() || Auth::estPilote()) {
            $statsModel = new StatistiqueModel();
            $data['stats'] = [
                'total_offres'         => $statsModel->countOffres(),
                'total_entreprises'    => $statsModel->countEntreprises(),
                'total_candidatures'   => $statsModel->countCandidatures(),
                'moyenne_candidatures' => $statsModel->moyenneCandidaturesParOffre(),
                'top_wishlist'         => $statsModel->topWishlist(),
            ];
        }

        // Affiche la vue d'accueil avec les données préparées.
        View::render("accueil.twig", $data);
    }

    /**
     * Affiche la page des mentions légales.
     *
     * @return void
     */
    public function mentions(): void
    {
        // Rend simplement la vue des mentions légales.
        View::render("mentions.twig");
    }

    /**
     * Effectue une recherche globale sur plusieurs types de contenus.
     *
     * La recherche peut porter sur :
     * - les offres ;
     * - les entreprises ;
     * - les profils, selon le rôle de l'utilisateur connecté.
     *
     * Un administrateur peut rechercher des étudiants et des pilotes.
     * Un pilote peut rechercher uniquement des étudiants.
     *
     * @return void
     */
    public function recherche(): void
    {
        // Initialise ou reprend la session afin de connaître le rôle de l'utilisateur.
        Auth::session();

        // Récupère le texte recherché et le type de recherche demandé.
        $query = $_GET['q'] ?? '';
        $type = $_GET['type'] ?? '';

        // Tableau final contenant tous les résultats formatés pour la vue.
        $resultats = [];

        // On ne lance les recherches que si une requête texte a été saisie.
        if ($query !== '') {
            // Instancie les modèles nécessaires selon les types de recherche disponibles.
            $utilisateurModel = new UtilisateurModel();
            $offreModel = new OffreModel();
            $entrepriseModel = new EntrepriseModel();

            // Recherche dans les offres si le type demandé est "offre"
            // ou si aucun filtre de type n'a été précisé.
            if ($type === 'offre' || $type === '') {
                $offres = $offreModel->search($query);

                // Formate les offres trouvées pour harmoniser l'affichage dans la vue.
                foreach ($offres as $offre) {
                    $resultats[] = [
                        'url'         => '/detail_offre?id=' . $offre['Id_Offre'],
                        'titre'       => $offre['Nom_Offre'],
                        'description' => $offre['Domaine_Offre'] . ' — ' . $offre['Remuneration'],
                        'type'        => 'offre',
                    ];
                }
            }

            // Recherche dans les entreprises si le type demandé est "entreprise"
            // ou si aucun type n'est imposé.
            if ($type === 'entreprise' || $type === '') {
                $entreprises = $entrepriseModel->search($query);

                // Formate les entreprises trouvées pour l'affichage.
                foreach ($entreprises as $entreprise) {
                    $resultats[] = [
                        'url'         => '/detail_entreprise?id=' . $entreprise['Id_Entreprise'],
                        'titre'       => $entreprise['Nom_Entreprise'],
                        'description' => $entreprise['Domaine_Entreprise'],
                        'type'        => 'entreprise',
                    ];
                }
            }

            // Recherche dans les profils si le type demandé est "profil"
            // ou si aucun filtre de type n'est appliqué.
            //
            // Règles métier :
            // - un administrateur peut rechercher les étudiants et les pilotes ;
            // - un pilote peut rechercher uniquement les étudiants.
            if ($type === 'profil' || $type === '') {
                if (Auth::estAdmin()) {
                    // L'administrateur peut rechercher les utilisateurs de rôle 2 et 3.
                    $utilisateurs = $utilisateurModel->search($query, [2, 3]);

                    // Formate les profils trouvés pour la vue.
                    foreach ($utilisateurs as $u) {
                        $resultats[] = [
                            'url'         => '/detail_utilisateur?id=' . $u['Id_Utilisateur'],
                            'titre'       => $u['Prenom'] . ' ' . $u['Nom_Utilisateur'],
                            'description' => $u['Email'],
                            'type'        => 'profil',
                            'id'          => $u['Id_Utilisateur'],
                            'id_role'     => $u['Id_Role'],
                        ];
                    }
                } elseif (Auth::estPilote()) {
                    // Le pilote ne peut rechercher que les utilisateurs de rôle 2.
                    $utilisateurs = $utilisateurModel->search($query, [2]);

                    // Formate les profils étudiants trouvés.
                    foreach ($utilisateurs as $u) {
                        $resultats[] = [
                            'url'         => '/detail_utilisateur?id=' . $u['Id_Utilisateur'],
                            'titre'       => $u['Prenom'] . ' ' . $u['Nom_Utilisateur'],
                            'description' => $u['Email'],
                            'type'        => 'profil',
                            'id'          => $u['Id_Utilisateur'],
                            'id_role'     => $u['Id_Role'],
                        ];
                    }
                }
            }
        }

        // Affiche la page de recherche avec la requête, le filtre sélectionné et les résultats.
        View::render('recherche.twig', [
            'query'      => $query,
            'query_type' => $type,
            'resultats'  => $resultats,
        ]);
    }
}