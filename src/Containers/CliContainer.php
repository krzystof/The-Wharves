<?php

namespace Wharf\Containers;

class CliContainer extends Container
{
    public function service()
    {
        return 'cli';
    }

    protected function defaultSettings()
    {
        return collect([
            'volumes_from' => ['code'],
            'working_dir'  => '/code',
            'links'        => ['db'],
        ]);
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
