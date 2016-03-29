<?php

namespace Wharf\Containers;

class PhpContainer extends Container
{
    const SERVICE = 'php';

    public static function supports($software)
    {
        return collect(['php'])->contains($software);
    }

    public static function fromConfig($config)
    {
        return new static('php', $config);
    }

    protected function configurables()
    {
    }
}
