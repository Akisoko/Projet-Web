<?php

namespace App\controllers;

use App\Core\View;
use App\Core\Auth;
use App\models\UtilisateurModel;

class UtilisateurController
{
    public function profil(): void
    {
        Auth::requis();

        $authUser = Auth::utilisateur();
        $authId   = $authUser['Id_Utilisateur'];

        $model = new UtilisateurModel();

        // Si un id est fourni dans l'URL, on affiche ce profil-là
        if (!empty($_GET['id'])) {
            $id = (int) $_GET['id'];
        } else {
            // Sinon on affiche le profil de l'utilisateur connecté
            $id = $authId;
        }

        $utilisateur = $model->findById($id);

        if (!$utilisateur) {
            header('Location: /connexion');
            exit;
        }

        $candidatures = [];
        $postulerModel = new \App\models\PostulerModel();
        if ($utilisateur['Id_Role'] == 2) {
            $candidatures = $postulerModel->findByUtilisateur($id);
        } elseif ($utilisateur['Id_Role'] == 3) {
            $candidatures = $postulerModel->findAllCandidatures();
        }

        View::render('profil.twig', [
            'utilisateur' => $utilisateur, // profil affiché
            'auth_user'   => $authUser,    // utilisateur connecté
            'auth_role'   => $authUser['Id_Role'] ?? null,
            'candidatures' => $candidatures,
        ]);
    }

    public function modifierProfil(): void
    {
        Auth::requis();
        $id = Auth::utilisateur()['Id_Utilisateur'];

        $model = new UtilisateurModel();
        $utilisateur = $model->findById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $motDePasse = $_POST['mot_de_passe'] ?? null;
            $motDePasseConfirmation = $_POST['mot_de_passe_confirmation'] ?? null;

            if ($motDePasse && $motDePasse !== $motDePasseConfirmation) {
                View::render('modifier_profil.twig', [
                    'erreur' => 'Les mots de passe ne correspondent pas.',
                    'utilisateur' => $utilisateur,
                ]);
                return;
            }

            $data = [
                'Nom_Utilisateur' => $_POST['nom'] ?? null,
                'Prenom'          => $_POST['prenom'] ?? null,
                'Email'           => $_POST['email'] ?? null,
                'Telephone'       => $_POST['telephone'] ?? null,
            ];

            if ($motDePasse) {
                $data['Mot_de_Passe'] = password_hash($motDePasse, PASSWORD_DEFAULT);
            }

            foreach ($data as $value) {
                if (!$value) {
                    View::render('modifier_profil.twig', [
                        'erreur' => 'Les champs prénom, nom et email sont obligatoires.',
                        'utilisateur' => $utilisateur,
                    ]);
                    return;
                }
            }

            $model->update($id, $data);
            header('Location: /profil');
            exit;
        }

        View::render('modifier_profil.twig', ['utilisateur' => $utilisateur]);
    }

    public function supprimerProfil(): void
    {
        Auth::requis();

        $authUser = Auth::utilisateur();
        $authRole = $authUser['Id_Role'] ?? null;
        $authId   = $authUser['Id_Utilisateur'];

        // UNIQUEMENT les admins peuvent supprimer
        if ($authRole != 1) {
            header('Location: /profil');
            exit;
        }

        $model = new UtilisateurModel();

        // Récupérer l'ID à supprimer (de l'URL ou du formulaire)
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

        if ($id <= 0) {
            header('Location: /profil');
            exit;
        }

        // Ne jamais supprimer l'admin connecté (protection)
        if ($id == $authId) {
            header('Location: /profil');
            exit;
        }

        // Vérifier que l'utilisateur existe
        $utilisateur = $model->findById($id);
        if (!$utilisateur) {
            header('Location: /profil');
            exit;
        }

        // Supprimer l'utilisateur
        $model->delete($id);

        // Rediriger vers la recherche ou profil
        header('Location: /recherche?type=profil');
        exit;
    }

    // UtilisateurController.php
    public function detailAdmin(): void
    {
        Auth::requisRole([1, 3]);

        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: /recherche'); exit; }

        $model = new UtilisateurModel();
        $utilisateur = $model->findById((int)$id);
        if (!$utilisateur) { header('Location: /recherche'); exit; }

        // Pilote ne peut voir que les étudiants
        if (Auth::estPilote() && $utilisateur['Id_Role'] !== 2) {
            header('Location: /recherche');
            exit;
        }

        View::render('detail_utilisateur.twig', ['utilisateur' => $utilisateur]);
    }

    public function modifierAdmin(): void
    {
        Auth::requisRole([1, 3]);

        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: /recherche'); exit; }

        $model = new UtilisateurModel();
        $utilisateur = $model->findById((int)$id);
        if (!$utilisateur) { header('Location: /recherche'); exit; }

        // Pilote ne peut modifier que les étudiants
        if (Auth::estPilote() && $utilisateur['Id_Role'] !== 2) {
            header('Location: /recherche');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'Nom_Utilisateur' => $_POST['nom'] ?? null,
                'Prenom'          => $_POST['prenom'] ?? null,
                'Email'           => $_POST['email'] ?? null,
                'Telephone'       => $_POST['telephone'] ?? null,
            ];

            // Admin peut aussi changer le rôle
            if (Auth::estAdmin() && isset($_POST['id_role'])) {
                $data['Id_Role'] = $_POST['id_role'];
            }

            $model->update((int)$id, $data);
            header('Location: /detail_utilisateur?id=' . $id);
            exit;
        }

        View::render('modifier_utilisateur.twig', ['utilisateur' => $utilisateur]);
    }

    public function supprimerAdmin(): void
    {
        Auth::requisRole([1, 3]);

        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: /recherche'); exit; }

        $model = new UtilisateurModel();
        $utilisateur = $model->findById((int)$id);
        if (!$utilisateur) { header('Location: /recherche'); exit; }

        // Pilote ne peut supprimer que les étudiants
        if (Auth::estPilote() && $utilisateur['Id_Role'] !== 2) {
            header('Location: /recherche');
            exit;
        }

        $model->delete((int)$id);
        header('Location: /recherche');
        exit;
    }
}