<?php

namespace App\controllers;

use App\Core\Auth;
use App\Core\View;
use App\models\UtilisateurModel;

class AuthController
{
    public function connexion(): void
    {
        Auth::session();

        if (Auth::estConnecte()) {
            header('Location: /accueil');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? null;
            $motDePasse = $_POST['mot_de_passe'] ?? null;

            $model = new UtilisateurModel();
            $utilisateur = $model->findByEmail($email);

            if ($utilisateur && password_verify($motDePasse, $utilisateur['Mot_de_Passe'])) {
                $_SESSION['utilisateur'] = $utilisateur;
                header('Location: /accueil');
                exit;
            }

            View::render('connexion.twig', ['erreur' => 'Identifiants incorrects.']);
            return;
        }

        View::render('connexion.twig');
    }

    public function inscription(): void
    {
        Auth::session();
        $isConnecte = Auth::estConnecte();
        $userRole = Auth::role();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $prenom = $_POST['prenom'] ?? null;
            $nom = $_POST['nom'] ?? null;
            $email = $_POST['email'] ?? null;
            $telephone = $_POST['telephone'] ?? null;
            $genre = $_POST['civilite'] === 'mr' ? 'Homme' : 'Femme';
            $motDePasse = $_POST['mot_de_passe'] ?? null;
            $motDePasseConfirmation = $_POST['mot_de_passe_confirmation'] ?? null;
            $jour = $_POST['jour'] ?? null;
            $mois = $_POST['mois'] ?? null;
            $annee = $_POST['annee'] ?? null;
            $dateNaissance = $annee . '-' . $mois . '-' . $jour;

            $roleCree = 2; // Default
            if ($isConnecte && isset($_POST['role_cree'])) {
                $postedRole = (int) $_POST['role_cree'];
                if ($userRole === 1 && in_array($postedRole, [2, 3])) {
                    $roleCree = $postedRole;
                } elseif ($userRole === 3 && $postedRole === 2) {
                    $roleCree = 2;
                }
            }

            if ($motDePasse !== $motDePasseConfirmation) {
                View::render('inscription.twig', ['erreur' => 'Les mots de passe ne correspondent pas.']);
                return;
            }

            $model = new UtilisateurModel();

            if ($model->findByEmail($email)) {
                View::render('inscription.twig', ['erreur' => 'Cet email est déjà utilisé.']);
                return;
            }

            $model->create([
                'Prenom'            => $prenom,
                'Nom_Utilisateur'   => $nom,
                'Email'             => $email,
                'Telephone'         => $telephone,
                'Genre'             => $genre,
                'Mot_de_Passe'      => password_hash($motDePasse, PASSWORD_DEFAULT),
                'Date_de_Naissance' => $dateNaissance,
                'Id_Role'           => $roleCree,
            ]);

            if ($isConnecte) {
                header('Location: /accueil');
            } else {
                header('Location: /connexion');
            }
            exit;
        }

        View::render('inscription.twig');
    }

    public function premiereConnexion(): void
    {
        session_start();
        View::render('premiere_connexion.twig');
    }

    public function deconnexion(): void
    {
        Auth::session();
        session_destroy();
        header('Location: /');
        exit;
    }
}