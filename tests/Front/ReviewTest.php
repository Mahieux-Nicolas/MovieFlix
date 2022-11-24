<?php

namespace App\Tests\Front;

use App\Repository\MovieRepository;
use App\Repository\UserRepository;
use App\Tests\CustomWebTestCase;

class ReviewTest extends CustomWebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();

        //  se logger

        /** @var UserRepository $userRepository  */
        $userRepository = static::getContainer()->get(UserRepository::class);

        // on recherche l'user
        $jbOclock = $userRepository->findOneBy(["email" => "jb@oclock.io"]);

        // on logge le clientBrowser
        $client->loginUser($jbOclock);
        
       
        // on demande au container de service de nous fournir le repository
        /** @var MovieRepository $movieRepository permet à vscode de savoir quelle type de classe est une variable */
        $movieRepository = static::getContainer()->get(MovieRepository::class);
        $randomMovie = $movieRepository->findRandomMovie();
        
        // le retour de la fonction est un tableau associatif
        $idRandomMovie = $randomMovie['id'];

        $crawler = $client->request('GET', '/movie/' . $idRandomMovie . '/review');

 
        $buttonCrawlerNode = $crawler->selectButton('Ajouter');
     
        $form = $buttonCrawlerNode->form();

        $form['review[username]'] = 'Les Incas';
        $form['review[email]'] = 'nain@porte.koi';
        $form['review[content]'] = 'encore mieux que le premier';
        $form['review[rating]'] = '5';
    // attention il s'agit d'un tableau de résultat
        $form['review[reactions]'] = ['cry', 'smile'];
        $form['review[watchedAt]'] = '2022-11-23';
        
// envoi en bdd
        $client->submit($form);
        
    // redirection
        $this->assertResponseStatusCodeSame(302);
    }
}
