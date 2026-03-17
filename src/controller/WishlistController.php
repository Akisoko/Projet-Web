<?php

namespace App\Controller;

use App\Core\View;

class WishlistController
{
    public function index(): void
    {
        // TODO : récupérer l'utilisateur connecté depuis la session
        // session_start();
        // $id = $_SESSION['utilisateur']['id'] ?? null;
        // $wishlist = WishlistModel::findByUtilisateur($id);

        // Simulation pour l'instant
        $wishlist = [
            [
                'id' => 1,
                'titre' => 'Développeur PHP',
                'entreprise' => 'Entreprise A',
                'description' => 'Description du poste',
                'lieu' => 'Paris',
                'duree' => '6 mois',
                'remuneration' => 1200,
            ],
            [
                'id' => 2,
                'titre' => 'Développeur JS',
                'entreprise' => 'Entreprise B',
                'description' => 'Description du poste',
                'lieu' => 'Lyon',
                'duree' => '3 mois',
                'remuneration' => 900,
            ],
        ];

        View::render('wishlist.twig', ['wishlist' => $wishlist]);
    }
}