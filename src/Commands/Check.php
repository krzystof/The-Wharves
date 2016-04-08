<?php

namespace Wharf\Commands;

use Wharf\Processes\DockerVersion;
use Wharf\Exceptions\EnvironmentNotReady;
use Wharf\Processes\DockerComposeVersion;

class Check extends Command
{
    protected $name = 'check';

    protected $description = 'Check that your machine meets the requirements.';

    public function handle()
    {
        $dockerVersion = new DockerVersion;
        $dockerVersion->run();

        if (!$dockerVersion->isSuccessful()) {
            throw new EnvironmentNotReady($process->getErrorOutput());
        }

        // TODO display here the version used

        $dockerComposeVersion = new DockerComposeVersion;
        $dockerComposeVersion->run();

        if (!$dockerComposeVersion->isSuccessful()) {
            throw new EnvironmentNotReady($process->getErrorOutput());
        }

        // TODO display here the version used

        $this->info('Docker and docker-compose are installed. Everything is good to go.');
    }
}
