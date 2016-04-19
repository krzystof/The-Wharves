<?php

namespace WharfTests\Doubles;

use League\Flysystem\Filesystem;
use Illuminate\Support\Collection;
use League\Flysystem\Memory\MemoryAdapter;

class InMemoryFilesystem
{
    protected $permissions = [];

    public function __construct()
    {
        $this->filesystem = new Filesystem(new MemoryAdapter);
    }

    public function makeDirectory($directory)
    {
        $this->filesystem->createDir($directory);
    }

    public function deleteDirectory($directory)
    {
        $this->filesystem->deleteDir($directory);
    }

    public function exists($path)
    {
        return $this->filesystem->has($path);
    }

    public function put($path, $content)
    {
        $this->filesystem->put($path, $content);
    }

    public function get($path)
    {
        return $this->filesystem->read($path);
    }

    public function directories($path = '')
    {
        return collect($this->filesystem->listContents($path, true))->filter(function ($item) {
            return $item['type'] === 'dir';
        });
    }

    public function makeWritableByAll($path)
    {
        if (! $this->filesystem->has($path)) {
            return false;
        }

        $this->permissions[$path] = '777';
    }

    public function isWritableByAll($path)
    {
        return array_key_exists($path, $this->permissions)
             ? substr($this->permissions[$path], -1) === '7'
             : false;
    }
}
