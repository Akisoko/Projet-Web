<?php

namespace App\controllers;

use App\Core\View;
use App\models\EntrepriseModel;
use App\models\NoterModel;
use App\Core\Auth;

/**
 * Contrôleur chargé de la gestion des entreprises.
 *
 * Gère l'affichage de la liste, le détail, l'ajout,
 * la modification, la suppression et la notation.
 */
class EntrepriseController
{
    /**
     * Affiche la liste paginée des entreprises.
     *
     * La méthode calcule la page courante, récupère le nombre total
     * d'entreprises, puis charge les résultats correspondant à la pagination.
     *
     * @return void
     */
    public function liste(): void
    {
        // Définit le nombre d'entreprises affichées par page.
        $parPage = 9;

        // Récupère le numéro de page depuis l'URL en garantissant une valeur minimale de 1.
        $page = max(1, (int)($_GET['page'] ?? 1));

        // Instancie le modèle pour accéder aux données des entreprises.
        $model = new EntrepriseModel();

        // Récupère le nombre total d'entreprises enregistrées.
        $total = $model->count();

        // Calcule le nombre total de pages nécessaires.
        $totalPages = ceil($total / $parPage);

        // Récupère uniquement les entreprises correspondant à la page demandée.
        $entreprises = $model->findPaginated($page, $parPage);

        // Envoie les données à la vue pour affichage.
        View::render('entreprises.twig', [
            'entreprises' => $entreprises,
            'page'        => $page,
            'totalPages'  => $totalPages,
        ]);
    }

    /**
     * Affiche le détail d'une entreprise.
     *
     * La méthode récupère l'identifiant passé en paramètre,
     * vérifie son existence, puis charge l'entreprise correspondante.
     * En cas d'identifiant absent ou invalide, l'utilisateur est redirigé.
     *
     * @return void
     */
    public function detail(): void
    {
        // Récupère l'identifiant de l'entreprise depuis l'URL.
        $id = $_GET['id'] ?? null;

        // Redirige vers la liste si aucun identifiant n'est fourni.
        if (!$id) {
            header('Location: /entreprises');
            exit;
        }

        // Recherche l'entreprise par son identifiant.
        $model = new EntrepriseModel();
        $entreprise = $model->findById((int)$id);

        // Redirige si l'entreprise demandée n'existe pas.
        if (!$entreprise) {
            header('Location: /entreprises');
            exit;
        }

        // Affiche la vue détaillée de l'entreprise.
        View::render('detail_entreprise.twig', [
            'entreprise' => $entreprise
        ]);
    }

    /**
     * Affiche le formulaire d'ajout d'une entreprise et traite sa soumission.
     *
     * L'accès à cette action est réservé aux rôles autorisés.
     * Lors d'une requête POST, la méthode vérifie que tous les champs
     * sont remplis avant de créer l'entreprise.
     *
     * @return void
     */
    public function ajouter(): void
    {
        // Restreint l'accès aux utilisateurs ayant le rôle 1 ou 3.
        Auth::requisRole([1, 3]);

        // Instancie le modèle pour manipuler les entreprises.
        $model = new EntrepriseModel();

        // Traite l'envoi du formulaire d'ajout.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupère et mappe les données du formulaire vers les champs attendus en base.
            $data = [
                'Nom_Entreprise'         => $_POST['nom'] ?? null,
                'Site_Web'               => $_POST['site_web'] ?? null,
                'Date_Creation'          => $_POST['date_creation'] ?? null,
                'Domaine_Entreprise'     => $_POST['domaine'] ?? null,
                'Nombre_Employes'        => $_POST['nb_employes'] ?? null,
                'Description_Entreprise' => $_POST['description'] ?? null,
                'Telephone'              => $_POST['telephone'] ?? null,
                'Mail'                   => $_POST['email'] ?? null,
                'Nombre_Stagiaires'      => $_POST['nb_stagiaires'] ?? null
            ];

            // Vérifie qu'aucun champ obligatoire n'est vide.
            foreach ($data as $value) {
                if (!$value) {
                    View::render('ajouter_entreprise.twig', [
                        'erreur' => 'Tous les champs sont obligatoires.'
                    ]);
                    return;
                }
            }

            // Crée l'entreprise puis redirige vers la liste.
            $model->createEntreprise($data);
            header('Location: /entreprises');
            exit;
        }

