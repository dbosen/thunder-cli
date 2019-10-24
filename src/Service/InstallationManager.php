<?php

namespace Thunder\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Thunder\Model\Installation;

class InstallationManager implements InstallationManagerInterface
{

    /**
     * The object serializer.
     *
     * @var Serializer $serializer
     */
    private $serializer;

    /**
     * The application kernel.
     *
     * @var Kernel $kernel
     */
    private $kernel;

    /**
     * The filesystem service.
     *
     * @var Filesystem $filesystem
     */
    private $filesystem;

    /**
     * Cached installations.
     *
     * @var Installation[] $installations
     */
    private $installations;

    /**
     * The directory containing the saved config files.
     */
    private const CONFIG_DIRECTORY='/var/installations/';

    public function __construct(KernelInterface $kernel, Filesystem $filesystem)
    {
        $this->kernel = $kernel;
        $this->filesystem = $filesystem;

        $normalizers = [new ObjectNormalizer()];
        $encoders = [new YamlEncoder()];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function getInstallation(string $name): Installation
    {
        if (!isset($this->installations[$name])) {
            $serializedInstallation = file_get_contents($this->getInstallationFile($name));
            $this->installations[$name] = $this->serializer->deserialize(
                $serializedInstallation,
                Installation::class,
                'yaml'
            );
        }
        return $this->installations[$name];
    }

    public function setInstallation(string $name, string $baseDirectory, string $type): void
    {
        $installation = new Installation();

        $installation->setName($name)
            ->setBaseDirectory($baseDirectory)
            ->setType($type);

        $serializedInstallation = $this->serializer->serialize($installation, 'yaml', ['yaml_inline' => 1]);

        $this->filesystem->dumpFile($this->getInstallationFile($name), $serializedInstallation);
    }

    public function isInstallation(string $name): bool
    {
        return $this->filesystem->exists($this->getInstallationFile($name));
    }


    public function getInstallations(): array
    {
        $scanDirectory = $this->kernel->getProjectDir() . self::CONFIG_DIRECTORY;
        $configFiles = array_filter(scandir($scanDirectory), function ($v) {
            return (strpos(strrev($v), 'lmay.') === 0);
        });

        foreach ($configFiles as $configFile) {
            $serializedInstallation = file_get_contents($scanDirectory . $configFile);
            $installation = $this->serializer->deserialize($serializedInstallation, Installation::class, 'yaml');
            $this->installations[$installation->getName()] = $installation;
        }

        return $this->installations;
    }

    private function getInstallationFile(string $name)
    {
        $name = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $name);
        $name = mb_ereg_replace("([\.]{2,})", '', $name);

        return $this->kernel->getProjectDir() . self::CONFIG_DIRECTORY . $name . '.yaml';
    }
}
