<?php

namespace Wharf\Processes;

use Symfony\Component\Process\Process;

class DockerComposeVersion extends Process
{
    const COMMAND = 'docker-compose -v';

    public function __construct()
    {
        parent::__construct($this->command());
    }

    protected function command()
    {
        return self::COMMAND;
    }
}
