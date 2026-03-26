<?php

namespace App\Core;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class View
{
    public static function render($template, $data = [])
    {
        $loader = new FilesystemLoader(dirname(__DIR__, 2) . '/templates');
        $twig = new Environment($loader);

        Auth::session();
        $data['auth'] = Auth::utilisateur();
        $data['auth_role'] = Auth::role();

        echo $twig->render($template, $data);
    }
}