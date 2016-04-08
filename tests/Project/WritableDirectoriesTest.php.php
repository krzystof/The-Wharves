<?php

namespace WharfTests\Project;


class WritableDirectoriesTest extends \PHPUnit_Framework_TestCase
{
    // * @test
    function it_should_returns_laravel_required_writable_dirs()
    {
        $laravelProject = $this->dummyLaravelProject();

        $directories = WritableDirectories::project($laravelProject);

        $this->assertContains('storage', $directories);
    }

    private function dummyLaravelProject()
    {
        $this->fileSystem = new Filesystem(new MemoryAdapter);

        return Project::onFilesystem($this->fileSystem);
    }
}
