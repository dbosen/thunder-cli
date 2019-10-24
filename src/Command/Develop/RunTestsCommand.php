<?php

namespace Thunder\Command\Develop;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class RunTestsCommand extends DevelopCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('develop:run-tests')
            ->setAliases(['run-tests', 'rt'])
            ->addOption('stage', 's', InputOption::VALUE_REQUIRED, 'The stage to run as defined by drupal/travis.', null)
            ->addOption('filter', 'f', InputOption::VALUE_REQUIRED, 'Filter which tests to run.', null)
            ->addOption('group', 'g', InputOption::VALUE_REQUIRED, 'Only runs tests from the specified group(s).', null)
            ->addOption('keep-files', 'k', InputOption::VALUE_NONE, 'Do not cleanup installation after successful tests.', null)
            ->setDescription('Run drupal tests. Run this command directly in the project directory. Requires the drupal/travis package.');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $group = $input->getOption('group');
        $filter = $input->getOption('filter');
        $stage = $input->getOption('stage');
        $keepFiles = $input->getOption('keep-files');

        $command = ['test-drupal-project'];

        if (!empty($stage)) {
            $command[] = $stage;
        }
        if ($keepFiles) {
            $command[] = '--no-cleanup';
        }

        $env = [];

        if (!empty($group)) {
            $env['DRUPAL_TRAVIS_TEST_GROUP'] = $group;
        }

        if (!empty($filter)) {
            $env['DRUPAL_TRAVIS_TEST_FILTER'] = $filter;
        }

        $process = new Process($command, null, $env, null, null);
        $process->start();

        foreach ($process as $type => $data) {
            echo $data;
        }
    }
}
