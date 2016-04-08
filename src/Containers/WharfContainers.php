<?php

namespace Wharf\Containers;

class WharfContainers
{
    protected static function make($container, $config)
    {
        // TODO normalise instantiation
        switch ($container) {
            case 'code':
                return CodeContainer::fromConfig($config);
            case 'web':
                return WebContainer::fromConfig($config);
            case 'php':
                return PhpContainer::fromConfig($config);
            case 'db':
                return DbContainer::fromConfig($config);
            default:
                throw new ContainerNotSupported(sprintf('The container "%s" is not supported', $container));
        }
    }

    public static function __callStatic($method, $args)
    {
        $config = $args ? $args[0] : [];

        return static::make($method, $config);
    }
}
