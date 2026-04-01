<?php

namespace App\Core;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

/**
 * Classe utilitaire responsable du rendu des vues Twig.
 *
 * Centralise la création de l'environnement Twig et injecte
 * automatiquement les informations d'authentification
 * dans toutes les vues de l'application.
 */
class View
{
    /**
     * Mode de test pour désactiver le rendu réel et capturer les données.
     * @var bool
     */
    public static bool $testMode = false;

    /**
     * Stocke les dernières données envoyées à la vue en mode test.
     * @var array
     */
    public static array $lastRenderedData = [];

    /**
     * Affiche un template Twig avec les données fournies.
     *
     * @param string $template Nom du fichier Twig à afficher.
     * @param array $data Données transmises à la vue.
     * @return void
     */
    public static function render($template, $data = [])
    {
        // En mode test, on capture les données et on s'arrête là.
        if (self::$testMode) {
            self::$lastRenderedData = $data;
            return;
        }

        // Définit le dossier racine contenant les templates Twig.
        $loader = new FilesystemLoader(dirname(__DIR__, 2) . '/templates');

        // Crée l'environnement Twig utilisé pour charger et rendre les vues.
        $twig = new Environment($loader);

        // Initialise la session afin de pouvoir récupérer les informations utilisateur.
        Auth::session();

        // Rend disponibles dans toutes les vues :
        // - l'utilisateur connecté ;
        // - son rôle éventuel.
        $data['auth'] = Auth::utilisateur();
        $data['auth_role'] = Auth::role();

        // Génère et affiche le HTML final à partir du template et des données fournies.
        echo $twig->render($template, $data);
    }
}