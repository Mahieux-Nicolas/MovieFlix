<?php

namespace App\DataFixtures\Providers;

class GenreProvider extends \Faker\Provider\Base
{
    // création de fixtures genre
    private $genres = [
        1 => "Action",
        2 => "Animation",
        3 => "Aventure",
        4 => "Comédie",
        5 => "Dessin animé",
        6 => "Documentaire",
        7 => "Drame",
        8 => "Espionnage",
        9 => "Famille",
        10 => "Fantastique",
        11 => "Historique",
        12 => "Policier",
        13 => "Romance",
        14 => "Science-Fiction",
        15 => "Thriller",
        16 => "Western",
    ];

    public function getGenres()
    {
        return $this->genres;
    }
}