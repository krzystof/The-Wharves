<?php

namespace Wharf\Containers;

use Illuminate\Contracts\Support\Arrayable;

class Image implements Arrayable
{
    const NOT_SET = 'not_set';

    private static $images = [
        'web'     => [
            'wharf/nginx' => ['1.8.1']
        ],
        'php'     => [
            'wharf/php' => ['7.0.5', '5.6']
        ],
        'db'      => [
            'wharf/mysql' => '5.7.12',
            'postgres' => '1.0.0'
        ],
        'code'    => ['wharf/code' => 'latest'],
        'not_set' => ['not_set' => self::NOT_SET],
    ];

    private $name;
    private $tag;
    private $is_custom = false;

    private function __construct($service = null, $name = null, $tag = null)
    {
        $this->service = $service ?: self::NOT_SET;
        $this->name    = $name    ?: self::NOT_SET;
        $this->tag     = $tag ?: $this->latest();

        $this->validateImage();
    }

    public static function make($service, $string)
    {
        $parts = explode(':', $string);

        $name    = isset($parts[0]) ? $parts[0] : null;
        $tag     = isset($parts[1]) ? $parts[1] : null;

        return new static($service, $name, $tag);
    }

    public static function makeFor($container)
    {
        return static::make($container->service(), $container->config()->get('image'));
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

    private function isWharfImage()
    {
        return strpos($this->name, 'wharf/') === 0;
    }

    private function validateImage()
    {
        if (! $this->availableServices()->contains($this->service)) {
            throw new InvalidImage(sprintf('The service "%s" is not valid', $this->service));
        }

        if ($this->isWharfImage() && ! $this->availableImages()->contains($this->name)) {
            throw new InvalidImage(sprintf('The wharf image "%s" does not exist.', $this->name));
        }

        if (! $this->availableImages()->contains($this->name)) {
            return $this->is_custom = true;
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

    private function latest()
    {
        return $this->availableTags()->last();
    }

    private function availableServices()
    {
        return $this->images()->keys();
    }

    private function findService()
    {
        return collect($this->images()->get($this->service));
    }

    private function availableImages()
    {
        return $this->findService()->keys();
    }

    public function availableTags()
    {
        return collect($this->findService()->get($this->name));
    }

    private function images()
    {
        return collect(static::$images);
    }

    public function toArray()
    {
        return $this->__toString();
    }

    public function exists()
    {
        return $this->name !== self::NOT_SET && $this->tag !== self::NOT_SET;
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

    private function sameAs($image)
    {
        return $this->name === $image->name() && $this->tag === $image->tag();
    }

    public function notSameAs($image)
    {
        return ! $this->sameAs($image);
    }

    public function isCustom()
    {
        return $this->is_custom;
    }

    public static function all()
    {
        return collect(static::$images)->filter(function ($image, $service) {
            return ! in_array($service, ['code', self::NOT_SET]);
        });
    }
}
