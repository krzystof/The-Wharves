<?php

namespace Wharf;

use Exception;
use Wharf\Containers\Container;
use Illuminate\Support\Collection;
use Wharf\Containers\WharfContainers;

class DockerComposeYml
{
    private $containers;

    public function __construct($parsedFile = [])
    {
        $this->loadContainers($parsedFile);
    }

    public function container($name)
    {
        return $this->containers->has($name)
             ? $this->containers->get($name)
             : WharfContainers::$name([]);
    }

    private function loadContainers($parsedFile)
    {
        $this->containers = new Collection;

        foreach ($parsedFile as $name => $config) {
            $this->containers->put($name, WharfContainers::$name($config));
        }
    }

    public function setContainer($service, $container)
    {
        $this->containers[$service] = $container;
    }

    public function content()
    {
        return $this->containers->toArray();
    }

    public function savedAllContainers()
    {
        foreach ($this->containers as $container) {
            $container->saved();
        }
    }
}
