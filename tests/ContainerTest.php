<?php

use Wharf\Project\Container;

class ContainerTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    function it_checks_if_a_container_is_supported()
    {
        $this->assertTrue(Container::isSupported('php'));
        $this->assertFalse(Container::isSupported('ruby'));
    }

    /** @test */
    function it_creates_a_database_container()
    {
        $container = Container::database('mysql');

        $this->assertEquals('mysql', $container->image());
        $this->assertEquals('5.7', $container->tag());
    }

    /** @test */
    function it_returns_no_container_for_an_empty_image()
    {
        $container = new Container;

        $this->assertEquals('no container', $container->image());
        $this->assertEmpty($container->tag());
    }
}
