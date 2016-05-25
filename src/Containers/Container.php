<?php

namespace Wharf\Containers;

use Wharf\Project\EnvFile;
use Illuminate\Contracts\Support\Arrayable;

abstract class Container implements Arrayable
{
    protected $config;
    protected $updated = false;
    private $isNew;

    abstract public function service();
    abstract protected function defaultSettings();
    abstract protected function configurables();
    abstract protected function requiredSettings();

    public static function fromConfig($containerName, $config, $envFile)
    {
        return new static($containerName, $config, $envFile);
    }

    private function __construct($containerName, $config = [], $envFile = [])
    {
        $this->name = $containerName;

        $this->isNew = count($config) === 0;

        if ($this->defaultSettings()->has('env_file')) {
            $config['env_file'] = $envFile;
        }

        $this->config = $this->defaultSettings()->merge($config);
    }

    public function env($key)
    {
        if (! $this->isValidSetting($key)) {
            return null;
        }

        if ($this->config->has('environment') && $this->config->get('environment')->has($key)) {
            return $this->environment()->get($key);
        }

        if (is_null($this->envFile())) {
            return 'not_set';
        }

        if ($this->envFile()->has($key)) {
            return $this->config->get('env_file')->get($key);
        }

        return 'not_set';
    }

    public function config()
    {
        return $this->config;
    }

    private function envFile()
    {
        return $this->config->has('env_file') ? $this->config->get('env_file') : new Collection;
    }

    public function has($option)
    {
        return $this->envFile()->has($option) || $this->config()->get('environment')->has($option);
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

    public function configure($options)
    {
        collect($options)->each(function ($value, $setting) {
            $this->environmentFrom($this->environment()->put($setting, $value));
        });
    }

    protected function filterConfig($config)
    {
        return collect($config)->filter(function ($default, $option) {
            return $this->isValidSetting($option);
        });
    }

    protected function isValidSetting($option)
    {
        return $this->configurables()->contains($option);
    }

    public function eachInvalidOptions($callback)
    {
        $invalidOptions = $this->requiredSettings()->filter(function ($default, $option) {
            return in_array($this->env($option), ['not_set', null]);
        });

        $invalidOptions->each($callback);
    }

    protected function changed()
    {
        $this->updated = true;
        $this->isNew = false;

        return $this;
    }

    public function saved()
    {
        $this->updated = false;
    }

    public function isNew()
    {
        return $this->isNew;
    }

    public function image($image = false)
    {
        if ($image) {
            return $this->setImage($image);
        }

        if (! $this->config->has('image')) {
            return Image::makeEmpty();
        }

        return Image::makeFor($this);
    }

    private function setImage($image)
    {
        $image = Image::make($this->service(), $image);

        $this->config->put('image', $image);

        return $this->changed();
    }

    protected function environment()
    {
        return collect($this->config->get('environment', []));
    }

    public function toArray()
    {
        if (! $this->isValid()) {
            throw new InvalidContainer(sprintf(
                'The container "%s" is not configured properly and cannot be saved.',
                $this->service()
            ));
        }

        return $this->config->sortBy(function ($value, $option) {
            return $this->orderOfOption($option);
        })->toArray();
    }

    private function orderOfOption($option)
    {
        return collect(['image', 'env_file', 'environment'])->search($option);
    }

    protected function isValid()
    {
        return $this->image()->exists() && $this->invalidSettings()->isEmpty();
    }

    protected function invalidSettings()
    {
        return $this->requiredSettings()->filter(function ($value, $setting) {
            return $this->env($setting) === 'not_set';
        });
    }

    protected function state()
    {
        if ($this->isNew()) {
            return '<comment>NEW</comment>';
        }

        if ($this->updated) {
            return '<comment>UPDATED</comment>';
        }

        return '<info>SAVED</info>';
    }

    protected function configState()
    {
        if ($this->image()->isCustom()) {
            return '<info>CUSTOM</info>';
        }

        return $this->isValid() ? '<info>OK</info>' : '<error>ERROR</error>';
    }

    public function displayTo($output)
    {
        $output->writeln("\n");

        $output->writeln(sprintf(
            'About    <comment>%s container</comment> (%s)',
            $this->service(),
            $this->state()
        ));

        $output->writeln(sprintf("Config   %s", $this->configState()));
        $output->writeln(sprintf('Image    %s', $this->image()->name()));
        $output->writeln(sprintf('Version  %s', $this->image()->tag()));

        $output->writeln("\n");

        $this->configurables()->each(function ($setting) use ($output) {
            $output->writeln(sprintf('%s - %s%s', $setting, ' ', $this->env($setting)));
        });
    }
}
