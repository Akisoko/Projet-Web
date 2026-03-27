<?php

namespace App\controllers;

use App\Core\View;
use App\models\EntrepriseModel;
use App\Core\Auth;

class EntrepriseController
{
    public function liste(): void
    {
        Auth::requis();

        $parPage = 9;
        $page = max(1, (int)($_GET['page'] ?? 1));

        $model = new EntrepriseModel();
        $total = $model->count();
        $totalPages = ceil($total / $parPage);
        $entreprises = $model->findPaginated($page, $parPage);

        View::render('entreprises.twig', [
            'entreprises' => $entreprises,
            'page'        => $page,
            'totalPages'  => $totalPages,
        ]);
    }

    public function detail(): void
    {
        Auth::requis();

        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /entreprises');
            exit;
        }

        $model = new EntrepriseModel();
        $entreprise = $model->findById((int)$id);

        if (!$entreprise) {
            header('Location: /entreprises');
            exit;
        }

        View::render('detail_entreprise.twig', [
            'entreprise' => $entreprise
        ]);
    }

    public function ajouter(): void
    {
        Auth::requisRole([1, 3]);

        $model = new EntrepriseModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'Nom_Entreprise'         => $_POST['nom'] ?? null,
                'Site_Web'               => $_POST['site_web'] ?? null,
                'Date_Creation'          => $_POST['date_creation'] ?? null,
                'Domaine_Entreprise'     => $_POST['domaine'] ?? null,
                'Nombre_Employes'        => $_POST['nb_employes'] ?? null,
                'Description_Entreprise' => $_POST['description'] ?? null,
                'Telephone'              => $_POST['telephone'] ?? null,
                'Mail'                   => $_POST['email'] ?? null,
                'Nombre_Stagiaires'      => $_POST['nb_stagiaires'] ?? null
            ];

            foreach ($data as $value) {
                if (!$value) {
                    View::render('ajouter_entreprise.twig', [
                        'erreur' => 'Tous les champs sont obligatoires.'
                    ]);
                    return;
                }
            }

            $model->createEntreprise($data);
            header('Location: /entreprises');
            exit;
        }

        View::render('ajouter_entreprise.twig');
    }

    public function modifier(): void
    {
        Auth::requisRole([1, 3]);

        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /entreprises');
            exit;
        }

        $model = new EntrepriseModel();
        $entreprise = $model->findById((int)$id);

        if (!$entreprise) {
            header('Location: /entreprises');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'Nom_Entreprise'         => $_POST['nom'] ?? null,
                'Site_Web'               => $_POST['site_web'] ?? null,
                'Date_Creation'          => $_POST['date_creation'] ?? null,
                'Domaine_Entreprise'     => $_POST['domaine'] ?? null,
                'Nombre_Employes'        => $_POST['nb_employes'] ?? null,
                'Description_Entreprise' => $_POST['description'] ?? null,
                'Telephone'              => $_POST['telephone'] ?? null,
                'Mail'                   => $_POST['email'] ?? null,
                'Nombre_Stagiaires'      => $_POST['nb_stagiaires'] ?? null
            ];

            foreach ($data as $value) {
                if (!$value) {
                    View::render('modifier_entreprise.twig', [
                        'entreprise' => $entreprise,
                        'erreur' => 'Tous les champs sont obligatoires.'
                    ]);
                    return;
                }
            }

            $model->update((int)$id, $data);
            header('Location: /detail_entreprise?id=' . $id);
            exit;
        }

        View::render('modifier_entreprise.twig', ['entreprise' => $entreprise]);
    }

    public function supprimer(): void
    {
        Auth::requisRole([1, 3]);

        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /entreprises');
            exit;
        }

        $model = new EntrepriseModel();
        $model->delete((int)$id);

        header('Location: /entreprises');
        exit;
    }
}