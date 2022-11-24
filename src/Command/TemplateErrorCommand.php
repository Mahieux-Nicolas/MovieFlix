<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpKernel\KernelInterface;

class TemplateErrorCommand extends Command
{
    protected static $defaultName = 'app:template:error';
    protected static $defaultDescription = 'generate directories for twig templates error';

    private $fs;
    private $projectDir;
    /**
    * Constructor
    */
    public function __construct(Filesystem $fs, KernelInterface $kernel)
    {   
        parent::__construct();

        $this->fs = $fs;
        $this->projectDir = $kernel->getProjectDir();
    }

    protected function configure(): void
    {
        $this
            ->addOption('error', null, InputOption::VALUE_REQUIRED, 'le numéro de l\'erreur, pour générer le fichier correspondant')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        


        try {
            $this->fs->mkdir(
                Path::normalize($this->projectDir.'/templates/bundles/TwigBundle/Exception'),
            );

            // je teste l'option pour créer un fichier avec le numéro fournit par l'utilisateur
            if ($input->getOption('error')) {
                $numError = $input->getOption('error');
                $this->fs->touch(Path::normalize($this->projectDir.'/templates/bundles/TwigBundle/Exception/error'.$numError.'.html.twig'));
            }

        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your directory at ".$exception->getPath();
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
