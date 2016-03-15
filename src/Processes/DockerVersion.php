<?php

namespace Wharf\Processes;

use Symfony\Component\Process\Process;

class DockerVersion extends Process
{
    const COMMAND = 'docker -v';

    public function __construct()
    {
        parent::__construct($this->command());
    }

    protected function command()
    {
        return self::COMMAND;
    }
}
