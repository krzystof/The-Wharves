<?php

namespace Wharf\Project;

use Exception;

class DockerComposeYml
{
    private $parsedFile;

    private $containers = [];

    public function __construct($parsedFile = [])
    {
        $this->parsedFile = $parsedFile;
    }

    public function container($name)
    {
        if ($this->hasLoadedContainer($name)) {
            return $this->containers[$name];
        }

        $this->loadContainer($name);

        return $this->containers[$name];
    }

    private function hasLoadedContainer($name)
    {
        return array_key_exists($name, $this->containers);
    }

    private function loadContainer($name)
    {
        if (! Container::isSupported($name)) {
            throw new Exception(sprintf('The container "%s" is not supported.', $name));
        }

        if (!array_key_exists($name, $this->parsedFile)) {
            return $this->containers[$name] = Container::getDefault($name);
        }

        return $this->containers[$name] = new Container($name, $this->parsedFile[$name]);
    }

    public function setContainer($type, $container)
    {
        $this->containers[$type] = $container;
    }

    public function content()
    {
        return array_map(function ($container) {
            return $container->toArray();
        }, $this->containers);
    }
}
