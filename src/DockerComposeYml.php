<?php

namespace Wharf;

use Exception;
use Wharf\Containers\Container;
use Illuminate\Support\Collection;
use Symfony\Component\Yaml\Dumper;
use Wharf\Containers\EmptyContainer;
use Wharf\Containers\WharfContainers;
use Wharf\Containers\ContainerDoesNotExist;

class DockerComposeYml
{
    const INDENTATION_DEPTH = 4;

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

    public function setContainer($container)
    {
        $this->containers[$container->service()] = $container;
    }

    private function content()
    {
        return $this->containers->sortBy(function ($container) {
            return $this->orderOfService($container->service());
        })->toArray();
    }

    private function orderOfService($service)
    {
        return collect(['code', 'php', 'web', 'db'])->search($service);
    }

    private function hasSavedAllContainers()
    {
        foreach ($this->containers as $container) {
            $container->saved();
        }
    }

    public function saveInFiles($filesystem)
    {
        $yaml = (new Dumper)->dump($this->content(), self::INDENTATION_DEPTH);

        $filesystem->put('docker-compose.yml', $yaml);

        $this->hasSavedAllContainers();
    }
}
