<?php

namespace Wharf\Project;

class Type
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function name()
    {
        return $this->name;
    }

    public static function detectOnFilesystem($filesystem)
    {
        $name = $filesystem->exists('artisan') ? 'laravel' : 'custom';

        return new static($name);
    }

    public function requiredWritableDirectories()
    {
        $directories = [
            'laravel' => ['bootstrap/cache', 'storage'],
            'custom' => [],
        ];

        return collect($directories[$this->name]);
    }
}
