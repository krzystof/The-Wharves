<?php

namespace WharfTests\Doubles;

use WharfTests\FilesystemContractTest;

class InMemoryFilesystemTest extends \PHPUnit_Framework_TestCase
{
    use FilesystemContractTest;

    public function setUp()
    {
        $this->filesystem = new InMemoryFilesystem;

        $this->filesystem->makeDirectory('testing_dir/');
    }

    public function tearDown()
    {
        $this->filesystem->deleteDirectory('testing_dir/');
    }
}
