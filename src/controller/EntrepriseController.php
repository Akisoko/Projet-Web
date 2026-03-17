<?php

namespace App\Controller;

use App\Core\View;

class EntrepriseController
{
    public function liste(): void
    {
        // TODO : récupérer les entreprises en BDD
        // $entreprises = EntrepriseModel::findAll();

        // Simulation pour l'instant
        $entreprises = [
            ['id' => 1, 'nom' => 'Entreprise A', 'secteur' => 'Informatique', 'description' => 'Description A', 'adresse' => 'Paris'],
            ['id' => 2, 'nom' => 'Entreprise B', 'secteur' => 'Finance', 'description' => 'Description B', 'adresse' => 'Lyon'],
        ];

        View::render('entreprises.twig', ['entreprises' => $entreprises]);
    }

    public function detail(): void
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /entreprises');
            exit;
        }

        // TODO : récupérer l'entreprise en BDD
        // $entreprise = EntrepriseModel::findById($id);

        // Simulation pour l'instant
        $entreprise = [
            'id' => $id,
            'nom' => 'Entreprise A',
            'secteur' => 'Informatique',
            'description' => 'Description de l\'entreprise A',
            'adresse' => 'Paris',
            'telephone' => '01 23 45 67 89',
            'email' => 'contact@entreprise-a.fr',
        ];

        View::render('detail_entreprise.twig', ['entreprise' => $entreprise]);
    }

    public function ajouter(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? null;
            $siret = $_POST['siret'] ?? null;
            $email = $_POST['email'] ?? null;
            $telephone = $_POST['telephone'] ?? null;
            $adresse = $_POST['adresse'] ?? null;
            $description = $_POST['description'] ?? null;

            if (!$nom || !$siret || !$email || !$telephone || !$adresse || !$description) {
                View::render('ajouter_entreprise.twig', ['erreur' => 'Tous les champs sont obligatoires.']);
                return;
            }

            // TODO : créer l'entreprise en BDD
            // EntrepriseModel::create($nom, $siret, $email, $telephone, $adresse, $description);

            header('Location: /entreprises');
            exit;
        }

        View::render('ajouter_entreprise.twig');
    }

    public function noter(): void
    {
        $id = $_GET['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $entrepriseId = $_POST['entreprise_id'] ?? null;
            $note = $_POST['note'] ?? null;
            $commentaire = $_POST['commentaire'] ?? null;

            if (!$entrepriseId || !$note) {
                View::render('noter_entreprise.twig', ['erreur' => 'Veuillez attribuer une note.']);
                return;
            }

            // TODO : enregistrer la note en BDD
            // NoteModel::create($entrepriseId, $note, $commentaire);

            header('Location: /detail_entreprise?id=' . $entrepriseId);
            exit;
        }

        // TODO : récupérer l'entreprise en BDD
        // $entreprise = EntrepriseModel::findById($id);

        // Simulation pour l'instant
        $entreprise = [
            'id' => $id,
            'nom' => 'Entreprise A',
        ];

        View::render('noter_entreprise.twig', ['entreprise' => $entreprise]);
    }
}