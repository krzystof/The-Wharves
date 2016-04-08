<?php

namespace Wharf\Commands;

use Wharf\Wharf;
use Wharf\Containers\Image;
use Wharf\Project\Container;

class Db extends Command
{
    protected $name = 'db';

    protected $description = 'Change the database system or version of your environment.';

    public function handle()
    {
        $this->container = $this->project->db();

        $this->displayCurrentContainerAndConfirmUpdate()
             ->sourceEnvironment();

        if ($this->project->dbIsLocalhost()) {
            $this->confirmOrAbort(
                'The database host is set to localhost. It will be set to your "db" to point to a docker container.',
                'yes'
            );

            $this->project->setEnvVariable('DB_HOST', 'db');
        }

        $database = $this->choose(
            'Select the db system to use:',
            Image::show('db')->toArray(),
            $this->container->image()->name()
        );

        $image = Image::make('db:'.$database);

        $version = $this->choose(
            'Select the version to use:',
            $image->availableTags()->toArray(),
            $this->container->image()->tag()
        );

        $image = $image->versionTo($version);

        $this->setImageIfNotSame($image)
             ->checkContainerInvalidOptions()
             ->displayCurrentContainer()
             ->saveProject();
    }
}
