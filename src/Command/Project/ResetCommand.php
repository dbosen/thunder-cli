<?php

namespace Thunder\Command\Project;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Thunder\Command\ProjectCommand;

class ResetCommand extends ProjectCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('project:reset')
            ->setAliases(['reset', 'pr'])
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Choose the Thunder installation, in case multiple installations are configured. 
              Use thunder:list command to show available installations. Only projects of type thunder-develop can be reset',
                'default'
            )

            ->setDescription('Reset all development repositories in a thunder-develop project to their current development head branch..');
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

        $name = $input->getArgument('name');
        $installation = $this->installationManager->getInstallation($name);

        chdir($installation->getBaseDirectory());

        $process = new Process(['composer', 'reset-repositories']);
        $process->run();

        foreach ($process as $type => $data) {
            $output->write($data);
        }

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output->writeln('');
    }

    protected function validateInput(InputInterface $input)
    {
        $installationName = $input->getArgument('name');

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
    }
}
