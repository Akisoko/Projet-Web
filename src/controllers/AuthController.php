<?php

namespace App\controllers;

use App\Core\Auth;
use App\Core\View;
use App\models\UtilisateurModel;

/**
 * Contrôleur responsable de l'authentification des utilisateurs.
 *
 * Gère la connexion, l'inscription, la première connexion
 * et la déconnexion
 */
class AuthController
{
    /**
     * Affiche la page de connexion et traite la tentative de connexion.
     *
     * Si l'utilisateur est déjà connecté, il est redirigé vers l'accueil.
     * Lors d'une requête POST, la méthode vérifie l'email et le mot de passe
     * avant de créer la session utilisateur.
     *
     * @return void
     */
    public function connexion(): void
    {
        // Initialise ou reprend la session utilisateur.
        Auth::session();

        // Empêche un utilisateur déjà connecté d'accéder au formulaire de connexion.
        if (Auth::estConnecte()) {
            header('Location: /accueil');
            exit;
        }

        // Si le formulaire a été soumis, on traite les données envoyées.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupère les champs du formulaire de connexion.
            $email = $_POST['email'] ?? null;
            $motDePasse = $_POST['mot_de_passe'] ?? null;

            // Recherche l'utilisateur correspondant à l'adresse email.
            $model = new UtilisateurModel();
            $utilisateur = $model->findByEmail($email);

            // Vérifie que l'utilisateur existe et que le mot de passe est correct.
            // password_verify() compare un mot de passe en clair avec son hash stocké.
            if ($utilisateur && password_verify($motDePasse, $utilisateur['Mot_de_Passe'])) {
                // Stocke les informations de l'utilisateur en session après authentification.
                $_SESSION['utilisateur'] = $utilisateur;

                header('Location: /accueil');
                exit;
            }

            // En cas d'échec, on réaffiche le formulaire avec un message d'erreur.
            View::render('connexion.twig', ['erreur' => 'Identifiants incorrects.']);
            return;
        }

        // Affiche simplement le formulaire de connexion si aucune soumission n'a eu lieu.
        View::render('connexion.twig');
    }

    /**
     * Affiche la page d'inscription et traite la création d'un compte.
     *
     * Si le formulaire est soumis, la méthode vérifie la cohérence
     * des mots de passe, l'unicité de l'email, puis crée l'utilisateur.
     * Le rôle attribué dépend du contexte et du rôle de l'utilisateur connecté.
     *
     * @return void
     */
    public function inscription(): void
    {
        // Initialise ou reprend la session.
        Auth::session();

        // Récupère l'état de connexion et le rôle de l'utilisateur courant.
        $isConnecte = Auth::estConnecte();
        $userRole = Auth::role();

        // Si le formulaire d'inscription est envoyé, on traite les données.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupération des données du formulaire.
            $prenom = $_POST['prenom'] ?? null;
            $nom = $_POST['nom'] ?? null;
            $email = $_POST['email'] ?? null;
            $telephone = $_POST['telephone'] ?? null;

            // Convertit la civilité envoyée par le formulaire en valeur exploitable.
            $genre = $_POST['civilite'] === 'mr' ? 'Homme' : 'Femme';

            $motDePasse = $_POST['mot_de_passe'] ?? null;
            $motDePasseConfirmation = $_POST['mot_de_passe_confirmation'] ?? null;

            // Récupère les composantes de la date de naissance puis les assemble.
            $jour = $_POST['jour'] ?? null;
            $mois = $_POST['mois'] ?? null;
            $annee = $_POST['annee'] ?? null;
            $dateNaissance = $annee . '-' . $mois . '-' . $jour;

            // Rôle attribué par défaut à l'inscription.
            $roleCree = 2;

            // Si un utilisateur connecté crée un compte, on contrôle les rôles autorisés.
            if ($isConnecte && isset($_POST['role_cree'])) {
                $postedRole = (int) $_POST['role_cree'];

                // Un administrateur (rôle 1) peut créer un utilisateur de rôle 2 ou 3.
                if ($userRole === 1 && in_array($postedRole, [2, 3])) {
                    $roleCree = $postedRole;

                // Un utilisateur de rôle 3 ne peut créer qu'un utilisateur de rôle 2.
                } elseif ($userRole === 3 && $postedRole === 2) {
                    $roleCree = 2;
                }
            }

            // Vérifie que le mot de passe et sa confirmation correspondent.
            if ($motDePasse !== $motDePasseConfirmation) {
                View::render('inscription.twig', ['erreur' => 'Les mots de passe ne correspondent pas.']);
                return;
            }

            $model = new UtilisateurModel();

            // Empêche la création d'un compte avec une adresse email déjà utilisée.
            if ($model->findByEmail($email)) {
                View::render('inscription.twig', ['erreur' => 'Cet email est déjà utilisé.']);
                return;
            }

            // Crée le nouvel utilisateur avec un mot de passe hashé pour la sécurité.
            // password_hash() génère un hash adapté au stockage en base de données.
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

            // Redirige selon le contexte :
            // - utilisateur déjà connecté : retour à l'accueil ;
            // - inscription classique : redirection vers la connexion.
            if ($isConnecte) {
                header('Location: /accueil');
            } else {
                header('Location: /connexion');
            }
            exit;
        }

        // Affiche le formulaire d'inscription si aucune donnée n'a été soumise.
        View::render('inscription.twig');
    }

    /**
     * Affiche la page de première connexion.
     *
     * Cette méthode démarre explicitement la session puis affiche
     * la vue associée à la première connexion de l'utilisateur.
     *
     * @return void
     */
    public function premiereConnexion(): void
    {
        // Démarre la session PHP pour rendre disponibles les données utilisateur si besoin.
        session_start();

        // Affiche la vue de première connexion.
        View::render('premiere_connexion.twig');
    }

    /**
     * Déconnecte l'utilisateur courant.
     *
     * La session est initialisée puis détruite avant de rediriger
     * l'utilisateur vers la page d'accueil du site.
     *
     * @return void
     */
    public function deconnexion(): void
    {
        // Initialise la session avant de la détruire proprement.
        Auth::session();

        // Détruit toutes les données de session de l'utilisateur.
        session_destroy();

        // Redirige vers la racine du site après déconnexion.
        header('Location: /');
        exit;
    }
}