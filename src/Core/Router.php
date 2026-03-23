<?php

namespace App\Core;

use App\controllers\HomeController;
use App\controllers\AuthController;
use App\controllers\EntrepriseController;
use App\controllers\OffreController;
use App\controllers\UtilisateurController;
use App\controllers\WishlistController;
use App\controllers\StatistiqueController;

class Router
{
    public function handle($uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);

        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        if ($scriptDir !== '/' && str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
        }

        $uri = str_replace('/index.php', '', $uri);

        if ($uri === '' || $uri === '/') {
            $uri = '/';
        }


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

            case '/postuler':
                (new OffreController())->postuler();
                break;

            case '/wishlist':
                (new WishlistController())->index();
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

            default:
                http_response_code(404);
                echo "404 - Page non trouvée";
        }
    }
}