<?php

namespace App\Tests\WebTest\Back;

use App\Repository\UserRepository;
use App\Tests\CustomWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AnonymeTest extends CustomWebTestCase
{
    /**
     * test anonyme
     *
     * @param string $url
     * 
     * @dataProvider getUrls
     */
    public function testAnonyme($url, $urlRedirect): void
    {
        // $url = '/backoffice/movie/';

        $client = static::createClient();
        // on teste une route du backoffice
        $crawler = $client->request('GET', $url);

        // on est bien redirigé
        $this->assertResponseRedirects($urlRedirect, Response::HTTP_FOUND);
    }

    /**
     * function utilisée par le dataProvider
     */
    public function getUrls()
    {
        
       // yield permets de tester plusieurs liens
        // on met 2 éléments (paramètres) dans notre tableau
        yield ['/backoffice/movie/', '/login'];
        yield ['/backoffice/genre/', '/login'];
        yield ['/backoffice/season/', '/login'];
        yield ['/backoffice/person/', '/login'];
        yield ['/backoffice/casting/', '/login'];
        
    }
    

    public function testwithUser(): void
    {
        $client = static::createClient();
     
        /** @var UserRepository $userRepository  */
        $userRepository = static::getContainer()->get(UserRepository::class);

 
        $jbOclock = $userRepository->findOneBy(["email" => "jb@oclock.io"]);


        $client->loginUser($jbOclock);


        $crawler = $client->request('GET', '/backoffice/movie/');

        $this->assertResponseIsSuccessful();
        
      
        $jbOclock = $userRepository->findOneBy(["email" => "jbManager@oclock.io"]);


        $client->loginUser($jbOclock);

     
        $crawler = $client->request('GET', '/backoffice/movie/');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
