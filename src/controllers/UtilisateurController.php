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
        $id = Auth::utilisateur()['Id_Utilisateur'];

        $model = new UtilisateurModel();
        $utilisateur = $model->findById($id);

        if (!$utilisateur) {
            header('Location: /connexion');
            exit;
        }

        View::render('profil.twig', [
            'utilisateur' => $utilisateur,
            'candidatures' => [], // TODO : brancher CandidatureModel
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
}