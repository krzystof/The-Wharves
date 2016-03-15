<?php

namespace Wharf\Project;

use Exception;

class EnvFile
{
    private $variables = [];

    private $filePath;

    private $content = '';

    public function __construct($filePath, $fileSystem)
    {
        $this->filePath = $filePath;
        $this->content  = $fileSystem->read($this->filePath);

        $this->loadContent();
    }

    public function loadContent()
    {
        $lines = explode("\n", $this->content);

        array_map(function ($line) {
            $this->loadVariable($line);
        }, $lines);
        // die(var_dump($this->content));
    }

    protected function loadVariable($line)
    {
        if (stristr($line, '=')) {
            $config = explode('=', $line);
            $this->variables[trim($config[0])] = trim($config[1]);
        }
    }

    public function name()
    {
        return $this->filePath;
    }

    public function get($key)
    {
        return array_key_exists($key, $this->variables) ? $this->variables[$key] : '';
    }

    public function set($key, $value)
    {
        $this->content = "\n$key=$value";

        $this->loadContent();
    }

    public function __toString()
    {
        return $this->content;
    }
}
