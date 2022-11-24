<?php

namespace App\Command;

use App\Repository\MovieRepository;
use App\Services\MySlugger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SluggifyCommand extends Command
{
  
    protected static $defaultName = 'app:services:slug';

    protected static $defaultDescription = 'Permet de mettre à jour tout les slugs de tout les films de toute la BDD';

    private $movieRepository;
    private $mySlugger;
    private $entityManager;

    /**
    * Constructor
    */
    public function __construct(MovieRepository $movieRepository, MySlugger $mySlugger, EntityManagerInterface $entityManager)
    {
        //! Command class "App\Command\SluggifyCommand" is not correctly initialized. You probably forgot to call the parent constructor.
        parent::__construct();

        $this->movieRepository = $movieRepository;
        $this->mySlugger = $mySlugger;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        
        $this
            ->addArgument('idMovie', InputArgument::OPTIONAL, 'id du film sur lequel on va regenérer le slug')
   
            ->addOption('lower', null, InputOption::VALUE_NONE, 'force l\'option toLower')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //? https://symfony.com/doc/5.4/console/style.html
        $io = new SymfonyStyle($input, $output);
        // récupérer la valeur de l'argument
        $idMovie = $input->getArgument('idMovie');

        // on vérifie si il y a une valeur
        if ($idMovie) {
            $io->note(sprintf('You passed an argument: %s', $idMovie));
            // TODO : récup le film par son id, mettre à jour son slug
            $movie = $this->movieRepository->find($idMovie);

            $slug = $this->mySlugger->slug($movie->getTitle());
            
            // on regarde si l'option est présente
            if ($input->getOption('lower')) {
                $slug = strtolower($slug);
            }

            $movie->setSlug($slug);
            
            $this->entityManager->flush();

            $io->success('Le film ' . $movie->getTitle() . ' a bien été modifié');

            return Command::SUCCESS;
        }
        


        $allMovies = $this->movieRepository->findAll();
    
        $io->note('il y a ' . count($allMovies) . ' films à mettre à jour');

        foreach ($allMovies as $movie) {
            $io->text('Génération du slug pour le film : ' . $movie->getTitle());

            $slug = $this->mySlugger->slug($movie->getTitle());
            // on regarde si l'option est présente
            if ($input->getOption('lower')) {
                $slug = strtolower($slug);
            }

            $movie->setSlug($slug);
        }


        $this->entityManager->flush();

        
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
