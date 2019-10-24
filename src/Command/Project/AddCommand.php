<?php

namespace Thunder\Command\Project;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Thunder\Command\ProjectCommand;

class AddCommand extends ProjectCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('project:add')
            ->setAliases(['padd', 'pa'])
            ->addArgument(
                'base',
                InputArgument::REQUIRED,
                'The projects base directory.'
            )

            ->addOption(
                'name',
                null,
                InputOption::VALUE_REQUIRED,
                'The projects name.'
            )
            ->addOption(
                'type',
                null,
                InputOption::VALUE_REQUIRED,
                'The projects type.'
            )

            ->setDescription('Add new local installation.');
    }
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $baseDirectory = $input->getArgument('base');
        $name = $input->getOption('name');
        $type = $input->getOption('type');

        $questionHelper = $this->getHelper('question');

        if ($baseDirectory === '.') {
            $baseDirectory = getcwd();
        }

        while (!is_dir($baseDirectory)) {
            $current = getcwd();
            $question = new Question('The given directory does not exists, please provide an existing directory (defaults to "' . $current . '"): ', $current);
            $baseDirectory = $questionHelper->ask($input, $output, $question);
        }

        if (!$name) {
            do {
                $retry = false;
                $question = new Question('Please enter the name of the project (defaults to "default"): ', 'default');
                $name = $questionHelper->ask($input, $output, $question);

                if (empty($name)) {
                    $retry = true;
                } elseif ($this->installationManager->isInstallation($name)) {
                    $confirmationQuestion = new ConfirmationQuestion(
                        'Project configuration for "' . $name . '" project exists. Do you want to overwrite? ',
                        false,
                        '/^(y|j)/i'
                    );
                    $retry = !$questionHelper->ask($input, $output, $confirmationQuestion);
                }
            } while ($retry);
        }

        if (!$type) {
            $question = new ChoiceQuestion(
                'Please select the type of the project (defaults to thunder-develop): ',
                ['thunder-develop', 'thunder-project', 'drupal-project'],
                0
            );
            $question->setErrorMessage('Type %s is invalid.');

            $type = $questionHelper->ask($input, $output, $question);
        }

        $this->installationManager->setInstallation($name, $baseDirectory, $type);
    }
}
