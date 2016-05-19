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

        $image = Image::make('php', 'wharf/php');

        $version = $this->choose(
            'Which version of php would you like to use?',
            $image->availableTags()->toArray(),
            $this->container->image()->tag()
        );

        $image = $image->versionTo($version);

        $this->setImageIfNotSame($image);

        $this->displayCurrentContainer();

        $this->saveProject();
    }
}
