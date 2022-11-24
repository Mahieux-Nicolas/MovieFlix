<?php

namespace App\Tests;

use App\Services\OmdbApi;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OmdbApiTest extends KernelTestCase
{
    public function testSomething(): void
    {
     
        //? on récupère le kernel
        $kernel = self::bootKernel();

        // on récupère l'env de test
        $this->assertSame('test', $kernel->getEnvironment());
        
      // on demande au container de fournir le service (injection de dépendance)
        /** @var OmdbApi $myOmdbApi  */
        $myOmdbApi = static::getContainer()->get(OmdbApi::class);

        $posterTotoro = $myOmdbApi->fetchPoster("Totoro");
        $urlPoster = "https://m.media-amazon.com/images/M/MV5BYzJjMTYyMjQtZDI0My00ZjE2LTkyNGYtOTllNGQxNDMyZjE0XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg";

        // on vérifie que le service renvoit bien la bonne URL
        $this->assertEquals($posterTotoro, $urlPoster);

        // on doit tester l'autre cas: pas de bon film
        //? on doit chercher le nom d'un film qui n'existe pas
        $posterFilmInconnu = $myOmdbApi->fetchPoster("filmkinexistepas");
        $urlPoster = "https://amc-theatres-res.cloudinary.com/amc-cdn/static/images/fallbacks/DefaultOneSheetPoster.jpg";

        // on vérifie que le service  renvoit bien la bonne URL
        $this->assertEquals($posterFilmInconnu, $urlPoster);

    }
}
