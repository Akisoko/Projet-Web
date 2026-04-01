<?php

namespace App\controllers;

use App\Core\View;
use App\Core\Auth;
use App\models\OffreModel;
use App\models\EntrepriseModel;
use App\models\WishlistModel;
use App\models\PostulerModel;

/**
 * Contrôleur chargé de la gestion des offres.
 *
 * Gère l'affichage de la liste, le détail, l'ajout, la modification,
 * la suppression, la candidature et la consultation des candidatures.
 */
class OffreController
{
    /**
     * Affiche la liste paginée des offres.
     *
     * @return void
     */
    public function liste(): void
    {
        // Nombre d'offres affichées par page.
        $parPage = 9;

        // Numéro de page courant, avec une valeur minimale de 1.
        $page = max(1, (int)($_GET['page'] ?? 1));

        // Récupère le total des offres et calcule le nombre de pages nécessaires.
        $model = new OffreModel();
        $total = $model->count();
        $totalPages = ceil($total / $parPage);

        // Charge les offres avec les données de l'entreprise associée.
        $offres = $model->findPaginatedWithEntreprise($page, $parPage);

        // Envoie les données à la vue.
        View::render('offres.twig', [
            'offres'      => $offres,
            'page'        => $page,
            'totalPages'  => $totalPages,
        ]);
    }

    /**
     * Affiche le détail d'une offre.
     *
     * La méthode vérifie aussi si l'offre est déjà dans la wishlist
     * lorsque l'utilisateur connecté est un étudiant.
     *
     * @return void
     */
    public function detail(): void
    {
        // Récupère l'identifiant de l'offre depuis l'URL.
        $id = $_GET['id'] ?? null;

        // Redirige si aucun identifiant n'est fourni.
        if (!$id) {
            header('Location: /offres');
            exit;
        }

        // Charge le détail de l'offre avec son entreprise associée.
        $model = new OffreModel();
        $offre = $model->findByIdWithEntreprise((int)$id);

        // Redirige si l'offre n'existe pas.
        if (!$offre) {
            header('Location: /offres');
            exit;
        }

        // Par défaut, l'offre n'est pas dans la wishlist.
        $enWishlist = false;

        // Si l'utilisateur est étudiant, on vérifie si l'offre est déjà ajoutée à sa wishlist.
        if (Auth::estEtudiant()) {
            $wishlistModel = new WishlistModel();
            $enWishlist = $wishlistModel->estDansWishlist((int)$id, Auth::utilisateur()['Id_Utilisateur']);
        }

        // Affiche la page de détail avec l'état de la wishlist.
        View::render('detail_offre.twig', [
            'offre' => $offre,
            'en_wishlist' => $enWishlist
        ]);
    }

    /**
     * Affiche le formulaire d'ajout d'une offre et traite sa soumission.
     *
     * L'accès est réservé aux rôles autorisés.
     * Les entreprises sont chargées pour alimenter la liste déroulante du formulaire.
     *
     * @return void
     */
    public function ajouter(): void
    {
        // Restreint l'accès aux rôles autorisés.
        Auth::requisRole([1, 3]);

        // Prépare le modèle des offres et la liste des entreprises.
        $model = new OffreModel();
        $entreprises = (new EntrepriseModel())->findAll();

        // Traitement du formulaire si la requête est en POST.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupère les champs du formulaire.
            $data = [
                'Nom_Offre'           => $_POST['nom'] ?? null,
                'Description_Offre'   => $_POST['description'] ?? null,
                'Domaine_Offre'       => $_POST['domaine'] ?? null,
                'Profil_Recherche'    => $_POST['profil'] ?? null,
                'Remuneration'        => $_POST['remuneration'] ?? null,
                'Date_Offre'          => $_POST['date_offre'] ?? null,
                'Nombre_Etudiants'    => $_POST['nb_etudiants'] ?? null,
                'Id_Entreprise'       => $_POST['id_entreprise'] ?? null,
            ];

            // Vérifie que tous les champs obligatoires sont remplis.
            foreach ($data as $value) {
                if (!$value) {
                    View::render('ajouter_offre.twig', [
                        'erreur' => 'Tous les champs sont obligatoires.',
                        'entreprises' => $entreprises
                    ]);
                    return;
                }
            }

            // Crée l'offre puis redirige vers la liste.
            $model->createOffre($data);
            header('Location: /offres');
            exit;
        }

