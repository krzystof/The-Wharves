<?php

namespace Wharf\Containers;

class PhpContainer extends Container
{
    public static function supportedImages()
    {
        return collect(['php']);
    }

    public static function service()
    {
        return 'php';
    }

    protected function configurables()
    {
        return collect([]);
    }

    protected function requiredSettings()
    {
        return collect([]);
    }
}
