<?php

namespace Thunder\Command\Project;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Thunder\Command\ProjectCommand;
use Thunder\Model\Installation;
use Thunder\Service\InstallationManagerInterface;

class GotoCommand extends ProjectCommand
{
    /**
     * Allowed values for the destination.
     *
     * @var array $allowedDestinations
     */
    private $allowedDestinations;

    public function __construct(string $name = null, InstallationManagerInterface $installationManager, array $allowedDestinations)
    {
        parent::__construct($name, $installationManager);
        $this->allowedDestinations = $allowedDestinations;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('project:goto')
            ->setAliases(['goto', 'pg'])
            ->addArgument(
                'destination',
                InputArgument::REQUIRED,
                'The destination within the Thunder installation.'
            )

            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Choose the Thunder installation, in case multiple installations are configured. 
              Use thunder:list to show available installations',
                'default'
            )

            ->setDescription('Switch to specific Thunder project destination.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->validateInput($input, $output);
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());
            return 1;
        }

        $installation = $this->installationManager->getInstallation($input->getArgument('name'));
        $destination = $input->getArgument('destination');

        $output->write($installation->getDirectory($destination), false, OutputInterface::OUTPUT_RAW);
    }

    protected function validateInput(InputInterface $input)
    {
        $destination = $input->getArgument('destination');
        $installation = $input->getArgument('name');

        if (!empty($destination) && !in_array($destination, $this->allowedDestinations)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid destination "%s" given, valid destinations are: %s',
                    $destination,
                    implode(', ', $this->allowedDestinations)
                )
            );
        }

        if (!$this->installationManager->isInstallation($installation)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Unknown installation given: %s',
                    $installation
                )
            );
        }
    }
}
