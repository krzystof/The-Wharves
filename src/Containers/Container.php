<?php

namespace Wharf\Containers;

use Exception;

abstract class Container
{
    protected $service;

    protected $config;

    protected $changed = false;

    protected function __construct($service = '', $config = [])
    {
        $this->service = $service;
        $this->config = collect($config);

        $this->validateImage();
    }

    protected function validateImage()
    {
        if ($this->config->has('image') && ! static::supports($this->image())) {
            throw new \Exception(sprintf('The image "%s" is not supported.', $this->image()));
        }

        if (! $this->tag() && $this->config->has('image')) {
            $this->config['image'] .= ':latest';
        }
    }

    public function getDefault()
    {
        # code...
    }

    public static function fromConfig($config)
    {
        return new static(static::SERVICE, $config);
    }

    public function env($key)
    {
        if (! $this->config->has('environment')) {
            return '';
        }

        if (! $this->environment()->has($key)) {
            return '';
        }

        return $this->environment()->get($key);
    }

    public function has($option)
    {
        return $this->environment()->has($option);
    }

    public function environmentFrom($config)
    {
        $filteredConfig = $this->filterConfig($config);

        if ($this->config->has('environment')) {
            $filteredConfig = $this->environment()->merge($filteredConfig);
        }

        $this->config->put('environment', $filteredConfig);

        $this->changed();
    }

    protected function filterConfig($config)
    {
        return collect($config)->filter(function ($value, $option) {
            return $this->isValidSetting($option);
        });
    }

    protected function isValidSetting($option)
    {
        return $this->configurables()->contains($option);
    }

    abstract protected function configurables();

    protected function changed()
    {
        $this->changed = true;
    }

    public static function supports($container)
    {
        return in_array($container, ['web', 'php', 'db']);
    }

    public function service()
    {
        return $this->service;
    }

    public function isNew()
    {
        return $this->isEmpty();
    }

    public function isEmpty()
    {
        return count($this->config) === 0;
    }

    public function image()
    {
        return $this->splitImage(0);
    }

    public function tag()
    {
        return $this->splitImage(1);
    }

    private function splitImage($key)
    {
        if (! $this->config->has('image')) {
            return 'not set';
        }

        $image = explode(':', $this->config->get('image'));

        return array_key_exists($key, $image) ? $image[$key] : '';
    }

    public function setTag($tag)
    {
        $this->config['image'] = str_replace($this->tag(), $tag, $this->config['image']);
    }

    public function environment()
    {
        return collect($this->config->get('environment', []));
    }

    public static function db($config = [])
    {
        return DbContainer::fromConfig($config);
    }

    public static function web($config = [])
    {
        return WebContainer::fromConfig($config);
    }

    public static function php($config = [])
    {
        return PhpContainer::fromConfig($config);
    }

    public function toArray()
    {
        return $this->config;
    }
}
