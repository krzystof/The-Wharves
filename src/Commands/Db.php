<?php

namespace Wharf\Commands;

use Wharf\Wharf;
use Wharf\Project\Container;

class Db extends Command
{
    protected $name = 'db';

    protected $description = 'Change the database system or version of your environment.';

    public function handle()
    {
        if ($this->project->detectEnvFile()) {
            $this->info(sprintf(
                'Reading environment informations from the file "%s"',
                $this->project->envFile()->name()
            ));
        } else {
            $specifyFile = $this->ask('Do you want to load a specific file with environment variables?');

            if ($specifyFile) {
                $filePath = $this->ask('Please type in the path relative to the project root:');

                $project->loadEnvFile($filePath);
            } else {
                $project->useWharfEnvFile();
            }
        }

        $this->info(sprintf('Currently using %s %s', $this->project->dbSystem(), $this->project->dbSystemVersion()));

        if ($this->project->dbIsLocalhost()) {
            $agrees = $this->askConfirmation('The database host is set to localhost. It will be set to your "db" to point to a docker container. [y/N]');

            if ($agrees) {
                $this->project->setEnvVariable('DB_HOST', 'db');
            } else {
                $this->abort('The command was cancelled.');
            }
        }

        $db = $this->choose('Please select the db system to use (empty will keep the current db):', Wharf::supportedDbSystems(), $this->project->dbSystem());

        $dbContainer = Container::database($db);

        $this->info(sprintf('Setting your database container to %s version %s', $dbContainer->image(), $dbContainer->tag()));

        $this->project->setDb($dbContainer);

        // TODO put a script in .wharf to create the database on the fly.
        // use .env: ()
        // DB_HOST=127.0.0.1
        // DB_DATABASE=homestead
        // DB_USERNAME=homestead
        // DB_PASSWORD=secret


        $this->project->save();
    }
}
