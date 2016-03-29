<?php

namespace Wharf\Containers;

class WebContainer extends Container
{
    const SERVICE = 'web';

    protected $type = 'web';

    protected $name = 'Web Container';

    protected $config;

    public static function supports($software)
    {
        return collect(['nginx'])->contains($software);
    }

    public function name()
    {
        return $this->name;
    }

    public static function database($dbName)
    {
        $dbContainer = new static($dbName, ['image' => static::imageFor($dbName)]);

        return $dbContainer;
    }

    public function toArray()
    {
        return $this->config;
    }

    public function infos()
    {
        return [];
    }

    protected function configurables()
    {
        return collect(['APP_URL', 'DIRECTORY']);
    }

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
            throw new Exception(sprintf('The container %s does not have default config set.', $name));
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

    public function service()
    {
        return 'web';
    }

    protected function state()
    {
        if ($this->isNew()) {
            return '<comment>NEW</comment>';
        }

        if ($this->changed) {
            return '<comment>CHANGED</comment>';
        }

        return '<info>SAVED</info>';
    }

    protected function configState()
    {
        return '<error>ERROR</error>';
    }

    protected function display($config)
    {
        if (! isset($this->$config)) {
            return 'not set';
        }
    }

    public function displayConfigState()
    {
        return '<error>ERROR</error>';
    }

    public function displayTo($output)
    {
        $output->writeln(sprintf('About: <comment>%s container</comment> (%s)', $this->service(), $this->state()));

        $output->writeln(sprintf('Config: %s', $this->configState()));

        if ($this->isNew()) {
            return $output->writeln(sprintf(
                '%s<error>This container does not exist.</error>%s',
                "\n",
                "\n"
            ));
        }

        $output->writeln(sprintf('Image: %s', $this->image()));
        $output->writeln(sprintf('Version: %s', $this->tag()));
    }
}
