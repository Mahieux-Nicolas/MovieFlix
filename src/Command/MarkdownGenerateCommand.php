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

class MarkdownGenerateCommand extends Command
{
    protected static $defaultName = 'app:markdown:generate';
    protected static $defaultDescription = 'Add a short description for your command';
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
            ->addArgument('file_name', InputArgument::REQUIRED, 'Name of the generated file')
            //->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fileName = $input->getArgument('file_name');

        if (!$fileName) {
            $io->error('You have not passed an argument');
            return Command::INVALID;
        }

        try {
            $this->fs->mkdir(
                Path::normalize($this->projectDir.'/docs/commandes'),
            );
            
            $this->fs->touch(Path::normalize($this->projectDir.'/docs/commandes/'.$fileName.'.md'));
            
            $this->fs->mkdir(
                Path::normalize($this->projectDir.'/docs/cours'),
            );
            
            $this->fs->touch(Path::normalize($this->projectDir.'/docs/cours/'.$fileName.'.md'));

        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your directory at ".$exception->getPath();
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
