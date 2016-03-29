<?php

use Wharf\Project\Config;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    function it_should_set_and_get_variables()
    {
        $config = new Config;

        $config->put('TEST', 'right');

        $this->assertEquals('right', $config->get('TEST'));
    }

    /** @test */
    function it_should_replace_a_variable_if_it_is_already_set()
    {
        $config = new Config;

        $config->put('TEST', 'wrong');
        $config->put('TEST', 'right');

        $this->assertEquals('right', $config->get('TEST'));
        $this->assertCount(1, $config);
    }
}
