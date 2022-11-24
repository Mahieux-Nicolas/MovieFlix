<?php
/*
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class MaintenanceSubscriber implements EventSubscriberInterface
{
    private $maintenanceIsEnabled;

    /*
    *
    * 
    
    public function __construct($maintenanceEnabled)
    {
   
        
        $this->maintenanceIsEnabled = $maintenanceEnabled;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        // je ne veux pas modifier les routes '_profiler' '_wdt' '/backoffice'
        // le pathInfo étant la route
        $pathInfo = $event->getRequest()->getPathInfo();
        // si la regex matche, c'est que j'ai trouvé une route que je ne veux pas traiter
        if (preg_match('/^\/(_profiler|_wdt|backoffice|api)/', $pathInfo)){
            // je ne fais rien
            return;
        }

        if ($this->maintenanceIsEnabled){
            // j'attrape la réponse, j'en récupère le contenu HTML
            $contentHtml = $event->getResponse()->getContent();
            // j'ai tout mon HTML en brut
            // dump($contentHtml);
            // je modifie le contenu HTML
            $contentHtmlModifie = str_replace('<body>', '<body><div class="alert alert-danger">Maintenance prévue mercredi 23 novembre à 17h00</div>', $contentHtml);

            // je dois redonner le contenu HTML à la réponse
            $event->getResponse()->setContent($contentHtmlModifie);
        }
        
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.response' => 'onKernelResponse',
        ];
    }
}
*/