<?php

namespace Wharf\Containers;

use Illuminate\Contracts\Support\Arrayable;

class Image implements Arrayable
{
    protected $name;
    protected $tag;

    protected function __construct($service = null, $name = null, $tag = null)
    {
        $this->service = $service ?: 'not_set';
        $this->name    = $name ?: 'not_set';
        $this->tag     = $tag && $tag !== 'latest' ? $tag : $this->latest();

        $this->validateImage();
    }

    public static function make($string)
    {
        $parts = explode(':', $string);

        $service = isset($parts[0]) ? $parts[0] : null;
        $name    = isset($parts[1]) ? $parts[1] : null;
        $tag     = isset($parts[2]) ? $parts[2] : null;

        return new static($service, $name, $tag);
    }

    public static function show($search)
    {
        return collect(static::images()->get($search))->keys();
    }

    public static function makeEmpty()
    {
        return new static;
    }

    public function name()
    {
        return $this->name;
    }

    public function tag()
    {
        return $this->tag;
    }

    public function __toString()
    {
        return $this->name.':'.$this->tag;
    }

    protected function validateImage()
    {
        if (! $this->availableServices()->contains($this->service)) {
            throw new InvalidImage(sprintf('The service "%s" is not valid', $this->service));
        }

        if (! $this->availableImages()->contains($this->name)) {
            $errorMessage = sprintf(
                'The image "%s" is not supported for the service %s',
                $this->name,
                $this->service
            );

            throw new InvalidImage($errorMessage);
        }

        if (! $this->availableTags()->contains($this->tag)) {
            $errorMessage = sprintf(
                'The tag "%s" is not supported for the image %s',
                $this->tag,
                $this->name
            );

            throw new InvalidImage($errorMessage);
        }
    }

    protected function latest()
    {
        return $this->availableTags()->last();
    }

    protected function availableServices()
    {
        return $this->images()->keys();
    }

    protected function findService()
    {
        return collect($this->images()->get($this->service));
    }

    protected function availableImages()
    {
        return $this->findService()->keys();
    }

    public function availableTags()
    {
        return collect($this->findService()->get($this->name));
    }

    protected function images()
    {
        return collect([
            'web'     => ['nginx' => ['1.8.1']],
            'php'     => ['php' => ['7.0', '5.6']],
            'db'      => ['postgres' => '1.0.0', 'mysql' => '5.7'],
            'code'    => ['code' => 'latest'],
            'not_set' => ['not_set' => 'not_set'],
        ]);
    }

    public function toArray()
    {
        return $this->__toString();
    }

    public function exists()
    {
        return $this->name !== 'not_set' && $this->tag !== 'not_set';
    }

    public function versionTo($version)
    {
        if (! $this->availableTags()->contains($version)) {
            throw new InvalidImage(sprintf(
                'The version "%s" is not valid on the image "%s %s"',
                $version,
                $this->service,
                $this->name
            ));
        }

        return new static($this->service, $this->name, $version);
    }

    protected function sameAs($image)
    {
        return $this->name === $image->name() && $this->tag === $image->tag();
    }

    public function notSameAs($image)
    {
        return ! $this->sameAs($image);
    }
}
