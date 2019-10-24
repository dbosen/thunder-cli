<?php

namespace Thunder\Command;

use Symfony\Component\Console\Command\Command;
use Thunder\Service\InstallationManagerInterface;

abstract class InstallationCommand extends Command
{
    /**
     * The installation manager service.
     *
     * @var InstallationManagerInterface $installationManager
     */
    protected $installationManager;

    public function __construct(string $name = null, InstallationManagerInterface $installationManager)
    {
        parent::__construct($name);

        $this->installationManager = $installationManager;
    }

}
