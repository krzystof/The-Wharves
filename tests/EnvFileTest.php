<?php

use Wharf\Project\EnvFile;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;

class EnvFileTest extends PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $this->fileSystem = new Filesystem(new MemoryAdapter);

        $this->fileSystem->put('.env', "# WHARF SETTINGS \n DATABASE_HOST=db");

        $this->envFile = new EnvFile('.env', $this->fileSystem);
    }

    function tearDown()
    {
        if (file_exists('stubs/.savedFile')) {
            unlink('stubs/.savedFile');
        }

        if (file_exists('.wharf/env')) {
            unlink('.wharf/env');
            rmdir('.wharf');
        }
    }

    /** @test */
    function it_loads_the_value_in_the_given_file()
    {
        $this->assertEquals('db', $this->envFile->get('DATABASE_HOST'));
    }

    /** @test */
    function it_returns_an_empty_string_if_the_config_is_not_set()
    {
        $this->assertEquals('', $this->envFile->get('NOT_SET'));
    }

    /** @test @expectedException Exception */
    function it_loads_throws_an_exception_if_the_file_does_not_exist()
    {
        new EnvFile('.noFile', $this->fileSystem);
    }

    /** @test */
    function it_sets_value()
    {
        $this->envFile->set('NEW_VAR', 'is here');

        $this->assertEquals('is here', $this->envFile->get('NEW_VAR'));
    }

    /** @test */
    function it_returns_its_file_name()
    {
        $this->assertEquals('.env', $this->envFile->name());
    }
}