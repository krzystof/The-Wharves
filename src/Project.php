<?php

namespace Wharf;

use Wharf\Project\Config;
use Wharf\Project\EnvFile;
use League\Flysystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use League\Flysystem\MountManager;
use Symfony\Component\Yaml\Dumper;
use League\Flysystem\Adapter\Local;

class Project
{
    const WHARF = 'wharf';
    const PROJECT = 'project';

    private static $commonDir = ['public', 'web', 'www'];
    private $fileSystem;
    private $dockerComposeFile;
    private $dockerComposeFilename = 'docker-compose.yml';
    private $envFile;
    private $configs = [];

    public static function onCurrentDirectory()
    {
        $projectFilesystem = new Filesystem(new Local(getcwd()));

        return static::onFilesystem($projectFilesystem, '.env');
    }

    public static function onFilesystem($filesystem, $envFile = '.env')
    {
        return new static($filesystem, $envFile);
    }

    private function __construct($projectFilesystem, $envFile = null)
    {
        $this->fileSystem = new MountManager([
            self::WHARF   => new Filesystem(new Local(dirname(dirname(__FILE__)))),
            self::PROJECT => $projectFilesystem,
        ]);

        $this->envFile = $envFile;

        $this->loadDockerComposeFile();
    }

    public function detectEnvFile()
    {
        return ! is_null($this->envFile);
    }

    public function envFile()
    {
        if ($this->hasLoadedEnvFile()) {
            return $this->envFile;
        }

        $this->loadEnvFile();

        return $this->envFile;
    }

    protected function hasLoadedEnvFile()
    {
        return $this->envFile instanceof EnvFile;
    }

    protected function projectDir()
    {
        return $this->fileSystem->getFilesystem(self::PROJECT);
    }

    private function loadEnvFile()
    {
        $this->envFile = new EnvFile($this->envFile, $this->projectDir());
    }

    private function loadDockerComposeFile()
    {
        if ($this->fileSystem->has($this->project($this->dockerComposeFilename))) {
            $this->loadProjectDockerComposeFile();
        }

        $this->dockerComposeFile = new DockerComposeYml;
    }

    private function loadProjectDockerComposeFile()
    {
        $parsedFile = Yaml::parse($this->fileSystem->read($this->project($this->dockerComposeFilename)));

        $this->dockerComposeFile = new DockerComposeYml($parsedFile);
    }

    private function wharf($filepath = '')
    {
        return self::WHARF.'://'.$filepath;
    }

    private function project($filepath = '')
    {
        return self::PROJECT.'://'.$filepath;
    }

    public function save()
    {
        $yaml = (new Dumper)->dump($this->dockerComposeFile->content(), 4);

        $this->projectDir()->put('docker-compose.yml', $yaml);

        array_map(function ($config) {
            $this->projectDir()->put($config->name(), $config);
        }, $this->configs);

    }

    public static function supportedPhpVersions()
    {
        return ['5.4', '5.5', '5.6', '7.0'];
    }

    public static function supportedDbSystems()
    {
        return ['mysql', 'postgres', 'sqlite', 'sql server'];
    }

    public function phpVersion()
    {
        return $this->dockerComposeFile->container('php')->tag();
    }

    public function setPhpVersion($version)
    {
        if (! in_array($version, Wharf::supportedPhpVersions())) {
            throw new \InvalidArgumentException(sprintf('The version %s for php is not valid.', $version));
        }

        return $this->dockerComposeFile->container('php')->setTag($version);
    }

    public function dbIsLocalhost()
    {
        return in_array($this->envFile()->get('DB_HOST'), ['localhost', '127.0.0.1']);
    }

    public function dbSystem()
    {
        return $this->dockerComposeFile->container('db')->image();
    }

    public function dbSystemVersion()
    {
        return $this->dockerComposeFile->container('db')->tag();
    }

    public function setEnvVariable($key, $value)
    {
        $this->envFile()->set($key, $value);

        $this->projectDir()->put($this->envFile()->name(), $this->envFile());
    }

    public function setDb($container)
    {
        $this->dockerComposeFile->setContainer('db', $container);
    }

    public function serveDir()
    {
        return $this->config('web')->get('SERVE_DIR');
    }

    public function setServeDir($directory)
    {
        $this->config('web')->set('SERVE_DIR', $directory);
    }

    public function config($name)
    {
        if (array_key_exists($name, $this->configs)) {
            return $this->configs[$name];
        }

        if (! $this->projectDir()->has(sprintf('.wharf/%s/variables', $name))) {
            $this->projectDir()->write(sprintf('.wharf/%s/variables', $name), '');
        }

        return $this->configs[$name] = Config::load(sprintf('.wharf/%s/variables', $name), $this->projectDir());
    }

    public function detectDirectoryToServe()
    {
        foreach (static::$commonDir as $dir) {
            if ($this->projectDir()->has($dir)) {
                return $dir;
            }
        }

        return '';
    }

    public function container($name)
    {
        return $this->dockerComposeFile->container($name);
    }

    public function addContainer($value='')
    {
        # code...
    }

    public function web()
    {
        return $this->dockerComposeFile->container('web');
    }

    public function db()
    {
        return $this->dockerComposeFile->container('db');
    }
}
