<?php

namespace Wharf\Containers;

class DbContainer extends Container
{
    // public static function supportedImages()
    // {
    //     return collect(['mysql', 'postgres']);
    // }

    public static function service()
    {
        return 'db';
    }

    protected function configurables()
    {
        return collect(['DB_USERNAME']);
    }

    protected function requiredSettings()
    {
        return collect(['DB_USERNAME' => 'wharf_user']);
    }
}
