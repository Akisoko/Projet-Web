<?php

namespace App\controllers;

use App\Core\View;

class HomeController
{
    public function accueil(): void
    {
        View::render("accueil.twig");
    }

    public function mentions(): void
    {
        View::render("mentions.twig");
    }

    public function recherche(): void
    {
        View::render("recherche.twig");
    }


}