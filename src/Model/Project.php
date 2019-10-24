<?php

namespace Thunder\Model;

class Project
{
    /**
     * The unique name of the project.
     *
     * @var string $name
     */
    private $name;

    /**
     * The projects composer type.
     *
     * Typical project types in Thunder development are drupal-module, drupal-profile and drupal-theme.
     *
     * @var string $type
     */
    private $type;

    /**
     * The projects composer name.
     *
     * @var string $composerName
     */
    private $composerName;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Project
     */
    public function setName(string $name): Project
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
     * @return Project
     */
    public function setType(string $type): Project
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getComposerName(): string
    {
        return $this->composerName;
    }

    /**
     * @param string $composerName
     * @return Project
     */
    public function setComposerName(string $composerName): Project
    {
        $this->composerName = $composerName;

        return $this;
    }


}
