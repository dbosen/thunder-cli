<?php

namespace Thunder\Command\Installation;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thunder\Command\InstallationCommand;

class ListCommand extends InstallationCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('installation:list')
            ->setAliases(['ilist', 'il'])
            ->setDescription('List available Thunder and Drupal installations.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['Name', 'Type', 'Directory']);

        foreach ($this->installationManager->getInstallations() as $installation) {
            $table->addRow([$installation->getName(), $installation->getType(), $installation->getBaseDirectory()]);
        }

        $table->render();
    }
}
