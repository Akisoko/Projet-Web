<?php

namespace App\controllers;

use App\Core\View;
use App\Core\Auth;
use App\models\OffreModel;
use App\models\EntrepriseModel;
use App\models\WishlistModel;
use App\models\PostulerModel;

class OffreController
{
    public function liste(): void
    {
        $parPage = 9;
        $page = max(1, (int)($_GET['page'] ?? 1));

        $model = new OffreModel();
        $total = $model->count();
        $totalPages = ceil($total / $parPage);
        $offres = $model->findPaginatedWithEntreprise($page, $parPage);

        View::render('offres.twig', [
            'offres'      => $offres,
            'page'        => $page,
            'totalPages'  => $totalPages,
        ]);
    }

    public function detail(): void
    {
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
            $idUtilisateur = Auth::utilisateur()['Id_Utilisateur'];
            $offreId = $_POST['offre_id'] ?? null;

            $postulerModel = new PostulerModel();
            if ($postulerModel->aDejaPostule((int)$offreId, $idUtilisateur)) {
                View::render('postuler_offre.twig', [
                    'offre' => $offre,
                    'erreur' => 'Vous avez déjà postulé à cette offre.'
                ]);
                return;
            }

            $cv = $_FILES['cv'] ?? null;
            if (!$cv || $cv['error'] !== UPLOAD_ERR_OK) {
                View::render('postuler_offre.twig', [
                    'offre' => $offre,
                    'erreur' => 'Le CV est obligatoire.'
                ]);
                return;
            }

            $extensionCv = strtolower(pathinfo($cv['name'], PATHINFO_EXTENSION));
            if ($extensionCv !== 'pdf') {
                View::render('postuler_offre.twig', [
                    'offre' => $offre,
                    'erreur' => 'Le CV doit être au format PDF.'
                ]);
                return;
            }

            $nomCv = 'cv_' . $idUtilisateur . '_' . $offreId . '_' . time() . '.pdf';
            $cheminCv = 'uploads/cv/' . $nomCv;
            move_uploaded_file($cv['tmp_name'], __DIR__ . '/../../public/' . $cheminCv);

            $cheminLettre = null;
            $lettre = $_FILES['lettre_motivation'] ?? null;
            if ($lettre && $lettre['error'] === UPLOAD_ERR_OK) {
                $extensionLm = strtolower(pathinfo($lettre['name'], PATHINFO_EXTENSION));
                if ($extensionLm === 'pdf') {
                    $nomLettre = 'lm_' . $idUtilisateur . '_' . $offreId . '_' . time() . '.pdf';
                    $cheminLettre = 'uploads/lettres/' . $nomLettre;
                    move_uploaded_file($lettre['tmp_name'], __DIR__ . '/../../public/' . $cheminLettre);
                }
            }

            $postulerModel->postuler(
                (int)$offreId,
                $idUtilisateur,
                $cheminCv,
                $cheminLettre ?? '',
                date('Y-m-d')
            );

            header('Location: /offres');
            exit;
        }

        View::render('postuler_offre.twig', ['offre' => $offre]);
    }
}