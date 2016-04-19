<?php

namespace WharfTest;

use Wharf\Project;
use Illuminate\Support\Collection;
use WharfTests\Doubles\InMemoryFilesystem;

class ProjectPermissionsTest extends \PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $this->filesystem = new InMemoryFilesystem;

        $this->project = new Project($this->filesystem, getcwd());
    }

    /** @test */
    function it_should_returns_an_empty_collection_of_writable_directories_if_there_isnt_any()
    {
        $this->filesystem->makeDirectory('/testing/private_one');

        $writableDirectories = $this->project->writableDirectories();

        $this->assertInstanceof(Collection::class, $writableDirectories);
        $this->assertTrue($writableDirectories->isEmpty());
    }

    /** @test */
    function it_should_get_a_directory_if_it_has_writable_permissions()
    {
        $this->filesystem->makeDirectory('testing/private_one');
        $this->filesystem->makeDirectory('testing/public_one');

        $this->filesystem->makeWritableByAll('testing/public_one');

        $writableDirectories = $this->project->writableDirectories();

        $this->assertCount(1, $writableDirectories);
    }

    /** @test */
    function it_should_detect_many_writable_directories()
    {
        $this->filesystem->makeDirectory('testing/public_one/nested');
        $this->filesystem->makeDirectory('testing/public_two');

        $this->filesystem->makeWritableByAll('testing/public_one/nested');
        $this->filesystem->makeWritableByAll('testing/public_two');

        $writableDirectories = $this->project->writableDirectories();

        $this->assertCount(2, $writableDirectories);
    }
}
