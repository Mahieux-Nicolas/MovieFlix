<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ThemeController extends AbstractController
{
    /**
     * toggle de theme
     * 
     * @Route("/theme",name="theme_toggle")
     */
    public function toggle(SessionInterface $session): Response
    {
       
        // on récupère la valeur lié à la clé theme
        // si pas de theme => netflix
        $theme = $session->get('theme', 'netflix');
      
        if ($theme === 'netflix'){
            $session->set('theme', 'allocine');
        } else {
            $session->set('theme', 'netflix');
        }
        
        //  redirection page accueil
        return $this->redirectToRoute('default');

    }
}