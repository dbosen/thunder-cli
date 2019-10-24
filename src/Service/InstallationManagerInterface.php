<?php


namespace Thunder\Service;


use Thunder\Model\Installation;

interface InstallationManagerInterface
{

    /**
     * @param string $name
     *
     * @return Installation
     */
    public function getInstallation(string $name): Installation;

    /**
     * @return Installation[]
     */
    public function getInstallations(): array;

    /**
     * @param string $name
     * @param string $baseDirectory
     * @param string $type
     */
    public function setInstallation(string $name, string $baseDirectory, string $type): void;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isInstallation(string $name): bool;
}
