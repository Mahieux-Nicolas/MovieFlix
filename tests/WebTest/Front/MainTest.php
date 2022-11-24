<?php

namespace App\Tests\WebTest\Front;

use App\Tests\CustomWebTestCase;

class MainTest extends CustomWebTestCase
{
    public function testSomething(): void
    {
        // création du client HTTP
        $client = static::createClient();
        
        // on récupère le résultat d'une route : '/'
        $crawler = $client->request('GET', '/');
        
        // status code = 200
        $this->assertResponseIsSuccessful();

        // on vérifie qu'il y a un h1 avec le titre de notre site
        $this->assertSelectorTextContains('h1', 'Films, séries TV et popcorn en illimité.');
    }

    public function testRouteRedirect(): void
    {
        // création du client HTTP
        $client = static::createClient();
        
        // on récupère le résultat d'une route : '/backoffice/casting'
        $crawler = $client->request('GET', '/backoffice/casting');
       
        $this->assertResponseRedirects();
       
        $crawler = $client->followRedirect();

        $this->assertSelectorTextContains('h1', 'Please sign in');
    }

    
}