        // Affiche le formulaire avec les entreprises disponibles.
        View::render('ajouter_offre.twig', ['entreprises' => $entreprises]);
    }

    /**
     * Affiche le formulaire de modification d'une offre et traite la mise à jour.
     *
     * @return void
     */
    public function modifier(): void
    {
        // Restreint l'accès aux rôles autorisés.
        Auth::requisRole([1, 3]);

        // Récupère l'identifiant de l'offre à modifier.
        $id = $_GET['id'] ?? null;

        // Redirige si aucun identifiant n'est fourni.
        if (!$id) {
            header('Location: /offres');
            exit;
        }

        // Charge l'offre et la liste des entreprises pour le formulaire.
        $model = new OffreModel();
        $offre = $model->findById((int)$id);
        $entreprises = (new EntrepriseModel())->findAll();

        // Redirige si l'offre n'existe pas.
        if (!$offre) {
            header('Location: /offres');
            exit;
        }

        // Si le formulaire est soumis, on récupère et valide les champs.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'Nom_Offre'           => $_POST['nom'] ?? null,
                'Description_Offre'   => $_POST['description'] ?? null,
                'Domaine_Offre'       => $_POST['domaine'] ?? null,
                'Profil_Recherche'    => $_POST['profil'] ?? null,
                'Remuneration'        => $_POST['remuneration'] ?? null,
                'Date_Offre'          => $_POST['date_offre'] ?? null,
                'Nombre_Etudiants'    => $_POST['nb_etudiants'] ?? null,
                'Id_Entreprise'       => $_POST['id_entreprise'] ?? null,
            ];

            // Vérifie que tous les champs obligatoires sont présents.
            foreach ($data as $value) {
                if (!$value) {
                    View::render('modifier_offre.twig', [
                        'offre' => $offre,
                        'erreur' => 'Tous les champs sont obligatoires.',
                        'entreprises' => $entreprises
                    ]);
                    return;
                }
            }

            // Met à jour l'offre puis redirige vers sa page de détail.
            $model->update((int)$id, $data);
            header('Location: /detail_offre?id=' . $id);
            exit;
        }

        // Affiche le formulaire prérempli avec les données de l'offre.
        View::render('modifier_offre.twig', [
            'offre' => $offre,
            'entreprises' => $entreprises
        ]);
    }

    /**
     * Supprime une offre.
     *
     * L'accès est réservé aux rôles autorisés.
     *
     * @return void
     */
    public function supprimer(): void
    {
        // Restreint l'accès aux rôles autorisés.
        Auth::requisRole([1, 3]);

        // Récupère l'identifiant de l'offre à supprimer.
        $id = $_GET['id'] ?? null;

        // Redirige si aucun identifiant n'est fourni.
        if (!$id) {
            header('Location: /offres');
            exit;
        }

        // Supprime l'offre puis revient à la liste.
        $model = new OffreModel();
        $model->delete((int)$id);

        header('Location: /offres');
        exit;
    }

    /**
     * Permet à un étudiant de postuler à une offre.
     *
     * La méthode vérifie que l'étudiant n'a pas déjà postulé,
     * impose un CV PDF obligatoire, puis enregistre la candidature.
     *
     * @return void
     */
    public function postuler(): void
    {
        // Seuls les étudiants peuvent postuler.
        Auth::requisRole([2]);

        // Récupère l'identifiant de l'offre depuis l'URL.
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /offres');
            exit;
        }

        // Charge les informations de l'offre pour affichage.
        $model = new OffreModel();
        $offre = $model->findByIdWithEntreprise((int)$id);

        // Redirige si l'offre n'existe pas.
        if (!$offre) {
            header('Location: /offres');
            exit;
        }

        // Si le formulaire est soumis, on traite la candidature.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupère l'utilisateur connecté et l'identifiant d'offre transmis.
            $idUtilisateur = Auth::utilisateur()['Id_Utilisateur'];
            $offreId = $_POST['offre_id'] ?? null;

            // Vérifie si l'utilisateur a déjà postulé à cette offre.
            $postulerModel = new PostulerModel();
            if ($postulerModel->aDejaPostule((int)$offreId, $idUtilisateur)) {
                View::render('postuler_offre.twig', [
                    'offre' => $offre,
                    'erreur' => 'Vous avez déjà postulé à cette offre.'
                ]);
                return;
            }

            // Récupère le fichier CV envoyé par l'utilisateur.
            $cv = $_FILES['cv'] ?? null;

            // Le CV est obligatoire et doit être correctement envoyé.
            if (!$cv || $cv['error'] !== UPLOAD_ERR_OK) {
                View::render('postuler_offre.twig', [
                    'offre' => $offre,
                    'erreur' => 'Le CV est obligatoire.'
                ]);
                return;
            }

            // Vérifie que le CV est bien un PDF.
            $extensionCv = strtolower(pathinfo($cv['name'], PATHINFO_EXTENSION));
            if ($extensionCv !== 'pdf') {
                View::render('postuler_offre.twig', [
                    'offre' => $offre,
                    'erreur' => 'Le CV doit être au format PDF.'
                ]);
                return;
            }

            // Génère un nom de fichier unique pour stocker le CV.
            $nomCv = 'cv_' . $idUtilisateur . '_' . $offreId . '_' . time() . '.pdf';
            $cheminCv = 'uploads/cv/' . $nomCv;

            // Déplace le fichier uploadé vers le dossier public.
            move_uploaded_file($cv['tmp_name'], __DIR__ . '/../../public/' . $cheminCv);

            // Prépare la lettre de motivation, facultative.
            $cheminLettre = null;
            $lettre = $_FILES['lettre_motivation'] ?? null;

            // Si une lettre de motivation a été envoyée, on vérifie qu'elle est au format PDF.
            if ($lettre && $lettre['error'] === UPLOAD_ERR_OK) {
                $extensionLm = strtolower(pathinfo($lettre['name'], PATHINFO_EXTENSION));
                if ($extensionLm === 'pdf') {
                    $nomLettre = 'lm_' . $idUtilisateur . '_' . $offreId . '_' . time() . '.pdf';
                    $cheminLettre = 'uploads/lettres/' . $nomLettre;
                    move_uploaded_file($lettre['tmp_name'], __DIR__ . '/../../public/' . $cheminLettre);
                }
            }

            // Enregistre la candidature avec les chemins des fichiers uploadés.
            $postulerModel->postuler(
                (int)$offreId,
                $idUtilisateur,
                $cheminCv,
                $cheminLettre ?? '',
                date('Y-m-d')
            );

            // Redirige vers la liste des offres après la candidature.
            header('Location: /offres');
            exit;
        }

        // Affiche le formulaire de candidature.
        View::render('postuler_offre.twig', ['offre' => $offre]);
    }

    /**
     * Affiche le détail d'une candidature.
     *
     * L'accès est limité à l'étudiant concerné, à un pilote ou à un administrateur.
     *
     * @return void
     */
    public function detailCandidature(): void
    {
        // Vérifie qu'un utilisateur est connecté.
        Auth::requis();

        // Récupère les identifiants nécessaires à la consultation de la candidature.
        $idOffre = $_GET['offre'] ?? null;
        $idUtilisateur = $_GET['utilisateur'] ?? null;

        // Redirige si les paramètres sont incomplets.
        if (!$idOffre || !$idUtilisateur) {
            header('Location: /profil');
            exit;
        }

        // Récupère les informations de l'utilisateur connecté.
        $authUser = Auth::utilisateur();
        $authRole = (int)($authUser['Id_Role'] ?? 0);
        $authId   = (int)$authUser['Id_Utilisateur'];

        // Seul l'étudiant concerné, un pilote ou un admin peut voir la candidature.
        if ($authRole === 2 && $authId !== (int)$idUtilisateur) {
            header('Location: /profil');
            exit;
        }

        // Sécurité supplémentaire : contrôle final sur les rôles autorisés.
        if (!in_array($authRole, [1, 2, 3])) {
            header('Location: /profil');
            exit;
        }

        // Recherche la candidature correspondante.
        $postulerModel = new PostulerModel();
        $candidature = $postulerModel->findOneCandidature((int)$idOffre, (int)$idUtilisateur);

        // Redirige si aucune candidature n'est trouvée.
        if (!$candidature) {
            header('Location: /profil');
            exit;
        }

        // Affiche la vue de détail de la candidature.
        View::render('detail_candidature.twig', [
            'candidature' => $candidature,
            'auth_role'   => $authRole,
        ]);
    }
}