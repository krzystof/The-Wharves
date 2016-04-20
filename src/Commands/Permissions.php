<?php

namespace Wharf\Commands;

class Permissions extends Command
{
    protected $name = 'permissions';

    protected $description = 'Set up permissions on your app directories.';

    public function handle()
    {
        $this->project->type()->requiredWritableDirectories()->each(function ($directory) {
            $this->project->isWritableByAll($directory)
                ? $this->info(sprintf('The directory "%s" is writable.', $directory))
                : $this->error(sprintf('The directory "%s" is not writable.', $directory));
        });

        if ($this->confirmPermissionsUpdate()) {
            $this->project->updatePermissions();

            $this->info('The permissions have been updated on your project filesystem.');
        }

    }

    private function displayCurrentWritableDirectories()
    {
        $writableDirectories = $this->project->writableDirectories();

        if ($writableDirectories->isEmpty()) {
            return $this->comment('No directories are writable');
        }

        $this->comment('Current writable directories to docker:');

        $this->project->writableDirectories()->each(function ($directory) {
            $this->comment($directory['path']);
        });
    }

    private function confirmPermissionsUpdate()
    {
        return ! $this->project->invalidPermissions()->isEmpty()
              && $this->confirmOrAbort('Wharf will change the permissions required on the directories.', 'no');
    }
}
