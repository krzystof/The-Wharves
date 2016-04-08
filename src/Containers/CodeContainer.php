<?php

namespace Wharf\Containers;

class CodeContainer extends Container
{
    public static function service()
    {
        return 'code';
    }

    protected function configurables()
    {
        return collect();
    }

    protected function requiredSettings()
    {
        return collect();
    }
}
