<?php

namespace Wharf\Containers;

class WebContainer extends Container
{
    // protected $name = 'Web Container';

    // protected $config;

    // public static function supportedImages()
    // {
    //     return collect(['nginx']);
    // }

    public function service()
    {
        return 'web';
    }

    protected function defaultSettings()
    {
        return collect([
            'env_file'     => '.env',
            'ports'        => ['80:80'],
            'links'        => ['php'],
            'volumes_from' => ['code'],
        ]);
    }

    protected function configurables()
    {
        return collect(['APP_URL', 'DIRECTORY']);
    }

    protected function requiredSettings()
    {
        return collect(['APP_URL' => 'wharf.dev', 'DIRECTORY' => 'public']);
    }

    protected function display($config)
    {
        if (! isset($this->$config)) {
            return 'not set';
        }
    }

    protected function displayConfigState()
    {
        // return '<error>ERROR</error>';
    }
}
