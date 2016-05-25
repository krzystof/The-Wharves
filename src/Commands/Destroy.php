<?php

namespace Wharf\Commands;

use Wharf\Wharf;
use Wharf\Containers\Image;
use Wharf\Project\Container;

class Destroy extends Command
{
    protected $name        = 'destroy';
    protected $description = 'Destroy a container';
    protected $arguments   = ['container' => 'The container that you would like to destroy.'];

    public function handle()
    {
        $service = $this->argument('container');

        if ($this->project->service($service)->isNew()) {
            $this->abort(sprintf('The service "%s" is not in your docker-compose.yml file.', $service));
        }

        $message = sprintf('This command will remove the container "%s" from your docker composer file. '
                          .'Are you sure?', $service);

        $this->confirmOrAbort($message, 'no');

        $this->project->remove($service);
    }
}
