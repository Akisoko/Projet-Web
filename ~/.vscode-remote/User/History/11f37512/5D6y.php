<?php

namespace App\controllers;

use App\Core\View;
use App\Core\Auth;
use App\models\WishlistModel;

/**
 * Contrôleur dédié à la gestion de la wishlist des étudiants.
 *
 * Permet aux étudiants uniquement d'afficher, ajouter et retirer
 * des offres de leur liste de favoris.
 */
class WishlistController
{
    /**
     * Affiche la wishlist de l'étudiant connecté.
     *
     * L'accès est strictement réservé aux étudiants (rôle 2).
     *
     * @return void
     */
    public function index(): void
    {
        // Restreint l'accès aux étudiants uniquement.
        Auth::requisRole([2]);

        // Récupère l'identifiant de l'étudiant connecté.
        $id = Auth::utilisateur()['Id_Utilisateur'];

        // Charge la liste complète des offres favorites de l'étudiant.
        $model = new WishlistModel();
        $wishlist = $model->findByUtilisateur($id);

        // Affiche la vue avec les offres de la wishlist.
        View::render('wishlist.twig', ['wishlist' => $wishlist]);
    }

    /**
     * Ajoute une offre à la wishlist de l'étudiant connecté.
     *
     * L'identifiant de l'offre est récupéré depuis l'URL.
     * L'étudiant est redirigé vers la page de détail de l'offre après ajout.
     *
     * @return void
     */
    public function ajouter(): void
    {
        // Restreint l'accès aux étudiants uniquement.
        Auth::requisRole([2]);

        // Récupère l'identifiant de l'offre à ajouter depuis l'URL.
        $idOffre = $_GET['id'] ?? null;

        // Redirige si aucun identifiant d'offre n'est fourni.
        if (!$idOffre) {
            header('Location: /offres');
            exit;
        }

        // Récupère l'identifiant de l'étudiant connecté.
        $id = Auth::utilisateur()['Id_Utilisateur'];

        // Ajoute l'offre à la wishlist de l'étudiant.
        $model = new WishlistModel();
        $model->ajouter((int)$idOffre, $id);

        // Redirige vers la page de détail de l'offre ajoutée.
        header('Location: /detail_offre?id=' . $idOffre);
        exit;
    }

    /**
     * Retire une offre de la wishlist de l'étudiant connecté.
     *
     * L'étudiant est redirigé vers sa page wishlist après suppression.
     *
     * @return void
     */
    public function retirer(): void
    {
        // Restreint l'accès aux étudiants uniquement.
        Auth::requisRole([2]);

        // Récupère l'identifiant de l'offre à retirer depuis l'URL.
        $idOffre = $_GET['id'] ?? null;

        // Redirige si aucun identifiant d'offre n'est fourni.
        if (!$idOffre) {
            header('Location: /wishlist');
            exit;
        }

        // Récupère l'identifiant de l'étudiant connecté.
        $id = Auth::utilisateur()['Id_Utilisateur'];

        // Supprime l'offre de la wishlist de l'étudiant.
        $model = new WishlistModel();
        $model->retirer((int)$idOffre, $id);

        // Redirige vers la page wishlist mise à jour.
        header('Location: /wishlist');
        exit;
    }
}