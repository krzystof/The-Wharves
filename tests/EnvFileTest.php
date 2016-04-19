<?php

namespace WharfTests;

use Wharf\Project\EnvFile;
use WharfTests\Doubles\InMemoryFilesystem;

class EnvFileTest extends \PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $this->filesystem = new InMemoryFilesystem;

        $this->filesystem->put('.env', "# WHARF SETTINGS \n DATABASE_HOST=db");

        $this->envFile = new EnvFile('.env', $this->filesystem);
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

    /** @test */
    function it_loads_an_empty_file_it_does_not_exist()
    {
        $envFile = new EnvFile('.noFile', $this->filesystem);

        $this->assertCount(0, $envFile);
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
