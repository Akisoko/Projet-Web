<?php

namespace App\controllers;

use App\Core\View;
use App\Core\Auth;
use App\models\WishlistModel;

class WishlistController
{
    public function index(): void
    {
        Auth::requisRole([2]); // Étudiant uniquement

        $id = Auth::utilisateur()['Id_Utilisateur'];
        $model = new WishlistModel();
        $wishlist = $model->findByUtilisateur($id);

        View::render('wishlist.twig', ['wishlist' => $wishlist]);
    }

    public function ajouter(): void
    {
        Auth::requisRole([2]);

        $idOffre = $_GET['id'] ?? null;
        if (!$idOffre) {
            header('Location: /offres');
            exit;
        }

        $id = Auth::utilisateur()['Id_Utilisateur'];
        $model = new WishlistModel();
        $model->ajouter((int)$idOffre, $id);

        header('Location: /detail_offre?id=' . $idOffre);
        exit;
    }

    public function retirer(): void
    {
        Auth::requisRole([2]);

        $idOffre = $_GET['id'] ?? null;
        if (!$idOffre) {
            header('Location: /wishlist');
            exit;
        }

        $id = Auth::utilisateur()['Id_Utilisateur'];
        $model = new WishlistModel();
        $model->retirer((int)$idOffre, $id);

        header('Location: /wishlist');
        exit;
    }
}