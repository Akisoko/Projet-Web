<?php

namespace App\controllers;

use App\Core\View;
use App\Core\Auth;
use App\models\OffreModel;
use App\models\EntrepriseModel;
use App\models\WishlistModel;

class OffreController
{
    public function liste(): void
    {
        Auth::requis();
        $model = new OffreModel();
        $offres = $model->findAllWithEntreprise();
        View::render('offres.twig', ['offres' => $offres]);
    }

    public function detail(): void
    {
        Auth::requis();
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: /offres'); exit; }

        $model = new OffreModel();
        $offre = $model->findByIdWithEntreprise((int)$id);
        if (!$offre) { header('Location: /offres'); exit; }

        $enWishlist = false;
        if (Auth::estEtudiant()) {
            $wishlistModel = new WishlistModel();
            $enWishlist = $wishlistModel->estDansWishlist((int)$id, Auth::utilisateur()['Id_Utilisateur']);
        }

        View::render('detail_offre.twig', [
            'offre' => $offre,
            'en_wishlist' => $enWishlist
        ]);
    }

    public function ajouter(): void
    {
        Auth::requisRole([1, 3]);
        $model = new OffreModel();
        $entreprises = (new EntrepriseModel())->findAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'Nom_Offre'           => $_POST['nom'] ?? null,
                'Description_Offre'   => $_POST['description'] ?? null,
                'Domaine_Offre'       => $_POST['domaine'] ?? null,
                'Profil_Recherche'    => $_POST['profil'] ?? null,
                'Remuneration'        => $_POST['remuneration'] ?? null,
                'Date_Offre'          => $_POST['date_offre'] ?? null,
                'Nombre_Etudiants'    => $_POST['nb_etudiants'] ?? null,
                'Id_Entreprise'       => $_POST['id_entreprise'] ?? null,
            ];

            foreach ($data as $value) {
                if (!$value) {
                    View::render('ajouter_offre.twig', [
                        'erreur' => 'Tous les champs sont obligatoires.',
                        'entreprises' => $entreprises
                    ]);
                    return;
                }
            }

            $model->createOffre($data);
            header('Location: /offres');
            exit;
        }

        View::render('ajouter_offre.twig', ['entreprises' => $entreprises]);
    }

    public function modifier(): void
    {
        Auth::requisRole([1, 3]);
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: /offres'); exit; }

        $model = new OffreModel();
        $offre = $model->findById((int)$id);
        $entreprises = (new EntrepriseModel())->findAll();
        if (!$offre) { header('Location: /offres'); exit; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'Nom_Offre'           => $_POST['nom'] ?? null,
                'Description_Offre'   => $_POST['description'] ?? null,
                'Domaine_Offre'       => $_POST['domaine'] ?? null,
                'Profil_Recherche'    => $_POST['profil'] ?? null,
                'Remuneration'        => $_POST['remuneration'] ?? null,
                'Date_Offre'          => $_POST['date_offre'] ?? null,
                'Nombre_Etudiants'    => $_POST['nb_etudiants'] ?? null,
                'Id_Entreprise'       => $_POST['id_entreprise'] ?? null,
            ];

            foreach ($data as $value) {
                if (!$value) {
                    View::render('modifier_offre.twig', [
                        'offre' => $offre,
                        'erreur' => 'Tous les champs sont obligatoires.',
                        'entreprises' => $entreprises
                    ]);
                    return;
                }
            }

            $model->update((int)$id, $data);
            header('Location: /detail_offre?id=' . $id);
            exit;
        }

        View::render('modifier_offre.twig', [
            'offre' => $offre,
            'entreprises' => $entreprises
        ]);
    }

    public function supprimer(): void
    {
        Auth::requisRole([1, 3]);
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: /offres'); exit; }

        $model = new OffreModel();
        $model->delete((int)$id);
        header('Location: /offres');
        exit;
    }

    public function postuler(): void
    {
        Auth::requisRole([2]);
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: /offres'); exit; }

        $model = new OffreModel();
        $offre = $model->findByIdWithEntreprise((int)$id);
        if (!$offre) { header('Location: /offres'); exit; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $offreId = $_POST['offre_id'] ?? null;
            $cv = $_FILES['cv'] ?? null;

            if (!$offreId || !$cv || $cv['error'] !== UPLOAD_ERR_OK) {
                View::render('postuler_offre.twig', [
                    'offre' => $offre,
                    'erreur' => 'Le CV est obligatoire.'
                ]);
                return;
            }

            // TODO : enregistrer la candidature en BDD
            header('Location: /offres');
            exit;
        }

        View::render('postuler_offre.twig', ['offre' => $offre]);
    }
}