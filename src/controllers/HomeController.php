<?php

namespace App\controllers;

use App\Core\View;
use App\Core\Auth;

class HomeController
{
    public function accueil(): void
    {
        Auth::requis();
        View::render("accueil.twig");
    }

    public function mentions(): void
    {
        View::render("mentions.twig");
    }

    public function recherche(): void
    {
        Auth::requis();
        View::render("recherche.twig");
    }
}