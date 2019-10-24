<?php

namespace Thunder\Command;

use Symfony\Component\Console\Command\Command;

abstract class DevelopCommand extends Command
{
    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }
}
