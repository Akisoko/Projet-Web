<?php

namespace App\Controller;

use App\Core\View;

class UtilisateurController
{
    public function profil(): void
    {
        // TODO : récupérer l'utilisateur connecté depuis la session
        // session_start();
        // $id = $_SESSION['utilisateur']['id'] ?? null;
        // $utilisateur = UtilisateurModel::findById($id);
        // $candidatures = CandidatureModel::findByUtilisateur($id);

        // Simulation pour l'instant
        $utilisateur = [
            'id' => 1,
            'prenom' => 'Nicolas',
            'nom' => 'Schnell',
            'email' => 'nicolas.schnell@viacesi.fr',
            'telephone' => '06 12 34 56 78',
            'role' => 'Etudiant',
        ];

        $candidatures = [
            [
                'offre_id' => 1,
                'offre_titre' => 'Développeur PHP',
                'entreprise' => 'Entreprise A',
                'statut' => 'en_attente',
            ],
            [
                'offre_id' => 2,
                'offre_titre' => 'Développeur JS',
                'entreprise' => 'Entreprise B',
                'statut' => 'accepte',
            ],
        ];

        View::render('profil.twig', [
            'utilisateur' => $utilisateur,
            'candidatures' => $candidatures,
        ]);
    }

    public function modifierProfil(): void
    {
        // TODO : récupérer l'utilisateur connecté depuis la session
        // session_start();
        // $id = $_SESSION['utilisateur']['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $prenom = $_POST['prenom'] ?? null;
            $nom = $_POST['nom'] ?? null;
            $email = $_POST['email'] ?? null;
            $telephone = $_POST['telephone'] ?? null;
            $motDePasse = $_POST['mot_de_passe'] ?? null;
            $motDePasseConfirmation = $_POST['mot_de_passe_confirmation'] ?? null;

            if (!$prenom || !$nom || !$email) {
                View::render('modifier_profil.twig', [
                    'erreur' => 'Les champs prénom, nom et email sont obligatoires.',
                    'utilisateur' => $_POST,
                ]);
                return;
            }

            if ($motDePasse && $motDePasse !== $motDePasseConfirmation) {
                View::render('modifier_profil.twig', [
                    'erreur' => 'Les mots de passe ne correspondent pas.',
                    'utilisateur' => $_POST,
                ]);
                return;
            }

            // TODO : mettre à jour l'utilisateur en BDD
            // UtilisateurModel::update($id, $prenom, $nom, $email, $telephone, $motDePasse);

            header('Location: /profil');
            exit;
        }

        // Simulation pour l'instant
        $utilisateur = [
            'id' => 1,
            'prenom' => 'Nicolas',
            'nom' => 'Schnell',
            'email' => 'nicolas.schnell@viacesi.fr',
            'telephone' => '06 12 34 56 78',
        ];

        View::render('modifier_profil.twig', ['utilisateur' => $utilisateur]);
    }
}