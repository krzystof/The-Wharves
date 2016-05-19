<?php

namespace WharfTests;

use Wharf\Project\EnvFile;

class EnvFileTest extends \PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $this->envFile = new EnvFile('.env', "# WHARF SETTINGS \n DATABASE_HOST=db");
    }

    function test_it_loads_the_value_in_the_given_file()
    {
        $this->assertEquals('db', $this->envFile->get('DATABASE_HOST'));
    }

    function test_it_returns_an_empty_string_if_the_config_is_not_set()
    {
        $this->assertEquals('', $this->envFile->get('NOT_SET'));
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
