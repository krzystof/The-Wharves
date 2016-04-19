<?php

namespace WharfTests;

use Illuminate\Filesystem\Filesystem;


trait FilesystemContractTest
{
    /** @test */
    function it_should_check_if_a_directory_exists()
    {
        $this->assertFalse($this->filesystem->exists('testing_dir/no_dir'));
    }

    /** @test */
    function it_should_create_and_check_that_a_directory_exists()
    {
        $this->filesystem->makeDirectory('testing_dir/something');

        $this->assertTrue($this->filesystem->exists('testing_dir/something'));
    }

    /** @test */
    function it_should_put_content_in_a_file()
    {
        $this->filesystem->put('testing_dir/fill.txt', 'Some content');

        $this->assertContains('Some content', $this->filesystem->get('testing_dir/fill.txt'));
    }

    /** @test */
    function it_should_list_the_content_of_the_project_root()
    {
        $this->filesystem->makeDirectory('testing_dir/something');
        $this->filesystem->makeDirectory('testing_dir/something_else');

        $directories = $this->filesystem->directories('testing_dir/');

        $this->assertCount(2, $directories);
    }

    /** @test */
    function it_should_check_that_directory_has_writable_permission()
    {
        $this->filesystem->makeDirectory('testing_dir/writable');

        $this->filesystem->makeWritableByAll('testing_dir/writable');

        $this->assertTrue($this->filesystem->isWritableByAll('testing_dir/writable'));
    }

    /** @test */
    function it_should_check_that_directory_has_no_writable_permission()
    {
        $this->filesystem->makeDirectory('testing_dir/not_writable');

        $this->assertFalse($this->filesystem->isWritableByAll('testing_dir/not_writable'));
    }
}
