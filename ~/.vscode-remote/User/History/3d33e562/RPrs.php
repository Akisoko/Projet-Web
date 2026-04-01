<?php

namespace App\controllers;

use App\Core\View;
use App\Core\Auth;
use App\models\UtilisateurModel;

/**
 * Contrôleur chargé de la gestion des profils utilisateurs.
 *
 * Gère l'affichage du profil connecté ou d'un autre utilisateur,
 * la modification du profil personnel, ainsi que les actions
 * d'administration sur les comptes utilisateurs.
 */
class UtilisateurController
{
    /**
     * Affiche le profil d'un utilisateur.
     *
     * Si un identifiant est fourni dans l'URL, le profil correspondant est affiché.
     * Sinon, la méthode affiche le profil de l'utilisateur connecté.
     * Des candidatures peuvent aussi être chargées selon le rôle du profil consulté.
     *
     * @return void
     */
    public function profil(): void
    {
        // Vérifie qu'un utilisateur est authentifié.
        Auth::requis();

        // Récupère les informations de l'utilisateur connecté.
        $authUser = Auth::utilisateur();
        $authId   = $authUser['Id_Utilisateur'];

        // Instancie le modèle des utilisateurs.
        $model = new UtilisateurModel();

        // Si un id est fourni dans l'URL, on affiche ce profil-là.
        if (!empty($_GET['id'])) {
            $id = (int) $_GET['id'];
        } else {
            // Sinon, on affiche le profil de l'utilisateur connecté.
            $id = $authId;
        }

        // Charge les informations du profil demandé.
        $utilisateur = $model->findById($id);

        // Redirige si l'utilisateur demandé n'existe pas.
        if (!$utilisateur) {
            header('Location: /connexion');
            exit;
        }

        // Prépare les candidatures associées au profil affiché.
        $candidatures = [];
        $postulerModel = new \App\models\PostulerModel();

        // Si le profil affiché est un étudiant, on charge uniquement ses candidatures.
        if ($utilisateur['Id_Role'] == 2) {
            $candidatures = $postulerModel->findByUtilisateur($id);

        // Si le profil affiché est un pilote, on charge l'ensemble des candidatures.
        } elseif ($utilisateur['Id_Role'] == 3) {
            $candidatures = $postulerModel->findAllCandidatures();
        }

        // Affiche la vue du profil avec :
        // - l'utilisateur affiché ;
        // - l'utilisateur connecté ;
        // - le rôle de l'utilisateur connecté ;
        // - les candidatures éventuelles.
        View::render('profil.twig', [
            'utilisateur'  => $utilisateur,
            'auth_user'    => $authUser,
            'auth_role'    => $authUser['Id_Role'] ?? null,
            'candidatures' => $candidatures,
        ]);
    }

    /**
     * Permet à l'utilisateur connecté de modifier son propre profil.
     *
     * La méthode gère aussi la mise à jour du mot de passe
     * lorsqu'un nouveau mot de passe est fourni.
     *
     * @return void
     */
    public function modifierProfil(): void
    {
        // Vérifie qu'un utilisateur est connecté.
        Auth::requis();

        // Récupère l'identifiant de l'utilisateur connecté.
        $id = Auth::utilisateur()['Id_Utilisateur'];

        // Charge les informations actuelles de l'utilisateur.
        $model = new UtilisateurModel();
        $utilisateur = $model->findById($id);

        // Si le formulaire est soumis, on traite les nouvelles données.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupère le mot de passe et sa confirmation.
            $motDePasse = $_POST['mot_de_passe'] ?? null;
            $motDePasseConfirmation = $_POST['mot_de_passe_confirmation'] ?? null;

            // Si un mot de passe a été saisi, il doit correspondre à sa confirmation.
            if ($motDePasse && $motDePasse !== $motDePasseConfirmation) {
                View::render('modifier_profil.twig', [
                    'erreur' => 'Les mots de passe ne correspondent pas.',
                    'utilisateur' => $utilisateur,
                ]);
                return;
            }

            // Prépare les données modifiables du profil.
            $data = [
                'Nom_Utilisateur' => $_POST['nom'] ?? null,
                'Prenom'          => $_POST['prenom'] ?? null,
                'Email'           => $_POST['email'] ?? null,
                'Telephone'       => $_POST['telephone'] ?? null,
            ];

            // Si un nouveau mot de passe est fourni, on le hash avant stockage.
            if ($motDePasse) {
                $data['Mot_de_Passe'] = password_hash($motDePasse, PASSWORD_DEFAULT);
            }

            // Vérifie que les champs requis sont bien renseignés.
            foreach ($data as $value) {
                if (!$value) {
                    View::render('modifier_profil.twig', [
                        'erreur' => 'Les champs prénom, nom et email sont obligatoires.',
                        'utilisateur' => $utilisateur,
                    ]);
                    return;
                }
            }

            // Met à jour le profil puis redirige vers la page profil.
            $model->update($id, $data);
            header('Location: /profil');
            exit;
        }

