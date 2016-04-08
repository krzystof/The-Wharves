<?php

namespace Wharf\Commands;

class Permissions extends Command
{
    protected $name = 'permissions';

    protected $description = 'Set up permissions on your app directories.';

    public function handle()
    {
        // list all current writable folders for docker (777)
        $this->displayCurrentWritableDirectories();

        // foreach of the directories that requires write access, display if it is ok or not

        // if there were some directories locked and the user confirm, chmod

        // info all good
    }

    private function displayCurrentWritableDirectories()
    {
        $writableDirectories = $this->project->writableDirectories();

        if ($writableDirectories->isEmpty()) {
            return $this->comment('No directories are writable');
        }

        $this->comment('Current writable directories:');

        $this->project->writableDirectories()->each(function ($directory) {
            $this->comment($directory['path']);
        });
    }
}
