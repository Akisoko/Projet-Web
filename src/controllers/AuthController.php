<?php

namespace App\controllers;

use App\Core\View;
use App\models\UtilisateurModel;

class AuthController
{
    public function connexion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? null;
            $motDePasse = $_POST['mot_de_passe'] ?? null;

            $utilisateur = UtilisateurModel::findByEmail($email);

            // password_verify compare le mot de passe avec le hash en BDD
            if ($utilisateur && password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
                session_start();
                $_SESSION['utilisateur'] = $utilisateur; // toutes les infos dispo en session
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

            // Vérifier que l'email n'est pas déjà utilisé
            if (UtilisateurModel::findByEmail($email)) {
                View::render('inscription.twig', ['erreur' => 'Cet email est déjà utilisé.']);
                return;
            }

            UtilisateurModel::create($prenom, $nom, $email, $motDePasse);
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

            $id = $_SESSION['utilisateur']['id'];
            UtilisateurModel::updatePassword($id, $motDePasse);
            header('Location: /');
            exit;
        }

        View::render('premiere_connexion.twig');
    }
}