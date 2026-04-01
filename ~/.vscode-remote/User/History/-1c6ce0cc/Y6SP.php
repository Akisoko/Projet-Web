<?php

namespace App\Core;

use App\controllers\HomeController;
use App\controllers\AuthController;
use App\controllers\EntrepriseController;
use App\controllers\OffreController;
use App\controllers\UtilisateurController;
use App\controllers\WishlistController;
use App\controllers\StatistiqueController;

/**
 * Routeur principal de l'application.
 *
 * Cette classe analyse l'URI demandée puis redirige l'exécution
 * vers le contrôleur et la méthode correspondants.
 */
class Router
{
    /**
     * Traite l'URI reçue et exécute l'action associée.
     *
     * La méthode :
     * - normalise l'URL ;
     * - retire le chemin du script si nécessaire ;
     * - supprime /index.php ;
     * - associe la route à un contrôleur via un switch.
     *
     * @param string $uri URI demandée par le navigateur.
     * @return void
     */
    public function handle($uri): void
    {
        // Extrait uniquement le chemin de l'URL, sans les paramètres GET.
        $uri = parse_url($uri, PHP_URL_PATH);

        // Récupère le dossier du script courant pour supporter un projet
        // installé dans un sous-répertoire et non forcément à la racine du domaine.
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

        // Si l'URI commence par le dossier du script, on le retire
        // pour obtenir une route exploitable par le routeur.
        if ($scriptDir !== '/' && str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
        }

        // Supprime explicitement "/index.php" de l'URI si présent.
        $uri = str_replace('/index.php', '', $uri);

        // Normalise la route vide vers la racine "/".
        if ($uri === '' || $uri === '/') {
            $uri = '/';
        }

        // Associe chaque route à un contrôleur et une action précise.
        switch ($uri) {

            case '/':
                (new AuthController())->premiereConnexion();
                break;

            case '/accueil':
                (new HomeController())->accueil();
                break;

            case '/connexion':
                (new AuthController())->connexion();
                break;

            case '/inscription':
                (new AuthController())->inscription();
                break;

            case '/profil':
                (new UtilisateurController())->profil();
                break;

            case '/modifier_profil':
                (new UtilisateurController())->modifierProfil();
                break;

            case '/supprimer_profil':
                (new UtilisateurController())->supprimerProfil();
                break;

            case '/entreprises':
                (new EntrepriseController())->liste();
                break;

            case '/detail_entreprise':
                (new EntrepriseController())->detail();
                break;

            case '/ajouter_entreprise':
                (new EntrepriseController())->ajouter();
                break;

            case '/noter_entreprise':
                (new EntrepriseController())->noter();
                break;

            case '/modifier_entreprise':
                (new EntrepriseController())->modifier();
                break;

            case '/supprimer_entreprise':
                (new EntrepriseController())->supprimer();
                break;

            case '/offres':
                (new OffreController())->liste();
                break;

            case '/detail_offre':
                (new OffreController())->detail();
                break;

            case '/ajouter_offre':
                (new OffreController())->ajouter();
                break;

            case '/modifier_offre':
                (new OffreController())->modifier();
                break;

            case '/supprimer_offre':
                (new OffreController())->supprimer();
                break;

            case '/postuler':
                (new OffreController())->postuler();
                break;

            case '/detail_candidature':
                (new OffreController())->detailCandidature();
                break;

            case '/wishlist':
                (new WishlistController())->index();
                break;

            case '/wishlist_ajouter':
                (new WishlistController())->ajouter();
                break;

            case '/wishlist_retirer':
                (new WishlistController())->retirer();
                break;

            case '/statistiques':
                (new StatistiqueController())->index();
                break;

            case '/mentions':
                (new HomeController())->mentions();
                break;

            case '/recherche':
                (new HomeController())->recherche();
                break;

            case '/deconnexion':
                (new AuthController())->deconnexion();
                break;

            case '/detail_utilisateur':
                (new UtilisateurController())->detailAdmin();
                break;

            case '/modifier_utilisateur':
                (new UtilisateurController())->modifierAdmin();
                break;

            case '/supprimer_utilisateur':
                (new UtilisateurController())->supprimerAdmin();
                break;

            default:
                // Si aucune route ne correspond, on retourne une erreur 404.
                http_response_code(404);
                echo "404 - Page non trouvée";
        }
    }
}