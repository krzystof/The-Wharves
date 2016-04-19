<?php

namespace Wharf\Commands;

class Laravel extends Command
{
    protected $name = 'laravel';

    protected $description = 'Set up a docker compose environment for a laravel app.';

    public function handle()
    {
        $this->info('Setting up your Laravel project...');

        $this->runCommand('check')
             ->runCommand('php')
             ->runCommand('web')
             ->runCommand('db')
             ->runCommand('code')
             ->runCommand('permissions');

        $this->info('All done!');
    }
}
