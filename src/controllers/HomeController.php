<?php

namespace App\controllers;

use App\Core\View;
use App\Core\Auth;
use App\models\UtilisateurModel;
use App\models\OffreModel;
use App\models\EntrepriseModel;
use App\models\WishlistModel;
use App\models\StatistiqueModel;

class HomeController
{
    public function accueil(): void
    {
        Auth::session();
        $data = [];

        // Dernières offres pour tout le monde
        $offreModel = new OffreModel();
        $data['dernieres_offres'] = $offreModel->findDernieres(6);

        // Wishlist pour les étudiants
        if (Auth::estEtudiant()) {
            $wishlistModel = new WishlistModel();
            $data['wishlist'] = $wishlistModel->findByUtilisateur(Auth::utilisateur()['Id_Utilisateur']);
        }

        // Stats pour admin et pilote
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

        View::render("accueil.twig", $data);
    }
    public function mentions(): void
    {
        View::render("mentions.twig");
    }

    public function recherche(): void
    {
        Auth::requis();

        $query = $_GET['q'] ?? '';
        $type = $_GET['type'] ?? '';
        $resultats = [];

        if ($query !== '') {
            $utilisateurModel = new UtilisateurModel();
            $offreModel = new OffreModel();
            $entrepriseModel = new EntrepriseModel();

            if ($type === 'offre' || $type === '') {
                $offres = $offreModel->search($query);
                foreach ($offres as $offre) {
                    $resultats[] = [
                        'url'         => '/detail_offre?id=' . $offre['Id_Offre'],
                        'titre'       => $offre['Nom_Offre'],
                        'description' => $offre['Domaine_Offre'] . ' — ' . $offre['Remuneration'],
                        'type'        => 'offre',
                    ];
                }
            }

            if ($type === 'entreprise' || $type === '') {
                $entreprises = $entrepriseModel->search($query);
                foreach ($entreprises as $entreprise) {
                    $resultats[] = [
                        'url'         => '/detail_entreprise?id=' . $entreprise['Id_Entreprise'],
                        'titre'       => $entreprise['Nom_Entreprise'],
                        'description' => $entreprise['Domaine_Entreprise'],
                        'type'        => 'entreprise',
                    ];
                }
            }

            // Recherche profil : Admin (1) peut chercher étudiants + pilotes
            // Pilote (3) peut chercher étudiants seulement
            if ($type === 'profil' || $type === '') {
                if (Auth::estAdmin()) {
                    $utilisateurs = $utilisateurModel->search($query, [2, 3]); // étudiants + pilotes
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
                    $utilisateurs = $utilisateurModel->search($query, [2]); // étudiants seulement
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

        View::render('recherche.twig', [
            'query'      => $query,
            'query_type' => $type,
            'resultats'  => $resultats,
        ]);
    }
}