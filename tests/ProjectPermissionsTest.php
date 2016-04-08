<?php

namespace WharfTest;

use Wharf\Project;
use League\Flysystem\Filesystem;
use Illuminate\Support\Collection;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Memory\MemoryAdapter;

class ProjectPermissionsTest extends \PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $adapter = new Local(__DIR__.'/flysystem/', LOCK_EX, Local::DISALLOW_LINKS, [
            'file' => [
                'public' => 0744,
                'private' => 0700,
            ],
            'dir' => [
                'public' => 0777,
                'private' => 0700,
            ]
        ]);

        $this->filesystem = new Filesystem($adapter);

        $this->project = Project::onFilesystem($this->filesystem);

        $this->filesystem->createDir('/testing/', ['visibility' => 'private']);
    }

    function tearDown()
    {
        $this->filesystem->deleteDir('testing');

        rmdir(__DIR__.'/flysystem/');
    }

    /** @test */
    function it_should_returns_an_empty_collection_of_writable_directories_if_there_isnt_any()
    {
        $this->filesystem->createDir('/testing/private_one', ['visibility' => 'private']);

        $writableDirectories = $this->project->writableDirectories();

        $this->assertInstanceof(Collection::class, $writableDirectories);
        $this->assertTrue($writableDirectories->isEmpty());
    }

    /** @test */
    function it_should_get_a_directory_if_it_has_writable_permissions()
    {
        $this->filesystem->createDir('/testing/public_one', ['visibility' => 'public']);
        $this->filesystem->createDir('/testing/private_one', ['visibility' => 'private']);

        $writableDirectories = $this->project->writableDirectories();

        $this->assertCount(1, $writableDirectories);
    }

    /** @test */
    function it_should_get_a_nested_directory_if_it_has_writable_permissions()
    {
        // TODO
        $this->markTestSkipped();

        $this->filesystem->createDir('/testing/public_one/nested', ['visibility' => 'public']);

        $writableDirectories = $this->project->writableDirectories();

        $this->assertCount(2, $writableDirectories);
    }

    /** @test */
    function it_should_detect_many_writable_directories()
    {
        // TODO
        $this->markTestSkipped();

        $this->filesystem->createDir('/testing/public_one/nested', ['visibility' => 'public']);
        $this->filesystem->createDir('/testing/public_two', ['visibility' => 'public']);

        $writableDirectories = $this->project->writableDirectories();

        $this->assertCount(3, $writableDirectories);
    }
}
