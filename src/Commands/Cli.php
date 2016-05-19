<?php

namespace Wharf\Commands;

use Wharf\Containers\Image;

class Cli extends Command
{
    protected $name        = 'cli';
    protected $description = 'Update the php version of the cli tools to match the php service.';

    public function handle()
    {
        $this->setContainerForService('cli');

        $this->displayCurrentContainerAndConfirmUpdate();

        $image = Image::make('cli', 'wharf/php');

        $version = $this->project->service('php')->image()->version();

        dd($version);

        $image = $image->versionTo($version);

        $this->setImageIfNotSame($image);

        $this->displayCurrentContainer();

        $this->saveProject();
    }
}
