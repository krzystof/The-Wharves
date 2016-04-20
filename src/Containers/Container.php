<?php

namespace Wharf\Containers;

use Wharf\Project\EnvFile;
use Illuminate\Contracts\Support\Arrayable;

abstract class Container implements Arrayable
{
    protected $config;

    protected $updated = false;

    abstract public static function service();

    abstract protected function configurables();

    abstract protected function requiredSettings();

    public static function fromConfig($config)
    {
        return new static($config);
    }

    protected function __construct($config = [])
    {
        $this->config = collect($config);

        $this->validateImage();
    }

    protected function validateImage()
    {
        if ($this->config->has('image')) {
            $image = Image::make($this->service().':'.$this->config->get('image'));

            $this->config->put('image', $image);
        }
    }

    public function env($key)
    {
        if (! $this->isValidSetting($key)) {
            return null;
        }

        if (! $this->environment()->has($key)) {
            return 'not_set';
        }

        return $this->environment()->get($key);
    }

    public function has($option)
    {
        return $this->environment()->has($option);
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
    }

    public function saved()
    {
        $this->updated = false;
    }

    public function isNew()
    {
        return count($this->config) === 0;
    }

    public function image($image = false)
    {
        if ($image) {
            return $this->setImage($image);
        }

        return $this->config->has('image') ? $this->config->get('image') : Image::makeEmpty();
    }

    protected function setImage($image)
    {
        $image = Image::make($this->service().':'.$image);

        $this->config->put('image', $image);

        $this->changed();

        return $this;
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
        return collect(['image', 'environment'])->search($option);
    }

    protected function isValid()
    {
        return $this->image()->exists() && $this->invalidSettings()->count() === 0;
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
        return $this->isValid() ? '<info>OK</info>' : '<error>ERROR</error>';
    }

    public function displayTo($output)
    {
        $output->writeln("\n");

        $output->writeln(sprintf(
            'About: <comment>%s container</comment> (%s)',
            $this->service(),
            $this->state()
        ));

        $output->writeln(sprintf('Config: %s', $this->configState()));

        if ($this->isNew()) {
            return $output->writeln(sprintf(
                '%s<error>This container does not exist.</error>%s',
                "\n",
                "\n"
            ));
        }

        $output->writeln(sprintf('Image: %s', $this->image()->name()));
        $output->writeln(sprintf('Version: %s', $this->image()->tag()));

        $this->configurables()->each(function ($setting) use ($output) {
            $output->writeln(sprintf('%s:%s%s', $setting, "\t", $this->env($setting)));
        });
    }
}
