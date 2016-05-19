<?php

namespace Wharf\Containers;

class PhpContainer extends Container
{
    // public static function supportedImages()
    // {
    //     return collect(['php']);
    // }

    public function service()
    {
        return 'php';
    }

    protected function defaultSettings()
    {
        return collect([
            'expose'       => ['9000'],
            'volumes_from' => ['code'],
            'working_dir'  => '/code',
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
