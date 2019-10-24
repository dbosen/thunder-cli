<?php

namespace Thunder\Command\Develop;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Thunder\Command\DevelopCommand;

class ReviewCommand extends DevelopCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('develop:review')
            ->setAliases(['review', 'dr'])
            ->addArgument('branch', InputArgument::REQUIRED, 'The branch to review.')
            ->addArgument('installation', InputArgument::OPTIONAL, 'The installation to test in.', 'default')
            ->addArgument('project', InputArgument::OPTIONAL, 'The project to review.', 'thunder')
            ->setDescription('Install a project branch in a given thunder installation, run composer update and install thunder.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->validateInput($input, $output);
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());
            return 1;
        }


    }

    protected function validateInput(InputInterface $input)
    {
        $installationName = $input->getArgument('installation');

        if (!$this->installationManager->isInstallation($installationName)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Unknown installation given: %s',
                    $installationName
                )
            );
        }

        $installation = $this->installationManager->getInstallation($installationName);

        if ($installation->getType() !== 'thunder-develop') {
            throw new \InvalidArgumentException(
                sprintf(
                    'Installation %s is not of type thunder-distribution and cannot be reset.',
                    $installationName
                )
            );
        }

        $projectName = $input->getArgument('project');


    }
}
