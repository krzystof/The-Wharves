<?php

namespace Wharf\Containers;

class DbContainer extends Container
{
    // public static function supportedImages()
    // {
    //     return collect(['mysql', 'postgres']);
    // }

    public function service()
    {
        return 'db';
    }

    protected function defaultSettings()
    {
        return collect(['env_file' => '.env']);
    }

    protected function configurables()
    {
        return collect(['DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD']);
    }

    protected function requiredSettings()
    {
        return collect(['DB_USERNAME' => 'wharf_user']);
    }
}
