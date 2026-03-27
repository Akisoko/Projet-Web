<?php

namespace App\controllers;

use App\Core\View;
use App\Core\Auth;
use App\models\UtilisateurModel;
use App\models\OffreModel;
use App\models\EntrepriseModel;

class HomeController
{
    public function accueil(): void
    {
        Auth::requis();
        View::render("accueil.twig");
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
                            'url'         => '/profil?id=' . $u['Id_Utilisateur'],
                            'titre'       => $u['Prenom'] . ' ' . $u['Nom_Utilisateur'],
                            'description' => $u['Email'],
                            'type'        => 'profil',
                        ];
                    }
                } elseif (Auth::estPilote()) {
                    $utilisateurs = $utilisateurModel->search($query, [2]); // étudiants seulement
                    foreach ($utilisateurs as $u) {
                        $resultats[] = [
                            'url'         => '/profil?id=' . $u['Id_Utilisateur'],
                            'titre'       => $u['Prenom'] . ' ' . $u['Nom_Utilisateur'],
                            'description' => $u['Email'],
                            'type'        => 'profil',
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