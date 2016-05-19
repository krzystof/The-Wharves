<?php

namespace Wharf\Commands;

class Init extends Command
{
    protected $name = 'init';

    protected $description = 'Set up a docker compose environment for a laravel app.';

    protected $optionFlags = ['fast' => 'Set up the environment with the default settings'];

    public function handle()
    {
        $this->info('Setting up your project...');

        $this->runCommandOnProject('check')
             ->runCommandOnProject('php')
             ->runCommandOnProject('cli')
             ->runCommandOnProject('web')
             ->runCommandOnProject('db')
             ->runCommandOnProject('code')
             ->runCommandOnProject('permissions');

        $this->info('All done!');
    }
}
