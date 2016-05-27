<?php

namespace Wharf\Containers;

class DbContainer extends Container
{
    public function service()
    {
        return 'db';
    }

    protected function defaultSettings()
    {
        return collect([
            'env_file'    => '.env',
            'ports'       => ['3307:3306'],
            'volumes'     => ['./.database:/var/lib/mysql'],
            'environment' => [],
        ]);
    }

    protected function configurables()
    {
        return collect(['DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD']);
    }

    protected function requiredSettings()
    {
        return collect(['DB_DATABASE' => 'wharf_db', 'DB_USERNAME' => 'wharf_user', 'DB_PASSWORD' => 'secret']);
    }
}
