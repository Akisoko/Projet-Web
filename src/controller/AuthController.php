<?php

namespace App\Controller;

use App\Core\View;

class AuthController
{
    public function connexion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? null;
            $motDePasse = $_POST['mot_de_passe'] ?? null;

            // TODO : vérifier les identifiants en BDD
            // $utilisateur = UtilisateurModel::findByEmail($email);

            // Simulation pour l'instant
            if ($email && $motDePasse) {
                session_start();
                $_SESSION['utilisateur'] = ['email' => $email];
                header('Location: /');
                exit;
            }

            View::render('connexion.twig', ['erreur' => 'Identifiants incorrects.']);
            return;
        }

        View::render('connexion.twig');
    }

    public function inscription(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $prenom = $_POST['prenom'] ?? null;
            $nom = $_POST['nom'] ?? null;
            $email = $_POST['email'] ?? null;
            $motDePasse = $_POST['mot_de_passe'] ?? null;
            $motDePasseConfirmation = $_POST['mot_de_passe_confirmation'] ?? null;

            if ($motDePasse !== $motDePasseConfirmation) {
                View::render('inscription.twig', ['erreur' => 'Les mots de passe ne correspondent pas.']);
                return;
            }

            // TODO : créer l'utilisateur en BDD
            // UtilisateurModel::create($prenom, $nom, $email, $motDePasse);

            header('Location: /connexion');
            exit;
        }

        View::render('inscription.twig');
    }

    public function premiereConnexion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $motDePasse = $_POST['mot_de_passe'] ?? null;
            $motDePasseConfirmation = $_POST['mot_de_passe_confirmation'] ?? null;

            if ($motDePasse !== $motDePasseConfirmation) {
                View::render('premiere_connexion.twig', ['erreur' => 'Les mots de passe ne correspondent pas.']);
                return;
            }

            // TODO : mettre à jour le mot de passe en BDD
            // UtilisateurModel::updatePassword($id, $motDePasse);

            header('Location: /');
            exit;
        }

        View::render('premiere_connexion.twig');
    }
}