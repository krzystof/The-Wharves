<?php

namespace Wharf\Commands;

use Wharf\Containers\Image;

class Php extends Command
{
    protected $name        = 'php';
    protected $description = 'Change the php version of your environment.';

    public function handle()
    {
        $this->setContainerForService('php');

        $this->displayCurrentContainerAndConfirmUpdate();

        $phpImage = Image::make('php', 'wharf/php');

        $version = $this->choose(
            'Which version of php would you like to use?',
            $phpImage->availableTags()->toArray(),
            $this->container->image()->tag()
        );

        $phpImage = $phpImage->versionTo($version);

        $this->setImageIfNotSame($phpImage)
             ->updateCliContainer($version)
             ->displayCurrentContainer()
             ->saveProject();
    }

    private function updateCliContainer($version)
    {
        $cliImage   = Image::make('cli', 'wharf/cli')->versionTo($version);
        $cliService = $this->project->service('cli')->image($cliImage);
        $this->project->save($cliService);

        return $this;
    }
}
