<?php

namespace Wharf\Project;

use Exception;

class Container
{
    protected $name;

    protected $settings;

    public function __construct($name = '', $settings = [])
    {
        $this->name = $name;
        $this->settings = $settings;
    }

    public static function isSupported($container)
    {
        return in_array($container, ['web', 'php', 'db']);
    }

    public static function getDefault($name)
    {
        return new static($name, static::defaultSettings($name));
    }

    public function name()
    {
        return $this->name;
    }

    public function image()
    {
        if (empty($this->settings)) {
            return 'no container';
        }

        return $this->splitImage(0);
    }

    protected function setImage($imageName)
    {
        $this->settings['image'] = $imageName;
    }

    public function tag()
    {
        if (empty($this->settings)) {
            return '';
        }

        return $this->splitImage(1);
    }

    private function splitImage($key)
    {
        $image = explode(':', $this->settings['image']);

        return $image[$key];
    }

    public function setTag($tag)
    {
        $this->settings['image'] = str_replace($this->tag(), $tag, $this->settings['image']);
    }

    public static function database($dbName)
    {
        $dbContainer = new static($dbName, ['image' => static::imageFor($dbName)]);

        return $dbContainer;
    }

    public function toArray()
    {
        return $this->settings;
    }


    // fpm:
    //     image: krzystof/the-wharves-php7fpm
    //     env_file: .env
    //     expose:
    //         - "9000"
    //     volumes_from:
    //       - code
    //     working_dir: /code
    //

    private static function defaultSettings($name)
    {
        $defaultSettings = [
            'php' => [
                'image' => 'php:7.0',
            ],
            'db' => [
                'image' => 'mysql:5.7',
            ]
        ];

        if (! array_key_exists($name, $defaultSettings)) {
            throw new Exception(sprintf('The container %s does not have default settings set.', $name));
        }

        return $defaultSettings[$name];
    }

    private function imageFor($name)
    {
        $images = [
            'mysql' => 'mysql:5.7',
            'postgres' => 'postgres:9.5',
        ];

        return $images[$name];
    }
}
