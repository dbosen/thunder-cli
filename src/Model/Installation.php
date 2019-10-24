<?php

namespace Thunder\Model;

class Installation
{

    /**
     * The unique name of the installation.
     *
     * @var string $name
     */
    private $name;

    /**
     * The installation type.
     *
     * Installations can be of type thunder-develop, thunder-project and
     * drupal-project.
     *
     * @var string $type
     */
    private $type;

    /**
     * The base directory of the installation on the local host.
     *
     * @var string $baseDirectory
     */
    private $baseDirectory;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Installation
     */
    public function setName(string $name): Installation
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Installation
     */
    public function setType(string $type): Installation
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseDirectory(): string
    {
        return $this->baseDirectory;
    }

    /**
     * @param string $baseDirectory
     *
     * @return Installation
     */
    public function setBaseDirectory(string $baseDirectory): Installation
    {
        $this->baseDirectory = $baseDirectory;

        return $this;
    }

    /**
     * Get directory of a destination within an installation.
     *
     * @param string $destination
     *  The destination to go to, could be project, docroot, modules, themes,
     *  profiles or the distribution folder.
     * @return string
     * @throws \Exception
     */
    public function getDirectory(string $destination)
    {
        $baseDirectory = $this->getBaseDirectory();
        $type = $this->getType();

        // TODO: Implement robust way to find docroot. Maybe get it from composer.json.
        foreach (['/docroot', '/web', '/htdocs'] as $subDirectory) {
            if (is_dir($baseDirectory . $subDirectory)) {
                $docroot = $baseDirectory . $subDirectory;
                break;
            }
        }

        if (empty($docroot)) {
            throw new \Exception('Cannot find docroot.');
        }

        switch ($destination) {
            case 'base':
                $directory = $baseDirectory;
                break;
            case 'docroot':
                $directory = $docroot;
                break;
            case 'modules':
            case 'themes':
            case 'profiles':
                $directory = $docroot . '/' . $destination;
                break;
            case 'distribution':
                if (strpos($type, 'thunder') === 0) {
                    $directory = $docroot . '/profiles/contrib/thunder';
                } else {
                    throw new \Exception('Cannot find distribution.');
                }
                break;
            default:
                $directory = '.';
        }

        return $directory;
    }
}