        // Affiche le formulaire si la requête est en GET.
        View::render('ajouter_entreprise.twig');
    }

    /**
     * Affiche le formulaire de modification d'une entreprise et traite la mise à jour.
     *
     * La méthode vérifie d'abord l'accès, puis l'existence de l'entreprise.
     * Si le formulaire est soumis, les données sont validées avant la mise à jour.
     *
     * @return void
     */
    public function modifier(): void
    {
        // Restreint l'accès aux utilisateurs autorisés.
        Auth::requisRole([1, 3]);

        // Récupère l'identifiant de l'entreprise à modifier.
        $id = $_GET['id'] ?? null;

        // Redirige si aucun identifiant n'est fourni.
        if (!$id) {
            header('Location: /entreprises');
            exit;
        }

        // Charge les informations actuelles de l'entreprise.
        $model = new EntrepriseModel();
        $entreprise = $model->findById((int)$id);

        // Redirige si l'entreprise n'existe pas.
        if (!$entreprise) {
            header('Location: /entreprises');
            exit;
        }

        // Si le formulaire de modification est soumis, on traite les données.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupère les nouvelles valeurs du formulaire.
            $data = [
                'Nom_Entreprise'         => $_POST['nom'] ?? null,
                'Site_Web'               => $_POST['site_web'] ?? null,
                'Date_Creation'          => $_POST['date_creation'] ?? null,
                'Domaine_Entreprise'     => $_POST['domaine'] ?? null,
                'Nombre_Employes'        => $_POST['nb_employes'] ?? null,
                'Description_Entreprise' => $_POST['description'] ?? null,
                'Telephone'              => $_POST['telephone'] ?? null,
                'Mail'                   => $_POST['email'] ?? null,
                'Nombre_Stagiaires'      => $_POST['nb_stagiaires'] ?? null
            ];

            // Vérifie que tous les champs requis sont bien renseignés.
            foreach ($data as $value) {
                if (!$value) {
                    View::render('modifier_entreprise.twig', [
                        'entreprise' => $entreprise,
                        'erreur' => 'Tous les champs sont obligatoires.'
                    ]);
                    return;
                }
            }

            // Met à jour l'entreprise puis redirige vers sa page de détail.
            $model->update((int)$id, $data);
            header('Location: /detail_entreprise?id=' . $id);
            exit;
        }

        // Affiche le formulaire prérempli avec les données existantes.
        View::render('modifier_entreprise.twig', ['entreprise' => $entreprise]);
    }

    /**
     * Supprime une entreprise à partir de son identifiant.
     *
     * L'accès à cette action est réservé aux rôles autorisés.
     * Si l'identifiant est absent, l'utilisateur est redirigé vers la liste.
     *
     * @return void
     */
    public function supprimer(): void
    {
        // Restreint l'accès aux rôles habilités.
        Auth::requisRole([1, 3]);

        // Récupère l'identifiant de l'entreprise à supprimer.
        $id = $_GET['id'] ?? null;

        // Redirige si aucun identifiant n'est fourni.
        if (!$id) {
            header('Location: /entreprises');
            exit;
        }

        // Supprime l'entreprise concernée.
        $model = new EntrepriseModel();
        $model->delete((int)$id);

        // Revient à la liste après suppression.
        header('Location: /entreprises');
        exit;
    }

    /**
     * Affiche le formulaire de notation d'une entreprise et enregistre la note.
     *
     * La méthode vérifie l'accès utilisateur, l'existence de l'entreprise,
     * puis contrôle que la note soumise est valide avant sauvegarde.
     *
     * @return void
     */
    public function noter(): void
    {
        // Restreint l'accès aux utilisateurs autorisés à noter une entreprise.
        Auth::requisRole([1, 3]);

        // Récupère l'identifiant de l'entreprise depuis l'URL.
        $id = $_GET['id'] ?? null;

        // En cas d'envoi du formulaire, l'identifiant peut venir du POST.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['entreprise_id'] ?? null;
        }

        // Redirige si aucun identifiant d'entreprise n'est disponible.
        if (!$id) {
            header('Location: /entreprises');
            exit;
        }

        // Charge l'entreprise concernée.
        $model = new EntrepriseModel();
        $entreprise = $model->findById((int)$id);

        // Redirige si l'entreprise n'existe pas.
        if (!$entreprise) {
            header('Location: /entreprises');
            exit;
        }

        // Traite la soumission du formulaire de notation.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupère la note et le commentaire saisis.
            $note = $_POST['note'] ?? null;
            $commentaire = $_POST['commentaire'] ?? null;

            // Vérifie que la note est bien comprise entre 1 et 5.
            if ($note === null || $note < 1 || $note > 5) {
                View::render('noter_entreprise.twig', [
                    'entreprise' => $entreprise,
                    'erreur' => 'Veuillez sélectionner une note valide.'
                ]);
                return;
            }

            // Récupère l'utilisateur connecté pour associer la note à son auteur.
            $user = Auth::utilisateur();
            $idUser = $user['Id_Utilisateur'];

            // Enregistre la note et le commentaire en base de données.
            $noterModel = new NoterModel();
            $noterModel->sauvegarderNote((int)$id, (int)$idUser, (int)$note, $commentaire);

            // Redirige vers la page de détail de l'entreprise après enregistrement.
            header('Location: /detail_entreprise?id=' . $id);
            exit;
        }

        // Affiche le formulaire de notation.
        View::render('noter_entreprise.twig', ['entreprise' => $entreprise]);
    }
}