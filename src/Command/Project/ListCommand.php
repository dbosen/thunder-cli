<?php

namespace Thunder\Command\Project;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thunder\Command\ProjectCommand;

class ListCommand extends ProjectCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('project:list')
            ->setAliases(['plist', 'pl'])
            ->setDescription('List available Thunder and Drupal projects.');
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
