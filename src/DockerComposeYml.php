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

    public function __construct($parsedFile = [], $envFile = [])
    {
        $this->loadContainers($parsedFile, $envFile);
    }

    public function container($name, $envFile = null)
    {
        return $this->containers->has($name)
             ? $this->containers->get($name)
             : WharfContainers::make($name, [], $envFile);
    }

    private function loadContainers($parsedFile, $envFile)
    {
        $this->containers = new Collection;

        collect($parsedFile)->each(function ($config, $name) use ($envFile) {
            $this->containers->put($name, WharfContainers::make($name, $config, $envFile));
        });
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

    public function saveInFile($filesystem)
    {
        $yaml = (new Dumper)->dump($this->content(), self::INDENTATION_DEPTH);

        $filesystem->put('docker-compose.yml', $yaml);

        $this->hasSavedAllContainers();
    }

    public function removeContainer($serviceToDelete)
    {
        $this->containers = $this->containers->filter(function ($container) use ($serviceToDelete) {
            return $container->service() !== $serviceToDelete;
        });

        return $this;
    }
}
