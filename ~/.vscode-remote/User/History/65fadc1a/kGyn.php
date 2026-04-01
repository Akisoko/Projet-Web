<?php

/**
 * Point d'entrée unique de l'application.
 *
 * Ce fichier index.php est appelé par le serveur web pour toute
 * requête vers l'application. Il se charge de :
 * - charger les dépendances via Composer ;
 * - lire la configuration de base de données ;
 * - initialiser le routeur ;
 * - traiter la requête entrante.
 */
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

/**
 * Charge le fichier de configuration d'environnement (.env).
 *
 * Contient les paramètres de connexion à la base de données.
 */
$env = parse_ini_file(__DIR__ . '/../.env');

/**
 * Définit les constantes globales de configuration de base de données.
 *
 * Ces constantes sont utilisées par la classe de connexion PDO
 * dans tout l'application.
 */
define('DB_HOST', $env['DB_HOST']);
define('DB_NAME', $env['DB_NAME']);
define('DB_USER', $env['DB_USER']);
define('DB_PASS', $env['DB_PASS']);

/**
 * Instancie et lance le routeur principal.
 *
 * Le routeur analyse l'URI de la requête et redirige vers
 * le contrôleur et la méthode appropriés.
 */
$router = new Router();
$router->handle($_SERVER['REQUEST_URI']);