<?php

namespace Wharf\Containers;

class WharfContainers
{
    public static function make($container, $config = [], $envFile = null)
    {
        $containerName = static::normalize($container);

        return $containerName::fromConfig($config, $envFile);
    }

    private static function normalize($containerName)
    {
        $class = '\\Wharf\Containers\\'.ucfirst($containerName).'Container';

        return class_exists($class)
             ? $class
             : '\\Wharf\Containers\\EmptyContainer';
    }
}