        // Affiche le formulaire de modification du profil.
        View::render('modifier_profil.twig', ['utilisateur' => $utilisateur]);
    }

    /**
     * Permet à un administrateur de supprimer un profil utilisateur.
     *
     * Un administrateur ne peut pas supprimer son propre compte
     * afin d'éviter une perte d'accès d'administration.
     *
     * @return void
     */
    public function supprimerProfil(): void
    {
        // Vérifie qu'un utilisateur est authentifié.
        Auth::requis();

        // Récupère les informations de l'utilisateur connecté.
        $authUser = Auth::utilisateur();
        $authRole = $authUser['Id_Role'] ?? null;
        $authId   = $authUser['Id_Utilisateur'];

        // Seuls les administrateurs peuvent supprimer un compte utilisateur.
        if ($authRole != 1) {
            header('Location: /profil');
            exit;
        }

        // Instancie le modèle utilisateur.
        $model = new UtilisateurModel();

        // Récupère l'identifiant à supprimer, depuis le formulaire ou l'URL.
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

        // Redirige si l'identifiant est invalide.
        if ($id <= 0) {
            header('Location: /profil');
            exit;
        }

        // Protection : empêche l'administrateur connecté de supprimer son propre compte.
        if ($id == $authId) {
            header('Location: /profil');
            exit;
        }

        // Vérifie que l'utilisateur ciblé existe.
        $utilisateur = $model->findById($id);
        if (!$utilisateur) {
            header('Location: /profil');
            exit;
        }

        // Supprime l'utilisateur.
        $model->delete($id);

        // Redirige vers la recherche des profils après suppression.
        header('Location: /recherche?type=profil');
        exit;
    }

    /**
     * Affiche le détail d'un utilisateur dans un contexte d'administration.
     *
     * Les administrateurs et pilotes peuvent accéder à cette action,
     * mais un pilote ne peut consulter que les profils étudiants.
     *
     * @return void
     */
    public function detailAdmin(): void
    {
        // Restreint l'accès aux administrateurs et pilotes.
        Auth::requisRole([1, 3]);

        // Récupère l'identifiant de l'utilisateur à consulter.
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /recherche');
            exit;
        }

        // Charge l'utilisateur demandé.
        $model = new UtilisateurModel();
        $utilisateur = $model->findById((int)$id);
        if (!$utilisateur) {
            header('Location: /recherche');
            exit;
        }

        // Un pilote ne peut consulter que les étudiants.
        if (Auth::estPilote() && $utilisateur['Id_Role'] !== 2) {
            header('Location: /recherche');
            exit;
        }

        // Affiche la fiche détaillée de l'utilisateur.
        View::render('detail_utilisateur.twig', ['utilisateur' => $utilisateur]);
    }

    /**
     * Permet à un administrateur ou un pilote de modifier un utilisateur.
     *
     * Un pilote ne peut modifier que les étudiants.
     * Un administrateur peut aussi modifier le rôle de l'utilisateur.
     *
     * @return void
     */
    public function modifierAdmin(): void
    {
        // Restreint l'accès aux administrateurs et pilotes.
        Auth::requisRole([1, 3]);

        // Récupère l'identifiant de l'utilisateur à modifier.
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /recherche');
            exit;
        }

        // Charge l'utilisateur ciblé.
        $model = new UtilisateurModel();
        $utilisateur = $model->findById((int)$id);
        if (!$utilisateur) {
            header('Location: /recherche');
            exit;
        }

        // Un pilote ne peut modifier que les profils étudiants.
        if (Auth::estPilote() && $utilisateur['Id_Role'] !== 2) {
            header('Location: /recherche');
            exit;
        }

        // Si le formulaire est soumis, on met à jour les données.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'Nom_Utilisateur' => $_POST['nom'] ?? null,
                'Prenom'          => $_POST['prenom'] ?? null,
                'Email'           => $_POST['email'] ?? null,
                'Telephone'       => $_POST['telephone'] ?? null,
            ];

            // Seul l'administrateur peut modifier le rôle de l'utilisateur.
            if (Auth::estAdmin() && isset($_POST['id_role'])) {
                $data['Id_Role'] = $_POST['id_role'];
            }

            // Enregistre les modifications puis redirige vers la fiche détaillée.
            $model->update((int)$id, $data);
            header('Location: /detail_utilisateur?id=' . $id);
            exit;
        }

        // Affiche le formulaire de modification.
        View::render('modifier_utilisateur.twig', ['utilisateur' => $utilisateur]);
    }

    /**
     * Permet à un administrateur ou un pilote de supprimer un utilisateur.
     *
     * Un pilote ne peut supprimer que des étudiants.
     *
     * @return void
     */
    public function supprimerAdmin(): void
    {
        // Restreint l'accès aux administrateurs et pilotes.
        Auth::requisRole([1, 3]);

        // Récupère l'identifiant de l'utilisateur à supprimer.
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /recherche');
            exit;
        }

        // Charge l'utilisateur ciblé.
        $model = new UtilisateurModel();
        $utilisateur = $model->findById((int)$id);
        if (!$utilisateur) {
            header('Location: /recherche');
            exit;
        }

        // Un pilote ne peut supprimer que des étudiants.
        if (Auth::estPilote() && $utilisateur['Id_Role'] !== 2) {
            header('Location: /recherche');
            exit;
        }

        // Supprime l'utilisateur puis revient à la recherche.
        $model->delete((int)$id);
        header('Location: /recherche');
        exit;
    }
}