<?php

namespace App\DataFixtures;

use App\DataFixtures\Providers\GenreProvider;
use App\Entity\Casting;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Person;
use App\Entity\Season;
use App\Entity\User;
use App\Services\MySlugger;
use App\Services\OmdbApi;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;
use Xylis\FakerCinema\Provider\Movie as MovieFaker;
use Xylis\FakerCinema\Provider\TvShow;

class AppFixtures extends Fixture
{

    private $omdbApi;
    private $mySlugger;

    /**
    * Constructor
    */
    public function __construct(OmdbApi $omdbApi, MySlugger $mySlugger)
    {
        $this->omdbApi = $omdbApi;
        $this->mySlugger = $mySlugger;
    }

    /**
     * @param ObjectManager $manager = EntityManager, c'est le persist() et le flush()
     * 
     * ! méthode abstraite donc pas de paramètre
     * ? on utilise le constructeur
     */
    public function load(ObjectManager $manager): void
    {
        
        $user = new User();
        $user->setEmail('nico@test.fr');
        $user->setRoles(['ROLE_ADMIN', 'ROLE_MANAGER']);
        $user->setPassword('$2y$13$pqoMg2V2vsFP6fEaEmDzZeXmALoxAfd5CHq02T4pYcq.opWYQUrfK');

        $manager->persist($user);
        
       
     // on utilise un faker
        //? https://fakerphp.github.io/locales/fr_FR/
        $faker = Factory::create('fr_FR');

        //on ajoute un provider pour l'utiliser
        $faker->addProvider(new GenreProvider($faker));

        // https://github.com/JulienRAVIA/FakerCinemaProviders
        $faker->addProvider(new MovieFaker($faker));
        $faker->addProvider(new TvShow($faker));

        //? https://fakerphp.github.io/#seeding-the-generator
        $faker->seed(2022);

        $allPerson = [];
      
        for ($i=0; $i < 900; $i++) { 
            $person = new Person();
            $person->setFirstname($faker->firstName());
            $person->setLastname($faker->lastName());

            $manager->persist($person);

            $allPerson[] = $person;
        }


        // les genres
        $allGenre = [];
        // utilisation du provider
        foreach ($faker->getGenres() as $genreName) {
            $genre = new Genre();
            $genre->setName($genreName);

            $manager->persist($genre);
            $allGenre[] = $genre;
        }

      
        // les films
        $allMovies = [];
        for ($i=0; $i < 20; $i++) { 
       
            $movie = new Movie();
            
            //? https://fakerphp.github.io/formatters/numbers-and-strings/#randomelement
            $movie->setType($faker->randomElement(['film', 'serie']));

            // * gestion des seasons
            if ($movie->getType() === 'serie'){
                
                $movie->setTitle($faker->tvShow());

                $nbSeason = rand(1,10);

                for ($j=1; $j <= $nbSeason; $j++) { 
                    $season = new Season();
                    // de 1 à nbSeason
                    $season->setNumber($j);
                    $season->setEpisodesCount(24);

                    $manager->persist($season);
                    // ajouter à la série
                    $movie->addSeason($season);
                }
            } else {

                $movie->setTitle($faker->movie());
            }

            // TODO : slug
            $slug = $this->mySlugger->slug($movie->getTitle());
            $movie->setSlug($slug);

           // association de genres
            $genresAlreadyAdded[] = $movie->getGenres();
            // entre 1 et 4 genre
            for ($k=0; $k<rand(1, 5); $k++) {
                // sélection aléatoire
                $genreToAdd = $allGenre[rand(0, count($allGenre)-1)];
                
                //on vérifie qu'il n'y a pas déjà le même genre
                if (!in_array($genreToAdd, $genresAlreadyAdded)) {
                    // si il n'est pas dans la liste on l'ajoute
                    $movie->addGenre($genreToAdd);
                    // on ajoute à la liste des présents
                    $genresAlreadyAdded[] = $genre;
                }
            }
           
            //? https://fakerphp.github.io/formatters/date-and-time/#datetimebetween
            $movie->setReleaseDate($faker->dateTimeBetween('-100 years'));
            //? https://fakerphp.github.io/formatters/numbers-and-strings/#numberbetween
            $movie->setDuration($faker->numberBetween(30, 263));
            //? https://fakerphp.github.io/formatters/numbers-and-strings/#randomfloat
            $movie->setRating($faker->randomFloat(1, 1, 5));

           
        // injection de poster
            $poster = $this->omdbApi->fetchPoster($movie->getTitle());
            $movie->setPoster($poster);
            //$movie->setPoster("https://amc-theatres-res.cloudinary.com/amc-cdn/static/images/fallbacks/DefaultOneSheetPoster.jpg");

            // création du résumé
            $movie->setSummary($faker->sentence());
            // on génère un paragraphe            
            $movie->setSynopsis($faker->paragraph());
            $movie->setCountry("FR");
            $manager->persist($movie);

            $allMovies[] = $movie;
        }


       //casting
        foreach ($allMovies as $movie) {
           // nombre aléatoire de casting
            $nbCasting = rand(1,4);
           // boucle
            for ($i=1; $i <= $nbCasting; $i++) {
                // TODO : utiliser le faker pour toutes les chaines de caractères.
                $casting = new Casting();
                // association du film
                $casting->setMovie($movie);
                // la personne aléatoire
                $randIndexPerson = rand(0, count($allPerson) -1);
                $randPerson = $allPerson[$randIndexPerson];
                $casting->setPerson($randPerson);
                // donner un nom de role
                $casting->setRole($faker->name());
              
                $casting->setCreditOrder($i);
            }

            $manager->persist($casting);
        }

        $manager->flush();
    }
}
