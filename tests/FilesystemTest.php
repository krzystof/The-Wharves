<?php

namespace WharfTests;

use Wharf\Project;
use Illuminate\Filesystem\Filesystem;
use WharfTests\FilesystemContractTest;

class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    use FilesystemContractTest;

    public function setUp()
    {
        $this->filesystem = Project::filesystem();

        $this->filesystem->makeDirectory('testing_dir/');
    }

    public function tearDown()
    {
        $this->filesystem->deleteDirectory('testing_dir/');
    }
}
