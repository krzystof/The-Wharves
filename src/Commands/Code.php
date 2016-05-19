<?php

namespace Wharf\Commands;

use Wharf\Wharf;
use Wharf\Containers\Image;
use Wharf\Project\Container;

class Code extends Command
{
    protected $name = 'code';

    protected $description = 'Create a code data container.';

    public function handle()
    {
        $this->container = $this->project->service('code');

        $this->displayCurrentContainerAndConfirmUpdate();

        $image = Image::make('code', 'wharf/code');

        $this->setImageIfNotSame($image)
             ->displayCurrentContainer()
             ->saveProject();
    }
}
