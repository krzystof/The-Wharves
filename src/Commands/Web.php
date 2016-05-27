<?php

namespace Wharf\Commands;

class Web extends Command
{
    protected $name        = 'web';
    protected $description = 'Change the web server on your environment.';

    public function handle()
    {
        $this->setContainerForService('web');

        $this->displayCurrentContainerAndConfirmUpdate();

        $this->container->image('wharf/nginx:1.8');

        $this->info(sprintf(
            'Using %s version %s',
            $this->container->image()->name(),
            $this->container->image()->tag()
        ));

        $this->sourceEnvironment();

        $directory = $this->configureDirectoryToServe();

        $this->container->configure(['DIRECTORY' => $directory]);

        $this->checkContainerInvalidOptions();

        $this->project->save($this->container);

        $this->info('The web container was configured and saved successfully');

        $this->container->displayTo($this->output);
    }

    protected function configureDirectoryToServe()
    {
        if ($this->container->has('DIRECTORY')) {
            $this->info(
                sprintf('Serving from the directory "%s"', $this->container->env('DIRECTORY'))
            );

            if (! $this->confirm('Do you want to update the directory to serve?', 'no')) {
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
            'The directory "/%s" is present. Would you like to serve from this location?',
            $directory
        ), 'yes');
    }
}
