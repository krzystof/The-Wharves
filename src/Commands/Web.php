<?php

namespace Wharf\Commands;

class Web extends Command
{
    protected $name = 'web';

    protected $description = 'Change the web server of your environment.';

    public function initialize()
    {
        parent::initialize();

        $this->webContainer = $this->project->web();
    }

    public function handle()
    {
        $this->webContainer->displayTo($this->output);

        $this->confirmUpdatingExistingConfig();

        $this->comment('Reading info from your .env file...');

        $this->webContainer->environmentFrom($this->project->envFile());

        $directory = $this->configureDirectoryToServe();

        $this->webContainer->configure(['DIRECTORY' => $directory]);

        $this->webContainer->eachInvalidOptions(function ($option) {
            $value = $this->promptWarning(sprintf('The setting "%s" is required, please enter a value:', $option));

            $this->webContainer->configure([$option => $value]);
        });

        $this->project->save();

        $this->info('The web container was configured successfully');

        $this->webContainer->displayTo($this->output);
    }

    public function confirmUpdatingExistingConfig()
    {
        if (! $this->webContainer->isNew() && ! $this->confirm('Do you want to update this configuration? [yes|NO]', false)) {
            $this->abort();
        }
    }

    protected function configureDirectoryToServe()
    {
        if ($this->webContainer->has('DIRECTORY')) {
            $this->info(
                sprintf('Serving from the directory "%s"', $this->webContainer->env('DIRECTORY'))
            );

            if (! $this->confirm('Do you want to update the directory to serve?', false)) {
                return;
            }
        } else {
            $this->comment('The directory to serve is not set.');
        }

        $directory = $this->project->detectDirectoryToServe();

        if ($this->confirmUsingDirectory($directory)) {
            return $directory;
        }

        return $this->prompt('Which directory should be served?');
    }

    protected function confirmUsingDirectory($directory)
    {
        return $this->confirm(sprintf(
            'The directory "/%s" is present. Would you like to serve from this location? [YES|no]',
            $directory
        ), true);
    }
}
