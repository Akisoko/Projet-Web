<?php

namespace App\Controller;

use App\Core\View;

class OffreController
{
    public function liste(): void
    {
        // TODO : récupérer les offres en BDD
        // $offres = OffreModel::findAll();

        // Simulation pour l'instant
        $offres = [
            [
                'id' => 1,
                'titre' => 'Développeur PHP',
                'entreprise' => 'Entreprise A',
                'description' => 'Description du poste',
                'lieu' => 'Paris',
                'duree' => '6 mois',
                'remuneration' => 1200,
                'wishlist' => false,
            ],
            [
                'id' => 2,
                'titre' => 'Développeur JS',
                'entreprise' => 'Entreprise B',
                'description' => 'Description du poste',
                'lieu' => 'Lyon',
                'duree' => '3 mois',
                'remuneration' => 900,
                'wishlist' => true,
            ],
        ];

        View::render('offres.twig', ['offres' => $offres]);
    }

    public function detail(): void
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /offres');
            exit;
        }

        // TODO : récupérer l'offre en BDD
        // $offre = OffreModel::findById($id);

        // Simulation pour l'instant
        $offre = [
            'id' => $id,
            'titre' => 'Développeur PHP',
            'entreprise' => 'Entreprise A',
            'description' => 'Description du poste',
            'lieu' => 'Paris',
            'duree' => '6 mois',
            'remuneration' => 1200,
            'wishlist' => false,
        ];

        View::render('detail_offre.twig', ['offre' => $offre]);
    }

    public function ajouter(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? null;
            $entreprise = $_POST['entreprise'] ?? null;
            $lieu = $_POST['lieu'] ?? null;
            $duree = $_POST['duree'] ?? null;
            $remuneration = $_POST['remuneration'] ?? null;
            $description = $_POST['description'] ?? null;

            if (!$titre || !$entreprise || !$lieu || !$duree || !$remuneration || !$description) {
                View::render('ajouter_offre.twig', ['erreur' => 'Tous les champs sont obligatoires.']);
                return;
            }

            // TODO : créer l'offre en BDD
            // OffreModel::create($titre, $entreprise, $lieu, $duree, $remuneration, $description);

            header('Location: /offres');
            exit;
        }

        View::render('ajouter_offre.twig');
    }

    public function modifier(): void
    {
        $id = $_GET['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $titre = $_POST['titre'] ?? null;
            $entreprise = $_POST['entreprise'] ?? null;
            $lieu = $_POST['lieu'] ?? null;
            $duree = $_POST['duree'] ?? null;
            $remuneration = $_POST['remuneration'] ?? null;
            $description = $_POST['description'] ?? null;

            if (!$id || !$titre || !$entreprise || !$lieu || !$duree || !$remuneration || !$description) {
                View::render('modifier_offre.twig', ['erreur' => 'Tous les champs sont obligatoires.']);
                return;
            }

            // TODO : modifier l'offre en BDD
            // OffreModel::update($id, $titre, $entreprise, $lieu, $duree, $remuneration, $description);

            header('Location: /detail_offre?id=' . $id);
            exit;
        }

        if (!$id) {
            header('Location: /offres');
            exit;
        }

        // TODO : récupérer l'offre en BDD
        // $offre = OffreModel::findById($id);

        // Simulation pour l'instant
        $offre = [
            'id' => $id,
            'titre' => 'Développeur PHP',
            'entreprise' => 'Entreprise A',
            'description' => 'Description du poste',
            'lieu' => 'Paris',
            'duree' => '6 mois',
            'remuneration' => 1200,
        ];

        View::render('modifier_offre.twig', ['offre' => $offre]);
    }

    public function postuler(): void
    {
        $id = $_GET['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $offreId = $_POST['offre_id'] ?? null;
            $cv = $_FILES['cv'] ?? null;
            $lettre = $_FILES['lettre_motivation'] ?? null;

            if (!$offreId || !$cv || $cv['error'] !== UPLOAD_ERR_OK) {
                View::render('postuler_offre.twig', ['erreur' => 'Le CV est obligatoire.']);
                return;
            }

            // TODO : enregistrer la candidature en BDD
            // CandidatureModel::create($offreId, $cv, $lettre);

            header('Location: /offres');
            exit;
        }

        if (!$id) {
            header('Location: /offres');
            exit;
        }

        // TODO : récupérer l'offre en BDD
        // $offre = OffreModel::findById($id);

        // Simulation pour l'instant
        $offre = [
            'id' => $id,
            'titre' => 'Développeur PHP',
            'entreprise' => 'Entreprise A',
        ];

        View::render('postuler_offre.twig', ['offre' => $offre]);
    }
}