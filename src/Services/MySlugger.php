<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class MySlugger 
{
    private $sluggerInterface;
    private $tolower;

    /**
    * Constructor
    */
    public function __construct(SluggerInterface $sluggerInterface, ParameterBagInterface $globalParams, $lower)
    {
        // dd($lower);

        $this->sluggerInterface = $sluggerInterface;
        //?  je récupère le paramètre global de services.yaml 
        $this->tolower = $lower;
        
    }

    /**
     * renvoit le slug à partir d'un titre
     *
     * @param string $titre le titre à transformer
     * @return string le slug du titre
     */
    public function slug(string $titre): string
    {
        
        if ($this->tolower){
            $slug = $this->sluggerInterface->slug($titre)->lower();
        } else {
            $slug = $this->sluggerInterface->slug($titre);
        }
        
        return $slug;
    }
}