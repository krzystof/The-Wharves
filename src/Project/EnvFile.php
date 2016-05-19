<?php

namespace Wharf\Project;

use Exception;

class EnvFile extends Config
{
    protected $filePath;
    protected $content = '';
    protected $variables = [];

    public function __construct($filePath = '', $content = '')
    {
        $this->filePath = $filePath;
        $this->content = $content;

        $this->loadContent();
    }

    public function name()
    {
        return $this->filePath;
    }

    private function loadContent()
    {
        $lines = explode("\n", $this->content);

        array_map(function ($line) {
            $this->loadVariable($line);
        }, $lines);
    }

    private function loadVariable($line)
    {
        if (stristr($line, '=')) {
            $config = explode('=', $line);
            $this->variables[trim($config[0])] = trim($config[1]);
        }
    }

    public static function load($filename, $content)
    {
        return new static($filename, $content);
    }

    public function get($key)
    {
        return array_key_exists($key, $this->variables) ? $this->variables[$key] : '';
    }

    public function has($key)
    {
        return array_key_exists($key, $this->variables);
    }

    public function set($key, $value)
    {
        if (strpos($this->content, $key)) {
            $currentValue = $this->get($key);
            str_replace(
                sprintf('%s=%s', $key, $currentValue),
                sprintf('%s=%s', $key, $value),
                $this->content
            );
        } else {
            $this->content = "$key=$value\n";
        }

        $this->loadContent();
    }

    public function filter($callback)
    {
        return collect($this->variables)->filter($callback);
    }

    public function content()
    {
        return $this->content;
    }

    public function __toString()
    {
        return $this->content;
    }

    public function toArray()
    {
        return $this->filePath;
    }
}
