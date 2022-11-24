<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OmdbApi
{
    private $httpClient;

    private $apiKey = "xxxxx";
    /**
    * Constructor
    */
    public function __construct(HttpClientInterface $client, ParameterBagInterface $globalParams)
    {
      // va permettre d'utiliser le $client partout
        $this->httpClient = $client;
       // on récupère le paramètre dans le service.yaml
        $this->apiKey = $globalParams->get('app.omdbapi.apikey');
    }
   


    /**
     * fetch all infos from title
     *
     * @param string $title
     */
    public function fetch(string $title)
    {
       
        // faire une requête http :
        // rajout du HttpClient en injection
        $response = $this->httpClient->request(
            'GET',
            'https://www.omdbapi.com/?t='.$title.'&apikey='.$this->apiKey
        );


        $content = $response->toArray();
        
        return $content;
    }

    public function fetchPoster($title)
    {
        $allinfos = $this->fetch($title);
        
     
        if (array_key_exists('Poster', $allinfos)){
            return $allinfos['Poster'];
        }
        // sinon on renvoie un lien d'image par défaut
        return "https://amc-theatres-res.cloudinary.com/amc-cdn/static/images/fallbacks/DefaultOneSheetPoster.jpg";
    }
}