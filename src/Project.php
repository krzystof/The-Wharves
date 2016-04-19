<?php

namespace Wharf;

use Wharf\Project\Type;
use Wharf\Project\EnvFile;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;

class Project
{
    const WHARF = 'wharf';
    const PROJECT = 'project';

    private static $commonDir = ['public', 'web', 'www'];
    private $filesystem;
    private $projectRoot;
    private $dockerComposeFile;
    private $dockerComposeFilename = 'docker-compose.yml';
    private $envFile;

    public static function onDirectory($directory)
    {
        $filesystem = static::filesystem();

        return new static($filesystem, $directory, '.env');
    }

    public static function filesystem()
    {
        $filesystem = new Filesystem;

        Filesystem::macro('makeWritableByAll', (new ClosureFactory)->makeWritableByAll());
        Filesystem::macro('isWritableByAll', (new ClosureFactory)->isWritableByAll());

        return $filesystem;
    }

    public function __construct($filesystem, $projectRoot, $envFile = '.env')
    {
        $this->filesystem = $filesystem;

        $this->projectRoot = $projectRoot;

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

        return $this->loadEnvFile();
    }

    private function hasLoadedEnvFile()
    {
        return $this->envFile instanceof EnvFile;
    }

    private function projectDir()
    {
        return $this->filesystem;
    }

    private function loadEnvFile()
    {
        return $this->envFile = new EnvFile($this->envFile, $this->filesystem);
    }

    private function loadDockerComposeFile()
    {
        if ($this->filesystem->exists($this->project($this->dockerComposeFilename))) {
            return $this->loadProjectDockerComposeFile();
        }

        return $this->dockerComposeFile = new DockerComposeYml;
    }

    private function loadProjectDockerComposeFile()
    {
        $parsedFile = Yaml::parse($this->filesystem->get($this->project($this->dockerComposeFilename)));

        return $this->dockerComposeFile = new DockerComposeYml($parsedFile);
    }

    private function project($filepath = '')
    {
        return $this->projectRoot.'/'.$filepath;
    }

    public function save($container = null)
    {
        if ($container) {
            $this->dockerComposeFile->setContainer($container);
        }

        $this->dockerComposeFile->saveInFiles($this->filesystem);

        // $yaml = (new Dumper)->dump($this->dockerComposeFile->content(), DockerComposeYml::INDENTATION_DEPTH);

        // $this->filesystem->put('docker-compose.yml', $yaml);

        // $this->dockerComposeFile->savedAllContainers();
    }

    public function dbIsLocalhost()
    {
        return in_array($this->envFile()->get('DB_HOST'), ['localhost', '127.0.0.1']);
    }

    public function setEnvVariable($key, $value)
    {
        $this->envFile()->set($key, $value);

        $this->filesystem->put($this->envFile()->name(), $this->envFile());
    }

    public function detectDirectoryToServe()
    {
        foreach (static::$commonDir as $dir) {
            if ($this->filesystem->exists($dir)) {
                return $dir;
            }
        }

        return '';
    }

    public function service($name)
    {
        return $this->dockerComposeFile->container($name);
    }

    public function writableDirectories()
    {
        $contents = $this->filesystem->directories();

        return collect($contents)->filter(function ($content) {
            return $this->filesystem->isWritableByAll($content['path']);
        });
    }

    public function listRequiredWritableDirectories()
    {
        return WritableDirectories::project($this);
    }

    public function type()
    {
        return Type::detectOnFilesystem($this->filesystem);
    }

    public function isWritableByAll($path)
    {
        return $this->filesystem->isWritableByAll($path);
    }

    public function invalidPermissions()
    {
        return $this->type()->requiredWritableDirectories()->filter(function ($directory) {
            return !$this->filesystem->isWritableByAll($directory);
        });
    }

    public function updatePermissions()
    {
        $this->invalidPermissions()->each(function ($directory) {
            $this->filesystem->makeWritableByAll($directory);
        });
    }
}
